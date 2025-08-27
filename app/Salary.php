<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
	use SoftDeletes;
	protected $guarded = ["id"];

	protected $fillable = [
		'user_id',
		'tightening_date',
		'other_payment_amount',
		'other_payment_reason',
		'other_deduction_amount',
		'other_deduction_reason',
		'other_deduction2_amount',
		'other_deduction2_reason',
		'other_deduction3_amount',
		'other_deduction3_reason',
		'health_insurance',
		'welfare_pension',
		'employment_insurance',
		'municipal_tax',
		'year_end_adjustment'
	];
	public function salary_detail()
	{
		return $this->hasMany(SalaryDetail::class);
	}
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	// add comma for amount
	public function addComma($amount)
	{
		return number_format($amount, 0, '', ',');
	}
}
