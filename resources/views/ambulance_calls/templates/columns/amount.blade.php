<div class="text-end pe-25">
    @if($row->amount)
        {{ checkNumberFormat($row->amount,strtoupper(getCurrentCurrency())) }}
    @else
        {{ __('messages.common.n/a') }}
    @endif
</div>
