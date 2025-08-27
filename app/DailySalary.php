<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailySalary extends Model
{
	use SoftDeletes;
	public function salary_detail()
	{
		return $this->hasMany(SalaryDetail::class);
	}
	public function transportation_expense()
	{
		return $this->hasMany(TransportationExpense::class);
	}
	public function user()
	{
		return $this->belongsTo(User::class);
	}
	public function job_description()
	{
		return $this->belongsTo(JobDescription::class);
	}
	public function school_building()
	{
		return $this->belongsTo(SchoolBuilding::class);
	}
}
