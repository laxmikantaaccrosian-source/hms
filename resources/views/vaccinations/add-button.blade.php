<div>
    <a href="{{ route('vaccinations.excel') }}"
    class="btn btn-primary me-4"  data-turbo="false">
    <i class="fas fa-file-excel"></i>
    </a>

    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#add_vaccinations_modal"
       class="btn btn-primary">{{ __('messages.vaccination.new_vaccination')}}</a>
</div>
