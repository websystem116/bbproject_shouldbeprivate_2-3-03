<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curriculum extends Model
{
	protected $guarded = ['id'];
	protected $table = 'curriculums';
	use SoftDeletes;

	protected $fillable = [
		'id',
		'name',
		'from_grade',
		'to_grade',
	];

}
