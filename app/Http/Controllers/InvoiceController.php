<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use App\Invoice;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * 請求書一覧表示
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // クエリパラメータから生徒IDを取得
        $studentNo = $request->query('student_no');
        $search_charge_month = $request->input('search_charge_month');

        // 請求書一覧データを取得（生徒IDでフィルタリング）
        $invoices = $this->invoiceService->getLists($request->all(), $studentNo);

        $param = [
            'search_charge_month' => $search_charge_month
        ];

        $data = [
            'invoices' => $invoices,
            'param' => $param,
            'search_charge_month' => $search_charge_month,
        ];

        return view('invoice.index', $data);
    }

    /**
     * 請求書詳細表示
     *
     * @param Invoice $invoice
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Invoice $invoice)
    {
        // 請求書詳細データを取得
        $invoiceData = $this->invoiceService->generateInvoiceData($invoice->id);

        return view('invoice.show', $invoiceData);
    }

    /**
     * PDFダウンロード
     *
     * @param Invoice $invoice
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadPdf(Invoice $invoice)
    {
        $pdf = $this->invoiceService->generatePdf($invoice->id);
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
}
