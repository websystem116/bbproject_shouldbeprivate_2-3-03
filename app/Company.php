<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $guarded = ["id"];

    // relation table name companys
    protected $table = 'companys';
}
