<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\ManualBillPayment;
use App\Repositories\ManualBillPaymentRepository;
use Exception;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class ManualBillPaymentController extends AppBaseController
{

     /** @var ManualBillPaymentRepository */
     private $manualBillPaymentRepository;

     public function __construct(ManualBillPaymentRepository $manualBillPaymentRepository)
     {
         $this->manualBillPaymentRepository = $manualBillPaymentRepository;
     }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manual_bill_payments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        if($request->payment_type == Bill::Stripe){
            $patientBill = bill::with('patient.patientUser')->whereId($input['id'])->first();

            $result = $this->manualBillPaymentRepository->createStripeSession($patientBill);

            return $this->sendResponse([
                'bill_id' => $patientBill->id,
                'payment_type' => $input['payment_type'],
                $result
            ],'Stripe session created successfully');

        }elseif($request->payment_type == Bill::Razorpay){

            return $this->sendResponse([
                'payment_type' => $input['payment_type'],
                'bill_id' => $input['id'],
            ],'Razorpay session created successfully');

        }else{
            $this->manualBillPaymentRepository->create($input);

            return $this->sendSuccess(__('messages.bill.paymentrequest_sent'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $this->manualBillPaymentRepository->updateTransaction($input, $id);

        return $this->sendSuccess(__('messages.common.status'). ' '.__('messages.common.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function paymentSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        $this->manualBillPaymentRepository->stripePaymentSuccess($sessionId);

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect(route('employee.bills.index'));
    }

    public function onBoard(Request $request)
    {
        $billId = $request->bill_id;

        $data = $this->manualBillPaymentRepository->razorpayPayment($billId);

        return $this->sendResponse($data, 'order created');
    }

    public function razorpayPaymentSuccess(Request $request)
    {
        $input = $request->all();

        $this->manualBillPaymentRepository->razorpayPaymentSuccess($input);

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect(route('employee.bills.index'));
    }
}
