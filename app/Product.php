<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use SoftDeletes;
	protected $guarded = ["id"];
	public $timestamps = false;

	public function getCommaPriceAttribute($value)
	{
		// add comma to price when displaying
		return number_format($this->price);
	}
}