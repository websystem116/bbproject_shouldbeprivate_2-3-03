<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
	use SoftDeletes;
	protected $guarded = ["id"];
	public $timestamps = false;

	// 「１対多」→ メソッド名は複数形
	public function discountdetails()
	{
		// DiscountDetailモデルのデータを引っ張てくる
		return $this->hasMany('App\DiscountDetail',  'discount_id');
	}
}
