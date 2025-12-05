<div class="d-flex justify-content-end pe-25">
    @if(!empty($row->amount))
        {{ checkNumberFormat($row->amount, strtoupper(getCurrentCurrency())) }}
    @endif
</div>
