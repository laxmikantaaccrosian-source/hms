<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseMedicineExport;
use App\Http\Requests\CreatePurchaseMedicineRequest;
use App\Models\Medicine;
use App\Models\PurchaseMedicine;
use App\Repositories\MedicineRepository;
use App\Repositories\PurchaseMedicineRepository;
use Laracasts\Flash\Flash;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseMedicineController extends AppBaseController
{
    /** @var PurchaseMedicineRepository */
    /** @var MedicineRepository */
    private $prchaseMedicineRepository;

    private $medicineRepository;

    public function __construct(PurchaseMedicineRepository $purchaseMedicineRepo, MedicineRepository $medicineRepository)
    {
        $this->prchaseMedicineRepository = $purchaseMedicineRepo;
        $this->medicineRepository = $medicineRepository;
    }

    public function index()
    {

        return view('purchase-medicines.index');

    }

    public function create()
    {

        $data = $this->medicineRepository->getSyncList();
        $medicines = $this->prchaseMedicineRepository->getMedicine();
        $medicineList = $this->prchaseMedicineRepository->getMedicineList();
        $categories = $this->prchaseMedicineRepository->getCategory();
        $categoriesList = $this->prchaseMedicineRepository->getCategoryList();

        return view('purchase-medicines.create', compact('medicines', 'medicineList', 'categories', 'categoriesList'))->with($data);
    }

    public function store(CreatePurchaseMedicineRequest $request)
    {

        $input = $request->all();
        $this->prchaseMedicineRepository->store($input);
        flash::success(__('messages.purchase_medicine.medicine_purchased_successfully'));

        return redirect(route('medicine-purchase.index'));
    }

    public function show(PurchaseMedicine $medicinePurchase)
    {
        $medicinePurchase->load(['purchasedMedcines.medicines']);

        return view('purchase-medicines.show', compact('medicinePurchase'));
    }

    public function getMedicine(Medicine $medicine)
    {
        return $this->sendResponse($medicine, 'retrieved');
    }

    public function purchaseMedicineExport()
    {

        $response = Excel::download(new PurchaseMedicineExport, 'purchase-medicine-'.time().'.xlsx');

        ob_end_clean();

        return $response;

    }

    public function usedMedicine()
    {

        return view('used-medicine.index');
    }

    public function destroy(PurchaseMedicine $medicinePurchase)
    {
        $medicinePurchase->delete();

        return $this->sendSuccess(__('messages.flash.medicine_deleted'));
    }
}
