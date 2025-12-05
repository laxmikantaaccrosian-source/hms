<div>
    <a href="{{ route('doctors.excel') }}"
    class="btn btn-primary me-4"  data-turbo="false">
    <i class="fas fa-file-excel"></i>
    </a>

    @if(Auth::user()->hasRole('Admin|Receptionist'))
        <a href="{{ route('doctors.create') }}"
        class="btn btn-primary">{{ __('messages.doctor.new_doctor') }}</a>
    @endif
</div>
