<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

// db
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'last_name',
		'first_name',
		'last_name_kana',
		'first_name_kana',
		'birthday',
		'sex',
		'post_code',
		'address1',
		'address2',
		'address3',
		'tel',
		'school_building',
		'employment_status',
		'occupation',
		'class_wage',
		'personal_wage',
		'pc_wage',
		'office_wage',
		'creator',
		'updater',
		'retirement_date',
		'user_id',
		'hiredate',
		'description_column',
		'deductible_spouse',
		'dependents_count',
		'bank_id',
		'branch_id',
		'account_type',
		'account_number',
		'recipient_name',
		'roles',

	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	// public function setPasswordAttribute($value)
	// {

	// 	$this->attributes['password'] = Hash::make($value);
	// }

	public function selectUserFindById($id)
	{
		// 「SELECT id, name, email WHERE id = ?」を発行する
		$query = $this->select([
			'id',
			'email',
			'password',
			'last_name',
			'first_name',
			'last_name_kana',
			'first_name_kana',
			'birthday',
			'sex',
			'post_code',
			'address1',
			'address2',
			'address3',
			'tel',
			'school_building',
			'employment_status',
			'occupation',
			'class_wage',
			'personal_wage',
			'pc_wage',
			'office_wage',
			'creator',
			'updater',
			'retirement_date',
			'user_id',
			'hiredate',
			'description_column',
			'deductible_spouse',
			'dependents_count',
			'bank_id',
			'branch_id',
			'account_type',
			'account_number',
			'recipient_name',
			'roles',
		])->where([
			'id' => $id
		]);
		// first()は1件のみ取得する関数
		return $query->first();
	}


	public function bank()
	{
		return $this->belongsTo('App\Bank');
	}


	public function getFullNameAttribute()
	{
		// return $this->last_name . "　" . $this->first_name;

		// ?が含まれている場合は、文字列置換を行う
		$this->last_name = str_replace('?', '', $this->last_name);
		$this->first_name = str_replace('?', '', $this->first_name);

		return $this->last_name . "　" . $this->first_name;
	}

	// 「１対１」→ メソッド名は単数形
	public function questionnaire_scores()
	{
		// return $this->belongsTo('App\Bank');
		return $this->hasMany('App\QuestionnaireScore');
		// return $this->hasMany('App\QuestionnaireScore', 'user_id');
	}

	public function school_buildings()
	{
		return $this->belongsTo('App\SchoolBuilding', 'school_building');
	}
	public function job_description_wages()
	{
		return $this->hasMany('App\JobDescriptionWage');
	}
	public function other_job_description_wages()
	{
		return $this->hasMany('App\OtherJobDescriptionWage');
	}

	//入社日の最も古い年を取得
	static function get_hire_date_list()
	{
		$min_date = DB::table('users')->min('hiredate');
		// $min_yearを年だけにする
		$min_year = date('Y', strtotime($min_date));

		$max_date = DB::table('users')->max('hiredate');
		// $max_yearを年だけにする
		$max_year = date('Y', strtotime($max_date));

		// $min_yearと$max_yearの差を取得して配列にする
		$hire_date_list = range($min_year, $max_year);



		// valueをkeyにする
		$hire_date_list = array_combine($hire_date_list, $hire_date_list);

		// 選択してくださいを先頭に追加
		// $hire_date_list = array_merge(['' => '選択してください'], $hire_date_list);

		$hire_date_list = ['' => '選択してください'] + $hire_date_list;

		return $hire_date_list;
	}


	// 退職日の最も古い年を取得
	static function get_retirement_date_list()
	{
		$min_date = DB::table('users')->min('retirement_date');
		// $min_yearを年だけにする
		$min_year = date('Y', strtotime($min_date));

		$max_date = DB::table('users')->max('retirement_date');
		// $max_yearを年だけにする
		$max_year = date('Y', strtotime($max_date));

		// $min_yearと$max_yearの差を取得して配列にする
		$retirement_date_list = range($min_year, $max_year);


		// valueをkeyにする
		$retirement_date_list = array_combine($retirement_date_list, $retirement_date_list);

		// 選択してくださいを先頭に追加
		$retirement_date_list = ['' => '選択してください'] + $retirement_date_list;


		return $retirement_date_list;
	}
}