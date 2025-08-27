<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '256M');

use App\BeforeStudent;
use App\Student;
use App\Bank;
use App\BranchBank;
use App\Discount;
use App\SchoolBuilding;
use App\School;
use App\HighschoolCourse;
use App\Product;

use Illuminate\Http\Request;

// use phpspreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpParser\Node\Stmt\Foreach_;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Align;


// 罫線引きたい
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border as Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class BeforeStudentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{

		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});
		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		//商品のセレクトリスト
		$products = Product::get();
		$products_select_list = $products->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		//割引のセレクトリスト
		$discounts = Discount::get();
		$discounts_select_list = $discounts->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//検索の処理
		$query = BeforeStudent::query();

		//検索の値取得
		$student_search['id_start'] = $request->get("id_start");
		$student_search['id_end'] = $request->get("id_end");
		$student_search['no_start'] = $request->get("no_start");
		$student_search['no_end'] = $request->get("no_end");
		$student_search['surname'] = $request->get("surname");
		$student_search['name'] = $request->get("name");
		$student_search['surname_kana'] = $request->get("surname_kana");
		$student_search['name_kana'] = $request->get("name_kana");
		$student_search['phone'] = $request->get("phone");
		$student_search['school_id'] = $request->get("school_id");
		$student_search['brothers_flg'] = $request->get("brothers_flg");
		$student_search['fatherless_flg'] = $request->get("fatherless_flg");
		$student_search['holiday_flg'] = $request->get("holiday_flg");
		$student_search['sign_up_juku_flg'] = $request->get("sign_up_juku_flg");
		$student_search['grade_start'] = $request->get("grade_start");
		$student_search['grade_end'] = $request->get("grade_end");
		$student_search['school_building_id'] = $request->get("school_building_id");
		$student_search['product_select'] = $request->get("product_select");

		// 検索するテキストが入力されている場合のみ
		if (!empty($student_search['id_start']) && empty($student_search['id_end'])) {
			$query->where('before_students.id', $student_search['id_start']);
		}
		if (!empty($student_search['id_end']) && empty($student_search['id_start'])) {
			$query->where('before_students.id', $student_search['id_end']);
		}
		if (!empty($student_search['id_start']) && !empty($student_search['id_end'])) {
			$query->whereBetween('before_students.id', [$student_search['id_start'], $student_search['id_end']]);
		}
		// 検索するテキストが入力されている場合のみ
		if (!empty($student_search['no_start']) && empty($student_search['no_end'])) {
			$query->where('before_students.before_student_no', $student_search['no_start']);
		}
		if (!empty($student_search['no_end']) && empty($student_search['no_start'])) {
			$query->where('before_students.before_student_no', $student_search['no_end']);
		}
		if (!empty($student_search['no_start']) && !empty($student_search['no_end'])) {
			$query->whereBetween('before_students.before_student_no', [$student_search['no_start'], $student_search['no_end']]);
		}
		if (!empty($student_search['surname'])) {
			$query->where('surname', 'like', '%' . $student_search['surname'] . '%');
		}
		if (!empty($student_search['name'])) {
			$query->where('name', 'like', '%' . $student_search['name'] . '%');
		}
		if (!empty($student_search['surname_kana'])) {
			$query->where('surname_kana', 'like', '%' . $student_search['surname_kana'] . '%');
		}
		if (!empty($student_search['name_kana'])) {
			$query->where('name_kana', 'like', '%' . $student_search['name_kana'] . '%');
		}
		if (!empty($student_search['phone'])) {
			$query->where('phone1', 'like', '%' . $student_search['phone'] . '%')->orWhere('phone2', 'like', '%' . $student_search['phone'] . '%');
		}
		if (!empty($student_search['school_id'])) {
			$query->where('school_id', $student_search['school_id']);
		}
		if (!empty($student_search['brothers_flg'])) {
			$query->where('brothers_flg', $student_search['brothers_flg']);
		}
		if (!empty($student_search['fatherless_flg'])) {
			$query->where('fatherless_flg', $student_search['fatherless_flg']);
		}
		if (!empty($student_search['sign_up_juku_flg'])) {
			$query->where('sign_up_juku_flg', 0);
		}
		if (!empty($student_search['grade_start']) && empty($student_search['grade_end'])) {
			$query->where('grade', $student_search['grade_start']);
		}
		if (!empty($student_search['grade_end']) && empty($student_search['grade_start'])) {
			$query->where('grade', $student_search['grade_end']);
		}
		if (!empty($student_search['grade_start']) && !empty($student_search['grade_end'])) {
			$query->whereBetween('grade', [$student_search['grade_start'], $student_search['grade_end']]);
		}
		if (!empty($student_search['school_building_id'])) {
			$query->where('before_students.school_building_id', $student_search['school_building_id']);
		}
		if (!empty($student_search['product_select'])) {
			$query->join('before_juku_sales', 'before_juku_sales.before_student_no', '=', 'before_students.before_student_no')
				->where('before_juku_sales.product_id', $student_search['product_select']);
		}
		if (!empty($student_search['discount_select'])) {
			$query->where('discount_id', $student_search['discount_select']);
		}
		if ($request->has('search')) { //検索ぼたんなら
			$before_student = $query->get();
			return view("before_student.index", compact("before_student", "student_search", "schools_select_list", "schooolbuildings_select_list", "products_select_list", "discounts_select_list"));
		} else {
			$before_student = BeforeStudent::where("id", 0)->get();
			return view("before_student.index", compact("before_student", "student_search", "schools_select_list", "schooolbuildings_select_list", "products_select_list", "discounts_select_list"));
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$year = date('Y', strtotime('-3 month')); //4月から年度変わる
		//銀行のセレクトリスト
		$banks = Bank::get();
		$banks_select_list = $banks->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['code'] . "　" . $item['name']];
		});

		//支店のセレクトリスト
		$branch_banks = BranchBank::get();
		$branch_banks_select_list = $branch_banks->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['code'] . "　" . $item['name']];
		});

		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//割引のセレクトリスト
		$discounts = Discount::get();
		$discounts_select_list = $discounts->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});

		//コースのセレクトリスト
		$highschoolcourses = HighschoolCourse::get();
		$highschoolcourses_select_list = $highschoolcourses->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['school_id'] . "　" . $item['name']];
		});

		return view("before_student.create", compact("banks_select_list", "year", "branch_banks_select_list", "schools_select_list", "discounts_select_list", "schooolbuildings_select_list", "highschoolcourses_select_list"));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//student_no生成する処理
		$student_latest_cd = BeforeStudent::withTrashed()->latest('before_student_no')->first(); //最新のstudent_no取得
		$student_latest_cd_int = intval($student_latest_cd['before_student_no']); //int型に返還
		$student_no = $student_latest_cd_int + 1; //+1する
		$student_no = strval($student_no); //文字列に変換
		$student_no = sprintf('%08d', $student_no); //8桁にする
		$request->merge(['before_student_no' => $student_no]); //配列に追加
		$request->validate([
			'before_student_no' => 'required|unique:before_students',
		]);

		$requestData = $request->all();


		BeforeStudent::create($requestData);

		if ($requestData['sign_up_juku_flg'] == 1) { //入塾フラグが1なら生徒情報に登録
			$student_latest_cd = Student::withTrashed()->latest('id')->first('student_no'); //最新のstudent_no取得
			$student_latest_cd_int = intval($student_latest_cd['student_no']); //int型に返還
			$student_no = $student_latest_cd_int + 1; //+1する
			$student_no = strval($student_no); //文字列に変換
			$student_no = sprintf('%08d', $student_no); //8桁にする
			$request->merge(['student_no' => $student_no]); //配列に追加
			$request->validate([
				'student_no' => 'required|unique:students',
			]);
			$requestData = $request->all();
			// dd($requestData);
			$student_data = [
				'id' => 0,
				'student_no' => $requestData['student_no'],
				'surname' => $requestData['surname'],
				'name' => $requestData['name'],
				'surname_kana' => $requestData['surname_kana'],
				'name_kana' => $requestData['name_kana'],
				'gender' => $requestData['gender'],
				'zip_code' => $requestData['zip_code'],
				'address1' => $requestData['address1'],
				'birthdate' => $requestData['birthdate'],
				'address2' => $requestData['address2'],
				'phone1' => $requestData['phone1'],
				'phone2' => $requestData['phone2'],
				'email' => $requestData['email'],
				'grade' => $requestData['grade'],
				'school_id' => $requestData['school_id'],
				'parent_surname' => $requestData['parent_surname'],
				'parent_name' => $requestData['parent_name'],
				'parent_surname_kana' => $requestData['parent_surname_kana'],
				'parent_name_kana' => $requestData['parent_name_kana'],
				'brothers_name1' => $requestData['brothers_name1'],
				'brothers_gender1' => $requestData['brothers_gender1'],
				'brothers_grade1' => $requestData['brothers_grade1'],
				'brothers_school_no1' => $requestData['brothers_school_no1'],
				'brothers_name2' => $requestData['brothers_name2'],
				'brothers_gender2' => $requestData['brothers_gender2'],
				'brothers_grade2' => $requestData['brothers_grade2'],
				'brothers_school_no2' => $requestData['brothers_school_no2'],
				'brothers_name3' => $requestData['brothers_name3'],
				'brothers_gender3' => $requestData['brothers_gender3'],
				'brothers_grade3' => $requestData['brothers_grade3'],
				'brothers_school_no3' => $requestData['brothers_school_no3'],
				'brothers_flg' => $requestData['brothers_flg'],
				'fatherless_flg' => $requestData['fatherless_flg'],
				'school_building_id' => $requestData['school_building_id'],
				'temporary_flg' => 1,
			];
			// dd($student_data);
			Student::create($student_data);
		}


		return redirect("/shinzemi/before_student")->with("flash_message", "データが登録されました。");
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\BeforeStudent  $beforeStudent
	 * @return \Illuminate\Http\Response
	 */
	public function show(BeforeStudent $beforeStudent)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\BeforeStudent  $before_student
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$year = date('Y', strtotime('-3 month')); //4月から年度変わる
		$before_student = BeforeStudent::findOrFail($id);

		//銀行のセレクトリスト
		$banks = Bank::get();
		$banks_select_list = $banks->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['code'] . "　" . $item['name']];
		});

		//支店のセレクトリスト
		$branch_banks = BranchBank::get();
		$branch_banks_select_list = $branch_banks->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['code'] . "　" . $item['name']];
		});

		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//割引のセレクトリスト
		$discounts = Discount::get();
		$discounts_select_list = $discounts->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});

		//コースのセレクトリスト
		$highschoolcourses = HighschoolCourse::get();
		$highschoolcourses_select_list = $highschoolcourses->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['school_id'] . "　" . $item['name']];
		});

		$url = url()->previous();
		// sessionにURLを保存
		session(["url" => $url]);

		return view("before_student.edit", compact("before_student", "year", "banks_select_list", "branch_banks_select_list", "schools_select_list", "discounts_select_list", "discounts_select_list", "schooolbuildings_select_list", "highschoolcourses_select_list"));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\BeforeStudent  $beforeStudent
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{

		// $this->validate($request, [
		// 	"surname" => "required|max:40",
		// 	"name" => "required|max:40",
		// 	"surname_kana" => "required|max:40",
		// 	"name_kana" => "required|max:40",
		// 	"zip_code" => "required|max:8",
		// 	"address1" => "required",
		// 	"phone1" => "required",
		// ], [
		// 	"surname.required" => "名前の入力は必須です。",
		// 	"name.required" => "名前の入力は必須です。",
		// 	"surname_kana.required" => "名前カナの入力は必須です。",
		// 	"name_kana.required" => "名前カナの入力は必須です。",
		// 	"zip_code.required" => "郵便番号の入力は必須です。",
		// 	"zip_code.max" => "郵便番号の入力は8桁です。",
		// 	"address1.required" => "住所の入力は必須です。",
		// 	"phone1.required" => "電話番号の入力は必須です。",
		// ]);
		$requestData = $request->all();
		unset($requestData['created_at']);
		// dd($requestData);
		$before_student = BeforeStudent::findOrFail($id);
		$before_student->update($requestData);

		if ($requestData['sign_up_juku_flg'] == 1) { //入塾フラグが1なら生徒情報に登録
			$student_latest_cd = Student::withTrashed()->latest('id')->first('student_no'); //最新のstudent_no取得
			// dd($student_latest_cd);
			$student_latest_cd_int = intval($student_latest_cd['student_no']); //int型に返還
			$student_no = $student_latest_cd_int + 1; //+1する
			$student_no = strval($student_no); //文字列に変換
			$student_no = sprintf('%08d', $student_no); //8桁にする
			$request->merge(['student_no' => $student_no]); //配列に追加
			$request->validate([
				'student_no' => 'required|unique:students',
			]);
			$requestData = $request->all();
			// dd($requestData);
			$student_data = [
				'id' => 0,
				'student_no' => $requestData['student_no'],
				'surname' => $requestData['surname'],
				'name' => $requestData['name'],
				'surname_kana' => $requestData['surname_kana'],
				'name_kana' => $requestData['name_kana'],
				'gender' => $requestData['gender'],
				'zip_code' => $requestData['zip_code'],
				'address1' => $requestData['address1'],
				'birthdate' => $requestData['birthdate'],
				'address2' => $requestData['address2'],
				'phone1' => $requestData['phone1'],
				'phone2' => $requestData['phone2'],
				'email' => $requestData['email'],
				'grade' => $requestData['grade'],
				'school_id' => $requestData['school_id'],
				'parent_surname' => $requestData['parent_surname'],
				'parent_name' => $requestData['parent_name'],
				'parent_surname_kana' => $requestData['parent_surname_kana'],
				'parent_name_kana' => $requestData['parent_name_kana'],
				'brothers_name1' => $requestData['brothers_name1'],
				'brothers_gender1' => $requestData['brothers_gender1'],
				'brothers_grade1' => $requestData['brothers_grade1'],
				'brothers_school_no1' => $requestData['brothers_school_no1'],
				'brothers_name2' => $requestData['brothers_name2'],
				'brothers_gender2' => $requestData['brothers_gender2'],
				'brothers_grade2' => $requestData['brothers_grade2'],
				'brothers_school_no2' => $requestData['brothers_school_no2'],
				'brothers_name3' => $requestData['brothers_name3'],
				'brothers_gender3' => $requestData['brothers_gender3'],
				'brothers_grade3' => $requestData['brothers_grade3'],
				'brothers_school_no3' => $requestData['brothers_school_no3'],
				'brothers_flg' => $requestData['brothers_flg'],
				'fatherless_flg' => $requestData['fatherless_flg'],
				'school_building_id' => $requestData['school_building_id'],
				'temporary_flg' => 1,
			];
			// dd($student_data);
			Student::create($student_data);
		}

		// get session url
		$url = session("url");
		session()->forget("url");

		if (strpos($url, "before_student") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/before_student")->with("flash_message", "データが更新されました。");
			// return redirect("/shinzemi/before_student/$id/edit/")->with("flash_message", "データが登録されました。");
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\BeforeStudent  $beforeStudent
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		BeforeStudent::destroy($id);


		return redirect("/shinzemi/before_student")->with("flash_message", "データが削除されました。");
	}

	public function before_student_info_output(Request $request)
	{
		$query = BeforeStudent::query();

		if ($request->has('student_check')) { //student_checkは生徒No
			$query->whereIn('before_students.before_student_no', $request->student_check);
		}
		$before_students = $query->get();
		// スプレッドシート作成
		$spreadsheet = new Spreadsheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('before_student');

		// 値セット位置
		$student_position = 1;
		$student_horizontal = 1;

		//見出し
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '管理No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '生徒No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '生徒氏名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '生徒氏名カナ');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '生年月日');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '性別');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '郵便番号');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '住所1');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '住所2');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '住所3');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '電話番号1');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '電話番号2');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, 'メールアドレス');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '学校');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '現在学年');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '保護者氏名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '保護者氏名カナ');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹1　名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹1　性別');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹1　学年');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹1　学校No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹2　名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹2　性別');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹2　学年');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹2　学校No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹3　名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹3　性別');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹3　学年');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹3　学校No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹在塾');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, 'ひとり親家庭');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '校舎No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '問い合わせ日(電話)');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '入塾説明');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '問い合わせ日(来塾)');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '入塾テスト');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '問合せ(資料請求)');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '特別体験');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '入塾フラグ');


		foreach ($before_students as $key => $before_student) {
			// 値リセット
			if ($key = 0) {
				$student_position = 2;
				$student_horizontal = 1;
			} else {
				$student_position = $student_position + 1;
				$student_horizontal = 1;

				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->id);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->before_student_no);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->surname . $before_student->name);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->surname_kana . $before_student->name_kana);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->birthdate);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, config('const.gender')[$before_student->gender]);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->zip_code);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->address1);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->address2);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->address3);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->phone1);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->phone2);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->email);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->school->name ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, config('const.school_year')[$before_student->grade]);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->parent_surname . $before_student->parent_name);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->parent_surname_kana . $before_student->parent_name_kana);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->brothers_name1);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, config('const.gender')[$before_student->brothers_gender1] ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, config('const.school_year')[$before_student->brothers_grade1] ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->brothers_school_no1_school->name ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->brothers_name2);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, config('const.gender')[$before_student->brothers_gender2] ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, config('const.school_year')[$before_student->brothers_grade2] ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->brothers_school_no2_school->name ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->brothers_name3);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, config('const.gender')[$before_student->brothers_gender3] ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, config('const.school_year')[$before_student->brothers_grade3] ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->brothers_school_no3_school->name ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->brothers_flg);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->fatherless_flg);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->schoolbuilding->name ?? "");

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->contact_tel_date);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->description_juku_date);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->coming_juku_date);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->juku_test_date);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->document_request_date);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $before_student->special_experience_date);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, config('const.sign_up_juku_methods')[$before_student->sign_up_juku_flg] ?? "");
			}
		}
		$filename = 'before_student.xlsx';
		ob_end_clean(); // this
		ob_start(); // and this
		// ダウンロード
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}
}
