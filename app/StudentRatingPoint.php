<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentRatingPoint extends Model
{
	protected $fillable = [
		'student_id',
		'student_no',
		'grade',
		'year',
		'result_category_id',
		'implementation_no',
		'subject_no',
		'rating_point'
	]; //保存したいカラム名が複数の場合
}
