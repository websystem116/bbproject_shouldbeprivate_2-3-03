<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
	protected $guarded = ['id'];
	use SoftDeletes;

	protected $fillable = [
		'id',
		'reqest_date',
		'application_no',
		'application_type',
		'description',
		'detail',
		'status',
		'created_by',
		'charged_by',
		'allowed_by',
		'sign_filepath',
	];

}
