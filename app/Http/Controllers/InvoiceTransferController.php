<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InvoiceService;

class InvoiceTransferController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    // 確認メッセージを取得するためのメソッドを追加
    public function confirmTransfer()
    {
        $message = $this->invoiceService->getTransferConfirmationMessage();
        return response()->json(['message' => $message]);
    }

    public function transfer(Request $request)
    {
        try {
            $message = $this->invoiceService->transferChargesToInvoices(); // 戻り値（メッセージ）を受け取る
            return response()->json(['message' => $message], 200); // メッセージを返すように変更
        } catch (\Exception $e) {
            // ログ出力はServiceで行うので、ここではエラーメッセージのみ返す
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}