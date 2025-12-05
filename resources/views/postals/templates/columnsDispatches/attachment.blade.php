@if ($row->document_url != '')
    <a data-turbo="false" href="{{ url('receives' . '/' . $row->id) }}" class="text-decoration-none"
        target="_blank">{{ __('messages.document.download') }}</a>
@else
    {{ __('messages.common.n/a') }}
@endif
