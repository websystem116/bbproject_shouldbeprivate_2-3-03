<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
	protected $guarded = ['id'];
	protected $table = 'courses';
	use SoftDeletes;

	protected $fillable = [
		'id',
		'brand',
		'name',
		'from_grade',
		'to_grade',
	];

	public function course_curriculum()
	{
		return $this->hasMany('App\CourseCurriculum', 'course_id', 'id');
	}

	public function course_type()
	{
		return $this->hasMany('App\CourseType', 'course_id', 'id');
	}

}
