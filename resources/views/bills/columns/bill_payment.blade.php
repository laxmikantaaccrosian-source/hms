@role('Patient')
    @if ($row->payment_mode == '0')
        <span class="badge bg-light-primary">{{ __('messages.bill.stripe') }}</span>
    @elseif($row->payment_mode == '1')
        <span class="badge bg-light-info">{{ __('messages.bill.manually') }}</span>
    @elseif($row->payment_mode == '2')
        <span class="badge bg-light-success">{{ __('messages.razorpay') }}</span>
    @else
        {{ Form::select('payment_type', getPaymentTypes(), null, ['id' => 'paymentModeType', 'data-id' => $row->id, 'class' => 'form-select', 'placeholder' => __('messages.common.choose') . ' ' . __('messages.ipd_payments.payment_mode'), 'data-control' => 'select2', 'required']) }}
    @endif
@endrole
