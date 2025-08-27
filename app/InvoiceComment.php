<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceComment extends Model
{
	protected $fillable = [
		'abbreviation',
		'comment',
	];
}
