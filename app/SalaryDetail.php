<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SalaryDetail extends Model
{
	// public static function insert($attributes)
	// {
	// 	$attributes['created_at'] = DB::raw('CURRENT_TIMESTAMP');
	// 	$attributes['updated_at'] = DB::raw('CURRENT_TIMESTAMP');
	// 	return (new static)->forwardCallTo((new static)->newQuery(), 'insert', [$attributes]);
	// }
	public function job_description()
	{
		return $this->belongsTo(JobDescription::class);
	}
	public function other_job_description()
	{
		return $this->belongsTo(OtherJobDescription::class, 'job_description');
	}
}
