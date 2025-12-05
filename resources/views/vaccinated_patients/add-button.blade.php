<div>
    <a href="{{ route('vaccinated-patients.excel') }}"
    class="btn btn-primary me-4"  data-turbo="false">
    <i class="fas fa-file-excel"></i>
    </a>

    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#add_vaccinated_patient_modal"
    class="btn btn-primary">{{__('messages.vaccinated_patient.new_vaccinate_patient')}}
</a>
</div>
