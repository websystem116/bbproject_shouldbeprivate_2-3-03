<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeforeStudent extends Model
{

	protected $guarded = ['id'];
	use SoftDeletes;

	/**
	 *ユーザーに関連する割引情報取得
	 */
	public function discount()
	{
		return $this->belongsTo('App\Discount');
	}

	/**
	 *ユーザーに関連する学校名情報取得
	 */
	public function school()
	{
		return $this->belongsTo('App\School');
	}
	/**
	 *ユーザーに関連する校舎情報取得
	 */
	public function schoolbuilding()
	{
		return $this->belongsTo('App\SchoolBuilding', 'school_building_id');
	}

	/**
	 *DBの「created_at」カラムが Y-m-d H:i:s なので　yyyy年mm月dd日で取得
	 */
	public function getCreatedAtAttribute()
	{
		return Carbon::parse($this->attributes['created_at'])->format('Y年m月d日');
	}

	/**
	 *入塾前生徒に関連する売上情報取得
	 */
	public function before_juku_sales()
	{
		return $this->hasMany('App\BeforeJukuSales', 'before_student_no', 'before_student_no');
	}

	/**
	 *入塾前生徒に関連する売上情報取得
	 */
	public function before_juku_sale()
	{
		return $this->belongsTo('App\BeforeJukuSales', 'before_student_no', 'before_student_no');
	}

	/**
	 *ユーザーに関連する学校名情報取得
	 */
	public function brothers_school_no1_school()
	{
		return $this->hasOne('App\School', 'id', 'brothers_school_no1');
	}

	/**
	 *ユーザーに関連する学校名情報取得
	 */
	public function brothers_school_no2_school()
	{
		return $this->hasOne('App\School', 'id', 'brothers_school_no2');
	}

	/**
	 *ユーザーに関連する学校名情報取得
	 */
	public function brothers_school_no3_school()
	{
		return $this->hasOne('App\School', 'id', 'brothers_school_no3');
	}
	public function getFullNameAttribute()
	{
		return $this->surname . $this->name;
	}
}
