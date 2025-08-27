<?php

namespace App\Repositories;

use App\Invoice;
use Carbon\Carbon;

class InvoiceRepository
{
    /**
     * IDで請求書データを取得（生徒情報付き）
     *
     * @param int $id 請求書ID
     * @return \App\Models\Invoice|null
     */
    public function getDataByIdWithStudent(int $id): ?Invoice
    {
        return Invoice::with('student')->find($id);
    }

    /**
     * 指定された生徒番号に紐づく請求書データを、指定された年月の範囲で取得する
     *
     * @param array|null $studentNos 生徒番号の配列
     * @param string|null $yearMonth 'YYYY-MM' 形式の年月
     * @param int $months 遡る月数
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getInvoicesWithinMonthsByStudentNos(?array $studentNos, ?string $yearMonth, int $months = 3)
    {
        $query = Invoice::query();

        if (!is_null($studentNos)) {
            $query->whereIn('student_no', $studentNos);
        }

        // $yearMonth が指定されている場合、該当月の請求書のみを取得
        if (!is_null($yearMonth)) {
            $query->where('charge_month', $yearMonth);
        }

        // 現在から過去3ヶ月の範囲で絞り込む
        $threeMonthsAgo = Carbon::now()->subMonths($months)->startOfDay(); // startOfDay() を追加して、時刻を 00:00:00 に設定
        $query->where('created_at', '>=', $threeMonthsAgo);

        $query->whereNull('deleted_at');

        return $query->paginate(30);
    }

    /**
     * 指定された生徒番号に紐づく請求書データからユニークな年月リストを取得する
     *
     * @param array|null $studentNos 生徒番号の配列
     * @param int $months 遡る月数
     * @return \Illuminate\Support\Collection
     */
    public function getUniqueChargeMonthsWithinMonths(?array $studentNos, int $months = 3)
    {
        $query = Invoice::query()->select('charge_month');

        if (!is_null($studentNos)) {
            $query->whereIn('student_no', $studentNos);
        }

        $query->whereNull('deleted_at');

        // 現在から過去3ヶ月の範囲で絞り込む
        $threeMonthsAgo = Carbon::now()->subMonths($months)->startOfDay(); // startOfDay() を追加して、時刻を 00:00:00 に設定
        $query->where('created_at', '>=', $threeMonthsAgo);

        return $query->distinct()->orderBy('charge_month', 'desc')->pluck('charge_month');
    }
}
