<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JukoInfo extends Model
{
	//fillable
	protected $fillable = [
		'id',
		'student_id',
		'student_no',
		'product_id',
		'deleted_at',
		'created_at',
		'updated_at',

	];
	protected $guarded = ["id"];

	/**
	 *受講情報に紐づく生徒情報取得
	 */
	public function student()
	{
		return $this->belongsTo('App\Student');
	}
	public function product()
	{
		return $this->belongsTo('App\Product');
	}
}
