<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Models\PatientIdCardTemplate;
use Illuminate\Support\Facades\Auth;
use App\Models\PatientAdmission;

class GeneratePatientIdCardRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'user_id',
        'patient_unique_id',
        'template_id',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return Patient::class;
    }

    public function getTemplates()
    {
        return PatientIdCardTemplate::pluck('name', 'id');
    }

    public function getPatients()
    {
        return Patient::whereNull('template_id')->with('user')->get()->pluck('user.first_name', 'user_id');
    }

    public function store($input)
    {
        if ($input['type'] == '1') {
           Patient::whereNotNull('user_id')->update(['template_id' => $input['template_id']]);
        }elseif($input['type'] == '2'){
            Patient::where('user_id',$input['patient_id'])->update(['template_id' => $input['template_id']]);
        }else{
            Patient::whereNull('template_id')->update(['template_id' => $input['template_id']]);
        }

        return true;
    }
}
