<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SalaryInvoiceService;

class SalaryInvoiceTransferController extends Controller
{
    protected $salaryInvoiceService;

    public function __construct(SalaryInvoiceService $salaryInvoiceService)
    {
        $this->salaryInvoiceService = $salaryInvoiceService;
    }

    // 確認メッセージを取得するためのメソッドを追加
    public function confirmTransfer()
    {
        $message = $this->salaryInvoiceService->getTransferConfirmationMessage();
        return response()->json(['message' => $message]);
    }

    public function transfer(Request $request)
    {
        try {
            $message = $this->salaryInvoiceService->transferChargesToParttime(); // 戻り値（メッセージ）を受け取る
            return response()->json(['message' => $message], 200); // メッセージを返すように変更
        } catch (\Exception $e) {
            // ログ出力はServiceで行うので、ここではエラーメッセージのみ返す
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
