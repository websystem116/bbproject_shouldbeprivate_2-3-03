<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireRule extends Model
{
    use SoftDeletes;
	protected $guarded = ["id"];
	public $timestamps = false;

}

