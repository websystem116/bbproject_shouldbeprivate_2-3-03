<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
	protected $guarded = ['id'];

	public function ResultCategory()
	{
		return $this->belongsTo('App\ResultCategory');
	}
}
