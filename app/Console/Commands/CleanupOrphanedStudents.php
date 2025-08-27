<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Student;
use App\ParentUser;
use Illuminate\Support\Facades\Log;

class CleanupOrphanedStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:orphaned-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set parent_user_id to NULL for students linked to non-existent parents.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('存在しない保護者アカウントに紐づいている生徒の parent_user_id を NULL にします...');

        // parent_user_id が NULL でなく、かつ parent_users テーブルに対応する id が存在しない Student を検索
        // LEFT JOIN を使用して、parent_users に存在しないレコードを特定
        $orphanedStudentsQuery = Student::whereNotNull('students.parent_user_id')
            ->leftJoin('parent_users', 'students.parent_user_id', '=', 'parent_users.id')
            ->whereNull('parent_users.id')
            ->select('students.*'); // students テーブルの全カラムを選択

        // 対象件数を取得して表示
        $count = $orphanedStudentsQuery->count();

        if ($count === 0) {
            $this->info('対象の生徒は見つかりませんでした。');
            return 0;
        }

        $this->warn("{$count} 件の対象生徒が見つかりました。parent_user_id を NULL に更新します。");

        if (!$this->confirm('処理を続行しますか？', true)) {
            $this->info('処理をキャンセルしました。');
            1;
        }

        // 更新処理 (件数が多い場合に備え、一括更新を使用)
        try {
            // LEFT JOIN を使った場合は、更新対象のIDリストを取得する必要がある
            $idsToUpdate = $orphanedStudentsQuery->pluck('students.id');
            $updatedCount = Student::whereIn('id', $idsToUpdate)->update(['parent_user_id' => null]);

            $this->info("{$updatedCount} 件の生徒の parent_user_id を NULL に更新しました。");

        } catch (\Exception $e) {
            $this->error("更新処理中にエラーが発生しました: " . $e->getMessage());
            Log::error("Orphaned student cleanup error: " . $e->getMessage(), ['exception' => $e]);
            1;
        }

        return 0;
    }
}