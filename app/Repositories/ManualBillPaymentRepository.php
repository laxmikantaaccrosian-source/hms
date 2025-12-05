<?php

namespace App\Repositories;

use App\Models\ManualBillPayment;
use App\Models\Bill;
use Stripe\Checkout\Session;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Exception;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class ManualBillPaymentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'transaction_id',
        'payment_type',
        'amount',
        'bill_id',
        'status',
        'meta',
        'is_manual_payment',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return ManualBillPayment::class;
    }

    public function create($input)
    {
        $bill = Bill::find($input['id']);

        if(!empty($bill)){
            ManualBillPayment::create([
                'payment_type' => $input['payment_type'],
                'amount' => $bill->amount,
                'bill_id' => $bill->id,
                'status' => $input['payment_type'] == 1 ? 0 : 1,
                'is_manual_payment' => $input['payment_type'] == 1 ? 1 : 0,
            ]);
            $bill->update(['payment_mode' => $input['payment_type']]);
        }

        return true;
    }

    public function updateTransaction($input, $id)
    {
        $billTransaction = ManualBillPayment::with('bill')->find($id);

        if ($input['payment_status'] == ManualBillPayment::Approved) {
            $billTransaction->update(['status' => ManualBillPayment::Approved]);
            $billTransaction->bill()->update(['status' => ManualBillPayment::Approved]);
        }else{
            $billTransaction->update(['status' => ManualBillPayment::Rejected]);
            $billTransaction->bill()->update(['status' => ManualBillPayment::Rejected,'payment_mode' => null]);
        }
    }

    // Make stripe payment
    public function createStripeSession($patientBill)
    {
        setStripeApiKey();

        $session = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $patientBill->patient->patientUser->email,
            'line_items' => [
                [
                    'price_data' => [
                        'product_data' => [
                            'name' => 'Payment for Patient bill',
                        ],
                        'unit_amount' => in_array(strtoupper(getCurrentCurrency()), zeroDecimalCurrencies()) ? $patientBill->amount : $patientBill->amount * 100,
                        'currency' => strtoupper(getCurrentCurrency()),
                    ],
                    'quantity' => 1,
                ],
            ],
            'client_reference_id' => $patientBill->id,
            'mode' => 'payment',
            'success_url' => route('stripe.payment.success').'?session_id={CHECKOUT_SESSION_ID}',
        ]);

        $result = [
            'sessionId' => $session['id'],
        ];

        return $result;
    }

    //after stripe payment show stripe success
    public function stripePaymentSuccess($sessionId)
    {
        if (empty($sessionId)) {
            throw new UnprocessableEntityHttpException(__('messages.bill.session_id_required'));
        }
        setStripeApiKey();

        $sessionData = \Stripe\Checkout\Session::retrieve($sessionId);
        $bill = Bill::find($sessionData->client_reference_id);

        try {
            DB::beginTransaction();

            if(!empty($bill)){
                ManualBillPayment::create([
                    'transaction_id' => null,
                    'payment_type' => Bill::Stripe,
                    'amount' => $bill->amount,
                    'bill_id' => $bill->id,
                    'status' => 1,
                    'meta' => null,
                    'is_manual_payment' => 0,
                ]);
                $bill->update(['payment_mode' => Bill::Stripe, 'status' => '1']);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

    }

    // Make razorpay payment
    public function razorpayPayment($billId)
    {
        $patientBill = bill::with('patient.patientUser')->whereId($billId)->first();
        $amount = $patientBill->amount;

        // $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret_key'));
        $api = new Api(getPaymentCredentials('razorpay_key'), getPaymentCredentials('razorpay_secret'));

        $orderData = [
            'receipt' => '1',
            'amount' => $amount * 100, // 100 = 1 rupees
            'currency' => strtoupper(getCurrentCurrency()),
            'notes' => [
                'billID' => $billId,
            ],
        ];
        $razorpayOrder = $api->order->create($orderData);
        $data['id'] = $razorpayOrder->id;
        $data['amount'] = $amount;

        return $data;
    }

    // after razorpay payment show razorpay success
    public function razorpayPaymentSuccess($input)
    {
        Log::info('RazorPay Payment Successfully');
        // $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret_key'));
        $api = new Api(getPaymentCredentials('razorpay_key'), getPaymentCredentials('razorpay_secret'));

        if (count($input) && ! empty($input['razorpay_payment_id'])) {
            try {
                DB::beginTransaction();

                $payment = $api->payment->fetch($input['razorpay_payment_id']);
                $generatedSignature = hash_hmac('sha256', $payment['order_id'].'|'.$input['razorpay_payment_id'],getPaymentCredentials('razorpay_secret'));

                if ($generatedSignature != $input['razorpay_signature']) {
                    return redirect()->back();
                }
                // Create Transaction Here
                $billId = $payment['notes']['billID'];
                $bill = Bill::find($billId);

                if(!empty($bill)){
                    ManualBillPayment::create([
                        'transaction_id' => null,
                        'payment_type' => Bill::Razorpay,
                        'amount' => $bill->amount,
                        'bill_id' => $bill->id,
                        'status' => 1,
                        'meta' => null,
                        'is_manual_payment' => 0,
                    ]);
                    $bill->update(['payment_mode' => Bill::Razorpay, 'status' => '1']);
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw new UnprocessableEntityHttpException($e->getMessage());
            }

            return false;
        }
    }
}
