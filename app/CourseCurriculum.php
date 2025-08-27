<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseCurriculum extends Model
{
	protected $guarded = ['id'];
	protected $table = 'course_curriculums';

	protected $fillable = [
		'id',
		'course_id',
		'curriculum_id',
	];

	public function course()
	{
		return $this->belongsTo('App\Course', 'course_id', 'id');
	}
}
