<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultCategory extends Model
{
	protected $table = 'result_categorys';
	protected $guarded = ['id'];


	public function subjects()
	{
		//ソートNo順に並び替え
		return $this->hasMany('App\Subject', 'result_category_id', 'id')->orderBy('sort_no', 'asc');
	}
	public function implementations()
	{
		return $this->hasMany('App\Implementation', 'result_category_id', 'id');
	}

	public function implementation()
	{
		return $this->hasOne('App\Implementation', 'result_category_id', 'id');
	}
}
