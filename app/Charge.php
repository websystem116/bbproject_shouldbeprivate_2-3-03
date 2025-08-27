<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
	protected $fillable = [
		'convenience_store_flg',
		'transferred_flg',
	];

	public function charge_detail()
	{
		return $this->hasMany('App\ChargeDetail', 'sales_number', 'sales_number');
	}
	public function student()
	{
		return $this->belongsTo('App\Student', 'student_no', 'student_no');
	}
	public function sale()
	{
		return $this->belongsTo('App\Sale', 'sales_number', 'sales_number');
	}
}
