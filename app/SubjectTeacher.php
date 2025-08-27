<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectTeacher extends Model
{
	use SoftDeletes;
	protected $guarded = ["id"];
	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('App\User', 'user_id');
	}

	//belongs to school_building_id
	public function school_building()
	{
		return $this->belongsTo('App\SchoolBuilding', 'school_building_id');
	}
}
