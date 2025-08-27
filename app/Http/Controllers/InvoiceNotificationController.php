<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InvoiceNotificationService;

class InvoiceNotificationController extends Controller
{
    protected $invoiceNotificationService;

    public function __construct(InvoiceNotificationService $invoiceNotificationService)
    {
        $this->invoiceNotificationService = $invoiceNotificationService;
    }

    /**
     * 請求書通知の確認メッセージを取得
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmNotification()
    {
        $message = $this->invoiceNotificationService->getNotificationConfirmationMessage();
        return response()->json(['message' => $message]);
    }

    /**
     * 請求書通知メールを送信
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotification()
    {
        try {
            $message = $this->invoiceNotificationService->sendInvoiceNotification();
            return response()->json(['message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}