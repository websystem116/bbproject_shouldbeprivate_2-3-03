<?php

namespace App\Services;

use App\Invoice;
use App\ChargeProgress;
use App\Mail\InvoiceNotificationMail; //  メールクラス
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceNotificationService
{
    /**
     * 請求書通知の確認メッセージを取得
     *
     * @return string
     */
    public function getNotificationConfirmationMessage()
    {
        $latestChargeProgress = ChargeProgress::latest('sales_month')->first();
        if (!$latestChargeProgress) {
            throw new \Exception('対象の請求データ作成月が見つかりません。');
        }
        $salesMonth = $latestChargeProgress->sales_month;

        $count = Invoice::where('charge_month', $salesMonth)
            ->where('notification_sent_flg', false) // 通知フラグが立っていない場合のみ処理
            ->count(); //来月

        if ($count === 0) {
            return '通知対象の請求書データがありません。';
        }

        $message = "{$count}件の請求書について、保護者様に通知メールを送信します。よろしいですか？";
        return $message;
    }

    /**
     * 請求書通知メールを送信
     *
     * @return string
     * @throws \Exception
     */
    public function sendInvoiceNotification()
    {
        DB::beginTransaction();

        try {
            $latestChargeProgress = ChargeProgress::latest('sales_month')->first();
            if (!$latestChargeProgress) {
                throw new \Exception('対象の請求データ作成月が見つかりません。');
            }
            $salesMonth = $latestChargeProgress->sales_month;

            $invoices = Invoice::where('charge_month', $salesMonth)
                ->where('notification_sent_flg', false) // 通知フラグが立っていない場合のみ処理
                ->with('student.parent') // リレーションをロード
                ->get();

            $sentCount = 0;
            $totalCount = 0; // 送信対象の件数

            foreach ($invoices as $invoice) {
                $student = $invoice->student;

                // parent_user_id が存在し、関連する parent が存在し、email が存在する場合のみ処理
                if ($student && $student->parent_user_id && $student->parent && $student->parent->email) {
                    $totalCount++; //送信対象をカウント

                    try {
                        Mail::to($student->parent->email)->send(new InvoiceNotificationMail($invoice));
                        // 送信成功したら、フラグを立てる
                        $invoice->notification_sent_flg = true;
                        $invoice->save();

                        $sentCount++; // 送信成功数をカウント
                        DB::commit(); //invoice毎

                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("請求書通知メール送信エラー (Invoice ID: {$invoice->id}): " . $e->getMessage());
                        // 他のメール送信処理には影響を与えないように、continue
                        continue;
                    }

                }
            }

            if ($totalCount === 0) { //送信対象が一つもない場合
                return "通知対象の請求書データがありません。";
            }

            return "{$sentCount}件の請求書通知メールを送信しました。";

        } catch (\Exception $e) {
            Log::error('請求書通知メール送信処理中にエラーが発生しました: ' . $e->getMessage());
            throw $e; // 呼び出し元に例外を投げる。
        }
    }

}