@if ($row->payment_mode == \App\Models\IpdPayment::PAYMENT_MODES_STRIPE)
    <span class="badge bg-light-primary">{{ __('messages.bill.stripe') }}</span>
@elseif($row->payment_mode == \App\Models\IpdPayment::PAYMENT_MODES_RAZORPAY)
    <span class="badge bg-light-success">{{ __('messages.razorpay') }}</span>
@elseif($row->payment_mode == 1)
    <span class="badge bg-light-info">{{ __('messages.cash') }}</span>
@elseif($row->payment_mode == 2)
    <span class="badge bg-light-warning">{{ __('messages.cheque') }}</span>
@endif
