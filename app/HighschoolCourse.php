<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HighschoolCourse extends Model
{
    use SoftDeletes;
	protected $guarded = ["id"];
	public $timestamps = false;

	Public function school()
	{
		return $this->belongsTo('App\School');
	}

}

