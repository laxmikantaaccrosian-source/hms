@extends('layouts.app')
@section('title')
    {{ __('messages.medicine_bills.add_medicine_bill') }}
@endsection
@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-7">
            <h1 class="mb-0">@yield('title')</h1>
            <div>
                <a href="javascript:void(0)" class="btn btn-primary me-3 add-patient-modal">{{ __('messages.patient.new_patient') }}</a>
                <a href="{{ route('medicine-bills.index') }}" class="btn btn-outline-primary">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="row">
                <div class="col-12">
                    @include('layouts.errors')
                    @include('flash::message')
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    {{Form::hidden('uniqueId',2,['id'=>'medicineUniqueId'])}}
                    {{ Form::hidden('associateMedicines', json_encode($medicineList), ['class' => 'associatePurchaseMedicines']) }}
                    {{ Form::hidden('medicineCategories', json_encode($medicineCategoriesList), ['id' => 'showMedicineCategoriesMedicineBill']) }}

                    {{ Form::open(['route' => 'medicine-bills.store', 'method' => 'post', 'id' => 'createMedicinebillForm']) }}
                    <div class="row">
                        @include('medicine-bills.medicine-table')
                    </div>
                    {{ Form::close() }}
                </div>
                @include('medicine-bills.templates.templates')
            </div>
        </div>
    </div>
    @include('medicine-bills.add_patient_modal')
@endsection
