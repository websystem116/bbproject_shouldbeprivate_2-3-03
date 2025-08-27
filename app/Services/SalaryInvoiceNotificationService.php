<?php

namespace App\Services;

use App\SalaryInvoice;
use App\SalaryProgress;
use App\Mail\SalaryInvoiceNotificationMail; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryInvoiceNotificationService
{
    /**
     * 非常勤給与明細通知の確認メッセージを取得
     *
     * @return string
     */
    public function getSalaryNotificationConfirmationMessage()
    {
        $latestSalaryProgress = SalaryProgress::latest('new_monthly_processing_month')->first();
        if (!$latestSalaryProgress) {
            throw new \Exception('対象の給与明細データ作成月が見つかりません。');
        }
        $salaryMonth = $latestSalaryProgress->new_monthly_processing_month;

        $count = SalaryInvoice::where('tightening_date', $salaryMonth)
            ->where('monthly_completion', false) // 通知フラグが立っていない場合のみ処理
            ->count(); //来月

        if ($count === 0) {
            return '通知対象の非常勤給与明細データがありません。';
        }

        $message = "{$count}件の非常勤給与明細通知メールを送信します。よろしいですか？";
        return $message;
    }

    /**
     * 非常勤給与明細通知メールを送信
     *
     * @return string
     * @throws \Exception
     */
    public function sendSalaryInvoiceNotification()
    {
        DB::beginTransaction();

        try {
            $latestSalaryProgress = SalaryProgress::latest('new_monthly_processing_month')->first();
            
            if (!$latestSalaryProgress) {
                throw new \Exception('対象の給与明細データ作成月が見つかりません。');
            }
            $salaryMonth = $latestSalaryProgress->new_monthly_processing_month;
            $salary_invoices = SalaryInvoice::where('tightening_date', $salaryMonth)
                ->where('monthly_completion', false) // 通知フラグが立っていない場合のみ処理
                ->with('user') // リレーションをロード
                ->get();

            $sentCount = 0;
            $totalCount = 0; // 送信対象の件数

            foreach ($salary_invoices as $salaryInvoice) {
                $user = $salaryInvoice->user;


                if ($user && $user->email) {
                    $totalCount++; 


                    try {
                        Mail::to($user->email)->send(new SalaryInvoiceNotificationMail($salaryInvoice));
                        // 送信成功したら、フラグを立てる
                        $salaryInvoice->monthly_completion = true;
                        $salaryInvoice->save();

                        $sentCount++; // 送信成功数をカウント
                        DB::commit(); 

                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("非常勤給与明細通知メール送信エラー (SalaryInvoice ID: {$salaryInvoice->id}): " . $e->getMessage());
                        // 他のメール送信処理には影響を与えないように、continue
                        continue;
                    }

                }
            }

            if ($totalCount === 0) { //送信対象が一つもない場合
                return "通知対象の非常勤給与明細データがありません。";
            }

            return "{$sentCount}件の非常勤給与明細通知メールを送信しました。";

        } catch (\Exception $e) {
            Log::error('非常勤給与明細通知メール送信処理中にエラーが発生しました: ' . $e->getMessage());
            throw $e; // 呼び出し元に例外を投げる。
        }
    }

}