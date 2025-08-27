<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherJobDescriptionWageBackup extends Model
{
    protected $table = 'other_job_description_wages_backup';
    protected $guarded = ["id"];
	public $timestamps = false;

}
