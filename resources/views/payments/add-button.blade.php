<div>
    <a href="{{ route('payments.excel') }}"
    class="btn btn-primary me-4"  data-turbo="false">
    <i class="fa-solid fa-file-csv"></i>
    </a>

    <a href="{{ route('payments.create') }}"
       class="btn btn-primary">{{ __('messages.payment.new_payment') }}</a>

</div>
