<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessUser extends Model
{
	protected $guarded = ['id'];
	use SoftDeletes;


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
	 *ユーザーに関連する受講情報取得　生徒情報で使用
	 */
	public function juko_info()
	{
		return $this->belongsTo('App\JukoInfo', 'student_no', 'student_no');
	}
	/**
	 *ユーザーに関連する銀行情報取得
	 */
	public function bank()
	{
		return $this->belongsTo('App\Bank', 'bank_id', 'code');
	}
	/**
	 *ユーザーに関連する銀行支店情報取得
	 */
	public function branch_bank()
	{
		return $this->belongsTo('App\BranchBank', 'branch_code', 'code');
	}
	/**
	 *ユーザーに関連する割引情報取得
	 */
	public function product()
	{
		return $this->hasOne('App\Product', 'product_id');
	}
	/**
	 *ユーザーに関連する受講情報取得　受講情報で使用
	 */
	public function juko_infos()
	{
		return $this->hasMany('App\JukoInfo', 'student_no', 'student_no');
	}

	public function getFullNameAttribute()
	{
		return $this->surname . $this->name;
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

	public function sale()
	{
		return $this->hasOne('App\Sale', 'student_no', 'student_no');
	}
	/**
	 * CSVの出力の順番を学年順でさらに同学年の中で入塾順で並ぶように
	 */
	function sort_student($a, $b)
	{
		if ($a['school_building_id'] == $b['school_building_id']) {
			return ($a['grade'] < $b['grade']) ? 1 : -1;
		}
		return ($a['school_building_id'] < $b['school_building_id']) ? 1 : -1;
	}

	// sur_nameから?を削除
	public function getSurNameAttribute($value)
	{
		return str_replace('?', '', $value);
	}

	// nameから?を削除
	public function getNameAttribute($value)
	{
		return str_replace('?', '', $value);
	}
}
