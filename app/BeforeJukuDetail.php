<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BeforeJukuDetail extends Model
{
	/**
	 *売上情報から入塾前生徒情報取得
	 */
	public function before_student()
	{
		return $this->hasOne('App\BeforeStudent', 'before_student_no', 'before_student_no');
	}
}
