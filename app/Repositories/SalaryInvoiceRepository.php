<?php

namespace App\Repositories;

use App\Invoice;
use App\SalaryInvoice;
use Carbon\Carbon;

class SalaryInvoiceRepository
{
    /**
     * IDで非常勤給与明細データ取得（ユーザー情報含む）
     *
     * @param int $id 非常勤給与明細ID
     * @return \App\Models\SalaryInvoice|null
     */
    public function getDataByIdWithUser(int $id): ?SalaryInvoice
    {
        return SalaryInvoice::with('user')->find($id);
    }

    /**
     * 指定されたユーザ番号に囲む給与明細データを、指定された年月の範囲で取得する
     *
     * @param array|null $userIds ユーザ番号の配列
     * @param string|null $yearMonth 'YYYY-MM' 形式の年月
     * @param int $months 遡る月数
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getSalaryInvoicesWithinMonthsByUserIds(?array $userIds, ?string $yearMonth, int $months = 3)
    {
        $query = SalaryInvoice::query();

        if (!is_null($userIds)) {
            $query->whereIn('user_id', $userIds);
        }

        if (!is_null($yearMonth)) {
            $query->where('tightening_date', $yearMonth);
        }

        $threeMonthsAgo = Carbon::now()->subMonths($months)->startOfDay(); // startOfDay() を追加して、時刻を 00:00:00 に設定
        $query->where('created_at', '>=', $threeMonthsAgo);

        $query->whereNull('deleted_at');

        return $query->paginate(30);
    }

    /**
     * 指定されたユーザー番号に囲まれた非常勤給与明細データからユニークな年次リストを取得します。
     *
     * @param array|null $userIds 生徒番号の配列
     * @param int $months 遡る月数
     * @return \Illuminate\Support\Collection
     */
    public function getUniqueSalaryMonthsWithinMonths(?array $userIds, int $months = 3)
    {
        $query = SalaryInvoice::query()->select('tightening_date');

        if (!is_null($userIds)) {
            $query->whereIn('user_id', $userIds);
        }

        $query->whereNull('deleted_at');

        $threeMonthsAgo = Carbon::now()->subMonths($months)->startOfDay(); // startOfDay() を追加して、時刻を 00:00:00 に設定
        $query->where('created_at', '>=', $threeMonthsAgo);

        return $query->distinct()->orderBy('tightening_date', 'desc')->pluck('tightening_date');
    }
}
