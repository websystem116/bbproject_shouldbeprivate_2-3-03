<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AveragePoint extends Model
{
	protected $fillable = [
		'year',
		'school_id',
		'grade',
		'result_category_id',
		'implementation_no',
		'subject_no',
		'average_point'
	]; //保存したいカラム名が複数の場合

	// public function getStudentPointAttribute($value)
	// {
	// 	return "{$this->first_name} {$this->last_name}";
	// }
}
