<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Implementation extends Model
{
	protected $guarded = ['id'];

	public function ResultCategory()
	{
		return $this->belongsTo('App\ResultCategory');
	}
}
