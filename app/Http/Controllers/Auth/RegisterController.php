<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Bank;
use App\BranchBank;
use App\JobDescription;
use App\OtherJobDescription;
use App\JobDescriptionWage;
use App\OtherJobDescriptionWage;
use App\SchoolBuilding;

class RegisterController extends Controller
{
	/*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// $this->middleware('guest');
		$this->redirectTo = route('home');
	}
	// 一般ユーザ登録用のviewを指定
	public function showRegistrationForm()
	{
		$banks_selects = Bank::all()->pluck('code_and_name', 'id');
		$branch_banks_alls = BranchBank::all();
		$branch_banks = $branch_banks_alls->pluck('code_and_name', 'code');
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		$job_description = JobDescription::all();
		$other_job_description = OtherJobDescription::all();
		foreach ($branch_banks_alls as $branch_banks_all) {
			$branch_banks_select[$branch_banks_all->bank_id][] = [
				'display' => $branch_banks_all->code . " " . $branch_banks_all->name,
				'value' => $branch_banks_all->code
			];
		}

		return view('auth.register', compact("banks_selects", "branch_banks", "school_buildings", "job_description", "other_job_description", 'branch_banks_select'));
	}

	public function index(Request $request)
	{
		$perPage = 25;


		$hire_date_list = User::get_hire_date_list();
		// $retirement_date_list = User::get_retirement_date_list();

		$users_name = User::all()->pluck('full_name', 'full_name');
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		$search_name = $request->get("name");
		$search_school_building = $request->get("school_building");
		$search_employment_status = $request->get("employment_status");
		$search_occupation = $request->get("occupation");
		$search_work_status = $request->get("work_status");
		$search_user_id = $request->get("user_id");
		$search_join_year = $request->get("join_year");
		$search_retire_year = $request->get("retire_year");
		$search_roles = $request->get("roles");
		$search_names = [];
		$user_search['name'] = $search_name;
		$user_search['school_building'] = $search_school_building;
		$user_search['employment_status'] = $search_employment_status;
		$user_search['occupation'] = $search_occupation;
		$user_search['work_status'] = $search_work_status;
		$user_search['user_id'] = $search_user_id;
		$user_search['join_year'] = $search_join_year;
		$user_search['retire_year'] = $search_retire_year;
		$user_search['roles'] = $search_roles;
		if (!empty($search_name)) {
			$search_names = explode(",", str_replace("　", ",", $search_name));
		}
		$user = User::when(!empty($search_names), function ($query) use ($search_names) {
			return $query->where('last_name', $search_names[0])->where('first_name', $search_names[1]);
		})->when(!empty($search_school_building), function ($query) use ($search_school_building) {
			return $query->where('school_building', $search_school_building);
		})->when(!empty($search_employment_status), function ($query) use ($search_employment_status) {
			return $query->where('employment_status', $search_employment_status);
		})->when(!empty($search_occupation), function ($query) use ($search_occupation) {
			return $query->where('occupation', $search_occupation);
		})->when(!empty($search_work_status), function ($query) use ($search_work_status) {
			if ($search_work_status == 2) {
				return $query->whereNotNull('retirement_date');
			}
		})->when(empty($search_work_status), function ($query) use ($search_work_status) {	//初期表示で勤務中のみ表示するように変更
			return $query->whereNull('retirement_date');
		})->when(!empty($search_user_id), function ($query) use ($search_user_id) {
			return $query->where('user_id', $search_user_id);
		})->when(!empty($search_join_year), function ($query) use ($search_join_year) {
			return $query->whereYear('hiredate', $search_join_year);
		})->when(!empty($search_retire_year), function ($query) use ($search_retire_year) {
			return $query->where('retirement_date', 'like', $search_retire_year . '%');
		})->when(!empty($search_roles), function ($query) use ($search_roles) {
			return $query->where('roles', $search_roles);
		})

			->paginate($perPage);
		return view("auth.index", compact("user", "users_name", "school_buildings", "user_search", "hire_date_list"));
	}
	public function register(Request $request)
	{
		$requestData = $request->all();
		$requestWageData["wage"] = $requestData["wage"];
		$requestOtherWageData["other_wage"] = $requestData["other_wage"];
		unset($requestData["wage"]);
		unset($requestData["other_wage"]);

		$this->validator($requestData)->validate();


		event(new Registered($user = $this->create($requestData)));
		$job_description = JobDescription::all();
		$other_job_description = OtherJobDescription::all();

		$requestWageDataCnt = is_countable($requestWageData['wage']) ? count($requestWageData['wage']) : 0;

		$requestOtherWageDataCnt = is_countable($requestOtherWageData['other_wage']) ? count($requestOtherWageData['other_wage']) : 0;
		for ($i = 0; $i < $requestWageDataCnt; $i++) {
			$Data = ['user_id' => $user->id, "job_description_id" => $job_description[$i]["id"], "wage" => $requestWageData['wage'][$i], "creator" => Auth::user()->id, "updater" => Auth::user()->id];
			JobDescriptionWage::create($Data);
		}
		for ($i = 0; $i < $requestOtherWageDataCnt; $i++) {

			// $Data = ['user_id' => $user->id, "other_job_description_id" => $other_job_description[$i]["id"], "wage" => $requestWageData['other_wage'][$i], "creator" => Auth::user()->id, "updater" => Auth::user()->id];
			$Data = ['user_id' => $user->id, "other_job_description_id" => $other_job_description[$i]["id"], "wage" => $requestOtherWageData['other_wage'][$i], "creator" => Auth::user()->id, "updater" => Auth::user()->id];
			OtherJobDescriptionWage::create($Data);
		}
		return $this->registered($request, $user)
			?: redirect($this->redirectPath());
	}
	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
		return Validator::make($data, [
			// 'name' => ['required', 'string', 'max:255'],

			// 'email' => ['required', 'string', 'email', 'max:255'],
			// 'password' => ['required', 'string', 'min:8', 'confirmed'],
			'password' => ['required', 'string', 'min:1'],
			'last_name' => ['required', 'string', 'max:255'],
			'first_name' => ['required', 'string', 'max:255'],
			'last_name_kana' => ['required', 'string', 'max:255'],
			'first_name_kana' => ['required', 'string', 'max:255'],
			'birthday' => ['required', 'date'],
			'sex' => ['required', 'integer'],
			'post_code' => ['string', 'max:8'],
			'tel' => ['string', 'max:15'],
			'school_building' => ['required'],
			'employment_status' => ['required'],
			'occupation' => ['required', 'integer'],
			'user_id' => ['required', 'string', 'max:10', 'unique:users'],
			'roles' => ['required', 'string'],

			// 'description_column' => ['required_if:employment_status,3', 'string', 'max:255'],

		], [
			'password.required' => 'パスワードは必須です。',
			'last_name.required' => '姓を入力してください。',
			'first_name.required' => '名を入力してください。',
			'last_name_kana.required' => '姓(カナ)を入力してください。',
			'first_name_kana.required' => '名(カナ)を入力してください。',
			'birthday.required' => '生年月日を入力してください。',
			'sex.required' => '性別を選択してください。',
			'post_code.required' => '郵便番号を入力してください。',
			'post_code.string' => '郵便番号を入力してください。',
			'post_code.max' => '郵便番号は8文字以内で入力してください。',
			'tel.required' => '電話番号を入力してください。',
			'tel.string' => '電話番号を入力してください。',
			'tel.max' => '電話番号は15文字以内で入力してください。',
			'school_building.required' => '校舎を選択してください。',
			'employment_status.required' => '職務を選択してください。',
			'occupation.required' => '職業を選択してください。',
			'user_id.required' => 'ユーザーIDを入力してください。',
			'user_id.unique' => 'ユーザーIDが重複しています。',
			'user_id.max' => 'ユーザーIDは10文字以内で入力してください。',
			'roles.required' => '権限を選択してください。',
			// 'description_column.required_if' => '摘要欄を選択してください。',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\User
	 */
	protected function create(array $data)
	{
		$data += [
			"creator" => Auth::user()->id,
			"updater" => Auth::user()->id
		];

		return User::create($data);
	}

	public function edit($id)
	{
		$url = url()->previous();
		// sessionにURLを保存
		session(["url" => $url]);

		$user = User::findOrFail($id);
		$banks_selects = Bank::all()->pluck('code_and_name', 'id');
		$branch_banks_alls = BranchBank::all();
		$branch_banks = BranchBank::where('bank_id', $user->bank_id)->get()->pluck('code_and_name', 'code');
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		foreach ($branch_banks_alls as $branch_banks_all) {
			$branch_banks_select[$branch_banks_all->bank_id][] = [
				'display' => $branch_banks_all->code . " " . $branch_banks_all->name,
				'value' => $branch_banks_all->code
			];
		}

		$job_description = JobDescription::all();
		$other_job_description = OtherJobDescription::all();
		$job_description_wages = $user->job_description_wages;
		$job_description_wage_values = [];

		foreach ($job_description_wages as $job_description_wage) {
			$job_description_wage_values[$job_description_wage->job_description_id] = $job_description_wage->wage;
		}
		// dd($job_description_wage_values);
		$other_job_description_wages = $user->other_job_description_wages;
		$other_job_description_wage_values = [];
		foreach ($other_job_description_wages as $other_job_description_wage) {
			$other_job_description_wage_values[$other_job_description_wage->other_job_description_id] = $other_job_description_wage->wage;
		}
		return view('auth.edit', compact("user", "banks_selects", "branch_banks", "school_buildings", "job_description", "other_job_description", "job_description_wage_values", "other_job_description_wage_values", "branch_banks_select"));
	}
	public function read($id)
	{
		$user = User::findOrFail($id);
		$banks_selects = Bank::all()->pluck('code_and_name', 'id');
		$branch_banks = BranchBank::where('bank_id', $user->bank_id)->get()->pluck('code_and_name', 'code');
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		$job_description = JobDescription::all();
		$other_job_description = OtherJobDescription::all();

		$job_description = JobDescription::all();
		$other_job_description = OtherJobDescription::all();
		$job_description_wages = $user->job_description_wages;
		$job_description_wage_values = [];


		foreach ($job_description_wages as $job_description_wage) {
			$job_description_wage_values[$job_description_wage->job_description_id] = $job_description_wage->wage;
		}

		// dd($job_description_wage_values);
		$other_job_description_wages = $user->other_job_description_wages;
		$other_job_description_wage_values = [];
		foreach ($other_job_description_wages as $other_job_description_wage) {
			$other_job_description_wage_values[$other_job_description_wage->other_job_description_id] = $other_job_description_wage->wage;
		}

		return view('auth.read', compact("user", "banks_selects", "branch_banks", "school_buildings", "job_description", "other_job_description", "other_job_description_wage_values", "job_description_wage_values"));
	}
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function update(Request $request, $id)
	{

		$this->validate($request, [
			// 'email' => ['required', 'string', 'email', 'max:255'],
			// 'password' => ['required', 'string', 'min:1', 'confirmed'],
			'password' => ['required', 'string', 'min:1'],
			'last_name' => ['required', 'string', 'max:255'],
			'first_name' => ['required', 'string', 'max:255'],
			'last_name_kana' => ['required', 'string', 'max:255'],
			'first_name_kana' => ['required', 'string', 'max:255'],
			'birthday' => ['required', 'date'],
			'sex' => ['required', 'integer', 'max:2'],
			'post_code' => ['string', 'max:8'],
			'tel' => ['string', 'max:15'],
			'school_building' => ['required'],
			'employment_status' => ['required'],
			'occupation' => ['required'],
			'user_id' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($id)],
			'roles' => ['required', 'string'],

			// 'description_column' => ['required_if:employment_status,3', 'string', 'max:255'],
		], [
			// 'email.required' => 'メールアドレスは必須です。',
			'password.required' => 'パスワードは必須です。',
			'last_name.required' => '姓を入力してください。',
			'first_name.required' => '名を入力してください。',
			'last_name_kana.required' => '姓(カナ)を入力してください。',
			'first_name_kana.required' => '名(カナ)を入力してください。',
			'birthday.required' => '生年月日を入力してください。',
			'sex.required' => '性別を選択してください。',
			'post_code.required' => '郵便番号を入力してください。',
			'post_code.string' => '郵便番号を入力してください。',
			'post_code.max' => '郵便番号は8文字以内で入力してください。',
			'tel.required' => '電話番号を入力してください。',
			'tel.string' => '電話番号を入力してください。',
			'tel.max' => '電話番号は15文字以内で入力してください。',
			'school_building.required' => '校舎を選択してください。',
			'employment_status.required' => '職務を選択してください。',
			'occupation.required' => '職業を選択してください。',
			'user_id.required' => 'ユーザーIDを入力してください。',
			'user_id.unique' => 'ユーザーIDが重複しています。',
			'user_id.max' => 'ユーザーIDは10文字以内で入力してください。',

			'roles.required' => '権限を選択してください。',
			// 'description_column.required_if' => '摘要欄を選択してください。',

		]);


		$requestData = $request->all();
		$requestWageData["wage"] = $requestData["wage"];
		$requestOtherWageData["other_wage"] = $requestData["other_wage"];
		unset($requestData["wage"]);
		unset($requestData["other_wage"]);
		$user = User::findOrFail($id);
		$user->update($requestData);

		$job_description = JobDescription::all();
		JobDescriptionWage::where('user_id', $id)->delete();
		$other_job_description = OtherJobDescription::all();
		OtherJobDescriptionWage::where('user_id', $id)->delete();

		$requestWageDataCnt = is_countable($requestWageData['wage']) ? count($requestWageData['wage']) : 0;
		$requestOtherWageDataCnt = is_countable($requestOtherWageData['other_wage']) ? count($requestOtherWageData['other_wage']) : 0;
		for ($i = 0; $i < $requestWageDataCnt; $i++) {
			$Data = ['user_id' => $id, "job_description_id" => $job_description[$i]["id"], "wage" => $requestWageData['wage'][$i], "creator" => Auth::user()->id, "updater" => Auth::user()->id];
			JobDescriptionWage::create($Data);
		}

		for ($i = 0; $i < $requestOtherWageDataCnt; $i++) {
			$Data = ['user_id' => $id, "other_job_description_id" => $other_job_description[$i]["id"], "wage" => $requestOtherWageData['other_wage'][$i], "creator" => Auth::user()->id, "updater" => Auth::user()->id];
			OtherJobDescriptionWage::create($Data);
		}

		// get session url
		$url = session("url");
		session()->forget("url");

		if (strpos($url, "auth/index") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/auth/index")->with("flash_message", "データが更新されました。");
		}
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */

	public function destroy($id)
	{
		User::destroy($id);
		return redirect($this->redirectPath())->with("flash_message", "データが削除されました。");
	}

	/**
	 * CSV出力処理
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function user_info_output(Request $request)
	{
		try {
			// 検索条件を取得
			$search_name = $request->get("name");
			$search_school_building = $request->get("school_building");
			$search_employment_status = $request->get("employment_status");
			$search_occupation = $request->get("occupation");
			$search_work_status = $request->get("work_status");
			$search_user_id = $request->get("user_id");
			$search_join_year = $request->get("join_year");
			$search_retire_year = $request->get("retire_year");
			$search_roles = $request->get("roles");
			$search_names = [];
			
			if (!empty($search_name)) {
				$search_names = explode(",", str_replace("　", ",", $search_name));
			}
			
			// 検索条件に基づいてユーザーを取得（ページネーションなし）
			$users = User::when(!empty($search_names), function ($query) use ($search_names) {
				return $query->where('last_name', $search_names[0])->where('first_name', $search_names[1]);
			})->when(!empty($search_school_building), function ($query) use ($search_school_building) {
				return $query->where('school_building', $search_school_building);
			})->when(!empty($search_employment_status), function ($query) use ($search_employment_status) {
				return $query->where('employment_status', $search_employment_status);
			})->when(!empty($search_occupation), function ($query) use ($search_occupation) {
				return $query->where('occupation', $search_occupation);
			})->when(!empty($search_work_status), function ($query) use ($search_work_status) {
				if ($search_work_status == 2) {
					return $query->whereNotNull('retirement_date');
				}
			})->when(empty($search_work_status), function ($query) use ($search_work_status) {
				return $query->whereNull('retirement_date');
			})->when(!empty($search_user_id), function ($query) use ($search_user_id) {
				return $query->where('user_id', $search_user_id);
			})->when(!empty($search_join_year), function ($query) use ($search_join_year) {
				return $query->whereYear('hiredate', $search_join_year);
			})->when(!empty($search_retire_year), function ($query) use ($search_retire_year) {
				return $query->where('retirement_date', 'like', $search_retire_year . '%');
			})->when(!empty($search_roles), function ($query) use ($search_roles) {
				return $query->where('roles', $search_roles);
			})->get();
			
			// CSVファイル名
			$filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
			
			// レスポンスヘッダーを設定
			header('Content-Type: text/csv; charset=UTF-8');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');
			
			// 出力バッファをクリア
			ob_clean();
			
			// BOMを出力（Excelで正しく日本語を表示するため）
			echo "\xEF\xBB\xBF";
			
			// CSV出力用のファイルポインタを開く
			$output = fopen('php://output', 'w');
			
			// CSVヘッダーを出力
			fputcsv($output, [
				'ユーザーID',
				'名前',
				'名前カナ',
				'入社日',
				'退社日',
				'勤務状況',
				'校舎',
				'職務',
				'職種',
				'権限',
				'生年月日',
				'性別',
				'郵便番号',
				'住所1',
				'住所2',
				'住所3',
				'電話番号',
				'メールアドレス',
				'銀行コード',
				'支店コード',
				'口座種別',
				'口座番号',
				'口座名義',
				'配偶者控除',
				'扶養人数',
				'摘要'
			]);
			
			// データ行を出力
			foreach ($users as $user) {
				fputcsv($output, [
					$user->user_id ?? '',
					($user->last_name ?? '') . ($user->first_name ?? ''),
					($user->last_name_kana ?? '') . ($user->first_name_kana ?? ''),
					$user->hiredate ?? '',
					$user->retirement_date ?? '',
					$user->retirement_date ? '退職' : '勤務',
					$user->school_buildings->name ?? '',
					config('const.employment_status')[$user->employment_status] ?? '',
					config('const.occupation')[$user->occupation] ?? '',
					config('const.roles')[$user->roles] ?? '',
					$user->birthday ?? '',
					config('const.gender')[$user->sex] ?? '',
					$user->post_code ?? '',
					$user->address1 ?? '',
					$user->address2 ?? '',
					$user->address3 ?? '',
					$user->tel ?? '',
					$user->email ?? '',
					$user->bank_id ?? '',
					$user->branch_id ?? '',
					config('const.account_type')[$user->account_type] ?? '',
					$user->account_number ?? '',
					$user->recipient_name ?? '',
					$user->deductible_spouse ?? '',
					$user->dependents_count ?? '',
					$user->description_column ?? ''
				]);
			}
			
			// ファイルポインタを閉じる
			fclose($output);
			
			// 処理終了
			exit();
			
		} catch (\Exception $e) {
			// エラーハンドリング
			return redirect()->back()->with('message', 'CSV出力中にエラーが発生しました: ' . $e->getMessage());
		}
	}

	// ログイン後のリダイレクト先を記述
	public function redirectPath()
	{
		return route("register.index");
	}

	// ログイン中のユーザーの情報変更
	public function myedit()
	{
		$id = Auth::user()->id;
		$url = url()->previous();
		// sessionにURLを保存
		session(["url" => $url]);

		$user = User::findOrFail($id);
		$banks_selects = Bank::all()->pluck('code_and_name', 'id');
		$branch_banks_alls = BranchBank::all();
		$branch_banks = BranchBank::where('bank_id', $user->bank_id)->get()->pluck('code_and_name', 'code');
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		foreach ($branch_banks_alls as $branch_banks_all) {
			$branch_banks_select[$branch_banks_all->bank_id][] = [
				'display' => $branch_banks_all->code . " " . $branch_banks_all->name,
				'value' => $branch_banks_all->code
			];
		}

		$job_description = JobDescription::all();
		$other_job_description = OtherJobDescription::all();
		$job_description_wages = $user->job_description_wages;
		$job_description_wage_values = [];

		foreach ($job_description_wages as $job_description_wage) {
			$job_description_wage_values[$job_description_wage->job_description_id] = $job_description_wage->wage;
		}
		// dd($job_description_wage_values);
		$other_job_description_wages = $user->other_job_description_wages;
		$other_job_description_wage_values = [];
		foreach ($other_job_description_wages as $other_job_description_wage) {
			$other_job_description_wage_values[$other_job_description_wage->other_job_description_id] = $other_job_description_wage->wage;
		}
		return view('auth.myedit', compact("user", "banks_selects", "branch_banks", "school_buildings", "job_description", "other_job_description", "job_description_wage_values", "other_job_description_wage_values", "branch_banks_select"));
	}

	public function myupdate(Request $request)
	{
		$id = Auth::user()->id;
		$this->validate($request, [
			// 'email' => ['required', 'string', 'email', 'max:255'],
			// 'password' => ['required', 'string', 'min:1', 'confirmed'],
			'password' => ['required', 'string', 'min:1'],
			'last_name' => ['required', 'string', 'max:255'],
			'first_name' => ['required', 'string', 'max:255'],
			'last_name_kana' => ['required', 'string', 'max:255'],
			'first_name_kana' => ['required', 'string', 'max:255'],
			'birthday' => ['required', 'date'],
			'sex' => ['required', 'integer', 'max:2'],
			'post_code' => ['string', 'max:8'],
			'tel' => ['string', 'max:15'],
			'school_building' => ['required'],
			'employment_status' => ['required'],
			'occupation' => ['required'],
			'user_id' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($id)],
			'roles' => ['required', 'string'],

			// 'description_column' => ['required_if:employment_status,3', 'string', 'max:255'],
		], [
			// 'email.required' => 'メールアドレスは必須です。',
			'password.required' => 'パスワードは必須です。',
			'last_name.required' => '姓を入力してください。',
			'first_name.required' => '名を入力してください。',
			'last_name_kana.required' => '姓(カナ)を入力してください。',
			'first_name_kana.required' => '名(カナ)を入力してください。',
			'birthday.required' => '生年月日を入力してください。',
			'sex.required' => '性別を選択してください。',
			'post_code.required' => '郵便番号を入力してください。',
			'post_code.string' => '郵便番号を入力してください。',
			'post_code.max' => '郵便番号は8文字以内で入力してください。',
			'tel.required' => '電話番号を入力してください。',
			'tel.string' => '電話番号を入力してください。',
			'tel.max' => '電話番号は15文字以内で入力してください。',
			'school_building.required' => '校舎を選択してください。',
			'employment_status.required' => '職務を選択してください。',
			'occupation.required' => '職業を選択してください。',
			'user_id.required' => 'ユーザーIDを入力してください。',
			'user_id.unique' => 'ユーザーIDが重複しています。',
			'user_id.max' => 'ユーザーIDは10文字以内で入力してください。',

			'roles.required' => '権限を選択してください。',
			// 'description_column.required_if' => '摘要欄を選択してください。',

		]);


		$requestData = $request->all();
		$requestWageData["wage"] = $requestData["wage"];
		$requestOtherWageData["other_wage"] = $requestData["other_wage"];
		unset($requestData["wage"]);
		unset($requestData["other_wage"]);
		$user = User::findOrFail($id);
		$user->update($requestData);

		Auth::setUser($user);

		$job_description = JobDescription::all();
		JobDescriptionWage::where('user_id', $id)->delete();
		$other_job_description = OtherJobDescription::all();
		OtherJobDescriptionWage::where('user_id', $id)->delete();

		$requestWageDataCnt = is_countable($requestWageData['wage']) ? count($requestWageData['wage']) : 0;
		$requestOtherWageDataCnt = is_countable($requestOtherWageData['other_wage']) ? count($requestOtherWageData['other_wage']) : 0;
		for ($i = 0; $i < $requestWageDataCnt; $i++) {
			$Data = ['user_id' => $id, "job_description_id" => $job_description[$i]["id"], "wage" => $requestWageData['wage'][$i], "creator" => Auth::user()->id, "updater" => Auth::user()->id];
			JobDescriptionWage::create($Data);
		}

		for ($i = 0; $i < $requestOtherWageDataCnt; $i++) {
			$Data = ['user_id' => $id, "other_job_description_id" => $other_job_description[$i]["id"], "wage" => $requestOtherWageData['other_wage'][$i], "creator" => Auth::user()->id, "updater" => Auth::user()->id];
			OtherJobDescriptionWage::create($Data);
		}

		// get session url
		$url = session("url");
		session()->forget("url");

		return redirect($url)->with("flash_message", "データが更新されました。");
	}
}
