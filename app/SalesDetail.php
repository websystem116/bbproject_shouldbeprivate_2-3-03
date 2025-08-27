<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDetail extends Model
{
	use SoftDeletes;

	public function student()
	{
		return $this->belongsTo('App\Student');
	}
	public function product()
	{
		return $this->belongsTo('App\Product');
	}
}
