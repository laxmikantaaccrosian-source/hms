<div>
    <a href="{{ route('receives.excel') }}"
    class="btn btn-primary me-4"  data-turbo="false">
    <i class="fas fa-file-excel"></i>
    </a>

    <a href="javascript:void(0)"  data-bs-toggle="modal" data-bs-target="#add_postal_receives_modal" class="btn btn-primary">
        {{ __('messages.postal.new_receive') }}
    </a>

{{--            <a href="{{ route('receives.excel') }}" data-turbo="false" class="dropdown-item  px-5">--}}

</div>
