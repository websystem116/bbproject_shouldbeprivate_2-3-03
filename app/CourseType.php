<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseType extends Model
{
	protected $guarded = ['id'];
	protected $table = 'course_types';
	use SoftDeletes;

	protected $fillable = [
		'id',
		'course_id',
		'type_name',
		'show_pulldown',
	];

	public function course()
	{
		return $this->belongsTo('App\Course', 'course_id', 'id');
	}

}
