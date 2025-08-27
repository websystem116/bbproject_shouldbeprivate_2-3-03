<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class BeforeJukuSales extends Model
{

	//fillable
	protected $fillable = [
		'id',
		'before_student_no',
		'school_building_id',
		'sales_date',
		'payment_date',
		'product_id',
		'price_after_discount',
		'tax',
		'subtotal',
		'note',
		'created_at',
		'updated_at',

	];
	protected $guarded = ["id"];

	protected $dates = [
		'payment_date',
	];


	/**
	 *入塾前売上情報に紐づく入塾前生徒情報取得
	 */
	public function beforestudent()
	{
		return $this->belongsTo('App\BeforeStudent', 'id');
	}

	/**
	 *入塾前生徒に関連する商品情報の取得
	 */
	public function product()
	{
		return $this->belongsTo('App\Product', 'product_id', 'id');
	}

	/**
	 *売上情報から入塾前生徒情報取得
	 */
	public function before_student()
	{
		return $this->belongsTo('App\BeforeStudent', 'before_student_no', 'before_student_no');
	}

	/**
	 *売上情報から入塾前生徒情報取得
	 */
	public function school_building()
	{
		return $this->belongsTo('App\SchoolBuilding', 'school_building_id');
	}
}
