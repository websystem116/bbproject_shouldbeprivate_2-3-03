<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportationExpense extends Model
{
	public function school_buildings()
	{
		return $this->belongsTo('App\SchoolBuilding', 'school_building');
	}
	public function users()
	{
		return $this->belongsTo('App\User','user_id');
	}
}
