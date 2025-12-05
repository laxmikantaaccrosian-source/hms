<div class="d-flex justify-content-end pe-22">
    @if(!empty($row->amount))
        {{ checkNumberFormat($row->amount - ($row->amount * $row->discount / 100), strtoupper(getCurrentCurrency())) }}
    @else
        {{ __('messages.common.n/a') }}
    @endif
</div>
