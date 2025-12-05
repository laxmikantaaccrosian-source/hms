<?php

namespace App\Http\Controllers;

use App\Exports\PaymentReportExport;
use App\Models\Account;
use Maatwebsite\Excel\Facades\Excel;

class PaymentReportController extends Controller
{
    public function index()
    {
        $accountTypes = Account::ACCOUNT_TYPES;

        return view('payment_reports.index', compact('accountTypes'));
    }

    public function paymentReportExport()
    {
        return Excel::download(new PaymentReportExport, 'payments-reports-'.time().'.xlsx');
    }
}
