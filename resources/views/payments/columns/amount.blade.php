<div class="text-end pe-10">
    @if(!empty($row->amount))
        {{ checkNumberFormat($row->amount, strtoupper(getCurrentCurrency())) }}
    @else
    @endif
</div>
