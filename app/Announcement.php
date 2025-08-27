<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    // ステータス定数
    const STATUS_DRAFT = 'draft';           // 下書き
    const STATUS_PENDING = 'pending';       // 承認待ち
    const STATUS_APPROVED = 'approved';     // 承認済み
    const STATUS_PUBLISHED = 'published';   // 公開済み

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'distribution_targets',
        'created_by',
        'status',
        'approved_by',
        'approved_at',
        'approval_comment',
        'published_at',
        'is_published'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'distribution_targets' => 'array',
        'published_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_published' => 'boolean'
    ];

    /**
     * Get the user who created this announcement
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this announcement
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for draft announcements
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope for pending announcements
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved announcements
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for published announcements
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Check if announcement is in draft status
     */
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if announcement is pending approval
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if announcement is approved
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if announcement is published
     */
    public function isPublished()
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Check if announcement can be edited
     */
    public function canBeEdited()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_APPROVED]);
    }

    /**
     * Get status label in Japanese
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_DRAFT => '下書き',
            self::STATUS_PENDING => '承認待ち',
            self::STATUS_APPROVED => '承認済み',
            self::STATUS_PUBLISHED => '公開済み'
        ];

        return $labels[$this->status] ?? '不明';
    }

    /**
     * Get CSS class for status badge
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            self::STATUS_DRAFT => 'label-default',
            self::STATUS_PENDING => 'label-warning',
            self::STATUS_APPROVED => 'label-success',
            self::STATUS_PUBLISHED => 'label-primary'
        ];

        return $classes[$this->status] ?? 'label-default';
    }

    /**
     * Get count of pending announcements (for badge)
     */
    public static function getPendingCount()
    {
        return self::where('status', self::STATUS_PENDING)->count();
    }

    /**
     * Get count of draft announcements
     */
    public static function getDraftCount()
    {
        return self::where('status', self::STATUS_DRAFT)->count();
    }

    /**
     * Get count of unpublished announcements (for badge)
     */
    public static function getUnpublishedCount()
    {
        return self::whereIn('status', [self::STATUS_DRAFT, self::STATUS_PENDING, self::STATUS_APPROVED])->count();
    }

    /**
     * Get announcements visible to a specific user's school building
     */
    public static function visibleToUser($userId)
    {
        // ユーザーの校舎IDを取得
        $user = \App\User::find($userId);
        if (!$user || !$user->school_building) {
            return self::where('status', self::STATUS_PUBLISHED)
                ->where('distribution_targets', 'LIKE', '%"all"%')
                ->orderBy('published_at', 'desc');
        }

        return self::where('status', self::STATUS_PUBLISHED)
            ->where(function ($query) use ($user) {
                $query->whereJsonContains('distribution_targets', 'all')
                      ->orWhereJsonContains('distribution_targets', (string)$user->school_building);
            })
            ->orderBy('published_at', 'desc');
    }

    /**
     * Get formatted distribution targets for display
     */
    public function getFormattedDistributionAttribute()
    {
        if (!$this->distribution_targets) {
            return '未設定';
        }

        if (in_array('all', $this->distribution_targets)) {
            return '全校舎';
        }

        $schoolBuildingIds = array_filter($this->distribution_targets, 'is_numeric');
        if (count($schoolBuildingIds) > 0) {
            $schoolBuildings = \App\SchoolBuilding::whereIn('id', $schoolBuildingIds)->get();
            return $schoolBuildings->pluck('name')->implode(', ');
        }

        return '未設定';
    }
}
