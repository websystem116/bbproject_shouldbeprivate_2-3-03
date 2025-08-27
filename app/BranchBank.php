<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchBank extends Model
{
	use SoftDeletes;
	protected $guarded = ["id"];
	public $timestamps = false;

	// 「１対１」→ メソッド名は単数形
	public function bank()
	{
		// Bankモデルのデータを引っ張てくる
		// return $this->belongsTo('App\Bank');
		return $this->belongsTo('App\Bank', 'bank_id');
	}
	public function getCodeAndNameAttribute()
	{
		return $this->code . "　" . $this->name;
	}
}
