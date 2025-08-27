<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
	protected $fillable = [
		'student_id',
		'student_no',
		'grade',
		'year',
		'result_category_id',
		'implementation_no',
		'subject_no',
		'point'
	]; //保存したいカラム名が複数の場合

	/**
	 *生徒情報の取得
	 */
	public function student()
	{
		return $this->hasOne('App\Student', 'student_no', 'student_no');
	}
	/**
	 *ユーザーに関連する校舎情報取得
	 */
	public function school()
	{
		return $this->belongsTo('App\School');
	}
	/**
	 *生徒情報の取得
	 */
	public function result_category()
	{
		return $this->hasOne('App\ResultCategory', 'id', 'result_category_id',);
	}

	public function student_results_grade()
	{
		return $this->belongsTo('App\StudentResult', 'student_no');
	}

	public function implementation()
	{
		return $this->hasOne('App\Implementation', 'result_category_id', 'result_category_id')->where('implementation_no', $this->implementation_no);
	}
}
