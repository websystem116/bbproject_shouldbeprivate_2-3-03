<?php

namespace App\Services;

use App\ChargeProgress;
use App\InvoiceComment;
use App\Charge;
use App\Invoice;
use App\InvoiceDetail;
use App\DivisionCode;
use Illuminate\Support\Facades\DB;
use App\Repositories\InvoiceRepository;
use Barryvdh\DomPDF\PDF as DomPDF;
use Illuminate\Support\Facades\Log;
use PDF;

class InvoiceService
{
    protected $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * 請求書一覧取得
     *
     * @param array $data リクエストデータ
     * @param int|null $studentNo 生徒ID
     * @return array
     */
    public function getLists(array $data, ?int $studentNo): array
    {
        // 請求年月による絞り込み
        $search_charge_month = $data['search_charge_month'] ?? null;

        // InvoiceRepository の新しいメソッドを使って、指定された年月の請求書を取得
        // studentNo が指定されている場合は、その生徒の請求書のみを取得
        $invoices = $this->invoiceRepository->getInvoicesWithinMonthsByStudentNos([$studentNo], $search_charge_month, 3);

        // 年月選択肢用のユニークな年月リストを取得
        $uniqueChargeMonths = $this->invoiceRepository->getUniqueChargeMonthsWithinMonths([$studentNo], 3);

        return [
            'invoices' => $invoices,
            'uniqueChargeMonths' => $uniqueChargeMonths,
            'search_charge_month' => $search_charge_month, // 検索条件をビューに渡す
        ];
    }

    /**
     * 請求書データ生成（PDF用）
     *
     * @param int $id 請求書ID
     * @return array
     */
    public function generateInvoiceData(int $id): array
    {
        $invoice = $this->invoiceRepository->getDataByIdWithStudent($id);

        $totalPrice = 0;
        $totalTax = 0;
        $totalSubtotal = 0;
        foreach ($invoice->invoiceDetails as $detail) {
            $totalPrice += $detail->price;
            $totalTax += $detail->tax;
            $totalSubtotal += $detail->subtotal;
        }

        return [
            'invoice' => $invoice,
            'totalPrice' => $totalPrice,
            'totalTax' => $totalTax,
            'totalSubtotal' => $totalSubtotal,
        ];
    }

    /**
     * 請求書PDF生成
     *
     * @param int $id 請求書ID
     * @return DomPDF
     */
    public function generatePdf(int $id): DomPDF
    {
        $invoiceData = $this->generateInvoiceData($id);

        $pdf = PDF::loadView('invoice.pdf_layout', $invoiceData)
            ->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * 請求データの移行（メッセージ生成のみ）
     *
     * @return string 確認メッセージ
     */
    public function getTransferConfirmationMessage()
    {
        try {
            $latestChargeProgress = ChargeProgress::latest('sales_month')->first();
            if (!$latestChargeProgress) {
                return '対象の請求データ作成月が見つかりません。';
            }
            $salesMonth = $latestChargeProgress->sales_month;

            $remainingCount = Charge::where('charge_month', $salesMonth)
                ->where('transferred_flg', false)
                ->count();

            $transferLimit = config('invoice.transfer_limit');

            if ($remainingCount === 0) {
                return '今月度の反映は完了してます。';
            }

            $message = "{$transferLimit}件づつ取り込まれます。\n";
            $message .= "残り取り込み残数：{$remainingCount}\n";
            $message .= "請求書自動発行システムに請求データを反映します。よろしいですか？";

            return $message;

        } catch (\Exception $e) {
            Log::error('請求データ移行確認メッセージ生成エラー: ' . $e->getMessage());
            return 'エラーが発生しました。'; // 例外発生時はエラーメッセージを返す
        }
    }
    /**
     * 請求データの移行
     *
     * @return string 移行結果メッセージ
     * @throws \Exception
     */
    public function transferChargesToInvoices()
    {
        // NOTE: テスト注意点
        // 割引データがある、ないの2パターンでテストする
        // role: 1以外でボタンが表示されないこと

        // トランザクション開始
        DB::beginTransaction();

        try {
            $latestChargeProgress = ChargeProgress::latest('sales_month')->first();
            if (!$latestChargeProgress) {
                throw new \Exception('対象の請求データ作成月が見つかりません。');
            }
            $salesMonth = $latestChargeProgress->sales_month;

            $charges = Charge::where('charge_month', $salesMonth)
                ->where('transferred_flg', false)
                ->get();

            if ($charges->isEmpty()) {
                return '今月度の反映は完了してます。';
            }

            $transferLimit = config('invoice.transfer_limit'); // configから取得

            $processedCount = 0;

            foreach ($charges as $charge) {
                if ($processedCount >= $transferLimit) {
                    break;
                }

                $student = $charge->student;

                if (!$student) {
                  Log::error("請求データ移行エラー：生徒データが見つかりません。 請求データID: {$charge->id}"); // ログ出力
                    continue; // スキップ
                }

                $invoiceId = Invoice::insertGetId([
                   'student_no' => $charge->student_no,
                    'sale_id' => $charge->sale_id,
                    'charge_month' => $charge->charge_month,
                    'carryover' => $charge->carryover,
                    'month_sum' => $charge->month_sum,
                    'month_tax_sum' => $charge->month_tax_sum,
                    'prepaid' => $charge->prepaid,
                    'sum' => $charge->sum,
                    'withdrawal_created_flg' => $charge->withdrawal_created_flg,
                    'withdrawal_confirmed' => $charge->withdrawal_confirmed,
                    'grade' => $student->grade,
                    'student_name' => $student->full_name,
                    'school_building_name' => $student->schoolbuilding->name,
                    'sales_number' => $charge->sales_number,
                    'recipient_zip_code' => $student->zip_code, // 請求書の宛先郵便番号
                    'recipient_address1' => $student->address1, // 請求書の宛先住所1
                    'recipient_address2' => $student->address2, // 請求書の宛先住所2
                    'recipient_address3' => $student->address3, // 請求書の宛先住所3
                    'recipient_surname' => $student->parent_surname, // 請求書の宛先姓
                    'recipient_name' => $student->parent_name, // 請求書の宛名
                    'display_message' => $this->getInvoiceComment($charge, $student), // 請求書に表示する特定の文言
                    'applied_discount_name' => optional($student->discount)->name, // 適用された割引の名前
                    'creator' => auth()->user()->id,
                    'updater' => auth()->user()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $details = $charge->charge_detail;

                // 明細の登録処理
                foreach ($details as $detail) {
                    InvoiceDetail::create([
                        'student_no' => $detail->student_no,
                        'sale_month' => $detail->sale_month,
                        'invoice_id' => $invoiceId,
                        'charges_date' => $detail->charges_date,
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product_name,
                        'product_price' => $detail->product_price,
                        'product_price_display' => $detail->product_price_display,
                        'price' => $detail->price,
                        'tax' => $detail->tax,
                        'subtotal' => $detail->subtotal,
                        'remarks' => $detail->remarks,
                        'creator' => auth()->user()->id,
                        'updater' => auth()->user()->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'sales_number' => $detail->sales_number,
                        'division_name' => $this->getDivisionName($detail),
                    ]);
                }

                $charge->transferred_flg = true;
                $charge->save();

                $processedCount++;
            }
            DB::commit();
            return '請求データの移行が完了しました';

        } catch (\Exception $e) {
            Log::error('請求データの登録に失敗しました。' . $e->getMessage() . ' 行番号: ' . $e->getLine() . ' ファイル: ' . $e->getFile());
            DB::rollback();
            throw $e;
        }
    }

    /**
     * 請求書に表示するコメントを取得する
     *
     * @param object $charge
     * @param object $student
     * @return string
     */
    private function getInvoiceComment($charge, $student): string
    {
        if ($charge->convenience_store_flg == 1) {
            return InvoiceComment::where('division', 3)->value('comment');
        }

        switch ($student->payment_methods) {
            case 1:
                return InvoiceComment::where('division', 1)->value('comment');
            case 2:
                return InvoiceComment::where('division', 2)->value('comment');
            case 3:
            case 4:
                return InvoiceComment::where('division', 4)->value('comment');
            default:
                return '';
        }
    }

    /**
     * 売上区分マスタから売上区分名を取得する
     *
     * @param object $chargeDetail
     * @return string
     */
    private function getDivisionName(object $chargeDetail): string
    {
        $product = $chargeDetail->product;
        $divisionCode = optional($product)->division_code;

        // 売上区分マスタ
        $divisionCodes = DivisionCode::all()->pluck('name', 'id');

        return $divisionCodes[$divisionCode] ?? '';
    }
}
