<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '256M');

use App\StudentKarte;
use Illuminate\Http\Request;

use App\Student;
use App\School;
use App\SchoolBuilding;
use App\ResultCategory;
use App\Subject;
use App\Implementation;
use App\StudentResult;
use App\StudentRatingPoint;
use App\BeforeStudent;
use App\Bank;
use App\BranchBank;
use App\Discount;
use App\HighschoolCourse;
use App\Product;
use App\Year;
use App\AveragePoint;

class StudentKarteController extends Controller
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
		$query = Student::query();
		$perPage = 25;

		//検索の値取得
		$student_search['id_start'] = $request->get("id_start");
		$student_search['id_end'] = $request->get("id_end");

		$student_search['student_no_start'] = $request->get("student_no_start");
		$student_search['student_no_end'] = $request->get("student_no_end");

		$student_search['surname'] = $request->get("surname");
		$student_search['name'] = $request->get("name");
		$student_search['surname_kana'] = $request->get("surname_kana");
		$student_search['name_kana'] = $request->get("name_kana");
		$student_search['phone'] = $request->get("phone");
		$student_search['school_id'] = $request->get("school_id");
		$student_search['brothers_flg'] = $request->get("brothers_flg");
		$student_search['fatherless_flg'] = $request->get("fatherless_flg");
		$student_search['rest_flg'] = $request->get("rest_flg");
		$student_search['graduation_flg'] = $request->get("graduation_flg");
		$student_search['withdrawal_flg'] = $request->get("withdrawal_flg");
		$student_search['temporary_flg'] = $request->get("temporary_flg");
		$student_search['grade_start'] = $request->get("grade_start");
		$student_search['grade_end'] = $request->get("grade_end");
		$student_search['school_building_id'] = $request->get("school_building_id");
		$student_search['product_select'] = $request->get("product_select");
		$student_search['discount_select'] = $request->get("discount_select");
		$student_search['juku_start_date'] = $request->get("juku_start_date");
		$student_search['juku_end_date'] = $request->get("juku_end_date");
		$student_search['juku_graduation_start_date'] = $request->get("juku_graduation_start_date");
		$student_search['juku_graduation_end_date'] = $request->get("juku_graduation_end_date");
		$student_search['juku_return_start_date'] = $request->get("juku_return_start_date");
		$student_search['juku_return_end_date'] = $request->get("juku_return_end_date");
		$student_search['juku_withdrawal_start_date'] = $request->get("juku_withdrawal_start_date");
		$student_search['juku_withdrawal_end_date'] = $request->get("juku_withdrawal_end_date");
		$student_search['juku_rest_start_date'] = $request->get("juku_rest_start_date");
		$student_search['juku_rest_end_date'] = $request->get("juku_rest_end_date");

		// 検索するテキストが入力されている場合のみ
		if (!empty($student_search['id_start']) && empty($student_search['id_end'])) {
			$query->where('students.id', $student_search['id_start']);
		}
		if (!empty($student_search['id_end']) && empty($student_search['id_start'])) {
			$query->where('students.id', $student_search['id_end']);
		}
		if (!empty($student_search['id_start']) && !empty($student_search['id_end'])) {
			$query->whereBetween('students.id', [$student_search['id_start'], $student_search['id_end']]);
		}

		if (!empty($student_search['student_no_start']) && empty($student_search['student_no_end'])) {
			$query->where('students.student_no', $student_search['student_no_start']);
		}
		if (!empty($student_search['student_no_end']) && empty($student_search['student_no_start'])) {
			$query->where('students.student_no', $student_search['student_no_end']);
		}
		if (!empty($student_search['student_no_start']) && !empty($student_search['student_no_end'])) {
			$query->whereBetween('students.student_no', [$student_search['student_no_start'], $student_search['student_no_end']]);
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
		if (!empty($student_search['rest_flg'])) {
			$query->WhereNotNull('juku_rest_date');
		}
		if (!empty($student_search['graduation_flg'])) {
			$query->WhereNotNull('juku_graduation_date');
		}
		if (!empty($student_search['withdrawal_flg'])) {
			$query->whereNotNull('juku_withdrawal_date');
		}
		if (!empty($student_search['temporary_flg'])) {
			$query->where('temporary_flg', $student_search['temporary_flg']);
		}
		if (!empty($student_search['grade_start']) && empty($student_search['grade_end'])) {
			$query->where('grade', $student_search['grade_start']);
		}
		if (!empty($student_search['grade_end']) && empty($student_search['grade_start'])) {
			$query->where('grade', $student_search['grade_end']);
		}
		if (!empty($student_search['grade_start']) && !empty($student_search['grade_end'])) {
			$student_search['grade_start'] = intval($student_search['grade_start']);
			$student_search['grade_end'] = intval($student_search['grade_end']);
			$query->WhereBetween('grade', [$student_search['grade_start'], $student_search['grade_end']]);
		}
		if (!empty($student_search['school_building_id'])) {
			$query->where('school_building_id', $student_search['school_building_id']);
		}
		if (!empty($student_search['product_select'])) {
			$query->join('juko_infos', 'juko_infos.student_no', '=', 'students.student_no')
				->where('juko_infos.product_id', $student_search['product_select']);
		}
		if (!empty($student_search['discount_select'])) {
			$query->where('discount_id', $student_search['discount_select']);
		}
		//入塾日
		if (!empty($student_search['juku_start_date']) && empty($student_search['juku_end_date'])) {
			return redirect("/shinzemi/student_karte")->with("message", "※入塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_end_date']) && empty($student_search['juku_start_date'])) {
			return redirect("/shinzemi/student_karte")->with("message", "※入塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_start_date']) && !empty($student_search['juku_end_date'])) {
			$query->whereBetween('juku_start_date', [$student_search['juku_start_date'], $student_search['juku_end_date']]);
		}
		//卒塾日
		if (!empty($student_search['juku_graduation_start_date']) && empty($student_search['juku_graduation_end_date'])) {
			return redirect("/shinzemi/student_karte")->with("message", "※卒塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_graduation_end_date']) && empty($student_search['juku_graduation_start_date'])) {
			return redirect("/shinzemi/student_karte")->with("message", "※卒塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_graduation_start_date']) && !empty($student_search['juku_graduation_end_date'])) {
			$student_search['graduation_flg'] = 1;
			$query->whereBetween('juku_graduation_date', [$student_search['juku_graduation_start_date'], $student_search['juku_graduation_end_date']]);
		}

		//復塾日
		if (!empty($student_search['juku_return_start_date']) && empty($student_search['juku_return_end_date'])) {
			return redirect("/shinzemi/student_karte")->with("message", "※復塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_return_end_date']) && empty($student_search['juku_return_start_date'])) {
			return redirect("/shinzemi/student_karte")->with("message", "※復塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_return_start_date']) && !empty($student_search['juku_return_end_date'])) {
			$query->whereBetween('juku_return_date', [$student_search['juku_return_start_date'], $student_search['juku_return_end_date']]);
		}

		//退塾日
		if (!empty($student_search['juku_withdrawal_start_date']) && empty($student_search['juku_withdrawal_end_date'])) {
			return redirect("/shinzemi/student_karte")->with("message", "※退塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_withdrawal_end_date']) && empty($student_search['juku_withdrawal_start_date'])) {
			return redirect("/shinzemi/student_karte")->with("message", "※退塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_withdrawal_start_date']) && !empty($student_search['juku_withdrawal_end_date'])) {
			$student_search['withdrawal_flg'] = 1;
			$query->whereBetween('juku_withdrawal_date', [$student_search['juku_withdrawal_start_date'], $student_search['juku_withdrawal_end_date']]);
		}

		//休塾日
		if (!empty($student_search['juku_rest_start_date']) && empty($student_search['juku_rest_end_date'])) {
			return redirect("/shinzemi/average_point")->with("message", "※休塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_rest_end_date']) && empty($student_search['juku_rest_start_date'])) {
			return redirect("/shinzemi/average_point")->with("message", "※休塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_rest_start_date']) && !empty($student_search['juku_rest_end_date'])) {
			$student_search['rest_flg'] = 1;
			$query->whereBetween('juku_rest_date', [$student_search['juku_rest_start_date'], $student_search['juku_rest_end_date']]);
		}


		if ($student_search['withdrawal_flg'] == 1) { //卒塾チェック
			$query->whereNotNull('juku_withdrawal_date');
		}
		if ($student_search['graduation_flg'] == 1) { //退塾チェック
			$query->whereNotNull('juku_graduation_date');
		}
		if ($student_search['rest_flg'] == 1) {
			$query->whereNotNull('juku_rest_date')->whereNull('juku_graduation_date')->whereNull('juku_withdrawal_date');
		}
		if (empty($student_search['withdrawal_flg']) && empty($student_search['graduation_flg']) && empty($student_search['rest_flg'])) { //在塾生徒
			$query->whereNull('juku_rest_date')->whereNull('juku_graduation_date')->whereNull('juku_withdrawal_date');
		}

		$student = $query->paginate($perPage);

		return view("student_karte.index", compact("student", "student_search", "schools_select_list", "schooolbuildings_select_list", "products_select_list", "discounts_select_list"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\StudentKarute  $studentKarute
	 * @return \Illuminate\Http\Response
	 */
	public function show(StudentKarute $studentKarute)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\StudentKarute  $studentKarute
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id)
	{
		//年度のセレクトリスト
		$averagepoints = AveragePoint::get();
		// dd($request);
		//検索の値取得
		$search['school_year'] = $request->get("school_year");
		$search['year'] = $request->get("year");
		// echo $search['year'];
		//生徒情報取得
		$target_student = Student::where('student_no', $id)->firstOrFail();
		//取得した生徒情報に紐づく学校情報の取得
		$target_school = School::findOrFail($target_student->school_id);

		//年度の計算
		if (!$search['year']) {
			$now_grade = $target_student->grade; //現在の学年
			if (!empty($search['school_year'])) {
				$select_grade = $search['school_year']; //セレクトした学年
			} else {
				$select_grade = $now_grade;
			}
			$difference_grade = $now_grade - $select_grade; //学年の差
			$now_year = Year::first(); //１レコードしかないはず
			$select_year = $now_year['year'] - $difference_grade;
		} else {
			$select_year = $search['year'];
			$select_grade = $search['school_year'];
		}


		//$schoolの学校区分と公立/私立区分で成績カテゴリー取得
		if ($select_grade >= 10) {
			$target_resultcategory = ResultCategory::where('junior_high_school_student_display_flg', 1)->get();

			//中1、中2のとき「学力診断テスト/実力テスト」を非表示にする
			if($select_grade < 12){
				$target_resultcategory = $target_resultcategory->where('id', '<>', 4);
			}

		} else {
			$target_resultcategory = ResultCategory::where('elementary_school_student_display_flg', 1)->get();
		}

		//生徒の成績取得
		$student_result = StudentResult::where('student_no', $id)->where('year', $select_year)->where('grade', $search['school_year'] ?? $target_student->grade)->get();
		// dd($student_result);
		if (!$student_result->isEmpty()) {
			foreach ($student_result as $key => $value) {
				$target_student_point[$value->result_category_id][$value->implementation_no][$value->subject_no] = $value->point;
			}
		} else {
			$target_student_point = "";
		}

		//対象の学校の平均点取得
		$average_point = AveragePoint::where('school_id', $target_school->id)->where('year', $select_year)->where('grade', $search['school_year'] ?? $target_student->grade)->get();
		// dd($average_point);
		if (!$average_point->isEmpty()) {
			foreach ($average_point as $key => $value) {
				$target_average_point[$value->result_category_id][$value->implementation_no][$value->subject_no] = $value->average_point;
			}
		} else {
			$target_average_point = "";
		}

		//生徒のの評定取得
		$rating_point = StudentRatingPoint::where('student_no', $id)->where('year', $select_year)->where('grade', $search['school_year'] ?? $target_student->grade)->get();
		if (!$rating_point->isEmpty()) {
			foreach ($rating_point as $key => $value) {
				$target_rating_point[$value->result_category_id][$value->implementation_no][$value->subject_no] = $value->rating_point;
			}
		} else {
			$target_rating_point = "";
		}


		// return view("average_point.edit", compact("target_student", "target_resultcategory", "target_student_point", "target_average_point", "target_rating_point"));

		return view("student_karte.edit", compact("target_student", "select_year", "search", "target_resultcategory", "target_student_point", "target_average_point", "target_rating_point", "select_grade"));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\StudentKarute  $studentKarute
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, StudentKarute $studentKarute)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\StudentKarute  $studentKarute
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(StudentKarute $studentKarute)
	{
		//
	}
}
