@if ($row->document_url != '')
    <a data-turbo="false" href="{{ url('visitors-download' . '/' . $row->id) }}"
        class="text-decoration-none">{{ __('messages.document.download') }}</a>
@else
    {{ __('messages.common.n/a')}}
@endif
