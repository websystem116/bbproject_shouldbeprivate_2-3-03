<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SalaryInvoiceNotificationService;

class SalaryInvoiceNotificationController extends Controller
{
    protected $salaryInvoiceNotificationService;

    public function __construct(SalaryInvoiceNotificationService $salaryInvoiceNotificationService)
    {
        $this->salaryInvoiceNotificationService = $salaryInvoiceNotificationService;
    }

    /**
     * 非常勤給与明細通知の確認メッセージを取得
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmNotification()
    {
        $message = $this->salaryInvoiceNotificationService->getSalaryNotificationConfirmationMessage();
        return response()->json(['message' => $message]);
    }

    /**
     * 非常勤給与明細通知メールを送信
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotification()
    {
        try {
            $message = $this->salaryInvoiceNotificationService->sendSalaryInvoiceNotification();
            return response()->json(['message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}