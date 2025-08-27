<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobDescriptionWageBackup extends Model
{
    protected $table = 'job_description_wages_backup';
    protected $guarded = ["id"];
	public $timestamps = false;

}
