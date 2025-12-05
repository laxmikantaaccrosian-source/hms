<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMedicineRequest;
use App\Http\Requests\CreatePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\User;
use App\Repositories\DoctorRepository;
use App\Repositories\MedicineRepository;
use App\Repositories\PrescriptionRepository;
use \PDF;
use Flash;
use Illuminate\Support\Facades\Redirect;

class PrescriptionController extends AppBaseController
{
    /** @var  PrescriptionRepository
     * @var DoctorRepository
     */
    private $prescriptionRepository;

    private $doctorRepository;

    private $medicineRepository;

    public function __construct(
        PrescriptionRepository $prescriptionRepo,
        DoctorRepository $doctorRepository,
        MedicineRepository $medicineRepository

    ) {
        $this->prescriptionRepository = $prescriptionRepo;
        $this->doctorRepository = $doctorRepository;
        $this->medicineRepository = $medicineRepository;
    }

    public function index()
    {
        $data['statusArr'] = Prescription::STATUS_ARR;

        return view('prescriptions.index', $data);
    }

    public function create()
    {
        $patients = $this->prescriptionRepository->getPatients();
        $medicines = $this->prescriptionRepository->getMedicines();
        $doctors = $this->doctorRepository->getDoctors();
        $data = $this->medicineRepository->getSyncList();
        $medicineList = $this->medicineRepository->getMedicineList($medicines['medicines']);
        $mealList = $this->medicineRepository->getMealList();
        $doseDurationList = $this->medicineRepository->getDoseDurationList();
        $doseIntervalList = $this->medicineRepository->getDoseIntervalList();

        return view('prescriptions.create',
            compact('patients', 'doctors', 'medicines', 'medicineList', 'mealList', 'doseDurationList', 'doseIntervalList'))->with($data);
    }

    public function store(CreatePrescriptionRequest $request)
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;

        if (! isset($input['medicine'])) {
            return $this->sendError(__('messages.medicine_bills.medicine_not_selected'));
        }

        $arr = collect($input['medicine']);
        $duplicateIds = $arr->duplicates();

        foreach ($input['medicine'] as $key => $value) {
            $medicine = Medicine::find($input['medicine'][$key]);
            if (! empty($duplicateIds)) {
                foreach ($duplicateIds as $key => $value) {
                    $medicine = Medicine::find($duplicateIds[$key]);
                    Flash::error(__('messages.medicine_bills.duplicate_medicine'));

                    return Redirect::back();
                }
            }

            $qty = $input['day'][$key] * $input['dose_interval'][$key];

            if ($medicine->available_quantity < $qty) {
                $available = $medicine->available_quantity == null ? 0 : $medicine->available_quantity;
                Flash::error(__('messages.medicine_bills.available_quantity').' '.$medicine->name.' '.__('messages.medicine_bills.is').' '.$available.'.');

                return Redirect::back();

            }
        }

        $prescription = $this->prescriptionRepository->create($input);
        $this->prescriptionRepository->createPrescription($input, $prescription);
        $this->prescriptionRepository->createNotification($input);
        Flash::success(__('messages.prescription.prescription').' '.__('messages.common.saved_successfully'));

        return redirect(route('prescriptions.index'));
    }

    public function show(Prescription $prescription)
    {
        $prescription = $this->prescriptionRepository->find($prescription->id);

        if (empty($prescription)) {
            Flash::error(__('messages.prescription.prescription').' '.__('messages.common.not_found'));

            return redirect(route('prescriptions.index'));
        }

        return view('prescriptions.show')->with('prescription', $prescription);
    }

    public function edit(Prescription $prescription)
    {
        if (getLoggedinDoctor() && checkRecordAccess($prescription->doctor_id)) {
            return view('errors.404');
        } else {

            $prescription->getMedicine;
            $patients = $this->prescriptionRepository->getPatients();
            $doctors = $this->doctorRepository->getDoctors();
            $medicines = $this->prescriptionRepository->getMedicines();
            $data = $this->medicineRepository->getSyncList();
            $medicineList = $this->medicineRepository->getMedicineList($medicines['medicines']);
            $mealList = $this->medicineRepository->getMealList();
            $doseDurationList = $this->medicineRepository->getDoseDurationList();
            $doseIntervalList = $this->medicineRepository->getDoseIntervalList();
            $medicineQty = Medicine::pluck('available_quantity', 'id');

            return view('prescriptions.edit',
                compact('patients', 'prescription', 'doctors', 'medicines', 'medicineList', 'mealList', 'doseDurationList', 'doseIntervalList', 'medicineQty'))->with($data);
        }
    }

    public function update(Prescription $prescription, UpdatePrescriptionRequest $request)
    {
        $prescription = $this->prescriptionRepository->find($prescription->id);
        $prescription->load('getMedicine');
        $prescriptionMedicineArray = [];
        $inputdoseAndMedicine = [];

        foreach ($prescription->getMedicine as $prescriptionMedicine) {
            $prescriptionMedicineArray[$prescriptionMedicine->medicine] = $prescriptionMedicine->dosage;
        }
        foreach ($request->medicine as $key => $value) {
            $inputdoseAndMedicine[$value] = $request->dosage[$key];
        }

        if (empty($prescription)) {
            Flash::error(__('messages.medicine_bills.prescription_not_found'));

            return redirect(route('prescriptions.index'));
        }

        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $arr = collect($input['medicine']);
        $duplicateIds = $arr->duplicates();

        foreach ($input['medicine'] as $key => $value) {
            $result = array_intersect($prescriptionMedicineArray, $inputdoseAndMedicine);
            $medicine = Medicine::find($input['medicine'][$key]);
            $qty = $input['day'][$key] * $input['dose_interval'][$key];

            if (! empty($duplicateIds)) {
                foreach ($duplicateIds as $key => $value) {
                    $medicine = Medicine::find($duplicateIds[$key]);
                    Flash::error(__('messages.medicine_bills.duplicate_medicine'));

                    return Redirect::back();
                }
            }

            if (! array_key_exists($input['medicine'][$key], $result) && $medicine->available_quantity < $qty) {
                $available = $medicine->available_quantity == null ? 0 : $medicine->available_quantity;
                Flash::error(__('messages.common.available_quantity_of') . $medicine->name. __('messages.common.is') . $available.'.');

                return Redirect::back();
            }
        }
        $this->prescriptionRepository->updatePrescription($prescription, $request->all());

        Flash::success(__('messages.prescription.prescription').' '.__('messages.common.updated_successfully'));

        return redirect(route('prescriptions.index'));
    }

    public function destroy(Prescription $prescription)
    {
        if (checkRecordAccess($prescription->doctor_id)) {
            $this->sendError(__('messages.prescription.prescription').' '.__('messages.common.not_found'));
        } else {
            $prescription = $this->prescriptionRepository->find($prescription->id);

            if (empty($prescription)) {
                Flash::error(__('messages.prescription.prescription').' '.__('messages.common.not_found'));

                return redirect(route('prescriptions.index'));
            }
            $prescription->delete();

            return $this->sendSuccess(__('messages.prescription.prescription').' '.__('messages.common.deleted_successfully'));
        }
    }

    public function activeDeactiveStatus($id)
    {
        $prescription = Prescription::find($id);

        if (getLoggedinDoctor() && checkRecordAccess($prescription->doctor_id)) {
            return $this->sendError(__('messages.prescription.prescription').' '.__('messages.common.not_found'));
        } else {
            $status = ! $prescription->status;
            $prescription->update(['status' => $status]);

            return $this->sendSuccess(__('messages.common.status_updated_successfully'));
        }
    }

    public function prescriptionsView($id)
    {
        $data = $this->prescriptionRepository->getSettingList();

        $prescription = $this->prescriptionRepository->getData($id);

        if (getLoggedinDoctor() && checkRecordAccess($prescription['prescription']->doctor_id)) {
            return view('errors.404');
        } else {
            $medicines = $this->prescriptionRepository->getMedicineData($id);

            return view('prescriptions.view', compact('prescription', 'medicines', 'data'));
        }
    }

    public function prescreptionMedicineStore(CreateMedicineRequest $request)
    {
        $input = $request->all();

        $this->medicineRepository->create($input);

        return $this->sendSuccess(__('messages.medicine.medicine').' '.__('messages.common.saved_successfully'));
    }

    public function convertToPDF($id)
    {
        $data = $this->prescriptionRepository->getSettingList();

        $prescription = $this->prescriptionRepository->getData($id);

        $medicines = $this->prescriptionRepository->getMedicineData($id);

        $pdf = PDF::loadView('prescriptions.prescription_pdf', compact('prescription', 'medicines', 'data'));

        return $pdf->stream($prescription['prescription']->patient->patientUser->full_name.'-'.$prescription['prescription']->id);
    }

    public function getAvailableMedicineQuantity(Medicine $medicine){
        return $this->sendResponse($medicine, 'Retrieved successfully');
    }
}
