<div class="d-flex justify-content-end pe-22">
    @if(!empty($row->net_salary))
            {{ checkNumberFormat($row->net_salary, strtoupper(getCurrentCurrency())) }}
    @else
        {{ __('messages.common.n/a') }}
    @endif
</div>
