<?php

namespace App\Services;

use App\SalaryProgress;
use App\InvoiceComment;
use App\Salary;
use App\Invoice;
use App\SalaryInvoice;
use App\SalaryInvoiceDetail;
use App\InvoiceDetail;
use App\SalaryDetail;
use App\DivisionCode;
use App\DailySalary;
use App\IncomeTax;
use App\JobDescription;
use Illuminate\Support\Facades\DB;
use App\Repositories\SalaryInvoiceRepository;
use Barryvdh\DomPDF\PDF as DomPDF;
use Illuminate\Support\Facades\Log;
use PDF;

class SalaryInvoiceService
{
    protected $salaryInvoiceRepository;

    public function __construct(SalaryInvoiceRepository $salaryInvoiceRepository)
    {
        $this->salaryInvoiceRepository = $salaryInvoiceRepository;
    }

    /**
     * 非常勤給与明細一覧取得
     *
     * @param array $data リクエストデータ
     * @param int|null $userId ユーザーID
     * @return array
     */
    public function getLists(array $data, ?int $userId): array
    {
        // 給与明細年月による絞り込み
        $search_salary_month = $data['search_salary_month'] ?? null;

        // salaryInvoiceRepository の新しいメソッドを使って、指定された年月の非常勤給与明細を取得
        // UserId が指定されている場合は、そのユーザーの非常勤給与明細のみを取得
        $salaryInvoices = $this->salaryInvoiceRepository->getSalryInvoicesWithinMonthsByUserIds([$userId], $search_salary_month, 3);

        // 年月選択肢用のユニークな年月リストを取得
        $uniqueSalaryMonths = $this->salaryInvoiceRepository->getUniqueSalaryMonthsWithinMonths([$userId], 3);

        return [
            'salaryInvoices' => $salaryInvoices,
            'uniqueSalaryMonths' => $uniqueSalaryMonths,
            'search_salary_month' => $search_salary_month, // 検索条件をビューに渡す
        ];
    }

    /**
     * 非常勤給与明細データ生成（PDF用）
     *
     * @param int $id 非常勤給与明細ID
     * @return array
     */
    public function generateSalaryInvoiceData(int $id): array
    {
        $salaryInvoice = $this->salaryInvoiceRepository->getDataByIdWithUser($id);

        $totalPrice = 0;
        $totalTax = 0;
        $totalSubtotal = 0;
        foreach ($salaryInvoice->salaryInvoiceDetails as $salaryDetail) {
            $totalPrice += $salaryDetail->deduction;
            $totalTax += $salaryDetail->income_tax_cost;
            $totalSubtotal += $salaryDetail->salary_sabtotal;
        }

        return [
            'salaryInvoice' => $salaryInvoice,
            'totalPrice' => $totalPrice,
            'totalTax' => $totalTax,
            'totalSubtotal' => $totalSubtotal,
        ];
    }

    /**
     * 非常勤給与明細PDF生成
     *
     * @param int $id 非常勤給与明細ID
     * @return DomPDF
     */
    public function salarygeneratePdf(int $id)
    {
        $salaryInvoiceData = $this->generateSalaryInvoiceData($id);
        // dd($salaryInvoiceData);
        $invoice = $salaryInvoiceData['salaryInvoice'];
        $user_id = $invoice->user_id ?? null;
        $work_month = $invoice->tightening_date ?? null;


        if ($user_id && $work_month) {
            $salary = DailySalary::where('work_month', $work_month)
                            ->where('user_id', $user_id)
                            ->first();
                    
            if ($salary) {
                $salary->salary_confirmation = true;
                $salary->save();
            }
        }

        $pdf = PDF::loadView('salary_invoice.pdf_layout', compact('salaryInvoiceData'))
            ->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * 給与明細データの移行（メッセージ生成のみ）
     *
     * @return string 確認メッセージ
     */
    public function getTransferConfirmationMessage()
    {
        try {
            $latestSalaryProgress = SalaryProgress::latest('new_monthly_processing_month')->first();
            if (!$latestSalaryProgress) {
                return '対象の給与明細データ作成月が見つかりません。';
            }
            $salaryMonth = $latestSalaryProgress->new_monthly_processing_month;

            $remainingCount = Salary::where('tightening_date', $salaryMonth)
                ->where('monthly_completion', false)
                ->count();

            $transferLimit = config('salary_invoice.transfer_limit');

            if ($remainingCount === 0) {
                return '今月度の反映は完了してます。';
            }

            $message = "{$transferLimit}件づつ取り込まれます。\n";
            $message .= "残り取り込み残数：{$remainingCount}\n";
            $message .= "非常勤給与明細自動発行システムに給与明細データを反映します。よろしいですか？";

            return $message;

        } catch (\Exception $e) {
            Log::error('給与明細データ移行確認メッセージ生成エラー: ' . $e->getMessage());
            return 'エラーが発生しました。'; // 例外発生時はエラーメッセージを返す
        }
    }
    /**
     * 給与明細データの移行
     *
     * @return string 移行結果メッセージ
     * @throws \Exception
     */
    public function transferChargesToParttime()
    {
        // NOTE: テスト注意点
        // 割引データがある、ないの2パターンでテストする
        // role: 1以外でボタンが表示されないこと

        // トランザクション開始
        DB::beginTransaction();

        try {
            $latestSalaryProgress = SalaryProgress::latest('new_monthly_processing_month')->first();
            if (!$latestSalaryProgress) {
                throw new \Exception('対象の給与明細データ作成月が見つかりません。');
            }
            $salaryMonth = $latestSalaryProgress->new_monthly_processing_month;

            $salaries = Salary::where('tightening_date', $salaryMonth)
                ->where('monthly_completion', false)
                ->get();
            
            $income_taxes = IncomeTax::all();
                

            if ($salaries->isEmpty()) {
                return '今月度の反映は完了してます。';
            }


            $transferLimit = config('salary_invoice.transfer_limit'); // configから取得

            $processedCount = 0;
            
            foreach ($salaries as $salary) {    
                if ($processedCount >= $transferLimit) {
                    break;
                }

                $user = $salary->user;

                if (!$user) {
                  Log::error("給与明細データ移行エラー：ユーザーデータが見つかりません。 給与明細データID: {$salary->id}"); // ログ出力
                    continue; // スキップ
                }

                $deduction_sum = $salary->health_insurance + $salary->welfare_pension + $salary->employment_insurance;
                
                $salary_sum = $salary->salary - $deduction_sum;
                $income_taxs = $income_taxes->filter(
                    function ($value) use ($salary_sum) {
                        return $value['or_more'] <= $salary_sum && $value['less_than'] >= $salary_sum;
                    }
                );
                foreach ($income_taxs as $value) {
                    $income_tax = $value;
                }
                $salary->user->description_column;
                $salary->user->dependents_count;
                if ($salary->user->description_column == 1) {

                    switch ($salary->user->dependents_count) {
                        case 1:
                            $income_tax_cost = $income_tax->support1;
                            break;
                        case 2:
                            $income_tax_cost = $income_tax->support2;
                            break;
                        case 3:
                            $income_tax_cost = $income_tax->support3;
                            break;
                        case 4:
                            $income_tax_cost = $income_tax->support4;
                            break;
                        case 5:
                            $income_tax_cost = $income_tax->support5;
                            break;
                        case 6:
                            $income_tax_cost = $income_tax->support6;
                            break;
                        case 7:
                            $income_tax_cost = $income_tax->support7;
                            break;
                        default:
                            $income_tax_cost = $income_tax->support0;
                    }
                } else {
                    $income_tax_cost = $income_tax->otsu;
                }
                if ($income_tax_cost == 3) {
                    $income_tax_cost = floor($salary_sum * 3.063 / 100);
                }
                $deduction = $deduction_sum + $income_tax_cost + $salary->municipal_tax;
                $salary_sabtotal = $salary->salary + $salary->transportation_expenses + $salary->other_payment_amount;
                $salary_sabtotal = $salary_sabtotal + $salary->year_end_adjustment - $deduction;

                $salaryInvoiceId = SalaryInvoice::insertGetId([
                   'user_id' => $salary->user_id,
                    'tightening_date' => $salary->tightening_date,
                    // 'other_payment_amount' => $salary->other_payment_amount,
                    // 'other_payment_reason' => $salary->other_payment_reason,
                    // 'other_deduction_amount' => $salary->other_deduction_amount,
                    // 'other_deduction_reason' => $salary->other_deduction_reason,
                    // 'transportation_expenses' => $salary->transportation_expenses,
                    'salary' => $salary->salary,
                    // 'year_end_adjustment' => $salary->year_end_adjustment,
                    'monthly_completion' => $salary->monthly_completion,
                    // 'monthly_approval' => $salary->monthly_approval,
                    'salary_approval' => $salary->salary_approval,
                    'monthly_tightening' => $salary->monthly_tightening,
                    'attendance_date' => $salary->attendance_date,
                    'municipal_tax' => $salary->municipal_tax,
                    'health_insurance' => $salary->health_insurance,
                    'welfare_pension' => $salary->welfare_pension,
                    'employment_insurance' => $salary->employment_insurance,
                    'user_name' => $user->first_name . " " . $user->last_name,
                    'user_name_kana' => $user->first_name_kana . " " . $user->last_name_kana,
                    'email' => $user->email,
                    'address1' => $user->address1,
                    'address2' => $user->address2,
                    'address3' => $user->address3,
                    'post_code' => $user->post_code,
                    'tel' => $user->tel,
                    'school_building_name' => $user->school_buildings->name ?? null, // 
                    'recipient_name' => $user->recipient_name, // 
                    'roles' => $user->roles, // 
                    'creator' => auth()->user()->id,
                    'updater' => auth()->user()->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $details = $salary->salary_detail;

                foreach ($details as $detail) {
                    $divisionCode = $detail->description_division;
                    $division = DivisionCode::where('id', $divisionCode)->first();
                    $division_name = $division->name ?? null;
                    
                    $job_description = JobDescription::where('id', $detail->job_description_id)->first();
                    $job_description_name = $job_description->name ?? null;
                    SalaryInvoiceDetail::create([
                        'salary_invoice_id' => $salaryInvoiceId,
                        'job_description_name' => $job_description_name,
                        'product_id' => $detail->product_id,
                        'payment_amount' => $detail->payment_amount,
                        'hourly_wage' => $detail->hourly_wage,
                        'attendance_date' => $detail->attendance_date,
                        'municipal_tax' => $salary->municipal_tax,
                        'deduction' => $deduction,
                        'salary_sabtotal' => $salary_sabtotal,
                        'income_tax_cost' => $income_tax_cost,
                        'division_name' => $division_name,
                        'transportation_expenses' => $salary->transportation_expenses,
                        'other_payment_amount' => $salary->other_payment_amount,
                        'other_deduction_amount' => $salary->other_deduction_amount,
                        'year_end_adjustment' => $salary->year_end_adjustment,

                        'creator' => auth()->user()->id,
                        'updater' => auth()->user()->id,

                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $salary->monthly_completion = true;
                $salary->save();

                $processedCount++;
            }
            DB::commit();
            return '給与明細データの移行が完了しました ';

        } catch (\Exception $e) {
            Log::error('給与明細データの登録に失敗しました。' . $e->getMessage() . ' 行番号: ' . $e->getLine() . ' ファイル: ' . $e->getFile());
            DB::rollback();
            throw $e;
        }
    }

    /**
     * 非常勤給与明細に表示するコメントを取得する
     *
     * @param object $charge
     * @param object $student
     * @return string
     */
    // private function getInvoiceComment($charge, $student): string
    // {
    //     if ($charge->convenience_store_flg == 1) {
    //         return InvoiceComment::where('division', 3)->value('comment');
    //     }

    //     switch ($student->payment_methods) {
    //         case 1:
    //             return InvoiceComment::where('division', 1)->value('comment');
    //         case 2:
    //             return InvoiceComment::where('division', 2)->value('comment');
    //         case 3:
    //         case 4:
    //             return InvoiceComment::where('division', 4)->value('comment');
    //         default:
    //             return '';
    //     }
    // }

    /**
     * 売上区分マスタから売上区分名を取得する
     *
     * @param object $detail
     * @return string
     */
    // private function getDivisionName(object $detail): string
    // {
    //     $divisionCode = $detail->description_division;

    //     // 売上区分マスタ
    //     $division = DivisionCode::where('id', $divisionCode)->first();
    //     $division_name = $division->name;

    //     return $division[$division_name] ?? '';
    // }
}
