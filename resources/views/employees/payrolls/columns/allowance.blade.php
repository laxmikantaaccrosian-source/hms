<div class="text-end">
    @if(!empty($row->allowance))
        {{ checkNumberFormat($row->allowance, strtoupper(getCurrentCurrency())) }}
    @else
        {{ __('messages.common.n/a') }}
    @endif
</div>
