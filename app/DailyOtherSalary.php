<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyOtherSalary extends Model
{
    public function school_buildings()
    {
        return $this->belongsTo('App\SchoolBuilding', 'school_building');
    }
    public function other_job_descriptions()
    {
        return $this->belongsTo('App\OtherJobDescription', 'job_description');
    }
	public function users()
	{
		return $this->belongsTo('App\User','user_id');
	}
}
