<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
	use SoftDeletes;
	protected $guarded = ["id"];
	public $timestamps = false;
	public function getCodeAndNameAttribute()
	{
		return $this->code . "　" . $this->name;
	}
}
