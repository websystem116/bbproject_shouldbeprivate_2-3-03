<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChargeProgress extends Model
{
	protected $table = "charge_progresss";
	protected $fillable = [
		"sales_data_created_flg",
		"sales_data_created_date",
		"sales_month",
		"monthly_processing_date",
		"new_monthly_processing_month",
		"creator",
		"updater"

	];
	protected $guarded = ["id"];
}
