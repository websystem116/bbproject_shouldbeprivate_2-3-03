<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SalaryInvoiceService;
use Illuminate\Http\Request;
use App\SalaryInvoice;
use App\DailySalary;

class SalaryInvoiceController extends Controller
{
    protected $salaryInvoiceService;

    public function __construct(SalaryInvoiceService $salaryInvoiceService)
    {
        $this->salaryInvoiceService = $salaryInvoiceService;
    }

    /**
     * 非常勤給与明細一覧表示
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        $search_salary_month = $request->input('search_salary_month');

        $salaryInvoices = $this->salaryInvoiceService->getLists($request->all(), $userId);

        $param = [
            'search_salary_month' => $search_salary_month
        ];

        $data = [
            'salaryInvoices' => $salaryInvoices,
            'param' => $param,
            'search_salary_month' => $search_salary_month,
        ];

        return view('salary_invoice.index', $data);
    }

    /**
     * 非常勤給与明細詳細表示
     *
     * @param SalaryInvoice $salaryInvoice
     * @return \Illuminate\Contracts\View\View
     */
    public function show(SalaryInvoice $salaryInvoice)
    {
        $salaryInvoiceData = $this->salaryInvoiceService->generateSalaryInvoiceData($salaryInvoice->id);

        return view('salary_invoice.show', $salaryInvoiceData);
    }

    /**
     * PDFダウンロード
     *
     * @param SalaryInvoice $salaryInvoice
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadPdf(SalaryInvoice $invoice)
    {
        // $salaryInvoice->id = 2;
        $pdf = $this->salaryInvoiceService->salarygeneratePdf($invoice->id * 1);
        return $pdf->download('給与明細.pdf');
    }
}
