<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolBuilding extends Model
{
	// 校舎マスタ

	use SoftDeletes;
	protected $guarded = ["id"];
	public $timestamps = false;
}