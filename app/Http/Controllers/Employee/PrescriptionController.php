<?php

namespace App\Http\Controllers\Employee;

use App\Exports\PrescriptionExport;
use App\Http\Controllers\Controller;
use App\Repositories\PrescriptionRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PrescriptionController extends Controller
{
    private $prescriptionRepository;

    public function index()
    {
        return view('employee_prescription_list.index');
    }

    public function show($id)
    {
        $prescriptionRepository = App::make(PrescriptionRepository::class);
        $data = $prescriptionRepository->getSettingList();
        $prescription = $prescriptionRepository->getData($id);
        $medicines = $prescriptionRepository->getMedicineData($id);

        return view('prescriptions.view', compact('prescription', 'medicines', 'data'));
    }

    public function prescriptionExport()
    {
        return Excel::download(new PrescriptionExport, 'prescriptions-'.time().'.xlsx');
    }
}
