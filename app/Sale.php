<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
	use SoftDeletes;

	protected $fillable = [
		"tax",
		"sales_sum",
		"creator",
		"updater"
	];
	public function student()
	{
		return $this->belongsTo('App\Student', 'student_no', 'student_no');
	}
	public function school_building()
	{
		return $this->belongsTo('App\SchoolBuilding');
	}
	public function sales_detail()
	{
		return $this->hasMany('App\SalesDetail', 'sales_number', 'sales_number');
	}
	public function getStudentNoAttribute()
	{
		return sprintf('%08d', $this->attributes['student_no']);
	}
	public function charge()
	{
		return $this->belongsTo('App\Charge', 'sales_number', 'sales_number');
	}
}
