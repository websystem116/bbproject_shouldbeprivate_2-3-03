<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireScore extends Model
{

	//fillable
	protected $fillable = [
		'id',
		'user_id',
		'school_building_id',
		'classroom_score',
		'subject_score',
		'created_at',
		'updated_at',
		'deleted_at',
	];

	// use SoftDeletes;
	protected $guarded = ["id"];
	public $timestamps = false;

	//belongs to school_building_id
	public function school_building()
	{
		return $this->belongsTo('App\SchoolBuilding', 'school_building_id');
	}
	//belongs to user_id
	public function user()
	{
		return $this->belongsTo('App\User', 'user_id');
	}
}
