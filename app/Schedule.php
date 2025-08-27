<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'color',  // 色情報を追加
        'schedule_date',
        'start_time',
        'end_time',
        'school_building_id',
        'created_by',
        'status',
        'approved_by',
        'approved_at',
        'approval_note'
    ];

    protected $dates = [
        'schedule_date',
        'approved_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'approved_at' => 'datetime'
    ];

    /**
     * スケジュールが関連する校舎
     */
    public function schoolBuilding()
    {
        return $this->belongsTo('App\SchoolBuilding', 'school_building_id');
    }

    /**
     * スケジュールを作成したユーザー
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * スケジュールを承認したユーザー
     */
    public function approver()
    {
        return $this->belongsTo('App\User', 'approved_by');
    }

    /**
     * 使用可能な色の定義
     */
    public static function getAvailableColors()
    {
        return [
            'yellow' => [
                'name' => '黄色',
                'bg' => '#ffd700',
                'border' => '#f7ca18',
                'text' => '#2c3e50'
            ],
            'blue' => [
                'name' => '青色',
                'bg' => '#3498db',
                'border' => '#2980b9',
                'text' => '#ffffff'
            ],
            'green' => [
                'name' => '緑色',
                'bg' => '#2ecc71',
                'border' => '#27ae60',
                'text' => '#ffffff'
            ],
            'red' => [
                'name' => '赤色',
                'bg' => '#e74c3c',
                'border' => '#c0392b',
                'text' => '#ffffff'
            ],
            'purple' => [
                'name' => '紫色',
                'bg' => '#9b59b6',
                'border' => '#8e44ad',
                'text' => '#ffffff'
            ],
            'orange' => [
                'name' => 'オレンジ',
                'bg' => '#e67e22',
                'border' => '#d35400',
                'text' => '#ffffff'
            ],
            'teal' => [
                'name' => 'ティール',
                'bg' => '#1abc9c',
                'border' => '#16a085',
                'text' => '#ffffff'
            ],
            'pink' => [
                'name' => 'ピンク',
                'bg' => '#e91e63',
                'border' => '#c2185b',
                'text' => '#ffffff'
            ]
        ];
    }

    /**
     * 色情報を取得
     */
    public function getColorInfoAttribute()
    {
        $colors = self::getAvailableColors();
        return $colors[$this->color] ?? $colors['yellow'];
    }

    /**
     * 承認済みかどうか
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * 保留中かどうか
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * 却下されたかどうか
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * 時間付きの表示用テキスト
     */
    public function getTimeDisplayAttribute()
    {
        if ($this->start_time && $this->end_time) {
            // Handle time fields as strings since they're stored as TIME in database
            $startTime = substr($this->start_time, 0, 5); // Get HH:MM part
            $endTime = substr($this->end_time, 0, 5);     // Get HH:MM part
            return $startTime . ' - ' . $endTime;
        } elseif ($this->start_time) {
            $startTime = substr($this->start_time, 0, 5);
            return $startTime . ' -';
        }
        return '終日';
    }

    /**
     * ステータス表示用
     */
    public function getStatusDisplayAttribute()
    {
        $statusMap = [
            'pending' => '承認待ち',
            'approved' => '承認済み',
            'rejected' => '却下'
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    /**
     * 指定した月のスケジュールを取得（承認済みのみ）
     */
    public static function getSchedulesForMonth($year, $month, $schoolBuildingId = null)
    {
        $query = self::whereYear('schedule_date', $year)
                    ->whereMonth('schedule_date', $month)
                    ->where('status', 'approved') // Only show approved schedules
                    ->with(['schoolBuilding', 'creator']);

        if ($schoolBuildingId) {
            $query->where('school_building_id', $schoolBuildingId);
        }

        return $query->orderBy('schedule_date')
                    ->orderBy('start_time')
                    ->get();
    }

    /**
     * 承認可能なスケジュールを取得
     */
    public static function getPendingSchedules($schoolBuildingId = null)
    {
        $query = self::where('status', 'pending')
                    ->with(['schoolBuilding', 'creator']);

        if ($schoolBuildingId) {
            $query->where('school_building_id', $schoolBuildingId);
        }

        return $query->orderBy('schedule_date')
                    ->orderBy('start_time')
                    ->get();
    }
}
