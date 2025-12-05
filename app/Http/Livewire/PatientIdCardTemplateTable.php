<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PatientIdCardTemplate;

class PatientIdCardTemplateTable extends DataTableComponent
{
    protected $model = PatientIdCardTemplate::class;

    protected $showFilterOnHeader = false;

    protected $showButtonOnHeader = true;

    protected $buttonComponent = 'patient_id_card_template.add_button';

    protected $listeners = ['refresh' => '$refresh', 'changeFilter', 'resetPage'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.user.name'), "name")
                ->sortable()
                ->searchable(),
            Column::make(__('messages.patient_id_card.color'), "color")
                ->view('patient_id_card_template.templates.column.color'),
            Column::make(__('messages.user.email'), "email")
                ->view('patient_id_card_template.templates.column.email'),
            Column::make(__('messages.user.phone'), "phone")
                ->view('patient_id_card_template.templates.column.phone'),
            Column::make(__('messages.user.dob'), "dob")
                ->view('patient_id_card_template.templates.column.dob'),
            Column::make(__('messages.user.blood_group'), "blood_group")
                ->view('patient_id_card_template.templates.column.blood_group'),
            Column::make(__('messages.common.address'), "address")
                ->view('patient_id_card_template.templates.column.address'),
            Column::make(__('messages.patient_id_card.patient_unique_id'), "patient_unique_id")
                ->view('patient_id_card_template.templates.column.uniqueid'),
            Column::make(__('messages.common.action'), "id")
            ->view('patient_id_card_template.action'),
        ];
    }
}
