<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleApprover extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'role',
        'school_building_id',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * 承認者が関連する校舎
     */
    public function schoolBuilding()
    {
        return $this->belongsTo('App\SchoolBuilding', 'school_building_id');
    }

    /**
     * 役割の表示用テキスト
     */
    public function getRoleDisplayAttribute()
    {
        $roleMap = [
            'admin' => '管理者',
            'office' => '事務',
            'manager' => 'マネージャー'
        ];

        return $roleMap[$this->role] ?? $this->role;
    }

    /**
     * 有効な承認者を取得
     */
    public static function getActiveApprovers($schoolBuildingId = null)
    {
        $query = self::where('is_active', true);

        if ($schoolBuildingId) {
            $query->where(function($q) use ($schoolBuildingId) {
                $q->where('school_building_id', $schoolBuildingId)
                  ->orWhereNull('school_building_id');
            });
        }

        return $query->orderBy('role')
                    ->orderBy('name')
                    ->get();
    }

    /**
     * 指定した役割の承認者を取得
     */
    public static function getApproversByRole($role, $schoolBuildingId = null)
    {
        $query = self::where('is_active', true)
                    ->where('role', $role);

        if ($schoolBuildingId) {
            $query->where(function($q) use ($schoolBuildingId) {
                $q->where('school_building_id', $schoolBuildingId)
                  ->orWhereNull('school_building_id');
            });
        }

        return $query->orderBy('name')->get();
    }
}
