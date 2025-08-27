<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '256M');
ini_set('max_execution_time', 120);
// ini_set('display_errors', '1');


use App\AveragePoint;
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

//グラフ
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Legend;

class AveragePointsController extends Controller
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

		// ユーザーが送信した年度の値をセッションに保存
		if ($request->has('year')) {
			$selectedYear = $request->input('year');
			session(['selected_year' => $selectedYear]);
		}

		//年度の計算
		$year = Year::where('id', 1)->first();
		$now_year = $year->year;

		//検索の処理
		$query = Student::query();

		//Implementationsテーブルのデータを取得
		$implementations = Implementation::get();

		//Implementationsテーブルのimplementation_nameカラムの値を取得
		$implementation_name = $implementations->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['implementation_name']];
		});

		//$implementation_nameの値を取得
		$implementation_name = $implementation_name->toArray();

		$perPage = 25;

		//Subjectsテーブルでresult_category_id毎のレコード数をを取得
		$subject_count = Subject::select('result_category_id', \DB::raw('count(*) as count'))
			->groupBy('result_category_id')
			->get();

		//result_category_idが1のときのcountを取得
		$subject_count_1 = $subject_count->where('result_category_id', 1)->pluck('count')->first();

		//result_category_idが2のときのcountを取得
		$subject_count_2 = $subject_count->where('result_category_id', 2)->pluck('count')->first();

		//result_category_idが3のときのcountを取得
		$subject_count_3 = $subject_count->where('result_category_id', 3)->pluck('count')->first();

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

		//未入力者の検索値取得
		$student_search['year'] = $request->get("year");

		// //未入力者の検索値取得
		// $student_search['not_input'] = [
		// 	'grade_start' => $request->get("not_input_grade_start"),
		// 	'grade_end' => $request->get("not_input_grade_end"),
		// 	'school_building_id' => $request->get("not_input_school_building_id"),
		// ];
		// $student_search['not_input'] = array_filter($student_search['not_input'], 'strlen');

		$student_search['implementation_id_1'] = $request->get("implementation_id_1");
		$student_search['implementation_id_2'] = $request->get("implementation_id_2");
		$student_search['implementation_id_3'] = $request->get("implementation_id_3");

		if ($request->has('search')) {
			if (!empty($student_search['implementation_id_1']) || !empty($student_search['implementation_id_2']) || !empty($student_search['implementation_id_3'])) {
				if (empty($student_search['school_building_id'])) {
					return redirect("/shinzemi/average_point")->with("message", "※校舎の選択をしてください")->withInput();
				}
			}

			$selectedImplementations = [
				'implementation_id_1',
				'implementation_id_2',
				'implementation_id_3',
			];

			$hasSelectedImplementations = array_filter($selectedImplementations, function ($key) use ($student_search) {
				return !empty($student_search[$key]);
			});

			if (count($hasSelectedImplementations) >= 2 && !empty($student_search['school_building_id'])) {
				return redirect("/shinzemi/average_point")->with("message", "※成績カテゴリーは1つに絞り込んでください")->withInput();
			}
		}

		$result_category_array = [];

		if (!empty($student_search['implementation_id_1'])) {
			$implementation_count1 = count($student_search['implementation_id_1']);
			$max_subject_count_1 = $subject_count_1 * $implementation_count1;
			$result_category_array = array_merge($result_category_array, ['1']);
		}

		if (!empty($student_search['implementation_id_2'])) {
			$implementation_count2 = count($student_search['implementation_id_2']);
			$max_subject_count_2 = $subject_count_2 * $implementation_count2;
			$result_category_array = array_merge($result_category_array, ['2']);
		}

		if (!empty($student_search['implementation_id_3'])) {
			$implementation_count3 = count($student_search['implementation_id_3']);
			$max_subject_count_3 = $subject_count_3 * $implementation_count3;
			$result_category_array = array_merge($result_category_array, ['3']);
		}

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
			return redirect("/shinzemi/average_point")->with("message", "※入塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_end_date']) && empty($student_search['juku_start_date'])) {
			return redirect("/shinzemi/average_point")->with("message", "※入塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_start_date']) && !empty($student_search['juku_end_date'])) {
			$query->whereBetween('juku_start_date', [$student_search['juku_start_date'], $student_search['juku_end_date']]);
		}
		//卒塾日
		if (!empty($student_search['juku_graduation_start_date']) && empty($student_search['juku_graduation_end_date'])) {
			return redirect("/shinzemi/average_point")->with("message", "※卒塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_graduation_end_date']) && empty($student_search['juku_graduation_start_date'])) {
			return redirect("/shinzemi/average_point")->with("message", "※卒塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_graduation_start_date']) && !empty($student_search['juku_graduation_end_date'])) {
			$student_search['graduation_flg'] = 1;
			$query->whereBetween('juku_graduation_date', [$student_search['juku_graduation_start_date'], $student_search['juku_graduation_end_date']]);
		}

		//復塾日
		if (!empty($student_search['juku_return_start_date']) && empty($student_search['juku_return_end_date'])) {
			return redirect("/shinzemi/average_point")->with("message", "※復塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_return_end_date']) && empty($student_search['juku_return_start_date'])) {
			return redirect("/shinzemi/average_point")->with("message", "※復塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_return_start_date']) && !empty($student_search['juku_return_end_date'])) {
			$query->whereBetween('juku_return_date', [$student_search['juku_return_start_date'], $student_search['juku_return_end_date']]);
		}

		//退塾日
		if (!empty($student_search['juku_withdrawal_start_date']) && empty($student_search['juku_withdrawal_end_date'])) {
			return redirect("/shinzemi/average_point")->with("message", "※退塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_withdrawal_end_date']) && empty($student_search['juku_withdrawal_start_date'])) {
			return redirect("/shinzemi/average_point")->with("message", "※退塾日の開始範囲を指定してください")->withInput();
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

		###################################
		#	未入力者の検索
		###################################
		if (!empty($student_search['implementation_id_1']) || !empty($student_search['implementation_id_2']) || !empty($student_search['implementation_id_3'])) {

			// if(!empty($student_search['not_input']['grade_start']) && empty($student_search['not_input']['grade_end'])){
			// 	$query->where('grade', $student_search['not_input']['grade_start']);
			// }

			// if(empty($student_search['not_input']['grade_start']) && !empty($student_search['not_input']['grade_end'])){
			// 	$query->where('grade', $student_search['not_input']['grade_end']);
			// }

			// if(!empty($student_search['not_input']['grade_start']) && !empty($student_search['not_input']['grade_end'])){
			// 	$student_search['not_input']['grade_start'] = intval($student_search['not_input']['grade_start']);
			// 	$student_search['not_input']['grade_end'] = intval($student_search['not_input']['grade_end']);
			// 	$query->WhereBetween('grade', [$student_search['not_input']['grade_start'], $student_search['not_input']['grade_end']]);
			// }

			// if(!empty($student_search['not_input']['school_building_id'])){
			// 	$query->where('school_building_id', $student_search['not_input']['school_building_id']);
			// }

			//Studentテーブルのstudent_noを取得
			$student_result_all = $query->pluck('student_no')->toArray();
			$student_no_all = array_values(array_unique($student_result_all));

			unset($student_result_all);

			if (!empty($student_search['implementation_id_1'])) {
				$implementationIds1 = array_values($student_search['implementation_id_1']);
			}
			if (!empty($student_search['implementation_id_2'])) {
				$implementationIds2 = array_values($student_search['implementation_id_2']);
			}

			if (!empty($student_search['implementation_id_3'])) {
				$implementationIds3 = array_values($student_search['implementation_id_3']);
			}

			$student_result_query = StudentResult::where('year', $student_search['year'])->whereIn('student_no', $student_no_all);

			$student_result_query->select('student_no', 'year', 'implementation_no', 'result_category_id', 'year');

			$student_result_query->when(!empty($student_search['implementation_id_1']), function ($student_result_query) use ($student_search) {
				$student_result_query->where('year', $student_search['year'])->where('grade', '>', 9)->orwhere('result_category_id', 1);

				$implementationIds1 = array_values($student_search['implementation_id_1']);
				$student_result_query->whereIn('implementation_no', $implementationIds1);
			})->when(!empty($student_search['implementation_id_2']), function ($student_result_query) use ($student_search) {
				$student_result_query->where('year', $student_search['year'])->where('grade', '>', 9)->orwhere('result_category_id', 2);

				$implementationIds2 = array_values($student_search['implementation_id_2']);
				$student_result_query->whereIn('implementation_no', $implementationIds2);
			})->when(!empty($student_search['implementation_id_3']), function ($student_result_query) use ($student_search) {
				$student_result_query->where('year', $student_search['year'])->where('grade', '>', 9)->orwhere('result_category_id', 3);

				$implementationIds3 = array_values($student_search['implementation_id_3']);
				$student_result_query->whereIn('implementation_no', $implementationIds3);
			});

			$student_result = $student_result_query->where('year', $student_search['year'])->whereIn('student_no', $student_no_all)->get();

			$student_no_array = $student_result->pluck('student_no')->unique()->toArray();
			$student_no_array = array_values($student_no_array);

			$exclusion_student_result = array_diff($student_no_all, $student_no_array);


			unset($student_no_all);

			$filtered_student_result1 = [];
			$filtered_student_result2 = [];
			$filtered_student_result3 = [];

			if (!empty($student_search['implementation_id_1'])) {
				foreach ($student_no_array as $studentno) {
					$student_result_count = $student_result->where('result_category_id', 1)->where('year', $student_search['year'])
						->whereIn('implementation_no', $implementationIds1)->whereIn('student_no', $studentno)->count();
					if ($student_result_count < $max_subject_count_1) {
						$filtered_student_result1 = array_merge($filtered_student_result1, [$studentno]);
					}
					unset($student_result_count);
				}
			}

			if (!empty($student_search['implementation_id_2'])) {
				foreach ($student_no_array as $studentno) {
					$student_result_count = $student_result->where('result_category_id', 2)->where('year', $student_search['year'])
						->whereIn('implementation_no', $implementationIds2)->whereIn('student_no', $studentno)->count();
					if ($student_result_count < $max_subject_count_2) {
						$filtered_student_result2 = array_merge($filtered_student_result2, [$studentno]);
					}
					unset($student_result_count);
				}
			}

			if (!empty($student_search['implementation_id_3'])) {
				foreach ($student_no_array as $studentno) {
					$student_result_count = $student_result->where('result_category_id', 3)->where('year', $student_search['year'])
						->whereIn('implementation_no', $implementationIds3)->whereIn('student_no', $studentno)->count();
					if ($student_result_count < $max_subject_count_3) {
						$filtered_student_result3 = array_merge($filtered_student_result3, [$studentno]);
					}
					unset($student_result_count);
				}
			}

			unset($student_no_array);
			unset($student_result);

			//$filtered_student_resultのstudent_noだけ取り出す
			$filtered_student_result = array_merge($filtered_student_result1, $filtered_student_result2, $filtered_student_result3); //未入力がある生徒のstudent_no

			$exclusion_student_result = array_values(array_unique($exclusion_student_result));
			$student_result_no = array_values(array_unique(array_merge($filtered_student_result, $exclusion_student_result)));

			$student = $query->whereIn('student_no', $student_result_no)->paginate($perPage);
		} else {
			$student = $query->paginate($perPage);
		}

		return view("average_point.index", compact("student", "student_search", "schools_select_list", "schooolbuildings_select_list", "products_select_list", "discounts_select_list", "now_year"));
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

		// $year = date('Y', strtotime('-3 month')); //4月から年度変わる
		$requestData = $request->all();
		// dd($requestData);
		//連想配列のキーを取得(成績カテゴリーの)
		$resultkeys = array_keys($requestData['point']);
		// dd($resultkeys);
		for ($i = 0; $i < count($resultkeys); $i++) { //成績カテゴリーの数だけ回る
			//連想配列のキーを取得(実施回の)   例：第1回、第2回、1月、2月
			$implementationkeys[$i] = array_keys($requestData['point'][$resultkeys[$i]]);
			for ($j = 0; $j < count($implementationkeys[$i]); $j++) { //試験の数だけ回る
				//連想配列のキーを取得(教科の)
				$subjectkeys[$j] = array_keys($requestData['point'][$resultkeys[$i]][$implementationkeys[$i][$j]]);
				for ($k = 0; $k < count($subjectkeys[$j]); $k++) { //教科の数だけ回る
					//student_results　生徒の成績情報　生徒と1対多で
					if (isset($requestData['point'][$resultkeys[$i]][$implementationkeys[$i][$j]][$subjectkeys[$j][$k]][0])) {
						$student_point_Data = [
							'student_id' => $requestData['student_id'],
							'student_no' => $requestData['student_no'],
							'grade' => $requestData['school_year'],
							'year' => $requestData['year'],
							'result_category_id' => $resultkeys[$i],
							'implementation_no' => $implementationkeys[$i][$j],
							'subject_no' => $subjectkeys[$j][$k],
							'point' => $requestData['point'][$resultkeys[$i]][$implementationkeys[$i][$j]][$subjectkeys[$j][$k]][0],
						];
						// dd($student_point_Data);
						//生徒成績の登録処理
						//過去のデータ削除
						StudentResult::where('student_no', $requestData['student_no'])->where('grade', $requestData['school_year'])->where('year', $requestData['year'])->where('result_category_id', $resultkeys[$i])->where('implementation_no', $implementationkeys[$i][$j])->where('subject_no', $subjectkeys[$j][$k])->delete();
						//新しくデータ挿入
						StudentResult::create($student_point_Data);
					}

					if (isset($requestData['average_point'][$resultkeys[$i]][$implementationkeys[$i][$j]][$subjectkeys[$j][$k]][0])) { //平均点がない場合のエラー回避
						//AveragePoint　平均点がある場合　平均点テーブル更新 平均点は学校と1対多で
						$average_point_Data = [
							'year' => $requestData['year'],
							'school_id' => $requestData['school_id'],
							'grade' => $requestData['school_year'],
							'result_category_id' => $resultkeys[$i],
							'implementation_no' => $implementationkeys[$i][$j],
							'subject_no' => $subjectkeys[$j][$k],
							'average_point' => $requestData['average_point'][$resultkeys[$i]][$implementationkeys[$i][$j]][$subjectkeys[$j][$k]][0],
						];
						// dd($average_point_Data);
						//平均点の登録処理
						//過去のデータ削除
						AveragePoint::where('year', $requestData['year'])->where('school_id', $requestData['school_id'])->where('grade', $requestData['school_year'])->where('result_category_id', $resultkeys[$i])->where('implementation_no', $implementationkeys[$i][$j])->where('subject_no', $subjectkeys[$j][$k])->delete();
						//新しくデータ挿入
						AveragePoint::create($average_point_Data);
					}

					if (isset($requestData['rating_point'][$resultkeys[$i]][$implementationkeys[$i][$j]][$subjectkeys[$j][$k]][0])) { //平均点がない場合のエラー回避
						//StudentRatingPoint　評定がある場合　評定テーブル更新 評定は生徒と1対多で
						$rating_point_Data = [
							'student_id' => $requestData['student_id'],
							'student_no' => $requestData['student_no'],
							'grade' => $requestData['school_year'],
							'year' => $requestData['year'],
							'result_category_id' => $resultkeys[$i],
							'implementation_no' => $implementationkeys[$i][$j],
							'subject_no' => $subjectkeys[$j][$k],
							'rating_point' => $requestData['rating_point'][$resultkeys[$i]][$implementationkeys[$i][$j]][$subjectkeys[$j][$k]][0],
						];
						//平均点の登録処理
						//過去のデータ削除
						StudentRatingPoint::where('year', $requestData['year'])->where('student_id', $requestData['student_id'])->where('grade', $requestData['school_year'])->where('result_category_id', $resultkeys[$i])->where('implementation_no', $implementationkeys[$i][$j])->where('subject_no', $subjectkeys[$j][$k])->delete();
						//新しくデータ挿入
						StudentRatingPoint::create($rating_point_Data);
					}
				}
			}
		}
		// get session url
		$url = session("url");
		session()->forget("url");

		if (strpos($url, "average_point") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/average_point")->with("flash_message", "データが更新されました。");
		}
		// return redirect("/shinzemi/average_point")->with("flash_message", "データが登録されました。");
		// return redirect("/shinzemi/average_point/" . $request->student_no . "/edit")->with("flash_message", "データが登録されました。");
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\AveragePoints  $AveragePoints
	 * @return \Illuminate\Http\Response
	 */
	public function show(AveragePoints $AveragePoints)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\AveragePoints  $AveragePoints
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
		if (!empty($target_student->school_id)) {
			$target_school = School::findOrFail($target_student->school_id);
		} else {
			// get session url
			$url = session("url");
			session()->forget("url");
			if (strpos($url, "average_point") !== false) {
				return redirect($url)->with("message", "学校の登録がありません");
			} else {
				return redirect("/shinzemi/average_point")->with("message", "学校の登録がありません");
			}
		}

		//年度の計算
		$now_grade = $target_student->grade; //現在の学年
		if (!empty($search['school_year'])) {
			$select_grade = $search['school_year']; //セレクトした学年
		} else {
			$select_grade = $now_grade;
		}
		$difference_grade = $now_grade - $select_grade; //学年の差
		$now_year = Year::first(); //１レコードしかないはず

		// dd($now_year);
		// $now_year = date('Y', strtotime('-3 month')); //現在の年度
		// $select_year = date('Y', strtotime('-' . $difference_grade . ' year')); //学年の差　年度を引く
		$select_year = $now_year['year'] - $difference_grade;

		//$schoolの学校区分と公立/私立区分で成績カテゴリー取得
		if ($select_grade >= 10) {
			$target_resultcategory = ResultCategory::where('junior_high_school_student_display_flg', 1)->get();

			//中1、中2のとき「学力診断テスト/実力テスト」「藤井模試」「五ツ木模試」「公立入試」「Vもし」を非表示にする
			if ($select_grade < 12) {
				$target_resultcategory = $target_resultcategory->where('id', '<>', 4)->where('id', '<>', 5)->where('id', '<>', 6)->where('id', '<>', 7)->where('id', '<>', 8);
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
		$url = url()->previous();
		// sessionにURLを保存
		session(["url" => $url]);

		return view("average_point.edit", compact("target_student", "select_year", "search", "target_resultcategory", "target_student_point", "target_average_point", "target_rating_point", "select_grade"));
		// $request->offsetUnset('division');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\AveragePoints  $AveragePoints
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{
		// $requestData = $request->all();
		// dd($requestData);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\AveragePoints  $AveragePoints
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(AveragePoints $AveragePoints)
	{
		//
	}

	/**
	 * 小学生成績出力処理
	 *
	 * @param [type] $id
	 * @return void
	 */
	public function output_elementary_school_student_result(Request $request, $id)
	{
		//生徒情報取得
		$student = Student::where('student_no', $id)->firstOrFail();
		// dd($student);
		//校舎情報取得
		$school_building = SchoolBuilding::findOrFail($student->school_building_id);
		//学校エリア（1：大阪or2：奈良）
		$school_area = $school_building->area;
		//学校の取得
		$school = School::findOrFail($student->school_id);
		// dd($school);
		//学校名の取得
		$school_name = $school->name;

		// スプレッドシート作成
		$spreadsheet = new Spreadsheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('【小1～3】');

		// 値とセルを指定
		/**
		 * 見出し
		 */
		if ($school_area == 1) {
			$sheet->setCellValue('A1', '大阪');
			$sheet->setCellValue('F1', '校舎');
		} elseif ($school_area == 2) {
			$sheet->setCellValue('A1', '奈良');
			$sheet->setCellValue('F1', '校舎');
		} else {
			$sheet->setCellValue('A1', '');
			$sheet->setCellValue('F1', '校舎');
		}
		$sheet->getStyle('A1')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('F1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('I1', '小学部　成績カルテ');
		$sheet->getStyle('I1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('X1', '進学ゼミナール');
		$sheet->getStyle('X1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('AG1',  $school_building->name);
		$sheet->getStyle('AG1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('A2', '小学');
		$sheet->getStyle('A2')->getFont()->setSize(20)->setBold(true);



		if ($student->grade == 4) {
			$sheet->setCellValue('D2', '1');
		} elseif ($student->grade == 5) {
			$sheet->setCellValue('D2', '2');
		} elseif ($student->grade == 6) {
			$sheet->setCellValue('D2', '3');
		} elseif ($student->grade == 7) {
			$sheet->setCellValue('D2', '4');
		} elseif ($student->grade == 8) {
			$sheet->setCellValue('D2', '5');
		} elseif ($student->grade == 9) {
			$sheet->setCellValue('D2', '6');
		} else {
			$sheet->setCellValue('D2', '');
		}
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('G2', '年');
		$sheet->getStyle('G2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('J2',  $school->name);
		$sheet->getStyle('J2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('U2', '氏名');
		$sheet->getStyle('U2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('X2', $student->surname . $student->name);
		$sheet->getStyle('X2')->getFont()->setSize(20)->setBold(true);

		$sheet->getStyle('A4')->getFont()->setSize(16)->setBold(true);




		// //成績カテゴリーの取得 塾内テストの取得 塾内テストid=3
		$resultcategorys = ResultCategory::where('id', 3)->firstOrFail();
		//実施回の取得
		$implementations = Implementation::where('result_category_id', $resultcategorys->id)->get();
		//教科の取得
		$subjects = Subject::where('result_category_id', $resultcategorys->id)->get();


		//値セット位置
		$grade_position = 5; //学年表示位置
		$implementation_position = 5; //実施回の値位置

		$header_position = 5; //見出しや項目の表示の位置

		$subject_position = 5; //教科の値位置
		$subject_horizontal = 11; //教科の横移動用　K=11

		$point_position = 6; //点数の値位置
		$point_horizontal = 11; //点数の横移動用　K=11

		//平均偏差値位置
		$average_position = 5;
		$subject_get = 5;
		$subject_get_horizontal = 11;

		// 学年の数回る
		for ($i = 0; $i < 3; $i++) {
			if ($i == 0) {
				$sheet->setCellValue('A' . $grade_position, "小学1年生");
			} else if ($i == 1) {
				$sheet->setCellValue('A' . $grade_position, "小学2年生");
			} else if ($i == 2) {
				$sheet->setCellValue('A' . $grade_position, "小学3年生");
			}
			//試験の値セット
			foreach ($implementations as $implementationkey => $implementation) {
				$max_col = 0; //右端用変数
				$max_row = 0; //最後の行変数

				$sheet->setCellValue('E' . $implementation_position, $implementation->implementation_name);
				$sheet->mergeCells('E' . $implementation_position . ':G' . $implementation_position + 2);

				$sheet->setCellValue('H' . $header_position, "教科");
				$sheet->mergeCells('H' . $header_position . ':J' . $header_position);

				$sheet->setCellValue('H' . $header_position + 1, "偏差値");
				$sheet->mergeCells('H' . $header_position + 1 . ':J' . $header_position + 2);

				$sheet->setCellValue('A4', '塾内テスト推移');
				$sheet->getStyle('A4')->getFont()->setSize(16)->setBold(true);


				//教科値セット
				foreach ($subjects as $subjectkey => $subject) {
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //K指定する
					if ($subject->subject_name == '数学') {
						$sheet->setCellValue($column_subject . $subject_position, "算数");
					} elseif ($subject->subject_name == '2科/3科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "2科");
					} elseif ($subject->subject_name == '3科/5科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "3科");
					} else {
						$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
					}
					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$subject_horizontal = $subject_horizontal + 3;


					//生徒成績取得
					$studentresults = StudentResult::where('student_no', $student->student_no)->where('grade', 4 + $i)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
					if ($studentresults->isNotEmpty()) {
						foreach ($studentresults as $resultkey => $studentresult) {
							$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
							$sheet->setCellValue($column_point . $point_position,  $studentresult->point);

							$sheet->getStyle($column_point . $point_position)->getFill()->setFillType('solid')->getStartColor()->setARGB('palegreen');

							$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
							$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
							$point_horizontal = $point_horizontal + 3;
						}
					} else {
						$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
						$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
						$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
						$point_horizontal = $point_horizontal + 3;
					}
				}
				$max_col = $subject_horizontal - 1; //右端用変数
				$max_col = Coordinate::stringFromColumnIndex($max_col);
				$max_row = $point_position; //一番最後の値セット位置
				$subject_max_row = $point_position; //一番最後の値セット位置

				$average_horizontal1 = Coordinate::stringFromColumnIndex($point_horizontal + 1); //平均偏差値　横移動用
				$average_horizontal2 = Coordinate::stringFromColumnIndex($point_horizontal + 4); //平均偏差値　横移動用
				$merge_average_horizontal1 = Coordinate::stringFromColumnIndex($point_horizontal + 6); //平均偏差値　横移動用
				$merge_average_horizontal2 = Coordinate::stringFromColumnIndex($point_horizontal + 3); //平均偏差値　横移動用


				$implementation_position = $implementation_position + 3; //実施回の値位置変更
				$header_position = $header_position + 3; //見出しや項目の表示の位置
				$subject_position = $subject_position + 3; //教科の値位置
				$subject_horizontal = 11; //教科の横移動用リセット
				$point_position = $point_position + 3; //点数の位置変更
				$point_horizontal = 11; //点数横位置リセット


			}
			//次のブロック用の前に結合　'A' . $grade_position→ブラック開始位置　A5
			$sheet->mergeCells('A' . $grade_position . ':D' . $max_row + 1);
			//次のブロック用の前にボーダー
			$sheet->getStyle('A' . $grade_position . ':' . $max_col . $max_row + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//平均偏差値
			$sheet->setCellValue($average_horizontal1 . $average_position, "平均偏差値");
			$sheet->mergeCells($average_horizontal1 . $average_position . ':' . $merge_average_horizontal1 . $average_position + 1);
			//平均偏差値　教科1
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal1 . $average_position + 2, "国語");
			$sheet->mergeCells($average_horizontal1 . $average_position + 2 . ':' . $merge_average_horizontal2 . $average_position + 3);
			//値セット
			$sheet->setCellValue($average_horizontal1 . $average_position + 4, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "),\"\")");
			$sheet->getStyle(($average_horizontal1 . $average_position + 4))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal1 . $average_position + 4 . ':' . $merge_average_horizontal2 . $average_position + 6);



			//平均偏差値　教科2
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 6); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal2 . $average_position + 2, "算数");
			$sheet->mergeCells($average_horizontal2 . $average_position + 2 . ':' . $merge_average_horizontal1 . $average_position + 3);
			//値セット
			$sheet->setCellValue($average_horizontal2 . $average_position + 4, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "),\"\")");
			$sheet->getStyle(($average_horizontal2 . $average_position + 4))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal2 . $average_position + 4 . ':' . $merge_average_horizontal1 . $average_position + 6);




			//平均偏差値　教科3
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 15); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal1 . $average_position + 7, "2科");
			$sheet->mergeCells($average_horizontal1 . $average_position + 7 . ':' . $merge_average_horizontal2 . $average_position + 8);

			$sheet->setCellValue($average_horizontal1 . $average_position + 9, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "),\"\")");
			$sheet->getStyle(($average_horizontal1 . $average_position + 9))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal1 . $average_position + 9 . ':' . $merge_average_horizontal2 . $average_position + 11);


			//平均偏差値　教科4
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 12); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal2 . $average_position + 7, "英語");
			$sheet->mergeCells($average_horizontal2 . $average_position + 7 . ':' . $merge_average_horizontal1 . $average_position + 8);

			$sheet->setCellValue($average_horizontal2 . $average_position + 9, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "),\"\")");
			$sheet->getStyle(($average_horizontal2 . $average_position + 9))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal2 . $average_position + 9 . ':' . $merge_average_horizontal1 . $average_position + 11);


			//平均偏差値ボーダー
			$sheet->getStyle($average_horizontal1 . $average_position . ':' . $merge_average_horizontal1 . $average_position + 11)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			//次のブロック用
			$grade_position =  $header_position + 1; //学年の値セット位置＝教科偏差値の位置
			$average_position =  $header_position + 1; //平均偏差値セット位置＝教科偏差値の位置
			$subject_get = $header_position + 1;
			$point_position = $point_position + 1;
			$subject_position = $subject_position + 1;
			$header_position = $header_position + 1;
			$implementation_position = $implementation_position + 1;
		}

		$col = Coordinate::stringFromColumnIndex(1); //A はじめのセル
		$max_col = $sheet->getHighestColumn(); //右端取得
		$max_col_remainder = Coordinate::columnIndexFromString($max_col);
		$max_col_remainder = $max_col_remainder + 20; //右端から余分にとる
		$max_col_remainder = Coordinate::stringFromColumnIndex($max_col_remainder); //string型に戻す
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得

		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setVertical(Align::VERTICAL_CENTER); //上下中央寄せ
		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER); //左右中央寄せ

		//セル結合処理
		$sheet->mergeCells('A1:E1');
		$sheet->mergeCells('F1:H1');
		$sheet->mergeCells('I1:W1');
		$sheet->mergeCells('X1:AF1');
		$sheet->mergeCells('AG1:AO1');
		$sheet->mergeCells('A2:C2');
		$sheet->mergeCells('D2:F2');
		$sheet->mergeCells('G2:H2');
		$sheet->mergeCells('J2:S2');
		$sheet->mergeCells('U2:W2');
		$sheet->mergeCells('X2:AG2');
		$sheet->mergeCells('A4:' . $max_col . '4');



		//ボーダー処理
		$sheet->getStyle('A2:C2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('D2:F2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('J2:S2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('U2:W2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('X2:AG2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('A4:' . $max_col . '4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

		//セルの幅調整
		$i = 1; //カウント用
		$j = 5; //カウント用

		while ($col != $max_col_remainder) { //右端と一致するまで回る
			$col = Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($col)->setWidth(2.5); //セルの幅調整
			$i++;
		}
		while ($j < $max_row) {
			$sheet->getRowDimension($j)->setRowHeight(12.5); //セルの高さ
			$j++;
		}


		/**
		 * 2枚目 シート追加
		 *
		 */
		$spreadsheet->createSheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');
		$sheet = $spreadsheet->getSheet(1);
		$sheet->setTitle('【小4～6】');
		// 値とセルを指定
		/**
		 * 見出し
		 */
		if ($school_area == 1) {
			$sheet->setCellValue('A1', '大阪');
			$sheet->setCellValue('F1', '校舎');
		} elseif ($school_area == 2) {
			$sheet->setCellValue('A1', '奈良');
			$sheet->setCellValue('F1', '校舎');
		} else {
			$sheet->setCellValue('A1', '');
			$sheet->setCellValue('F1', '校舎');
		}
		$sheet->getStyle('A1')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('F1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('I1', '小学部　成績カルテ');
		$sheet->getStyle('I1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('X1', '進学ゼミナール');
		$sheet->getStyle('X1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('AG1',  $school_building->name);
		$sheet->getStyle('AG1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('A2', '小学');
		$sheet->getStyle('A2')->getFont()->setSize(20)->setBold(true);



		if ($student->grade == 4) {
			$sheet->setCellValue('D2', '1');
		} elseif ($student->grade == 5) {
			$sheet->setCellValue('D2', '2');
		} elseif ($student->grade == 6) {
			$sheet->setCellValue('D2', '3');
		} elseif ($student->grade == 7) {
			$sheet->setCellValue('D2', '4');
		} elseif ($student->grade == 8) {
			$sheet->setCellValue('D2', '5');
		} elseif ($student->grade == 9) {
			$sheet->setCellValue('D2', '6');
		} else {
			$sheet->setCellValue('D2', '');
		}
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('G2', '年');
		$sheet->getStyle('G2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('J2',  $school->name);
		$sheet->getStyle('J2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('U2', '氏名');
		$sheet->getStyle('U2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('X2', $student->surname . $student->name);
		$sheet->getStyle('X2')->getFont()->setSize(20)->setBold(true);

		$sheet->getStyle('A4')->getFont()->setSize(16)->setBold(true);




		// //成績カテゴリーの取得 塾内テストの取得 塾内テストid=3
		$resultcategorys = ResultCategory::where('id', 3)->firstOrFail();
		//実施回の取得
		$implementations = Implementation::where('result_category_id', $resultcategorys->id)->get();
		//教科の取得
		$subjects = Subject::where('result_category_id', $resultcategorys->id)->get();


		//値セット位置
		$grade_position = 5; //学年表示位置
		$implementation_position = 5; //実施回の値位置

		$header_position = 5; //見出しや項目の表示の位置

		$subject_position = 5; //教科の値位置
		$subject_horizontal = 11; //教科の横移動用　K=11

		$point_position = 6; //点数の値位置
		$point_horizontal = 11; //点数の横移動用　K=11

		//平均偏差値位置
		$average_position = 5;
		$subject_get = 5;
		$subject_get_horizontal = 11;

		// 学年の数回る
		for ($i = 0; $i < 3; $i++) {
			if ($i == 0) {
				$sheet->setCellValue('A' . $grade_position, "小学4年生");
			} else if ($i == 1) {
				$sheet->setCellValue('A' . $grade_position, "小学5年生");
			} else if ($i == 2) {
				$sheet->setCellValue('A' . $grade_position, "小学6年生");
			}
			//試験の値セット
			foreach ($implementations as $implementationkey => $implementation) {
				$max_col = 0; //右端用変数
				$max_row = 0; //最後の行変数

				$sheet->setCellValue('E' . $implementation_position, $implementation->implementation_name);
				$sheet->mergeCells('E' . $implementation_position . ':G' . $implementation_position + 2);

				$sheet->setCellValue('H' . $header_position, "教科");
				$sheet->mergeCells('H' . $header_position . ':J' . $header_position);

				$sheet->setCellValue('H' . $header_position + 1, "偏差値");
				$sheet->mergeCells('H' . $header_position + 1 . ':J' . $header_position + 2);

				$sheet->setCellValue('A4', '塾内テスト推移');
				$sheet->getStyle('A4')->getFont()->setSize(16)->setBold(true);


				//教科値セット
				foreach ($subjects as $subjectkey => $subject) {
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //K指定する
					if ($subject->subject_name == '数学') {
						$sheet->setCellValue($column_subject . $subject_position, "算数");
					} elseif ($subject->subject_name == '2科/3科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "2科");
					} elseif ($subject->subject_name == '3科/5科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "3科");
					} else {
						$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
					}
					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$subject_horizontal = $subject_horizontal + 3;


					//生徒成績取得
					$studentresults = StudentResult::where('student_no', $student->student_no)->where('grade', 7 + $i)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
					if ($studentresults->isNotEmpty()) {
						foreach ($studentresults as $resultkey => $studentresult) {
							$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
							$sheet->setCellValue($column_point . $point_position,  $studentresult->point);

							$sheet->getStyle($column_point . $point_position)->getFill()->setFillType('solid')->getStartColor()->setARGB('palegreen');

							$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
							$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
							$point_horizontal = $point_horizontal + 3;
						}
					} else {
						$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
						$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
						$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
						$point_horizontal = $point_horizontal + 3;
					}
				}
				$max_col = $subject_horizontal - 1; //右端用変数
				$max_col = Coordinate::stringFromColumnIndex($max_col);
				$max_row = $point_position; //一番最後の値セット位置
				$subject_max_row = $point_position; //一番最後の値セット位置

				$average_horizontal1 = Coordinate::stringFromColumnIndex($point_horizontal + 1); //平均偏差値　横移動用
				$average_horizontal2 = Coordinate::stringFromColumnIndex($point_horizontal + 4); //平均偏差値　横移動用
				$merge_average_horizontal1 = Coordinate::stringFromColumnIndex($point_horizontal + 6); //平均偏差値　横移動用
				$merge_average_horizontal2 = Coordinate::stringFromColumnIndex($point_horizontal + 3); //平均偏差値　横移動用


				$implementation_position = $implementation_position + 3; //実施回の値位置変更
				$header_position = $header_position + 3; //見出しや項目の表示の位置
				$subject_position = $subject_position + 3; //教科の値位置
				$subject_horizontal = 11; //教科の横移動用リセット
				$point_position = $point_position + 3; //点数の位置変更
				$point_horizontal = 11; //点数横位置リセット


			}
			//次のブロック用の前に結合　'A' . $grade_position→ブラック開始位置　A5
			$sheet->mergeCells('A' . $grade_position . ':D' . $max_row + 1);
			//次のブロック用の前にボーダー
			$sheet->getStyle('A' . $grade_position . ':' . $max_col . $max_row + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//平均偏差値
			$sheet->setCellValue($average_horizontal1 . $average_position, "平均偏差値");
			$sheet->mergeCells($average_horizontal1 . $average_position . ':' . $merge_average_horizontal1 . $average_position + 1);
			//平均偏差値　教科1
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal1 . $average_position + 2, "国語");
			$sheet->mergeCells($average_horizontal1 . $average_position + 2 . ':' . $merge_average_horizontal2 . $average_position + 3);
			//値セット
			$sheet->setCellValue($average_horizontal1 . $average_position + 4, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal1 . $average_position + 4))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal1 . $average_position + 4 . ':' . $merge_average_horizontal2 . $average_position + 6);



			//平均偏差値　教科2
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 6); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal2 . $average_position + 2, "算数");
			$sheet->mergeCells($average_horizontal2 . $average_position + 2 . ':' . $merge_average_horizontal1 . $average_position + 3);
			//値セット
			$sheet->setCellValue($average_horizontal2 . $average_position + 4, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal2 . $average_position + 4))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal2 . $average_position + 4 . ':' . $merge_average_horizontal1 . $average_position + 6);




			//平均偏差値　教科3
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 15); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal1 . $average_position + 7, "2科");
			$sheet->mergeCells($average_horizontal1 . $average_position + 7 . ':' . $merge_average_horizontal2 . $average_position + 8);

			$sheet->setCellValue($average_horizontal1 . $average_position + 9, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal1 . $average_position + 9))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal1 . $average_position + 9 . ':' . $merge_average_horizontal2 . $average_position + 11);


			//平均偏差値　教科4
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 12); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal2 . $average_position + 7, "英語");
			$sheet->mergeCells($average_horizontal2 . $average_position + 7 . ':' . $merge_average_horizontal1 . $average_position + 8);

			$sheet->setCellValue($average_horizontal2 . $average_position + 9, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal2 . $average_position + 9))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal2 . $average_position + 9 . ':' . $merge_average_horizontal1 . $average_position + 11);


			//平均偏差値ボーダー
			$sheet->getStyle($average_horizontal1 . $average_position . ':' . $merge_average_horizontal1 . $average_position + 11)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			//次のブロック用
			$grade_position =  $header_position + 1; //学年の値セット位置＝教科偏差値の位置
			$average_position =  $header_position + 1; //平均偏差値セット位置＝教科偏差値の位置
			$subject_get = $header_position + 1;
			$point_position = $point_position + 1;
			$subject_position = $subject_position + 1;
			$header_position = $header_position + 1;
			$implementation_position = $implementation_position + 1;
		}

		$col = Coordinate::stringFromColumnIndex(1); //A はじめのセル
		$max_col = $sheet->getHighestColumn(); //右端取得
		$max_col_remainder = Coordinate::columnIndexFromString($max_col);
		$max_col_remainder = $max_col_remainder + 20; //右端から余分にとる
		$max_col_remainder = Coordinate::stringFromColumnIndex($max_col_remainder); //string型に戻す
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得

		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setVertical(Align::VERTICAL_CENTER); //上下中央寄せ
		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER); //左右中央寄せ

		//セル結合処理
		$sheet->mergeCells('A1:E1');
		$sheet->mergeCells('F1:H1');
		$sheet->mergeCells('I1:W1');
		$sheet->mergeCells('X1:AF1');
		$sheet->mergeCells('AG1:AO1');
		$sheet->mergeCells('A2:C2');
		$sheet->mergeCells('D2:F2');
		$sheet->mergeCells('G2:H2');
		$sheet->mergeCells('J2:S2');
		$sheet->mergeCells('U2:W2');
		$sheet->mergeCells('X2:AG2');
		$sheet->mergeCells('A4:' . $max_col . '4');



		//ボーダー処理
		$sheet->getStyle('A2:C2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('D2:F2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('J2:S2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('U2:W2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('X2:AG2')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN); //下方向単線;
		$sheet->getStyle('A4:' . $max_col . '4')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

		//セルの幅調整
		$i = 1; //カウント用
		$j = 5; //カウント用

		while (
			$col != $max_col_remainder
		) { //右端と一致するまで回る
			$col = Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($col)->setWidth(2.5); //セルの幅調整
			$i++;
		}
		while (
			$j < $max_row
		) {
			$sheet->getRowDimension($j)->setRowHeight(12.5); //セルの高さ
			$j++;
		}

		//ファイル名用の姓と名を取得
		$student_surname = $student->surname;
		$student_name = $student->name;

		//ファイル名の作成
		$filename = 's_result_' . $student_surname . '_' . $student_name . '.xlsx';

		// $filename = 'elementary_school_student_result.xlsx';
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



	/**
	 * 中学生成績出力処理
	 *
	 * @param [type] $id
	 * @return void
	 */
	public function output_junior_high_school_student_result($id)
	{

		// $year = date('Y', strtotime('-3 month')); //4月から年度変わる
		//生徒情報取得
		$student = Student::where('student_no', $id)->firstOrFail();
		//校舎情報取得
		$school_building = SchoolBuilding::findOrFail($student->school_building_id);
		//学校エリア（1：大阪or2：奈良）
		$school_area = $school_building->area;
		// dd($school_area);
		//学校の取得
		$school = School::findOrFail($student->school_id);
		// dd($school);
		//学校名の取得
		$school_name = $school->name;
		//年度の取得
		$now_year = Year::first(); //１レコードしかないはず

		//'【中１】定期年度の計算
		$now_grade = $student->grade; //現在の学年
		$select_grade = 10; //今回出力する学年　（中1）
		$difference_grade = $now_grade - $select_grade; //学年の差
		// $select_year = date('Y', strtotime('-' . $difference_grade . 'year')); //学年の差　年度を引く
		$select_year = $now_year['year'] - $difference_grade;

		// スプレッドシート作成 テンプレ読み込み
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/preferred_school.xlsx'); //template.xlsx 読込
		// $spreadsheet = new Spreadsheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');

		// $sheet = $spreadsheet->getActiveSheet();
		$spreadsheet->createSheet();
		$sheet = $spreadsheet->getSheet(2);
		$sheet->setTitle('【中１】定期');

		if ($school_area == 1) {
			$sheet->setCellValue('A1', '大阪');
			$sheet->setCellValue('F1', '校舎');
		} elseif ($school_area == 2) {
			$sheet->setCellValue('A1', '奈良');
			$sheet->setCellValue('F1', '校舎');
		} else {
			$sheet->setCellValue('A1', '');
			$sheet->setCellValue('F1', '校舎');
		}
		$sheet->getStyle('A1')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('F1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('I1', '中学部　成績カルテ');
		$sheet->getStyle('I1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('X1',  $school_building->name);
		$sheet->getStyle('X1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('A2', '中学');
		$sheet->getStyle('A2')->getFont()->setSize(20)->setBold(true);



		if ($student->grade == 10) {
			$sheet->setCellValue('D2', '1');
		} elseif ($student->grade == 11) {
			$sheet->setCellValue('D2', '2');
		} elseif ($student->grade == 12) {
			$sheet->setCellValue('D2', '3');
		} else {
			$sheet->setCellValue('D2', '');
		}
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('G2', '年');
		$sheet->getStyle('G2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('J2',  $school->name);
		$sheet->getStyle('J2')->getFont()->setSize(20)->setBold(true);
		$sheet->setCellValue('U2', '氏名');
		$sheet->getStyle('U2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('X2', $student->surname . $student->name);
		$sheet->getStyle('X2')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('A3', '中学１年生');
		$sheet->getStyle('A3')->getFont()->setSize(16)->setBold(true);



		//成績カテゴリーの取得（中学1年）id=1 学校成績
		$resultcategorys = ResultCategory::where('id', 1)->firstOrFail();
		//実施回の取得
		$implementations = Implementation::where('result_category_id', $resultcategorys->id)->get();
		//教科の取得
		$subjects = Subject::where('result_category_id', $resultcategorys->id)->get();

		//値セット位置
		$implementation_position = 4; //実施回の値位置
		$implementation_horizontal = 3; //C
		$header_position = 4; //見出しや項目の表示の位置
		$subject_position = 4; //教科の値位置
		$subject_horizontal = 7; //教科の横移動用　G
		$point_position = 5; //点数の値位置
		$point_horizontal = 7; //点数の横移動用　G

		$average_point_position = 7; //学校平均点数の値位置
		$average_point_horizontal = 7; //学校平均点数の横移動用　G

		$average_difference_position = 9; //平均との差値位置
		$average_difference_horizontal = 7; //平均との差横移動用　

		$term_average_get_start_horizontal1 = 7; //5教科の取得スタート位置
		$term_average_get_start_horizontal2 = 25; //9教科の取得スタート位置
		$term_average_horizontal1 = 7; //学期平均

		//実施回の値セット
		foreach ($implementations as $implementationkey => $implementation) {
			$sheet->setCellValue('A' . $implementation_position, $implementation->implementation_name);
			$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal); //K→N
			$sheet->mergeCells('A' . $implementation_position . ':' . $merge_implementation . $implementation_position + 6);
			$sheet->getStyle('A' . $implementation_position . ':' . $merge_implementation . $implementation_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position, "教科");
			$sheet->mergeCells('D' . $header_position . ':F' . $header_position);
			$sheet->getStyle('D' . $header_position . ':F' . $header_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position + 1, "点数");
			$sheet->mergeCells('D' . $header_position + 1 . ':F' . $header_position + 2);
			$sheet->getStyle('D' . $header_position + 1 . ':F' . $header_position + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position + 3, "学校平均");
			$sheet->mergeCells('D' . $header_position + 3 . ':F' . $header_position + 4);
			$sheet->getStyle('D' . $header_position + 3 . ':F' . $header_position + 4)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position + 5, "平均との差");
			$sheet->mergeCells('D' . $header_position + 5 . ':F' . $header_position + 6);
			$sheet->getStyle('D' . $header_position + 5 . ':F' . $header_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			foreach ($subjects as $subjectkey => $subject) {
				$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する
				if (mb_strlen($subject->subject_name) < 5) {
					$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
				} else {
					$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
					$sheet->getStyle($column_subject . $subject_position)->getFont()->setSize(9)->setBold(true);
				}
				$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
				$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
				$sheet->getStyle($column_subject . $subject_position . ':' . $merge_subject . $subject_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				$sheet->getStyle(($column_subject . $subject_position))->getNumberFormat()->setFormatCode('0.0');


				//点数セット
				$studentresults = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 10)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
				// dd($studentresults);
				if ($studentresults->isNotEmpty()) {
					foreach ($studentresults as $resultkey => $studentresult) {
						$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
						$sheet->setCellValue($column_point . $point_position,  $studentresult->point);
						$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
						$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);

						$point_horizontal = $point_horizontal + 3;
					}
				} else {
					$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
					$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
					$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
					$point_horizontal = $point_horizontal + 3;
				}
				$sheet->getStyle($column_point . $point_position . ':' . $merge_point . $point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

				//学校平均セット
				$averagepoints = AveragePoint::where('school_id', $student->school_id)->where('year', $select_year)->where('grade', 10)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
				if ($averagepoints->isNotEmpty()) {
					foreach ($averagepoints as $averagepointkey => $averagepoint) {
						$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
						$sheet->setCellValue($column_average_point . $average_point_position,  $averagepoint->average_point);
						$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
						$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
						$average_point_horizontal = $average_point_horizontal + 3;
					}
				} else {
					$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
					$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
					$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
					$average_point_horizontal = $average_point_horizontal + 3;
				}
				$sheet->getStyle($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


				//平均との差
				$column_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal); //G指定する
				$sheet->setCellValue($column_point_difference . $average_difference_position, "=" . $column_point_difference . $average_difference_position - 4 . "-" . $column_point_difference . $average_difference_position - 2);
				$merge_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal + 2);
				$sheet->mergeCells($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1);
				$sheet->getStyle($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



				$term_average_position = $average_difference_position; //学期の平均位置
				$average_difference_horizontal = $average_difference_horizontal + 3;

				//５教科平均点
				if ($subjectkey == 4) {
					$subject_horizontal = $subject_horizontal + 3;
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する
					$sheet->setCellValue($column_subject . $subject_position, "5科目合計");
					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$sheet->getStyle($column_subject . $subject_position . ':' . $merge_subject . $subject_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
					$column_term_average_get_start_horizontal1 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal1); //G指定する
					$column_term_average_get_total_horizontal = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal1 - 3); //5教科合計取得位置
					$column_term_average_get_end_horizontal1 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal1 + 12); //U指定する

					$sheet->setCellValue($column_point . $point_position, "=SUM(" . $column_term_average_get_start_horizontal1 . $point_position . ":" . $column_term_average_get_end_horizontal1 . $point_position . ")");
					$total_point_position[] = $column_point . $point_position; //成績推移で使用
					$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
					$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
					$sheet->getStyle($column_point . $point_position . ':' . $merge_point . $point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

					$point_horizontal = $point_horizontal + 3;



					$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
					$sheet->setCellValue($column_average_point . $average_point_position, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position . ":" . $column_term_average_get_end_horizontal1 . $average_point_position . ")");
					$sheet->getStyle(($column_average_point . $average_point_position))->getNumberFormat()->setFormatCode('0.0');
					$total_average_point_position[] = $column_average_point . $average_point_position; //成績推移で使用
					$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
					$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
					$average_point_horizontal = $average_point_horizontal + 3;
					$sheet->getStyle($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal); //G指定する
					// dd($column_point_difference);
					// $sheet->setCellValue($column_point_difference . $average_difference_position, "=V5-V7");
					$sheet->setCellValue($column_point_difference . $average_difference_position, '=' . $column_point_difference . $average_difference_position - 4 . '-' . $column_point_difference . $average_difference_position - 2);
					// $sheet->getStyle(($column_point_difference . $average_difference_position))->getNumberFormat()->setFormatCode('0.0');
					$merge_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal + 2);
					$sheet->mergeCells($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1);
					$sheet->getStyle($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

					$average_difference_horizontal = $average_difference_horizontal + 3;
				}
				//9教科平均点
				if ($subjectkey == 8) {
					$subject_horizontal = $subject_horizontal + 3;
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する
					$sheet->setCellValue($column_subject . $subject_position, "9科目合計");
					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$sheet->getStyle($column_subject . $subject_position . ':' . $merge_subject . $subject_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
					$column_term_average_get_start_horizontal2 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal2); //Y指定する
					$column_totalpoint = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal2 - 3); //5教科合計の取得
					$column_term_average_get_end_horizontal2 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal2 + 9); //Y指定する
					// $sheet->setCellValue($column_point . $point_position, "=SUM(G5:U6)");
					$sheet->setCellValue($column_point . $point_position, "=SUM(" . $column_totalpoint . $point_position . "," . $column_term_average_get_start_horizontal2 . $point_position . ":" . $column_term_average_get_end_horizontal2 . $point_position . ")");
					// $sheet->getStyle(($column_point . $point_position))->getNumberFormat()->setFormatCode('0.0');
					$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
					$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
					$sheet->getStyle($column_point . $point_position . ':' . $merge_point . $point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

					$point_horizontal = $point_horizontal + 3;


					$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
					$sheet->setCellValue($column_average_point . $average_point_position, "=SUM(" . $column_totalpoint . $average_point_position  . "," . $column_term_average_get_start_horizontal2 . $average_point_position . ":" . $column_term_average_get_end_horizontal2 . $average_point_position . ")");
					$sheet->getStyle(($column_average_point . $average_point_position))->getNumberFormat()->setFormatCode('0.0');
					$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
					$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
					$average_point_horizontal = $average_point_horizontal + 3;
					$sheet->getStyle($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal); //G指定する
					$sheet->setCellValue($column_point_difference . $average_difference_position, '=' . $column_point_difference . $average_difference_position - 4 . '-' . $column_point_difference . $average_difference_position - 2);
					// $sheet->getStyle(($column_point_difference . $average_difference_position))->getNumberFormat()->setFormatCode('0.0');
					$merge_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal + 2);
					$sheet->mergeCells($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1);
					$sheet->getStyle($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


					$average_difference_horizontal = $average_difference_horizontal + 3;
				}

				$subject_horizontal = $subject_horizontal + 3;
			}

			//平均と評定を表示
			//成績カテゴリーの取得（中学1年）id=2 通知表

			if ($implementation->implementation_name == "1学期末" || $implementation->implementation_name == "2学期末" || $implementation->implementation_name == "学期末") {

				$resultcategory_rating = ResultCategory::where('id', 2)->firstOrFail();
				//通知表の実施回の取得
				//学校成績の実施回で取得する通知表変わる
				if ($implementation->implementation_name == "1学期末") {
					$implementation_rating = Implementation::where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 1)->firstOrFail(); //1学期
				} elseif ($implementation->implementation_name == "2学期末") {
					$implementation_rating = Implementation::where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 2)->firstOrFail(); //2学期
				} elseif ($implementation->implementation_name == "学期末") {
					$implementation_rating = Implementation::where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 3)->firstOrFail(); //3学期
				}

				//教科の取得
				$subject_ratings = Subject::where('result_category_id', $resultcategory_rating->id)->get();

				$implementation_position = $implementation_position + 5; //実施回の値位置
				$header_position = $header_position + 5; //見出しや項目の表示の位置
				$subject_position = $subject_position + 5; //教科の値位置
				$point_position = $point_position + 5; //点数の値位置
				$average_point_position = $average_point_position + 5; //点数の値位置
				$average_difference_position = $average_difference_position + 5; //点数の値位置

				foreach ($subject_ratings as $subject_rating_key => $subject_rating) {
					if ($implementation_rating->implementation_name == "1学期評定") {
						$studentratingpoints = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 10)->where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 1)->where('subject_no', $subject_rating->subject_no)->get();
						//1学期平均
						$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
						$sheet->setCellValue('A' . $implementation_position + 2, "1学期平均");
						$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
						$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


						$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
						$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
						$sheet->setCellValue($column_term_average . $term_average_position + 2, "=IFERROR(AVERAGE(" . $column_term_average . $term_average_position - 11 . "," . $column_term_average . $term_average_position - 4 . "), \"\")");
						// $sheet->getStyle(($column_term_average . $term_average_position + 2))->getNumberFormat()->setFormatCode('0.0');
						$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
						$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


						//1学期評定
						$sheet->setCellValue('A' . $implementation_position + 4, "1学期評定");
						$sheet->mergeCells('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5);
						$sheet->getStyle('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						if ($studentratingpoints->isNotEmpty()) {
							foreach ($studentratingpoints as $tudentratingpointkey => $studentratingpoint) {
								$sheet->setCellValue($column_term_average . $term_average_position + 4, $studentratingpoint->point);
								$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						} else {
							$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
							$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						$annual_rating['1学期評定'][] = $column_term_average . $term_average_position + 4;

						if ($subject_rating_key == 4) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($column_term_average . $average_point_position);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_term_average . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

							if ($implementation->implementation_name != "3学期評定") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_term_average . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
						if ($subject_rating_key == 8) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($merge_term_average);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM("  . $column_totalpoint . $average_point_position - 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_average_point . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_totalpoint . $average_point_position + 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_average_point . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
					} elseif ($implementation_rating->implementation_name == "2学期評定") {
						$studentratingpoints = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 10)->where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 2)->where('subject_no', $subject_rating->subject_no)->get();
						//2学期平均
						$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
						$sheet->setCellValue('A' . $implementation_position + 2, "2学期平均");
						$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
						$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

						$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
						$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
						$sheet->setCellValue($column_term_average . $term_average_position + 2, "=IFERROR(AVERAGE(" . $column_term_average . $term_average_position - 11 . "," . $column_term_average . $term_average_position - 4 . "), \"\")");
						$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
						$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


						//2学期評定
						$sheet->setCellValue('A' . $implementation_position + 4, "2学期評定");
						$sheet->mergeCells('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5);
						$sheet->getStyle('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						if ($studentratingpoints->isNotEmpty()) {
							foreach ($studentratingpoints as $tudentratingpointkey => $studentratingpoint) {
								$sheet->setCellValue($column_term_average . $term_average_position + 4, $studentratingpoint->point);
								// $sheet->getStyle(($column_term_average . $term_average_position + 4))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						} else {
							$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
							$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						$annual_rating['2学期評定'][] = $column_term_average . $term_average_position + 4;

						if ($subject_rating_key == 4) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($column_term_average . $average_point_position);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_term_average . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_term_average . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
						if ($subject_rating_key == 8) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($merge_term_average);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM("  . $column_totalpoint . $average_point_position - 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_average_point . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_totalpoint . $average_point_position + 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_average_point . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
					} elseif ($implementation_rating->implementation_name == "3学期評定") {
						$studentratingpoints = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 10)->where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 3)->where('subject_no', $subject_rating->subject_no)->get();
						//3学期評定
						if ($school_area == 1) { //大阪
							$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
							$sheet->setCellValue('A' . $implementation_position + 2, "学年評定");
							$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
							$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						} else { //奈良
							$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
							$sheet->setCellValue('A' . $implementation_position + 2, "3学期評定");
							$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
							$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
						$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
						if ($studentratingpoints->isNotEmpty()) {
							foreach ($studentratingpoints as $tudentratingpointkey => $studentratingpoint) {
								$sheet->setCellValue($column_term_average . $term_average_position + 2, $studentratingpoint->point);
								// $sheet->getStyle(($column_term_average . $term_average_position + 2))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
								$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
								$osaka_annual_rating[] = $column_term_average . $term_average_position + 2;
							}
						} else {
							$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							$osaka_annual_rating[] = $column_term_average . $term_average_position + 2;
						}
						$annual_rating['3学期評定'][] = $column_term_average . $term_average_position + 2;

						if ($subject_rating_key == 4) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($column_term_average . $average_point_position);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_term_average . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_term_average . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
							$osak_annual_rating_five = $column_term_average . $average_point_position - 1;
						}
						if ($subject_rating_key == 8) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($merge_term_average);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM("  . $column_totalpoint . $average_point_position - 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_average_point . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							$osaka_annual_rating_nine = $column_term_average . $average_point_position - 1;
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_totalpoint . $average_point_position + 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_average_point . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
					}

					$term_average_horizontal1 = $term_average_horizontal1 + 3;
				}
				$term_average_horizontal1 = 7; //学期平均
			}

			//次のブロック用に横移動変数リセット
			$subject_horizontal = 7; //教科の横移動用　G
			$point_horizontal = 7; //点数の横移動用　G
			$average_point_horizontal = 7; //学校平均点数の横移動用　G
			$average_difference_horizontal = 7; //平均との差横移動用　

			//次のブロックへ
			$implementation_position = $implementation_position + 7; //実施回の値位置
			$header_position = $header_position + 7; //見出しや項目の表示の位置
			$subject_position = $subject_position + 7; //教科の値位置
			$point_position = $point_position + 7; //点数の値位置
			$average_point_position = $average_point_position + 7; //点数の値位置
			$average_difference_position = $average_difference_position + 7; //点数の値位置

		}

		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		$max_col = $sheet->getHighestColumn(); //右端取得

		//奈良なら年間平均評定表示
		if ($school_area == 2) {
			// dd($school_area);
			$header_horizontal1 = 6; //年間平均評定表示の見出し
			$annual_rating_position = $max_row + 2;
			$annual_rating_horizontal1 = 7;
			$annual_rating_start_horizontal1 = 7;
			$annual_rating_start_horizontal2 = 22;
			// $count = 1;
			// dd($annual_rating['3学期評定']);
			$merge_header = Coordinate::stringFromColumnIndex($header_horizontal1); //K→N
			$sheet->setCellValue('A' . $max_row + 2, '年間平均評定');
			$sheet->mergeCells('A' . $max_row + 2 . ':' . $merge_header . $max_row + 3);
			$sheet->getStyle('A' . $max_row + 2 . ':' . $merge_header . $max_row + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			foreach ($subjects as $subjectkey => $subject) { //教科の数回る
				$annual_average_rating = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 10)->where('result_category_id', 2)->where('implementation_no', 4)->where('subject_no', $subject->subject_no)->first();
				$column_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1);
				$merge_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1 + 2);
				if ($annual_average_rating != null) {
					$sheet->setCellValue($column_annual_rating . $annual_rating_position, $annual_average_rating->point ?? "=IFERROR(AVERAGE(" . $annual_rating['1学期評定'][$subjectkey] . "," . $annual_rating['2学期評定'][$subjectkey] . "," . $annual_rating['3学期評定'][$subjectkey] . "),)");
				} else {
					$sheet->setCellValue($column_annual_rating . $annual_rating_position, "=IFERROR(AVERAGE(" . $annual_rating['1学期評定'][$subjectkey] . "," . $annual_rating['2学期評定'][$subjectkey] . "," . $annual_rating['3学期評定'][$subjectkey] . "),)");
				}
				$nara_annual_rating[] = $column_annual_rating . $annual_rating_position;
				$sheet->getStyle(($column_annual_rating . $annual_rating_position))->getNumberFormat()->setFormatCode('0');
				$sheet->mergeCells($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1);
				$sheet->getStyle($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				$annual_rating_horizontal1 = $annual_rating_horizontal1 + 3;

				if ($subjectkey == 4) {
					$column_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1);
					$merge_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1 + 2);
					$annual_rating_start = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal1);
					$annual_rating_end = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal1 + 12);

					$sheet->setCellValue($column_annual_rating . $annual_rating_position, "=SUM(" . $annual_rating_start . $annual_rating_position . ":" . $annual_rating_end . $annual_rating_position . ")");
					$nara_annual_rating_five = $column_annual_rating . $annual_rating_position;
					$sheet->getStyle(($column_annual_rating . $annual_rating_position))->getNumberFormat()->setFormatCode('0');
					$sheet->mergeCells($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1);
					$sheet->getStyle($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
					$annual_rating_horizontal1 = $annual_rating_horizontal1 + 3;
				}
				if ($subjectkey == 8) {
					$column_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1);
					$merge_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1 + 2);
					$annual_rating_start2 = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal2);
					$annual_rating_end2 = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal2 + 12);
					$sheet->setCellValue($column_annual_rating . $annual_rating_position, "=SUM(" . $annual_rating_start2 . $annual_rating_position . ":" . $annual_rating_end2 . $annual_rating_position . ")");
					$nara_annual_rating_nine = $column_annual_rating . $annual_rating_position;
					$sheet->getStyle(($column_annual_rating . $annual_rating_position))->getNumberFormat()->setFormatCode('0');
					$sheet->mergeCells($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1);
					$sheet->getStyle($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
					$annual_rating_horizontal1 = $annual_rating_horizontal1 + 3;
				}
			}
			// dd($nara_annual_rating);
		}

		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		$max_col = $sheet->getHighestColumn(); //右端取得

		$transition_position = $max_row + 6;

		$transition_horizontal = 6; //F

		$sheet->setCellValue('A' . $max_row + 2, '成績の推移');
		$row = $max_row + 2; //セル幅広げる用
		$sheet->getStyle('A' . $max_row + 2)->getFont()->setSize(16)->setBold(true);
		$sheet->mergeCells('A' . $max_row + 2 . ':' . $max_col . $max_row + 2);
		$sheet->getStyle('A' . $max_row + 2 . ':' . $max_col . $max_row + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		$sheet->setCellValue('F' . $max_row + 4, '1学期中間');
		$sheet->mergeCells('F' . $max_row + 4 . ':' . 'L' . $max_row + 5);
		$sheet->getStyle('F' . $max_row + 4 . ':' . 'L' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		//1学期中間の値セット
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 6); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[0]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[0]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('M' . $max_row + 4, '1学期期末');
		$sheet->mergeCells('M' . $max_row + 4 . ':' . 'S' . $max_row + 5);
		$sheet->getStyle('M' . $max_row + 4 . ':' . 'S' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		//1学期期末の値セット
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 7); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 13); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[1]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[1]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('T' . $max_row + 4, '2学期中間');
		$sheet->mergeCells('T' . $max_row + 4 . ':' . 'Z' . $max_row + 5);
		$sheet->getStyle('T' . $max_row + 4 . ':' . 'Z' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		//2学期中間値セット
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 14); //F+7
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 20); //結合用+7
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[2]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[2]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('AA' . $max_row + 4, '2学期期末');
		$sheet->mergeCells('AA' . $max_row + 4 . ':' . 'AG' . $max_row + 5);
		$sheet->getStyle('AA' . $max_row + 4 . ':' . 'AG' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 21); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 27); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[3]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[3]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('AH' . $max_row + 4, '学年末');
		$sheet->mergeCells('AH' . $max_row + 4 . ':' . 'AN' . $max_row + 5);
		$sheet->getStyle('AH' . $max_row + 4 . ':' . 'AN' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 28); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 34); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[4]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[4]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('B' . $max_row + 6, '5科点数');
		$sheet->mergeCells('B' . $max_row + 6 . ':' . 'E' . $max_row + 7);
		$sheet->getStyle('B' . $max_row + 6 . ':' . 'E' . $max_row + 7)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 35); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 41); //結合用

		$sheet->setCellValue('B' . $max_row + 8, '5科平均');
		$sheet->mergeCells('B' . $max_row + 8 . ':' . 'E' . $max_row + 9);
		$sheet->getStyle('B' . $max_row + 8 . ':' . 'E' . $max_row + 9)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		$col = Coordinate::stringFromColumnIndex(1); //A はじめのセル
		$max_col = $sheet->getHighestColumn(); //右端取得
		$max_col_remainder = Coordinate::columnIndexFromString($max_col);
		$max_col_remainder = $max_col_remainder + 20; //右端から余分にとる
		$max_col_remainder = Coordinate::stringFromColumnIndex($max_col_remainder); //string型に戻す
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得

		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setVertical(Align::VERTICAL_CENTER); //上下中央寄せ
		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER); //左右中央寄せ

		// //セル結合処理
		$sheet->mergeCells('A1:E1');
		$sheet->mergeCells('F1:H1');
		$sheet->mergeCells('I1:W1');
		$sheet->mergeCells('X1:AF1');
		$sheet->mergeCells('A2:C2');
		$sheet->mergeCells('D2:F2');
		$sheet->mergeCells('G2:H2');
		$sheet->mergeCells('J2:S2');
		$sheet->mergeCells('U2:W2');
		$sheet->mergeCells('X2:AG2');
		$sheet->mergeCells('A3:' . $max_col . '3');
		$sheet->getStyle('A3:' . $max_col . '3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN); //外周に枠つける
		//セルの幅調整
		$i = 1; //カウント用
		$j = 5; //カウント用

		while (
			$col != $max_col_remainder
		) { //右端と一致するまで回る
			$col = Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($col)->setWidth(2.5); //セルの幅調整
			$i++;
		}
		while ($j < $max_row) {
			$sheet->getRowDimension($j)->setRowHeight(12.5); //セルの高さ
			$j++;
		}
		$sheet->getRowDimension($row)->setRowHeight(20.5); //セルの高さ
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToWidth(1);


		//2枚目 シート追加
		$spreadsheet->createSheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');
		$sheet = $spreadsheet->getSheet(3);
		$sheet->setTitle('【中１】もし');

		if ($school_area == 1) { //1なら大阪
			$sheet->setCellValue('A1', '大阪');
			$sheet->setCellValue('F1', '校舎');
		} elseif ($school_area == 2) { //２なら奈良
			$sheet->setCellValue('A1', '奈良');
			$sheet->setCellValue('F1', '校舎');
		} else {
			$sheet->setCellValue('A1', '');
			$sheet->setCellValue('F1', '校舎');
		}
		$sheet->getStyle('A1')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('F1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('I1', '中学部　成績カルテ');
		$sheet->getStyle('I1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('X1', $school_building->name);
		$sheet->getStyle('X1')->getFont()->setSize(20)->setBold(true);

		if ($student->grade == 10) {
			$sheet->setCellValue('A2', '塾内テスト推移');
		} elseif ($student->grade == 11) {
			$sheet->setCellValue('A2', '塾内テスト推移');
		} elseif ($student->grade == 12) {
			$sheet->setCellValue('A2', '塾内テスト・模試成績推移');
		} else {
			$sheet->setCellValue('A2', '');
		}
		$sheet->getStyle('A2')->getFont()->setSize(16)->setBold(true);

		// //成績カテゴリーの取得 塾内テストの取得 塾内テストid=3
		$resultcategorys = ResultCategory::where('id', 3)->firstOrFail();
		//実施回の取得
		$implementations = Implementation::where('result_category_id', $resultcategorys->id)->get();
		//教科の取得
		$subjects = Subject::where('result_category_id', $resultcategorys->id)->get();

		//値セット位置
		$grade_position = 3; //学年表示位置
		$implementation_position = 3; //実施回の値位置

		$header_position = 3; //見出しや項目の表示の位置

		$subject_position = 3; //教科の値位置
		$subject_horizontal = 11; //教科の横移動用　K=11

		$point_position = 4; //点数の値位置
		$point_horizontal = 11; //点数の横移動用　K=11

		//平均偏差値位置
		$average_position = 3;
		$subject_get = 3;
		$subject_get_horizontal = 11;

		for ($i = 0; $i < 2; $i++) { //中学1年生2年生分回る
			if ($i == 0) {
				$sheet->setCellValue('A' . $grade_position, "中学1年生");
			} else if ($i == 1) {
				$sheet->setCellValue('A' . $grade_position, "中学2年生");
			}

			//試験の値セット
			foreach ($implementations as $implementationkey => $implementation) {
				$max_col = 0; //右端用変数
				$max_row = 0; //最後の行変数

				$sheet->setCellValue('E' . $implementation_position, $implementation->implementation_name);
				$sheet->mergeCells('E' . $implementation_position . ':G' . $implementation_position + 2);

				$sheet->setCellValue('H' . $header_position, "教科");
				$sheet->mergeCells('H' . $header_position . ':J' . $header_position);

				$sheet->setCellValue('H' . $header_position + 1, "偏差値");
				$sheet->mergeCells('H' . $header_position + 1 . ':J' . $header_position + 2);

				//教科値セット
				foreach ($subjects as $subjectkey => $subject) {
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //K指定する

					if ($subject->subject_name == '2科/3科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "3科");
					} elseif ($subject->subject_name == '3科/5科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "5科");
					} else {
						$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
					}

					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$subject_horizontal = $subject_horizontal + 3;
					//生徒成績取得
					$studentresults = StudentResult::where('student_no', $student->student_no)->where('year', $select_year + $i)->where('grade', 10 + $i)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
					if ($studentresults->isNotEmpty()) {
						foreach ($studentresults as $resultkey => $studentresult) {
							$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
							$sheet->setCellValue($column_point . $point_position,  $studentresult->point);

							$sheet->getStyle($column_point . $point_position)->getFill()->setFillType('solid')->getStartColor()->setARGB('palegreen');

							$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
							$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
							$point_horizontal = $point_horizontal + 3;
						}
					} else {
						$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
						$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
						$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
						$point_horizontal = $point_horizontal + 3;
					}
				}
				$max_col = $subject_horizontal - 1; //右端用変数
				$max_col = Coordinate::stringFromColumnIndex($max_col);
				$max_row = $point_position; //一番最後の値セット位置
				$subject_max_row = $point_position; //一番最後の値セット位置

				$average_horizontal1 = Coordinate::stringFromColumnIndex($point_horizontal); //平均偏差値　横移動用
				$average_horizontal2 = Coordinate::stringFromColumnIndex($point_horizontal + 3); //平均偏差値　横移動用
				$average_horizontal3 = Coordinate::stringFromColumnIndex($point_horizontal + 6); //平均偏差値　横移動用
				$average_horizontal4 = Coordinate::stringFromColumnIndex($point_horizontal + 2); //平均偏差値　横移動用

				$merge_average_horizontal = Coordinate::stringFromColumnIndex($point_horizontal + 8); //平均偏差値　ヘッダー
				$merge_average_horizontal1 = Coordinate::stringFromColumnIndex($point_horizontal + 2); //平均偏差値教科1　横移動用
				$merge_average_horizontal2 = Coordinate::stringFromColumnIndex($point_horizontal + 5); //平均偏差値教科2　横移動用
				$merge_average_horizontal3 = Coordinate::stringFromColumnIndex($point_horizontal + 8); //平均偏差値教科3　横移動用
				$merge_average_horizontal4 = Coordinate::stringFromColumnIndex($point_horizontal + 6); //平均偏差値教科3　横移動用

				$implementation_position = $implementation_position + 3; //実施回の値位置変更
				$header_position = $header_position + 3; //見出しや項目の表示の位置
				$subject_position = $subject_position + 3; //教科の値位置
				$subject_horizontal = 11; //教科の横移動用リセット
				$point_position = $point_position + 3; //点数の位置変更
				$point_horizontal = 11; //点数横位置リセット
			}


			//次のブロック用の前に結合　'A' . $grade_position→ブラック開始位置　A5
			$sheet->mergeCells('A' . $grade_position . ':D' . $max_row + 1);
			//次のブロック用の前にボーダー
			$sheet->getStyle('A' . $grade_position . ':' . $max_col . $max_row + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//平均偏差値
			$sheet->setCellValue($average_horizontal1 . $average_position, "平均偏差値");
			$sheet->mergeCells($average_horizontal1 . $average_position . ':' . $merge_average_horizontal . $average_position + 1);
			$sheet->getStyle($average_horizontal1 . $average_position . ':' . $merge_average_horizontal . $average_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//平均偏差値　教科1
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal1 . $average_position + 2, "国語");
			$sheet->mergeCells($average_horizontal1 . $average_position + 2 . ':' . $merge_average_horizontal1 . $average_position + 2);
			$sheet->getStyle($average_horizontal1 . $average_position + 2 . ':' . $merge_average_horizontal1 . $average_position + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//値セット
			$sheet->setCellValue($average_horizontal1 . $average_position + 3, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal1 . $average_position + 3))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal1 . $average_position + 3 . ':' . $merge_average_horizontal1 . $average_position + 5);
			$sheet->getStyle($average_horizontal1 . $average_position + 3 . ':' . $merge_average_horizontal1 . $average_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);




			//平均偏差値　教科2
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 6); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal2 . $average_position + 2, "数学");
			$sheet->mergeCells($average_horizontal2 . $average_position + 2 . ':' . $merge_average_horizontal2 . $average_position + 2);
			$sheet->getStyle($average_horizontal2 . $average_position + 2 . ':' . $merge_average_horizontal2 . $average_position + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//値セット
			$sheet->setCellValue($average_horizontal2 . $average_position + 3, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal2 . $average_position + 3))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal2 . $average_position + 3 . ':' . $merge_average_horizontal2 . $average_position + 5);
			$sheet->getStyle($average_horizontal2 . $average_position + 3 . ':' . $merge_average_horizontal2 . $average_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			//平均偏差値　教科3
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 12); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal3 . $average_position + 2, "英語");
			$sheet->mergeCells($average_horizontal3 . $average_position + 2 . ':' . $merge_average_horizontal3 . $average_position + 2);
			$sheet->getStyle($average_horizontal3 . $average_position + 2 . ':' . $merge_average_horizontal3 . $average_position + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//値セット
			$sheet->setCellValue($average_horizontal3 . $average_position + 3, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal3 . $average_position + 3))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal3 . $average_position + 3 . ':' . $merge_average_horizontal3 . $average_position + 5);
			$sheet->getStyle($average_horizontal3 . $average_position + 3 . ':' . $merge_average_horizontal3 . $average_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			// //平均偏差値　教科4
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 15); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal1 . $average_position + 6, "3科");
			$sheet->mergeCells($average_horizontal1 . $average_position + 6 . ':' . $merge_average_horizontal1 . $average_position + 6);
			$sheet->getStyle($average_horizontal1 . $average_position + 6 . ':' . $merge_average_horizontal1 . $average_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue($average_horizontal1 . $average_position + 7, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal1 . $average_position + 7))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal1 . $average_position + 7 . ':' . $merge_average_horizontal1 . $average_position + 9);
			$sheet->getStyle($average_horizontal1 . $average_position + 7 . ':' . $merge_average_horizontal1 . $average_position + 9)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			//平均偏差値　教科5
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 9); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal2 . $average_position + 6, "理科");
			$sheet->mergeCells($average_horizontal2 . $average_position + 6 . ':' . $merge_average_horizontal2 . $average_position + 6);
			$sheet->getStyle($average_horizontal2 . $average_position + 6 . ':' . $merge_average_horizontal2 . $average_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue($average_horizontal2 . $average_position + 7, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal2 . $average_position + 7))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal2 . $average_position + 7 . ':' . $merge_average_horizontal2 . $average_position + 9);
			$sheet->getStyle($average_horizontal2 . $average_position + 7 . ':' . $merge_average_horizontal2 . $average_position + 9)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			//平均偏差値　教科5
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 3); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal3 . $average_position + 6, "社会");
			$sheet->mergeCells($average_horizontal3 . $average_position + 6 . ':' . $merge_average_horizontal3 . $average_position + 6);
			$sheet->getStyle($average_horizontal3 . $average_position + 6 . ':' . $merge_average_horizontal3 . $average_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue($average_horizontal3 . $average_position + 7, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal3 . $average_position + 7))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal3 . $average_position + 7 . ':' . $merge_average_horizontal3 . $average_position + 9);
			$sheet->getStyle($average_horizontal3 . $average_position + 7 . ':' . $merge_average_horizontal3 . $average_position + 9)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			//平均偏差値　教科6
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 18); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal4 . $average_position + 10, "5科");
			$sheet->mergeCells($average_horizontal4 . $average_position + 10 . ':' . $merge_average_horizontal4 . $average_position + 10);
			$sheet->getStyle($average_horizontal4 . $average_position + 10 . ':' . $merge_average_horizontal4 . $average_position + 10)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue($average_horizontal4 . $average_position + 11, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal4 . $average_position + 11))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal4 . $average_position + 11 . ':' . $merge_average_horizontal4 . $average_position + 14);
			$sheet->getStyle($average_horizontal4 . $average_position + 11 . ':' . $merge_average_horizontal4 . $average_position + 14)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$grade_position =  $header_position + 1; //学年の値セット位置＝教科偏差値の位置
			$average_position =  $header_position + 1; //平均偏差値セット位置＝教科偏差値の位置
			$subject_get = $header_position + 1;
			$point_position = $point_position + 1;
			$subject_position = $subject_position + 1;
			$header_position = $header_position + 1;
			$implementation_position = $implementation_position + 1;
		}

		//入試内申点
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		$max_col = $sheet->getHighestColumn(); //右端取得

		$sheet->setCellValue('A' . $max_row + 2, '入試内申点');
		$row = $max_row + 2; //セル幅広げる用
		$sheet->getStyle('A' . $max_row + 2)->getFont()->setSize(16)->setBold(true);
		$sheet->mergeCells('A' . $max_row + 2 . ':' . $max_col . $max_row + 2);
		$sheet->getStyle('A' . $max_row + 2 . ':' . $max_col . $max_row + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		if ($school_area == 1) { //1なら大阪
			$sheet->setCellValue('A' . $max_row + 3, '※大阪公立高校(特別)は１年次の学年評定(45点)＋2年次の学年評定(45点)＋3年の学年評定(45点)×3＝225点満点(ベース点)');
			$examination_row1 = $max_row + 3; //セル幅広げる用
			$sheet->getStyle('A' . $max_row + 3)->getFont()->setSize(10)->setBold(true);
			$sheet->mergeCells('A' . $max_row + 3 . ':' . $max_col . $max_row + 3);
			$sheet->setCellValue('A' . $max_row + 4, '※大阪公立高校(一般)は１年次の学年評定(45点)×2＋2年次の学年評定(45点)×２＋3年の学年評定(45点)×6＝450点満点(ベース点)');
			$examination_row2 = $max_row + 4; //セル幅広げる用
			$sheet->getStyle('A' . $max_row + 4)->getFont()->setSize(10)->setBold(true);
			$sheet->mergeCells('A' . $max_row + 4 . ':' . $max_col . $max_row + 4);
		} else {
			$sheet->setCellValue('A' . $max_row + 3, '※奈良公立高校は2年次の平均評定(45点)＋3年1学期評定(45点)＋3年2学期評定(45点)＝135点満点(ベース点)');
			$examination_row1 = $max_row + 3; //セル幅広げる用
			$sheet->getStyle('A' . $max_row + 3)->getFont()->setSize(10)->setBold(true);
			$sheet->mergeCells('A' . $max_row + 3 . ':' . $max_col . $max_row + 3);
		}

		$examination_position = $max_row + 6;
		$examination_horizontal = 1; //A

		$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
		$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
		$sheet->setCellValue($column_examination . $examination_position, '中学1年生');
		$sheet->mergeCells($column_examination . $examination_position . ':' . $merge_examination . $examination_position + 1);

		$sheet->setCellValue($column_examination . $examination_position + 2, '学年評定');
		$sheet->mergeCells($column_examination . $examination_position + 2 . ':' . $merge_examination . $examination_position + 3);

		$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
		$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
		$sheet->setCellValue($column_examination . $examination_position + 5, '中学2年生');
		$sheet->mergeCells($column_examination . $examination_position + 5 . ':' . $merge_examination . $examination_position + 6);

		$sheet->setCellValue($column_examination . $examination_position + 7, '学年評定');
		$sheet->mergeCells($column_examination . $examination_position + 7 . ':' . $merge_examination . $examination_position + 8);

		$examination_subject_position = $max_row + 6;
		$examination_subject_horizontal = 7; //G

		$subjects = Subject::where('result_category_id', 1)->get();
		//教科値セット
		// dd($osak_annual_rating_five);
		for ($i = 0; $i < 2; $i++) {
			if ($i == 0) {
				if ($school_area == 1) { //大阪
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $osaka_annual_rating[$subjectkey]);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $osak_annual_rating_five);
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $osaka_annual_rating_nine);
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				} else { //奈良
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating_five);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating_nine);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				}
			} else {
				if ($school_area == 1) { //大阪
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						// $sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating[$subjectkey]);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							// $sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating_five);
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							// $sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating_nine);
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position + 5 . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				} else { //奈良
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						// $sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating[$subjectkey]);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							// $sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating_five);
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							// $sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating_nine);
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position + 5 . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				}
			}
			$examination_subject_horizontal = 7; //G
			$examination_subject_position = $examination_subject_position + 5;
		}




		$col = Coordinate::stringFromColumnIndex(1); //A はじめのセル
		$max_col = $sheet->getHighestColumn(); //右端取得
		$max_col_remainder = Coordinate::columnIndexFromString($max_col);
		$max_col_remainder = $max_col_remainder + 20; //右端から余分にとる
		$max_col_remainder = Coordinate::stringFromColumnIndex($max_col_remainder); //string型に戻す
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得

		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setVertical(Align::VERTICAL_CENTER); //上下中央寄せ
		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER); //左右中央寄せ

		// //セル結合処理
		$sheet->mergeCells('A1:E1');
		$sheet->mergeCells('F1:H1');
		$sheet->mergeCells('I1:W1');
		$sheet->mergeCells('X1:AF1');
		$sheet->mergeCells('AG1:AO1');
		$sheet->mergeCells('A2:' . $max_col . '2');
		$sheet->getStyle('A2:' . $max_col . '2')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN); //外周に枠つける
		//セルの幅調整
		$i = 1; //カウント用
		$j = 5; //カウント用

		while (
			$col != $max_col_remainder
		) { //右端と一致するまで回る
			$col = Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($col)->setWidth(2.5); //セルの幅調整
			$i++;
		}
		while (
			$j < $max_row
		) {
			$sheet->getRowDimension($j)->setRowHeight(12.5); //セルの高さ
			$j++;
		}
		$sheet->getRowDimension($row)->setRowHeight(20.5); //セルの高さ

		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToWidth(1);



		//【中２】定期年度の計算
		$select_grade = 11; //今回出力する学年　（中2）
		$difference_grade = $now_grade - $select_grade; //学年の差
		// $select_year = date('Y', strtotime('-' . $difference_grade . 'year')); //学年の差　年度を引く
		$select_year = $now_year['year'] - $difference_grade;
		// dd($select_year);
		//3枚目 シート追加
		$spreadsheet->createSheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');
		$sheet = $spreadsheet->getSheet(4);
		$sheet->setTitle('【中２】定期');

		if ($school_area == 1) {
			$sheet->setCellValue('A1', '大阪');
			$sheet->setCellValue('F1', '校舎');
		} elseif ($school_area == 2) {
			$sheet->setCellValue('A1', '奈良');
			$sheet->setCellValue('F1', '校舎');
		} else {
			$sheet->setCellValue('A1', '');
			$sheet->setCellValue('F1', '校舎');
		}
		$sheet->getStyle('A1')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('F1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('I1', '中学部　成績カルテ');
		$sheet->getStyle('I1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('X1',  $school_building->name);
		$sheet->getStyle('X1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('A2', '中学');
		$sheet->getStyle('A2')->getFont()->setSize(20)->setBold(true);



		if ($student->grade == 10) {
			$sheet->setCellValue('D2', '1');
		} elseif ($student->grade == 11) {
			$sheet->setCellValue('D2', '2');
		} elseif ($student->grade == 12) {
			$sheet->setCellValue('D2', '3');
		} else {
			$sheet->setCellValue('D2', '');
		}
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('G2', '年');
		$sheet->getStyle('G2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('J2',  $school->name);
		$sheet->getStyle('J2')->getFont()->setSize(20)->setBold(true);
		$sheet->setCellValue('U2', '氏名');
		$sheet->getStyle('U2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('X2', $student->surname . $student->name);
		$sheet->getStyle('X2')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('A3', '中学２年生');
		$sheet->getStyle('A3')->getFont()->setSize(16)->setBold(true);



		//成績カテゴリーの取得（中学1年）id=1 学校成績
		$resultcategorys = ResultCategory::where('id', 1)->firstOrFail();
		//実施回の取得
		$implementations = Implementation::where('result_category_id', $resultcategorys->id)->get();
		//教科の取得
		$subjects = Subject::where('result_category_id', $resultcategorys->id)->get();

		//値セット位置
		$implementation_position = 4; //実施回の値位置
		$implementation_horizontal = 3; //C
		$header_position = 4; //見出しや項目の表示の位置
		$subject_position = 4; //教科の値位置
		$subject_horizontal = 7; //教科の横移動用　G
		$point_position = 5; //点数の値位置
		$point_horizontal = 7; //点数の横移動用　G

		$average_point_position = 7; //学校平均点数の値位置
		$average_point_horizontal = 7; //学校平均点数の横移動用　G

		$average_difference_position = 9; //平均との差値位置
		$average_difference_horizontal = 7; //平均との差横移動用　

		$term_average_get_start_horizontal1 = 7; //5教科の取得スタート位置
		$term_average_get_start_horizontal2 = 25; //9教科の取得スタート位置

		$term_average_horizontal1 = 7; //学期平均

		//実施回の値セット
		foreach ($implementations as $implementationkey => $implementation) {
			$sheet->setCellValue('A' . $implementation_position, $implementation->implementation_name);
			$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal); //K→N
			$sheet->mergeCells('A' . $implementation_position . ':' . $merge_implementation . $implementation_position + 6);
			$sheet->getStyle('A' . $implementation_position . ':' . $merge_implementation . $implementation_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position, "教科");
			$sheet->mergeCells('D' . $header_position . ':F' . $header_position);
			$sheet->getStyle('D' . $header_position . ':F' . $header_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position + 1, "点数");
			$sheet->mergeCells('D' . $header_position + 1 . ':F' . $header_position + 2);
			$sheet->getStyle('D' . $header_position + 1 . ':F' . $header_position + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position + 3, "学校平均");
			$sheet->mergeCells('D' . $header_position + 3 . ':F' . $header_position + 4);
			$sheet->getStyle('D' . $header_position + 3 . ':F' . $header_position + 4)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position + 5, "平均との差");
			$sheet->mergeCells('D' . $header_position + 5 . ':F' . $header_position + 6);
			$sheet->getStyle('D' . $header_position + 5 . ':F' . $header_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			foreach ($subjects as $subjectkey => $subject) {
				$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する
				if (mb_strlen($subject->subject_name) < 5) {
					$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
				} else {
					$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
					$sheet->getStyle($column_subject . $subject_position)->getFont()->setSize(9)->setBold(true);
				}
				$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
				$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
				$sheet->getStyle($column_subject . $subject_position . ':' . $merge_subject . $subject_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

				//点数セット
				$studentresults = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 11)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
				if ($studentresults->isNotEmpty()) {
					foreach ($studentresults as $resultkey => $studentresult) {
						$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
						$sheet->setCellValue($column_point . $point_position,  $studentresult->point);
						$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
						$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
						$point_horizontal = $point_horizontal + 3;
					}
				} else {
					$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
					$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
					$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
					$point_horizontal = $point_horizontal + 3;
				}
				$sheet->getStyle($column_point . $point_position . ':' . $merge_point . $point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

				//学校平均セット
				$averagepoints = AveragePoint::where('school_id', $student->school_id)->where('year', $select_year)->where('grade', 11)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
				// dd($averagepoints);
				if ($averagepoints->isNotEmpty()) {
					foreach ($averagepoints as $averagepointkey => $averagepoint) {
						$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
						$sheet->setCellValue($column_average_point . $average_point_position,  $averagepoint->average_point);
						$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
						$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
						$average_point_horizontal = $average_point_horizontal + 3;
					}
				} else {
					$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
					$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
					$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
					$average_point_horizontal = $average_point_horizontal + 3;
				}
				$sheet->getStyle($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


				//平均との差
				$column_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal); //G指定する
				$sheet->setCellValue($column_point_difference . $average_difference_position, "=" . $column_point_difference . $average_difference_position - 4 . "-" . $column_point_difference . $average_difference_position - 2);
				$merge_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal + 2);
				$sheet->mergeCells($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1);
				$sheet->getStyle($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



				$term_average_position = $average_difference_position; //学期の平均位置
				$average_difference_horizontal = $average_difference_horizontal + 3;

				//５教科平均点
				if ($subjectkey == 4) {
					$subject_horizontal = $subject_horizontal + 3;
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する
					$sheet->setCellValue($column_subject . $subject_position, "5科目合計");
					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$sheet->getStyle($column_subject . $subject_position . ':' . $merge_subject . $subject_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
					$column_term_average_get_start_horizontal1 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal1); //G指定する
					$column_term_average_get_total_horizontal = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal1 - 3); //5教科合計取得位置
					$column_term_average_get_end_horizontal1 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal1 + 12); //U指定する

					$sheet->setCellValue($column_point . $point_position, "=SUM(" . $column_term_average_get_start_horizontal1 . $point_position . ":" . $column_term_average_get_end_horizontal1 . $point_position . ")");
					$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
					$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
					$sheet->getStyle($column_point . $point_position . ':' . $merge_point . $point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

					$point_horizontal = $point_horizontal + 3;



					$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
					$sheet->setCellValue($column_average_point . $average_point_position, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position . ":" . $column_term_average_get_end_horizontal1 . $average_point_position . ")");
					$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
					$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
					$average_point_horizontal = $average_point_horizontal + 3;
					$sheet->getStyle($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal); //G指定する
					// dd($column_point_difference);
					// $sheet->setCellValue($column_point_difference . $average_difference_position, "=V5-V7");
					$sheet->setCellValue($column_point_difference . $average_difference_position, '=' . $column_point_difference . $average_difference_position - 4 . '-' . $column_point_difference . $average_difference_position - 2);
					$merge_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal + 2);
					$sheet->mergeCells($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1);
					$sheet->getStyle($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

					$average_difference_horizontal = $average_difference_horizontal + 3;
				}
				//9教科平均点
				if ($subjectkey == 8) {
					$subject_horizontal = $subject_horizontal + 3;
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する
					$sheet->setCellValue($column_subject . $subject_position, "9科目合計");
					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$sheet->getStyle($column_subject . $subject_position . ':' . $merge_subject . $subject_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
					$column_term_average_get_start_horizontal2 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal2); //Y指定する
					$column_totalpoint = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal2 - 3); //5教科合計の取得
					$column_term_average_get_end_horizontal2 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal2 + 9); //Y指定する
					// $sheet->setCellValue($column_point . $point_position, "=SUM(G5:U6)");
					$sheet->setCellValue($column_point . $point_position, "=SUM(" . $column_totalpoint . $point_position . "," . $column_term_average_get_start_horizontal2 . $point_position . ":" . $column_term_average_get_end_horizontal2 . $point_position . ")");
					$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
					$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
					$sheet->getStyle($column_point . $point_position . ':' . $merge_point . $point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

					$point_horizontal = $point_horizontal + 3;


					$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
					$sheet->setCellValue($column_average_point . $average_point_position, "=SUM(" . $column_totalpoint . $average_point_position  . "," . $column_term_average_get_start_horizontal2 . $average_point_position . ":" . $column_term_average_get_end_horizontal2 . $average_point_position . ")");
					$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
					$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
					$average_point_horizontal = $average_point_horizontal + 3;
					$sheet->getStyle($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal); //G指定する
					$sheet->setCellValue($column_point_difference . $average_difference_position, '=' . $column_point_difference . $average_difference_position - 4 . '-' . $column_point_difference . $average_difference_position - 2);

					$merge_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal + 2);
					$sheet->mergeCells($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1);
					$sheet->getStyle($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


					$average_difference_horizontal = $average_difference_horizontal + 3;
				}

				$subject_horizontal = $subject_horizontal + 3;
			}

			if ($implementation->implementation_name == "1学期末" || $implementation->implementation_name == "2学期末" || $implementation->implementation_name == "学期末") {

				$resultcategory_rating = ResultCategory::where('id', 2)->firstOrFail();
				//通知表の実施回の取得
				//学校成績の実施回で取得する通知表変わる
				if ($implementation->implementation_name == "1学期末") {
					$implementation_rating = Implementation::where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 1)->firstOrFail(); //1学期
				} elseif ($implementation->implementation_name == "2学期末") {
					$implementation_rating = Implementation::where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 2)->firstOrFail(); //2学期
				} elseif ($implementation->implementation_name == "学期末") {
					$implementation_rating = Implementation::where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 3)->firstOrFail(); //3学期
				}

				//教科の取得
				$subject_ratings = Subject::where('result_category_id', $resultcategory_rating->id)->get();


				$implementation_position = $implementation_position + 5; //実施回の値位置
				$header_position = $header_position + 5; //見出しや項目の表示の位置
				$subject_position = $subject_position + 5; //教科の値位置
				$point_position = $point_position + 5; //点数の値位置
				$average_point_position = $average_point_position + 5; //点数の値位置
				$average_difference_position = $average_difference_position + 5; //点数の値位置


				// dd($average_point_position);
				foreach ($subject_ratings as $subject_rating_key => $subject_rating) {
					if ($implementation_rating->implementation_name == "1学期評定") {
						$studentratingpoints = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 11)->where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 1)->where('subject_no', $subject_rating->subject_no)->get();
						//1学期平均
						$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
						$sheet->setCellValue('A' . $implementation_position + 2, "1学期平均");
						$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
						$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


						$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
						$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
						$sheet->setCellValue($column_term_average . $term_average_position + 2, "=IFERROR(AVERAGE(" . $column_term_average . $term_average_position - 11 . "," . $column_term_average . $term_average_position - 4 . "), \"\")");
						$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
						$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


						//1学期評定
						$sheet->setCellValue('A' . $implementation_position + 4, "1学期評定");
						$sheet->mergeCells('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5);
						$sheet->getStyle('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						if ($studentratingpoints->isNotEmpty()) {
							foreach ($studentratingpoints as $tudentratingpointkey => $studentratingpoint) {
								$sheet->setCellValue($column_term_average . $term_average_position + 4, $studentratingpoint->point);
								$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						} else {
							$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
							$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						if ($subject_rating_key == 4) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($column_term_average . $average_point_position);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_term_average . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_term_average . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
						if ($subject_rating_key == 8) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($merge_term_average);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM("  . $column_totalpoint . $average_point_position - 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position - 1 . ")");
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_totalpoint . $average_point_position + 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position + 1 . ")");
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
					} elseif ($implementation_rating->implementation_name == "2学期評定") {
						$studentratingpoints = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 11)->where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 2)->where('subject_no', $subject_rating->subject_no)->get();
						//2学期平均
						$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
						$sheet->setCellValue('A' . $implementation_position + 2, "2学期平均");
						$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
						$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

						$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
						$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
						$sheet->setCellValue($column_term_average . $term_average_position + 2, "=IFERROR(AVERAGE(" . $column_term_average . $term_average_position - 11 . "," . $column_term_average . $term_average_position - 4 . "), \"\")");
						$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
						$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


						//2学期評定
						$sheet->setCellValue('A' . $implementation_position + 4, "2学期評定");
						$sheet->mergeCells('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5);
						$sheet->getStyle('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						if ($studentratingpoints->isNotEmpty()) {
							foreach ($studentratingpoints as $tudentratingpointkey => $studentratingpoint) {
								$sheet->setCellValue($column_term_average . $term_average_position + 4, $studentratingpoint->point);
								$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						} else {
							$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
							$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						if ($subject_rating_key == 4) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($column_term_average . $average_point_position);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_term_average . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_term_average . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
						if ($subject_rating_key == 8) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($merge_term_average);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM("  . $column_totalpoint . $average_point_position - 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_average_point . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_totalpoint . $average_point_position + 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_average_point . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
					} elseif ($implementation_rating->implementation_name == "3学期評定") {
						//3学期評定
						$studentratingpoints = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 11)->where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 3)->where('subject_no', $subject_rating->subject_no)->get();
						if ($school_area == 1) { //大阪
							$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
							$sheet->setCellValue('A' . $implementation_position + 2, "学年評定");
							$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
							$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						} else { //奈良
							$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
							$sheet->setCellValue('A' . $implementation_position + 2, "3学期評定");
							$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
							$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
						$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
						if ($studentratingpoints->isNotEmpty()) {
							foreach ($studentratingpoints as $tudentratingpointkey => $studentratingpoint) {
								$sheet->setCellValue($column_term_average . $term_average_position + 2, $studentratingpoint->point);
								$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
								$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						} else {
							$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						$osaka_annual_rating2[] = $column_term_average . $term_average_position + 2;

						if ($subject_rating_key == 4) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($column_term_average . $average_point_position);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position - 1 . ")");
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

							$osak_annual_rating_five2 = $column_term_average . $average_point_position - 1;
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position + 1 . ")");
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
						if ($subject_rating_key == 8) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($merge_term_average);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM("  . $column_totalpoint . $average_point_position - 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position - 1 . ")");
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							$osaka_annual_rating_nine2 = $column_term_average . $average_point_position - 1;

							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_totalpoint . $average_point_position + 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position + 1 . ")");
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
					}
					$term_average_horizontal1 = $term_average_horizontal1 + 3;
				}
				$term_average_horizontal1 = 7; //学期平均
			}


			//次のブロック用に横移動変数リセット
			$subject_horizontal = 7; //教科の横移動用　G
			$point_horizontal = 7; //点数の横移動用　G
			$average_point_horizontal = 7; //学校平均点数の横移動用　G
			$average_difference_horizontal = 7; //平均との差横移動用　

			//次のブロックへ
			$implementation_position = $implementation_position + 7; //実施回の値位置
			$header_position = $header_position + 7; //見出しや項目の表示の位置
			$subject_position = $subject_position + 7; //教科の値位置
			$point_position = $point_position + 7; //点数の値位置
			$average_point_position = $average_point_position + 7; //点数の値位置
			$average_difference_position = $average_difference_position + 7; //点数の値位置

		}

		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		$max_col = $sheet->getHighestColumn(); //右端取得

		//奈良なら年間平均評定表示
		if ($school_area == 2) {
			$header_horizontal1 = 6; //年間平均評定表示の見出し
			$annual_rating_position = $max_row + 2;
			$annual_rating_horizontal1 = 7;
			$annual_rating_start_horizontal1 = 7;
			$annual_rating_start_horizontal2 = 22;
			// $count = 1;
			// dd($annual_rating['3学期評定']);
			$merge_header = Coordinate::stringFromColumnIndex($header_horizontal1); //K→N
			$sheet->setCellValue('A' . $max_row + 2, '年間平均評定');
			$sheet->mergeCells('A' . $max_row + 2 . ':' . $merge_header . $max_row + 3);
			$sheet->getStyle('A' . $max_row + 2 . ':' . $merge_header . $max_row + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			foreach ($subjects as $subjectkey => $subject) { //教科の数回る
				$annual_average_rating = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 11)->where('result_category_id', 2)->where('implementation_no', 4)->where('subject_no', $subject->subject_no)->first();
				$column_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1);
				$merge_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1 + 2);
				// dd($annual_rating['1学期評定']);
				if ($annual_average_rating != null) {
					$sheet->setCellValue($column_annual_rating . $annual_rating_position, $annual_average_rating->point ?? "=IFERROR(AVERAGE(" . $annual_rating['1学期評定'][$subjectkey] . "," . $annual_rating['2学期評定'][$subjectkey] . "," . $annual_rating['3学期評定'][$subjectkey] . "),)");
				} else {
					$sheet->setCellValue($column_annual_rating . $annual_rating_position, "=IFERROR(AVERAGE(" . $annual_rating['1学期評定'][$subjectkey] . "," . $annual_rating['2学期評定'][$subjectkey] . "," . $annual_rating['3学期評定'][$subjectkey] . "),)");
				}
				$nara_annual_rating2[] = $column_annual_rating . $annual_rating_position;
				$sheet->getStyle(($column_annual_rating . $annual_rating_position))->getNumberFormat()->setFormatCode('0');
				$sheet->mergeCells($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1);
				$sheet->getStyle($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				$annual_rating_horizontal1 = $annual_rating_horizontal1 + 3;

				if ($subjectkey == 4) {
					$column_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1);
					$merge_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1 + 2);
					$annual_rating_start = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal1);
					$annual_rating_end = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal1 + 12);

					$sheet->setCellValue($column_annual_rating . $annual_rating_position, "=SUM(" . $annual_rating_start . $annual_rating_position . ":" . $annual_rating_end . $annual_rating_position . ")");
					$nara_annual_rating_five2 = $column_annual_rating . $annual_rating_position;
					$sheet->getStyle(($column_annual_rating . $annual_rating_position))->getNumberFormat()->setFormatCode('0');
					$sheet->mergeCells($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1);
					$sheet->getStyle($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
					$annual_rating_horizontal1 = $annual_rating_horizontal1 + 3;
				}
				if ($subjectkey == 8) {
					$column_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1);
					$merge_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1 + 2);
					$annual_rating_start2 = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal2);
					$annual_rating_end2 = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal2 + 12);
					$sheet->setCellValue($column_annual_rating . $annual_rating_position, "=SUM(" . $annual_rating_start2 . $annual_rating_position . ":" . $annual_rating_end2 . $annual_rating_position . ")");
					$nara_annual_rating_nine2 = $column_annual_rating . $annual_rating_position;
					$sheet->getStyle(($column_annual_rating . $annual_rating_position))->getNumberFormat()->setFormatCode('0');
					$sheet->mergeCells($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1);
					$sheet->getStyle($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
					$annual_rating_horizontal1 = $annual_rating_horizontal1 + 3;
				}
			}
			// dd($nara_annual_rating);
		}


		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		$max_col = $sheet->getHighestColumn(); //右端取得

		$transition_position = $max_row + 6;

		$transition_horizontal = 6; //F

		$sheet->setCellValue('A' . $max_row + 2, '成績の推移');
		$row = $max_row + 2; //セル幅広げる用
		$sheet->getStyle('A' . $max_row + 2)->getFont()->setSize(16)->setBold(true);
		$sheet->mergeCells('A' . $max_row + 2 . ':' . $max_col . $max_row + 2);
		$sheet->getStyle('A' . $max_row + 2 . ':' . $max_col . $max_row + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		$sheet->setCellValue('F' . $max_row + 4, '1学期中間');
		$sheet->mergeCells('F' . $max_row + 4 . ':' . 'L' . $max_row + 5);
		$sheet->getStyle('F' . $max_row + 4 . ':' . 'L' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		//1学期中間の値セット
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 6); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[0]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[0]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('M' . $max_row + 4, '1学期期末');
		$sheet->mergeCells('M' . $max_row + 4 . ':' . 'S' . $max_row + 5);
		$sheet->getStyle('M' . $max_row + 4 . ':' . 'S' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		//1学期期末の値セット
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 7); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 13); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[1]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[1]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('T' . $max_row + 4, '2学期中間');
		$sheet->mergeCells('T' . $max_row + 4 . ':' . 'Z' . $max_row + 5);
		$sheet->getStyle('T' . $max_row + 4 . ':' . 'Z' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		//2学期中間値セット
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 14); //F+7
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 20); //結合用+7
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[2]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[2]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('AA' . $max_row + 4, '2学期期末');
		$sheet->mergeCells('AA' . $max_row + 4 . ':' . 'AG' . $max_row + 5);
		$sheet->getStyle('AA' . $max_row + 4 . ':' . 'AG' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 21); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 27); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[3]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[3]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('AH' . $max_row + 4, '学年末');
		$sheet->mergeCells('AH' . $max_row + 4 . ':' . 'AN' . $max_row + 5);
		$sheet->getStyle('AH' . $max_row + 4 . ':' . 'AN' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 28); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 34); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[4]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[4]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('B' . $max_row + 6, '5科点数');
		$sheet->mergeCells('B' . $max_row + 6 . ':' . 'E' . $max_row + 7);
		$sheet->getStyle('B' . $max_row + 6 . ':' . 'E' . $max_row + 7)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 35); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 41); //結合用

		$sheet->setCellValue('B' . $max_row + 8, '5科平均');
		$sheet->mergeCells('B' . $max_row + 8 . ':' . 'E' . $max_row + 9);
		$sheet->getStyle('B' . $max_row + 8 . ':' . 'E' . $max_row + 9)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



		$col = Coordinate::stringFromColumnIndex(1); //A はじめのセル
		$max_col = $sheet->getHighestColumn(); //右端取得
		$max_col_remainder = Coordinate::columnIndexFromString($max_col);
		$max_col_remainder = $max_col_remainder + 20; //右端から余分にとる
		$max_col_remainder = Coordinate::stringFromColumnIndex($max_col_remainder); //string型に戻す
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得

		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setVertical(Align::VERTICAL_CENTER); //上下中央寄せ
		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER); //左右中央寄せ

		// //セル結合処理
		$sheet->mergeCells('A1:E1');
		$sheet->mergeCells('F1:H1');
		$sheet->mergeCells('I1:W1');
		$sheet->mergeCells('X1:AF1');
		$sheet->mergeCells('AG1:AO1');
		$sheet->mergeCells('A2:C2');
		$sheet->mergeCells('D2:F2');
		$sheet->mergeCells('G2:H2');
		$sheet->mergeCells('J2:S2');
		$sheet->mergeCells('U2:W2');
		$sheet->mergeCells('X2:AG2');
		$sheet->mergeCells('A3:' . $max_col . '3');
		$sheet->getStyle('A3:' . $max_col . '3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN); //外周に枠つける
		//セルの幅調整
		$i = 1; //カウント用
		$j = 5; //カウント用

		while (
			$col != $max_col_remainder
		) { //右端と一致するまで回る
			$col = Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($col)->setWidth(2.5); //セルの幅調整
			$i++;
		}
		while ($j < $max_row) {
			$sheet->getRowDimension($j)->setRowHeight(12.5); //セルの高さ
			$j++;
		}
		$sheet->getRowDimension($row)->setRowHeight(20.5); //セルの高さ


		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToWidth(1);



		$spreadsheet->createSheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');
		$sheet = $spreadsheet->getSheet(5);
		$sheet->setTitle('【中２】もし');

		$select_grade = 10; //今回出力する学年　（中2）
		$difference_grade = $now_grade - $select_grade; //学年の差
		// $select_year = date('Y', strtotime('-' . $difference_grade . 'year')); //学年の差　年度を引く
		$select_year = $now_year['year'] - $difference_grade;

		if ($school_area == 1) { //1なら大阪
			$sheet->setCellValue('A1', '大阪');
			$sheet->setCellValue('F1', '校舎');
		} elseif ($school_area == 2) { //２なら奈良
			$sheet->setCellValue('A1', '奈良');
			$sheet->setCellValue('F1', '校舎');
		} else {
			$sheet->setCellValue('A1', '');
			$sheet->setCellValue('F1', '校舎');
		}
		$sheet->getStyle('A1')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('F1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('I1', '中学部　成績カルテ');
		$sheet->getStyle('I1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('X1', $school_building->name);
		$sheet->getStyle('X1')->getFont()->setSize(20)->setBold(true);

		if ($student->grade == 10) {
			$sheet->setCellValue('A2', '塾内テスト推移');
		} elseif ($student->grade == 11) {
			$sheet->setCellValue('A2', '塾内テスト推移');
		} elseif ($student->grade == 12) {
			$sheet->setCellValue('A2', '塾内テスト・模試成績推移');
		} else {
			$sheet->setCellValue('A2', '');
		}
		$sheet->getStyle('A2')->getFont()->setSize(16)->setBold(true);

		// //成績カテゴリーの取得 塾内テストの取得 塾内テストid=3
		$resultcategorys = ResultCategory::where('id', 3)->firstOrFail();
		//実施回の取得
		$implementations = Implementation::where('result_category_id', $resultcategorys->id)->get();
		//教科の取得
		$subjects = Subject::where('result_category_id', $resultcategorys->id)->get();

		//値セット位置
		$grade_position = 3; //学年表示位置
		$implementation_position = 3; //実施回の値位置

		$header_position = 3; //見出しや項目の表示の位置

		$subject_position = 3; //教科の値位置
		$subject_horizontal = 11; //教科の横移動用　K=11

		$point_position = 4; //点数の値位置
		$point_horizontal = 11; //点数の横移動用　K=11

		//平均偏差値位置
		$average_position = 3;
		$subject_get = 3;
		$subject_get_horizontal = 11;
		for ($i = 0; $i < 2; $i++) { //中学1年生2年生分回る
			if ($i == 0) {
				$sheet->setCellValue('A' . $grade_position, "中学1年生");
			} else if ($i == 1) {
				$sheet->setCellValue('A' . $grade_position, "中学2年生");
			}

			//試験の値セット
			foreach ($implementations as $implementationkey => $implementation) {
				$max_col = 0; //右端用変数
				$max_row = 0; //最後の行変数

				$sheet->setCellValue('E' . $implementation_position, $implementation->implementation_name);
				$sheet->mergeCells('E' . $implementation_position . ':G' . $implementation_position + 2);

				$sheet->setCellValue('H' . $header_position, "教科");
				$sheet->mergeCells('H' . $header_position . ':J' . $header_position);

				$sheet->setCellValue('H' . $header_position + 1, "偏差値");
				$sheet->mergeCells('H' . $header_position + 1 . ':J' . $header_position + 2);

				//教科値セット
				foreach ($subjects as $subjectkey => $subject) {
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //K指定する

					if ($subject->subject_name == '2科/3科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "3科");
					} elseif ($subject->subject_name == '3科/5科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "5科");
					} else {
						$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
					}

					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$subject_horizontal = $subject_horizontal + 3;
					//生徒成績取得
					$studentresults = StudentResult::where('student_no', $student->student_no)->where('year', $select_year + $i)->where('grade', 10 + $i)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
					if ($studentresults->isNotEmpty()) {
						foreach ($studentresults as $resultkey => $studentresult) {
							$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
							$sheet->setCellValue($column_point . $point_position,  $studentresult->point);

							$sheet->getStyle($column_point . $point_position)->getFill()->setFillType('solid')->getStartColor()->setARGB('palegreen');

							$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
							$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
							$point_horizontal = $point_horizontal + 3;
						}
					} else {
						$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
						$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
						$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
						$point_horizontal = $point_horizontal + 3;
					}
				}
				$max_col = $subject_horizontal - 1; //右端用変数
				$max_col = Coordinate::stringFromColumnIndex($max_col);
				$max_row = $point_position; //一番最後の値セット位置
				$subject_max_row = $point_position; //一番最後の値セット位置

				$average_horizontal1 = Coordinate::stringFromColumnIndex($point_horizontal); //平均偏差値　横移動用
				$average_horizontal2 = Coordinate::stringFromColumnIndex($point_horizontal + 3); //平均偏差値　横移動用
				$average_horizontal3 = Coordinate::stringFromColumnIndex($point_horizontal + 6); //平均偏差値　横移動用
				$average_horizontal4 = Coordinate::stringFromColumnIndex($point_horizontal + 2); //平均偏差値　横移動用

				$merge_average_horizontal = Coordinate::stringFromColumnIndex($point_horizontal + 8); //平均偏差値　ヘッダー
				$merge_average_horizontal1 = Coordinate::stringFromColumnIndex($point_horizontal + 2); //平均偏差値教科1　横移動用
				$merge_average_horizontal2 = Coordinate::stringFromColumnIndex($point_horizontal + 5); //平均偏差値教科2　横移動用
				$merge_average_horizontal3 = Coordinate::stringFromColumnIndex($point_horizontal + 8); //平均偏差値教科3　横移動用
				$merge_average_horizontal4 = Coordinate::stringFromColumnIndex($point_horizontal + 6); //平均偏差値教科3　横移動用

				$implementation_position = $implementation_position + 3; //実施回の値位置変更
				$header_position = $header_position + 3; //見出しや項目の表示の位置
				$subject_position = $subject_position + 3; //教科の値位置
				$subject_horizontal = 11; //教科の横移動用リセット
				$point_position = $point_position + 3; //点数の位置変更
				$point_horizontal = 11; //点数横位置リセット
			}


			//次のブロック用の前に結合　'A' . $grade_position→ブラック開始位置　A5
			$sheet->mergeCells('A' . $grade_position . ':D' . $max_row + 1);
			//次のブロック用の前にボーダー
			$sheet->getStyle('A' . $grade_position . ':' . $max_col . $max_row + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//平均偏差値
			$sheet->setCellValue($average_horizontal1 . $average_position, "平均偏差値");
			$sheet->mergeCells($average_horizontal1 . $average_position . ':' . $merge_average_horizontal . $average_position + 1);
			$sheet->getStyle($average_horizontal1 . $average_position . ':' . $merge_average_horizontal . $average_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//平均偏差値　教科1
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal1 . $average_position + 2, "国語");
			$sheet->mergeCells($average_horizontal1 . $average_position + 2 . ':' . $merge_average_horizontal1 . $average_position + 2);
			$sheet->getStyle($average_horizontal1 . $average_position + 2 . ':' . $merge_average_horizontal1 . $average_position + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//値セット
			$sheet->setCellValue($average_horizontal1 . $average_position + 3, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal1 . $average_position + 3))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal1 . $average_position + 3 . ':' . $merge_average_horizontal1 . $average_position + 5);
			$sheet->getStyle($average_horizontal1 . $average_position + 3 . ':' . $merge_average_horizontal1 . $average_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);




			//平均偏差値　教科2
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 6); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal2 . $average_position + 2, "数学");
			$sheet->mergeCells($average_horizontal2 . $average_position + 2 . ':' . $merge_average_horizontal2 . $average_position + 2);
			$sheet->getStyle($average_horizontal2 . $average_position + 2 . ':' . $merge_average_horizontal2 . $average_position + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//値セット
			$sheet->setCellValue($average_horizontal2 . $average_position + 3, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal2 . $average_position + 3))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal2 . $average_position + 3 . ':' . $merge_average_horizontal2 . $average_position + 5);
			$sheet->getStyle($average_horizontal2 . $average_position + 3 . ':' . $merge_average_horizontal2 . $average_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			//平均偏差値　教科3
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 12); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal3 . $average_position + 2, "英語");
			$sheet->mergeCells($average_horizontal3 . $average_position + 2 . ':' . $merge_average_horizontal3 . $average_position + 2);
			$sheet->getStyle($average_horizontal3 . $average_position + 2 . ':' . $merge_average_horizontal3 . $average_position + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			//値セット
			$sheet->setCellValue($average_horizontal3 . $average_position + 3, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal3 . $average_position + 3))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal3 . $average_position + 3 . ':' . $merge_average_horizontal3 . $average_position + 5);
			$sheet->getStyle($average_horizontal3 . $average_position + 3 . ':' . $merge_average_horizontal3 . $average_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			// //平均偏差値　教科4
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 15); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal1 . $average_position + 6, "3科");
			$sheet->mergeCells($average_horizontal1 . $average_position + 6 . ':' . $merge_average_horizontal1 . $average_position + 6);
			$sheet->getStyle($average_horizontal1 . $average_position + 6 . ':' . $merge_average_horizontal1 . $average_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue($average_horizontal1 . $average_position + 7, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal1 . $average_position + 7))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal1 . $average_position + 7 . ':' . $merge_average_horizontal1 . $average_position + 9);
			$sheet->getStyle($average_horizontal1 . $average_position + 7 . ':' . $merge_average_horizontal1 . $average_position + 9)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			//平均偏差値　教科5
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 9); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal2 . $average_position + 6, "理科");
			$sheet->mergeCells($average_horizontal2 . $average_position + 6 . ':' . $merge_average_horizontal2 . $average_position + 6);
			$sheet->getStyle($average_horizontal2 . $average_position + 6 . ':' . $merge_average_horizontal2 . $average_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue($average_horizontal2 . $average_position + 7, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal2 . $average_position + 7))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal2 . $average_position + 7 . ':' . $merge_average_horizontal2 . $average_position + 9);
			$sheet->getStyle($average_horizontal2 . $average_position + 7 . ':' . $merge_average_horizontal2 . $average_position + 9)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			//平均偏差値　教科5
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 3); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal3 . $average_position + 6, "社会");
			$sheet->mergeCells($average_horizontal3 . $average_position + 6 . ':' . $merge_average_horizontal3 . $average_position + 6);
			$sheet->getStyle($average_horizontal3 . $average_position + 6 . ':' . $merge_average_horizontal3 . $average_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue($average_horizontal3 . $average_position + 7, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal3 . $average_position + 7))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal3 . $average_position + 7 . ':' . $merge_average_horizontal3 . $average_position + 9);
			$sheet->getStyle($average_horizontal3 . $average_position + 7 . ':' . $merge_average_horizontal3 . $average_position + 9)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			//平均偏差値　教科6
			$subject_get_col = Coordinate::stringFromColumnIndex($subject_get_horizontal + 18); //平均偏差値　横移動用
			$sheet->setCellValue($average_horizontal4 . $average_position + 10, "5科");
			$sheet->mergeCells($average_horizontal4 . $average_position + 10 . ':' . $merge_average_horizontal4 . $average_position + 10);
			$sheet->getStyle($average_horizontal4 . $average_position + 10 . ':' . $merge_average_horizontal4 . $average_position + 10)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue($average_horizontal4 . $average_position + 11, "=IFERROR(AVERAGE(" . $subject_get_col . $subject_get . ":" . $subject_get_col . $subject_max_row . "), \"\")");
			$sheet->getStyle(($average_horizontal4 . $average_position + 11))->getNumberFormat()->setFormatCode('0.0');
			$sheet->mergeCells($average_horizontal4 . $average_position + 11 . ':' . $merge_average_horizontal4 . $average_position + 14);
			$sheet->getStyle($average_horizontal4 . $average_position + 11 . ':' . $merge_average_horizontal4 . $average_position + 14)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$grade_position =  $header_position + 1; //学年の値セット位置＝教科偏差値の位置
			$average_position =  $header_position + 1; //平均偏差値セット位置＝教科偏差値の位置
			$subject_get = $header_position + 1;
			$point_position = $point_position + 1;
			$subject_position = $subject_position + 1;
			$header_position = $header_position + 1;
			$implementation_position = $implementation_position + 1;
		}

		//入試内申点
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		$max_col = $sheet->getHighestColumn(); //右端取得

		$sheet->setCellValue('A' . $max_row + 2, '入試内申点');
		$row = $max_row + 2; //セル幅広げる用
		$sheet->getStyle('A' . $max_row + 2)->getFont()->setSize(16)->setBold(true);
		$sheet->mergeCells('A' . $max_row + 2 . ':' . $max_col . $max_row + 2);
		$sheet->getStyle('A' . $max_row + 2 . ':' . $max_col . $max_row + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		if ($school_area == 1) { //1なら大阪
			$sheet->setCellValue('A' . $max_row + 3, '※大阪公立高校(特別)は１年次の学年評定(45点)＋2年次の学年評定(45点)＋3年の学年評定(45点)×3＝225点満点(ベース点)');
			$examination_row1 = $max_row + 3; //セル幅広げる用
			$sheet->getStyle('A' . $max_row + 3)->getFont()->setSize(10)->setBold(true);
			$sheet->mergeCells('A' . $max_row + 3 . ':' . $max_col . $max_row + 3);
			$sheet->setCellValue('A' . $max_row + 4, '※大阪公立高校(一般)は１年次の学年評定(45点)×2＋2年次の学年評定(45点)×２＋3年の学年評定(45点)×6＝450点満点(ベース点)');
			$examination_row2 = $max_row + 4; //セル幅広げる用
			$sheet->getStyle('A' . $max_row + 4)->getFont()->setSize(10)->setBold(true);
			$sheet->mergeCells('A' . $max_row + 4 . ':' . $max_col . $max_row + 4);
		} else {
			$sheet->setCellValue('A' . $max_row + 3, '※奈良公立高校は2年次の平均評定(45点)＋3年1学期評定(45点)＋3年2学期評定(45点)＝135点満点(ベース点)');
			$examination_row1 = $max_row + 3; //セル幅広げる用
			$sheet->getStyle('A' . $max_row + 3)->getFont()->setSize(10)->setBold(true);
			$sheet->mergeCells('A' . $max_row + 3 . ':' . $max_col . $max_row + 3);
		}

		$examination_position = $max_row + 6;
		$examination_horizontal = 1; //A

		$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
		$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
		$sheet->setCellValue($column_examination . $examination_position, '中学1年生');
		$sheet->mergeCells($column_examination . $examination_position . ':' . $merge_examination . $examination_position + 1);

		$sheet->setCellValue($column_examination . $examination_position + 2, '学年評定');
		$sheet->mergeCells($column_examination . $examination_position + 2 . ':' . $merge_examination . $examination_position + 3);

		$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
		$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
		$sheet->setCellValue($column_examination . $examination_position + 5, '中学2年生');
		$sheet->mergeCells($column_examination . $examination_position + 5 . ':' . $merge_examination . $examination_position + 6);

		$sheet->setCellValue($column_examination . $examination_position + 7, '学年評定');
		$sheet->mergeCells($column_examination . $examination_position + 7 . ':' . $merge_examination . $examination_position + 8);

		$examination_subject_position = $max_row + 6;
		$examination_subject_horizontal = 7; //G

		$subjects = Subject::where('result_category_id', 1)->get();
		//教科値セット
		// dd($osak_annual_rating_five);
		for ($i = 0; $i < 2; $i++) {
			if ($i == 0) {
				if ($school_area == 1) { //大阪
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $osaka_annual_rating[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $osak_annual_rating_five);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $osaka_annual_rating_nine);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				} else { //奈良
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating_five);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $nara_annual_rating_nine);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				}
			} else {
				if ($school_area == 1) { //大阪
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $osaka_annual_rating2[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $osak_annual_rating_five2);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $osaka_annual_rating_nine2);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position + 5 . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				} else { //奈良
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $nara_annual_rating2[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $nara_annual_rating_five2);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $nara_annual_rating_nine2);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position + 5 . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				}
			}
			$examination_subject_horizontal = 7; //G
			$examination_subject_position = $examination_subject_position + 5;
		}

		$col = Coordinate::stringFromColumnIndex(1); //A はじめのセル
		$max_col = $sheet->getHighestColumn(); //右端取得
		$max_col_remainder = Coordinate::columnIndexFromString($max_col);
		$max_col_remainder = $max_col_remainder + 20; //右端から余分にとる
		$max_col_remainder = Coordinate::stringFromColumnIndex($max_col_remainder); //string型に戻す
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得

		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setVertical(Align::VERTICAL_CENTER); //上下中央寄せ
		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER); //左右中央寄せ

		// //セル結合処理
		$sheet->mergeCells('A1:E1');
		$sheet->mergeCells('F1:H1');
		$sheet->mergeCells('I1:W1');
		$sheet->mergeCells('X1:AF1');
		$sheet->mergeCells('AG1:AO1');
		$sheet->mergeCells('A2:' . $max_col . '2');
		$sheet->getStyle('A2:' . $max_col . '2')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN); //外周に枠つける
		//セルの幅調整
		$i = 1; //カウント用
		$j = 5; //カウント用

		while (
			$col != $max_col_remainder
		) { //右端と一致するまで回る
			$col = Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($col)->setWidth(2.5); //セルの幅調整
			$i++;
		}
		while (
			$j < $max_row
		) {
			$sheet->getRowDimension($j)->setRowHeight(12.5); //セルの高さ
			$j++;
		}
		$sheet->getRowDimension($row)->setRowHeight(20.5); //セルの高さ
		// $sheet->getPageSetup()->setPrintArea('A1:' . $max_col_remainder . $max_row); //A1から最大範囲まで印刷する
		// $sheet->getPageSetup()->setHorizontalCentered(true);
		// $sheet->getPageSetup()->setVerticalCentered(true);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToWidth(1);


		//【中３】定期年度の計算
		$select_grade = 12; //今回出力する学年　（中2）
		$difference_grade = $now_grade - $select_grade; //学年の差
		// $select_year = date('Y', strtotime('-' . $difference_grade . 'year')); //学年の差　年度を引く
		$select_year = $now_year['year'] - $difference_grade;
		// dd($select_year);

		//3枚目 シート追加
		$spreadsheet->createSheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');
		$sheet = $spreadsheet->getSheet(6);
		$sheet->setTitle('【中３】定期');

		if ($school_area == 1) {
			$sheet->setCellValue('A1', '大阪');
			$sheet->setCellValue('F1', '校舎');
		} elseif ($school_area == 2) {
			$sheet->setCellValue('A1', '奈良');
			$sheet->setCellValue('F1', '校舎');
		} else {
			$sheet->setCellValue('A1', '');
			$sheet->setCellValue('F1', '校舎');
		}
		$sheet->getStyle('A1')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('F1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('I1', '中学部　成績カルテ');
		$sheet->getStyle('I1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('X1',  $school_building->name);
		$sheet->getStyle('X1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('A2', '中学');
		$sheet->getStyle('A2')->getFont()->setSize(20)->setBold(true);



		if ($student->grade == 10) {
			$sheet->setCellValue('D2', '1');
		} elseif ($student->grade == 11) {
			$sheet->setCellValue('D2', '2');
		} elseif ($student->grade == 12) {
			$sheet->setCellValue('D2', '3');
		} else {
			$sheet->setCellValue('D2', '');
		}
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('D2')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('G2', '年');
		$sheet->getStyle('G2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('J2',  $school->name);
		$sheet->getStyle('J2')->getFont()->setSize(20)->setBold(true);
		$sheet->setCellValue('U2', '氏名');
		$sheet->getStyle('U2')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('X2', $student->surname . $student->name);
		$sheet->getStyle('X2')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('A3', '中学３年生');
		$sheet->getStyle('A3')->getFont()->setSize(16)->setBold(true);



		//成績カテゴリーの取得（中学1年）id=1 学校成績
		$resultcategorys = ResultCategory::where('id', 1)->firstOrFail();
		//実施回の取得
		$implementations = Implementation::where('result_category_id', $resultcategorys->id)->get();
		//教科の取得
		$subjects = Subject::where('result_category_id', $resultcategorys->id)->get();

		//値セット位置
		$implementation_position = 4; //実施回の値位置
		$implementation_horizontal = 3; //C
		$header_position = 4; //見出しや項目の表示の位置
		$subject_position = 4; //教科の値位置
		$subject_horizontal = 7; //教科の横移動用　G
		$point_position = 5; //点数の値位置
		$point_horizontal = 7; //点数の横移動用　G

		$average_point_position = 7; //学校平均点数の値位置
		$average_point_horizontal = 7; //学校平均点数の横移動用　G

		$average_difference_position = 9; //平均との差値位置
		$average_difference_horizontal = 7; //平均との差横移動用　

		$term_average_get_start_horizontal1 = 7; //5教科の取得スタート位置
		$term_average_get_start_horizontal2 = 25; //9教科の取得スタート位置

		$term_average_horizontal1 = 7; //学期平均

		//実施回の値セット
		foreach ($implementations as $implementationkey => $implementation) {
			$sheet->setCellValue('A' . $implementation_position, $implementation->implementation_name);
			$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal); //K→N
			$sheet->mergeCells('A' . $implementation_position . ':' . $merge_implementation . $implementation_position + 6);
			$sheet->getStyle('A' . $implementation_position . ':' . $merge_implementation . $implementation_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position, "教科");
			$sheet->mergeCells('D' . $header_position . ':F' . $header_position);
			$sheet->getStyle('D' . $header_position . ':F' . $header_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position + 1, "点数");
			$sheet->mergeCells('D' . $header_position + 1 . ':F' . $header_position + 2);
			$sheet->getStyle('D' . $header_position + 1 . ':F' . $header_position + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position + 3, "学校平均");
			$sheet->mergeCells('D' . $header_position + 3 . ':F' . $header_position + 4);
			$sheet->getStyle('D' . $header_position + 3 . ':F' . $header_position + 4)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$sheet->setCellValue('D' . $header_position + 5, "平均との差");
			$sheet->mergeCells('D' . $header_position + 5 . ':F' . $header_position + 6);
			$sheet->getStyle('D' . $header_position + 5 . ':F' . $header_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			foreach ($subjects as $subjectkey => $subject) {
				$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する
				if (mb_strlen($subject->subject_name) < 5) {
					$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
				} else {
					$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
					$sheet->getStyle($column_subject . $subject_position)->getFont()->setSize(9)->setBold(true);
				}
				$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
				$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
				$sheet->getStyle($column_subject . $subject_position . ':' . $merge_subject . $subject_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

				//点数セット
				$studentresults = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 12)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
				if ($studentresults->isNotEmpty()) {
					foreach ($studentresults as $resultkey => $studentresult) {
						$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
						$sheet->setCellValue($column_point . $point_position,  $studentresult->point);
						$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
						$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
						$point_horizontal = $point_horizontal + 3;
					}
				} else {
					$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
					$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
					$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
					$point_horizontal = $point_horizontal + 3;
				}
				$sheet->getStyle($column_point . $point_position . ':' . $merge_point . $point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

				//学校平均セット
				$averagepoints = AveragePoint::where('school_id', $student->school_id)->where('year', $select_year)->where('grade', 12)->where('result_category_id', $resultcategorys->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
				// dd($averagepoints);
				if ($averagepoints->isNotEmpty()) {
					foreach ($averagepoints as $averagepointkey => $averagepoint) {
						$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
						$sheet->setCellValue($column_average_point . $average_point_position,  $averagepoint->average_point);
						$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
						$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
						$average_point_horizontal = $average_point_horizontal + 3;
					}
				} else {
					$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
					$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
					$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
					$average_point_horizontal = $average_point_horizontal + 3;
				}
				$sheet->getStyle($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


				//平均との差
				$column_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal); //G指定する
				$sheet->setCellValue($column_point_difference . $average_difference_position, "=" . $column_point_difference . $average_difference_position - 4 . "-" . $column_point_difference . $average_difference_position - 2);
				$merge_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal + 2);
				$sheet->mergeCells($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1);
				$sheet->getStyle($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



				$term_average_position = $average_difference_position; //学期の平均位置
				$average_difference_horizontal = $average_difference_horizontal + 3;

				//５教科平均点
				if ($subjectkey == 4) {
					$subject_horizontal = $subject_horizontal + 3;
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する
					$sheet->setCellValue($column_subject . $subject_position, "5科目合計");
					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$sheet->getStyle($column_subject . $subject_position . ':' . $merge_subject . $subject_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
					$column_term_average_get_start_horizontal1 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal1); //G指定する
					$column_term_average_get_total_horizontal = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal1 - 3); //5教科合計取得位置
					$column_term_average_get_end_horizontal1 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal1 + 12); //U指定する

					$sheet->setCellValue($column_point . $point_position, "=SUM(" . $column_term_average_get_start_horizontal1 . $point_position . ":" . $column_term_average_get_end_horizontal1 . $point_position . ")");
					$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
					$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
					$sheet->getStyle($column_point . $point_position . ':' . $merge_point . $point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

					$point_horizontal = $point_horizontal + 3;



					$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
					$sheet->setCellValue($column_average_point . $average_point_position, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position . ":" . $column_term_average_get_end_horizontal1 . $average_point_position . ")");
					$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
					$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
					$average_point_horizontal = $average_point_horizontal + 3;
					$sheet->getStyle($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal); //G指定する
					// dd($column_point_difference);
					// $sheet->setCellValue($column_point_difference . $average_difference_position, "=V5-V7");
					$sheet->setCellValue($column_point_difference . $average_difference_position, '=' . $column_point_difference . $average_difference_position - 4 . '-' . $column_point_difference . $average_difference_position - 2);
					$merge_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal + 2);
					$sheet->mergeCells($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1);
					$sheet->getStyle($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

					$average_difference_horizontal = $average_difference_horizontal + 3;
				}
				//9教科平均点
				if ($subjectkey == 8) {
					$subject_horizontal = $subject_horizontal + 3;
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する
					$sheet->setCellValue($column_subject . $subject_position, "9科目合計");
					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$sheet->getStyle($column_subject . $subject_position . ':' . $merge_subject . $subject_position)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
					$column_term_average_get_start_horizontal2 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal2); //Y指定する
					$column_totalpoint = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal2 - 3); //5教科合計の取得
					$column_term_average_get_end_horizontal2 = Coordinate::stringFromColumnIndex($term_average_get_start_horizontal2 + 9); //Y指定する
					// $sheet->setCellValue($column_point . $point_position, "=SUM(G5:U6)");
					$sheet->setCellValue($column_point . $point_position, "=SUM(" . $column_totalpoint . $point_position . "," . $column_term_average_get_start_horizontal2 . $point_position . ":" . $column_term_average_get_end_horizontal2 . $point_position . ")");
					$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
					$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
					$sheet->getStyle($column_point . $point_position . ':' . $merge_point . $point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

					$point_horizontal = $point_horizontal + 3;


					$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal); //G指定する
					$sheet->setCellValue($column_average_point . $average_point_position, "=SUM(" . $column_totalpoint . $average_point_position  . "," . $column_term_average_get_start_horizontal2 . $average_point_position . ":" . $column_term_average_get_end_horizontal2 . $average_point_position . ")");
					$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
					$sheet->mergeCells($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1);
					$average_point_horizontal = $average_point_horizontal + 3;
					$sheet->getStyle($column_average_point . $average_point_position . ':' . $merge_average_point . $average_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



					$column_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal); //G指定する
					$sheet->setCellValue($column_point_difference . $average_difference_position, '=' . $column_point_difference . $average_difference_position - 4 . '-' . $column_point_difference . $average_difference_position - 2);

					$merge_point_difference = Coordinate::stringFromColumnIndex($average_difference_horizontal + 2);
					$sheet->mergeCells($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1);
					$sheet->getStyle($column_point_difference . $average_difference_position . ':' . $merge_point_difference . $average_difference_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


					$average_difference_horizontal = $average_difference_horizontal + 3;
				}

				$subject_horizontal = $subject_horizontal + 3;
			}

			//平均と評定を表示
			//成績カテゴリーの取得（中学1年）id=2 通知表

			if ($implementation->implementation_name == "1学期末" || $implementation->implementation_name == "2学期末" || $implementation->implementation_name == "学期末") {
				$resultcategory_rating = ResultCategory::where('id', 2)->firstOrFail();
				//通知表の実施回の取得
				//学校成績の実施回で取得する通知表変わる
				if ($implementation->implementation_name == "1学期末") {
					$implementation_rating = Implementation::where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 1)->firstOrFail(); //1学期
				} elseif ($implementation->implementation_name == "2学期末") {
					$implementation_rating = Implementation::where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 2)->firstOrFail(); //2学期
				} elseif ($implementation->implementation_name == "学期末") {
					$implementation_rating = Implementation::where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 3)->firstOrFail(); //3学期
				}

				//教科の取得
				$subject_ratings = Subject::where('result_category_id', $resultcategory_rating->id)->get();

				$implementation_position = $implementation_position + 5; //実施回の値位置
				$header_position = $header_position + 5; //見出しや項目の表示の位置
				$subject_position = $subject_position + 5; //教科の値位置
				$point_position = $point_position + 5; //点数の値位置
				$average_point_position = $average_point_position + 5; //点数の値位置
				$average_difference_position = $average_difference_position + 5; //点数の値位置

				foreach ($subject_ratings as $subject_rating_key => $subject_rating) {

					if ($implementation_rating->implementation_name == "1学期評定") {
						$studentratingpoints = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 12)->where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 1)->where('subject_no', $subject_rating->subject_no)->get();
						//1学期平均
						$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
						$sheet->setCellValue('A' . $implementation_position + 2, "1学期平均");
						$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
						$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


						$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
						$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
						$sheet->setCellValue($column_term_average . $term_average_position + 2, "=IFERROR(AVERAGE(" . $column_term_average . $term_average_position - 11 . "," . $column_term_average . $term_average_position - 4 . "), \"\")");
						$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
						$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


						//1学期評定
						$sheet->setCellValue('A' . $implementation_position + 4, "1学期評定");
						$sheet->mergeCells('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5);
						$sheet->getStyle('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						if ($studentratingpoints->isNotEmpty()) {
							foreach ($studentratingpoints as $tudentratingpointkey => $studentratingpoint) {
								$sheet->setCellValue($column_term_average . $term_average_position + 4, $studentratingpoint->point);
								$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						} else {
							$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
							$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						$nara_annual_rating3[] = $column_term_average . $term_average_position + 4;

						if ($subject_rating_key == 4) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($column_term_average . $average_point_position);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_term_average . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

							$nara_annual_rating_five3[] = $column_term_average . $average_point_position + 1;
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_term_average . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
						if ($subject_rating_key == 8) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($merge_term_average);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM("  . $column_totalpoint . $average_point_position - 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_average_point . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							$nara_annual_rating_nine3[] = $column_term_average . $average_point_position + 1;
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_totalpoint . $average_point_position + 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position + 1 . ")");
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
					} elseif ($implementation_rating->implementation_name == "2学期評定") {
						$studentratingpoints = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 12)->where('result_category_id', $resultcategory_rating->id)->where('implementation_no', 2)->where('subject_no', $subject_rating->subject_no)->get();
						//2学期平均
						$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
						$sheet->setCellValue('A' . $implementation_position + 2, "2学期平均");
						$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
						$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

						$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
						$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
						// $sheet->setCellValue($column_term_average . $term_average_position + 2, "=" . $column_point_difference . $average_difference_position - 4 . "-" . $column_point_difference . $average_difference_position - 2);
						$sheet->setCellValue($column_term_average . $term_average_position + 2, "=IFERROR(AVERAGE(" . $column_term_average . $term_average_position - 11 . "," . $column_term_average . $term_average_position - 4 . "), \"\")");
						$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
						$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


						//2学期評定
						$sheet->setCellValue('A' . $implementation_position + 4, "2学期評定");
						$sheet->mergeCells('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5);
						$sheet->getStyle('A' . $implementation_position + 4 . ':' . $merge_implementation . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						if ($studentratingpoints->isNotEmpty()) {
							foreach ($studentratingpoints as $tudentratingpointkey => $studentratingpoint) {
								$sheet->setCellValue($column_term_average . $term_average_position + 4, $studentratingpoint->point);
								$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						} else {
							$sheet->mergeCells($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5);
							$sheet->getStyle($column_term_average . $term_average_position + 4 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						$nara_annual_rating4[] = $column_term_average . $term_average_position + 4;

						if ($subject_rating_key == 4) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($column_term_average . $average_point_position);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_term_average . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

							$nara_annual_rating_five3[] = $column_term_average . $average_point_position + 1;


							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_term_average . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
						if ($subject_rating_key == 8) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($merge_term_average);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM("  . $column_totalpoint . $average_point_position - 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position - 1 . ")");
							// $sheet->getStyle(($column_average_point . $average_point_position - 1))->getNumberFormat()->setFormatCode('0.0');
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							$nara_annual_rating_nine3[] = $column_term_average . $average_point_position + 1;
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_totalpoint . $average_point_position + 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position + 1 . ")");
								// $sheet->getStyle(($column_average_point . $average_point_position + 1))->getNumberFormat()->setFormatCode('0.0');
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
					} elseif ($implementation_rating->implementation_name == "3学期評定") {
						//3学期評定
						if ($school_area == 1) { //大阪
							$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
							$sheet->setCellValue('A' . $implementation_position + 2, "学年評定");
							$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
							$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						} else { //奈良
							$merge_implementation = Coordinate::stringFromColumnIndex($implementation_horizontal + 3);
							$sheet->setCellValue('A' . $implementation_position + 2, "3学期評定");
							$sheet->mergeCells('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3);
							$sheet->getStyle('A' . $implementation_position + 2 . ':' . $merge_implementation . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
						$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
						if ($studentratingpoints->isNotEmpty()) {
							foreach ($studentratingpoints as $tudentratingpointkey => $studentratingpoint) {
								$sheet->setCellValue($column_term_average . $term_average_position + 2, $studentratingpoint->point);
								$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
								$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						} else {
							$sheet->mergeCells($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $term_average_position + 2 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						}
						$osaka_annual_rating3[] = $column_term_average . $term_average_position + 2;

						if ($subject_rating_key == 4) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($column_term_average . $average_point_position);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position - 1 . ")");
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							$osak_annual_rating_five3 = $column_term_average . $average_point_position - 1;


							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_term_average_get_start_horizontal1 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal1 . $average_point_position + 1 . ")");
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
						if ($subject_rating_key == 8) {
							$term_average_horizontal1 = $term_average_horizontal1 + 3;
							$column_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1);
							$merge_term_average = Coordinate::stringFromColumnIndex($term_average_horizontal1 + 2);
							// dd($merge_term_average);
							$sheet->setCellValue($column_term_average . $average_point_position - 1, "=SUM("  . $column_totalpoint . $average_point_position - 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position - 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position - 1 . ")");
							$sheet->mergeCells($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3);
							$sheet->getStyle($column_term_average . $average_point_position - 1 . ':' . $merge_term_average . $implementation_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							$osaka_annual_rating_nine3 = $column_term_average . $average_point_position - 1;
							if ($implementation->implementation_name != "学期末") {
								$sheet->setCellValue($column_term_average . $average_point_position + 1, "=SUM(" . $column_totalpoint . $average_point_position + 1  . "," . $column_term_average_get_start_horizontal2 . $average_point_position + 1 . ":" . $column_term_average_get_end_horizontal2 . $average_point_position + 1 . ")");
								$sheet->mergeCells($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5);
								$sheet->getStyle($column_term_average . $average_point_position + 1 . ':' . $merge_term_average . $implementation_position + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
							}
						}
					}

					$term_average_horizontal1 = $term_average_horizontal1 + 3;
				}
				$term_average_horizontal1 = 7; //学期平均
			}

			//次のブロック用に横移動変数リセット
			$subject_horizontal = 7; //教科の横移動用　G
			$point_horizontal = 7; //点数の横移動用　G
			$average_point_horizontal = 7; //学校平均点数の横移動用　G
			$average_difference_horizontal = 7; //平均との差横移動用　

			//次のブロックへ
			$implementation_position = $implementation_position + 7; //実施回の値位置
			$header_position = $header_position + 7; //見出しや項目の表示の位置
			$subject_position = $subject_position + 7; //教科の値位置
			$point_position = $point_position + 7; //点数の値位置
			$average_point_position = $average_point_position + 7; //点数の値位置
			$average_difference_position = $average_difference_position + 7; //点数の値位置
		}

		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		$max_col = $sheet->getHighestColumn(); //右端取得

		//奈良なら年間平均評定表示
		if ($school_area == 2) {
			$header_horizontal1 = 6; //年間平均評定表示の見出し
			$annual_rating_position = $max_row + 2;
			$annual_rating_horizontal1 = 7;
			$annual_rating_start_horizontal1 = 7;
			$annual_rating_start_horizontal2 = 22;
			// $count = 1;
			// dd($annual_rating['3学期評定']);
			$merge_header = Coordinate::stringFromColumnIndex($header_horizontal1); //K→N
			$sheet->setCellValue('A' . $max_row + 2, '年間平均評定');
			$sheet->mergeCells('A' . $max_row + 2 . ':' . $merge_header . $max_row + 3);
			$sheet->getStyle('A' . $max_row + 2 . ':' . $merge_header . $max_row + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

			foreach ($subjects as $subjectkey => $subject) { //教科の数回る
				$annual_average_rating = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 12)->where('result_category_id', 2)->where('implementation_no', 4)->where('subject_no', $subject->subject_no)->first();
				$column_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1);
				$merge_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1 + 2);
				if ($annual_average_rating != null) {
					$sheet->setCellValue($column_annual_rating . $annual_rating_position, $annual_average_rating->point ?? "=IFERROR(AVERAGE(" . $annual_rating['1学期評定'][$subjectkey] . "," . $annual_rating['2学期評定'][$subjectkey] . "," . $annual_rating['3学期評定'][$subjectkey] . "),)");
				} else {
					$sheet->setCellValue($column_annual_rating . $annual_rating_position, "=IFERROR(AVERAGE(" . $annual_rating['1学期評定'][$subjectkey] . "," . $annual_rating['2学期評定'][$subjectkey] . "," . $annual_rating['3学期評定'][$subjectkey] . "),)");
				}
				$nara_annual_rating[] = $column_annual_rating . $annual_rating_position;
				$sheet->getStyle(($column_annual_rating . $annual_rating_position))->getNumberFormat()->setFormatCode('0');
				$sheet->mergeCells($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1);
				$sheet->getStyle($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				$annual_rating_horizontal1 = $annual_rating_horizontal1 + 3;

				if ($subjectkey == 4) {
					$column_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1);
					$merge_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1 + 2);
					$annual_rating_start = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal1);
					$annual_rating_end = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal1 + 12);

					$sheet->setCellValue($column_annual_rating . $annual_rating_position, "=SUM(" . $annual_rating_start . $annual_rating_position . ":" . $annual_rating_end . $annual_rating_position . ")");
					$nara_annual_rating_five = $column_annual_rating . $annual_rating_position;
					$sheet->getStyle(($column_annual_rating . $annual_rating_position))->getNumberFormat()->setFormatCode('0');
					$sheet->mergeCells($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1);
					$sheet->getStyle($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
					$annual_rating_horizontal1 = $annual_rating_horizontal1 + 3;
				}
				if ($subjectkey == 8) {
					$column_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1);
					$merge_annual_rating = Coordinate::stringFromColumnIndex($annual_rating_horizontal1 + 2);
					$annual_rating_start2 = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal2);
					$annual_rating_end2 = Coordinate::stringFromColumnIndex($annual_rating_start_horizontal2 + 12);
					$sheet->setCellValue($column_annual_rating . $annual_rating_position, "=SUM(" . $annual_rating_start2 . $annual_rating_position . ":" . $annual_rating_end2 . $annual_rating_position . ")");
					$nara_annual_rating_nine = $column_annual_rating . $annual_rating_position;
					$sheet->getStyle(($column_annual_rating . $annual_rating_position))->getNumberFormat()->setFormatCode('0');
					$sheet->mergeCells($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1);
					$sheet->getStyle($column_annual_rating . $annual_rating_position . ':' . $merge_annual_rating . $annual_rating_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
					$annual_rating_horizontal1 = $annual_rating_horizontal1 + 3;
				}
			}
			// dd($nara_annual_rating);
		}


		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		$max_col = $sheet->getHighestColumn(); //右端取得

		$transition_position = $max_row + 6;

		$transition_horizontal = 6; //F

		$sheet->setCellValue('A' . $max_row + 2, '成績の推移');
		$row = $max_row + 2; //セル幅広げる用
		$sheet->getStyle('A' . $max_row + 2)->getFont()->setSize(16)->setBold(true);
		$sheet->mergeCells('A' . $max_row + 2 . ':' . $max_col . $max_row + 2);
		$sheet->getStyle('A' . $max_row + 2 . ':' . $max_col . $max_row + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		$sheet->setCellValue('F' . $max_row + 4, '1学期中間');
		$sheet->mergeCells('F' . $max_row + 4 . ':' . 'L' . $max_row + 5);
		$sheet->getStyle('F' . $max_row + 4 . ':' . 'L' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		//1学期中間の値セット
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 6); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[0]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[0]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('M' . $max_row + 4, '1学期期末');
		$sheet->mergeCells('M' . $max_row + 4 . ':' . 'S' . $max_row + 5);
		$sheet->getStyle('M' . $max_row + 4 . ':' . 'S' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		//1学期期末の値セット
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 7); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 13); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[1]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[1]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('T' . $max_row + 4, '2学期中間');
		$sheet->mergeCells('T' . $max_row + 4 . ':' . 'Z' . $max_row + 5);
		$sheet->getStyle('T' . $max_row + 4 . ':' . 'Z' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		//2学期中間値セット
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 14); //F+7
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 20); //結合用+7
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[2]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[2]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('AA' . $max_row + 4, '2学期期末');
		$sheet->mergeCells('AA' . $max_row + 4 . ':' . 'AG' . $max_row + 5);
		$sheet->getStyle('AA' . $max_row + 4 . ':' . 'AG' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 21); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 27); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[3]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[3]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('AH' . $max_row + 4, '学年末');
		$sheet->mergeCells('AH' . $max_row + 4 . ':' . 'AN' . $max_row + 5);
		$sheet->getStyle('AH' . $max_row + 4 . ':' . 'AN' . $max_row + 5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 28); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 34); //結合用
		//5科点数
		$sheet->setCellValue($column_transition . $transition_position, '=' . $total_point_position[4]);
		$sheet->mergeCells($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1);
		$sheet->getStyle($column_transition . $transition_position . ':' . $merge_transition . $transition_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		//5科平均
		$sheet->setCellValue($column_transition . $transition_position + 2, '=' . $total_average_point_position[4]);
		$sheet->mergeCells($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3);
		$sheet->getStyle($column_transition . $transition_position + 2 . ':' . $merge_transition . $transition_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$sheet->setCellValue('B' . $max_row + 6, '5科点数');
		$sheet->mergeCells('B' . $max_row + 6 . ':' . 'E' . $max_row + 7);
		$sheet->getStyle('B' . $max_row + 6 . ':' . 'E' . $max_row + 7)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$column_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 35); //F
		$merge_transition = Coordinate::stringFromColumnIndex($transition_horizontal + 41); //結合用

		$sheet->setCellValue('B' . $max_row + 8, '5科平均');
		$sheet->mergeCells('B' . $max_row + 8 . ':' . 'E' . $max_row + 9);
		$sheet->getStyle('B' . $max_row + 8 . ':' . 'E' . $max_row + 9)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


		$col = Coordinate::stringFromColumnIndex(1); //A はじめのセル
		$max_col = $sheet->getHighestColumn(); //右端取得
		$max_col_remainder = Coordinate::columnIndexFromString($max_col);
		$max_col_remainder = $max_col_remainder + 20; //右端から余分にとる
		$max_col_remainder = Coordinate::stringFromColumnIndex($max_col_remainder); //string型に戻す
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得

		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setVertical(Align::VERTICAL_CENTER); //上下中央寄せ
		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER); //左右中央寄せ

		// //セル結合処理
		$sheet->mergeCells('A1:E1');
		$sheet->mergeCells('F1:H1');
		$sheet->mergeCells('I1:W1');
		$sheet->mergeCells('X1:AF1');
		$sheet->mergeCells('AG1:AO1');
		$sheet->mergeCells('A2:C2');
		$sheet->mergeCells('D2:F2');
		$sheet->mergeCells('G2:H2');
		$sheet->mergeCells('J2:S2');
		$sheet->mergeCells('U2:W2');
		$sheet->mergeCells('X2:AG2');
		$sheet->mergeCells('A3:' . $max_col . '3');
		$sheet->getStyle('A3:' . $max_col . '3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN); //外周に枠つける
		//セルの幅調整
		$i = 1; //カウント用
		$j = 5; //カウント用

		while (
			$col != $max_col_remainder
		) { //右端と一致するまで回る
			$col = Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($col)->setWidth(2.5); //セルの幅調整
			$i++;
		}
		while ($j < $max_row) {
			$sheet->getRowDimension($j)->setRowHeight(12.5); //セルの高さ
			$j++;
		}

		$sheet->getRowDimension($row)->setRowHeight(20.5); //セルの高さ
		// $sheet->getPageSetup()->setPrintArea('A1:' . $max_col_remainder . $max_row); //A1から最大範囲まで印刷する
		// $sheet->getPageSetup()->setFitToWidth(1);

		// $sheet->getPageSetup()->setHorizontalCentered(true);
		// $sheet->getPageSetup()->setVerticalCentered(true);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToWidth(1);


		$spreadsheet->createSheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');
		$sheet = $spreadsheet->getSheet(7);
		$sheet->setTitle('【中３】もし');

		if ($school_area == 1) { //1なら大阪
			$sheet->setCellValue('A1', '大阪');
			$sheet->setCellValue('F1', '校舎');
		} elseif ($school_area == 2) { //２なら奈良
			$sheet->setCellValue('A1', '奈良');
			$sheet->setCellValue('F1', '校舎');
		} else {
			$sheet->setCellValue('A1', '');
			$sheet->setCellValue('F1', '校舎');
		}
		$sheet->getStyle('A1')->getFont()->setSize(20)->setBold(true);
		$sheet->getStyle('F1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('I1', '中学部　成績カルテ');
		$sheet->getStyle('I1')->getFont()->setSize(20)->setBold(true);


		$sheet->setCellValue('X1',  $school_building->name);
		$sheet->getStyle('X1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('AG1', $student->surname . $student->name);
		$sheet->getStyle('AG1')->getFont()->setSize(20)->setBold(true);

		$sheet->setCellValue('A2', '塾内テスト・模試成績推移');
		$sheet->getStyle('A2')->getFont()->setSize(16)->setBold(true);

		// //成績カテゴリーの取得 塾内テストの取得
		$resultcategorys = ResultCategory::where('id', '!=', 1)->where('id', '!=', 2)->get(); //学校成績除く

		$resultcategory_position = 4;
		$implementation_position = 4;
		$header_position = 4; //見出しや項目の表示の位置

		$subject_position = 4;
		$subject_horizontal = 11;

		$point_position = 5; //点数の値位置
		$point_horizontal = 11; //点数の横移動用　K=11

		$average_point_horizontal = 11;

		//試験の値セット
		foreach ($resultcategorys as $resultcategorykey => $resultcategory) {
			if ($resultcategory->result_category_name == "学力診断テスト/実力テスト") {
				if ($school_area == 1) {
					$sheet->setCellValue('A' . $resultcategory_position, "実力テスト");
				} else {
					$sheet->setCellValue('A' . $resultcategory_position, "学力診断テスト");
				}
			} else {
				$sheet->setCellValue('A' . $resultcategory_position, $resultcategory->result_category_name);
			}
			//実施回の取得
			$implementations = Implementation::where('result_category_id', $resultcategory->id)->get();
			// dd($implementations);
			foreach ($implementations as $implementationkey => $implementation) {

				$sheet->setCellValue('H' . $header_position, "教科");
				$sheet->mergeCells('H' . $header_position . ':J' . $header_position);

				if ($resultcategory->average_point_flg == 1) {
					$sheet->setCellValue('H' . $header_position + 1, "点数");
					$sheet->mergeCells('H' . $header_position + 1 . ':J' . $header_position + 2);
				} elseif ($resultcategory->result_category_name == "通知表" || $resultcategory->result_category_name == "公立入試") {
					$sheet->setCellValue('H' . $header_position + 1, "素点");
					$sheet->mergeCells('H' . $header_position + 1 . ':J' . $header_position + 2);
				} else {
					$sheet->setCellValue('H' . $header_position + 1, "偏差値");
					$sheet->mergeCells('H' . $header_position + 1 . ':J' . $header_position + 2);
				}
				$sheet->setCellValue('E' . $implementation_position, $implementation->implementation_name);

				if ($resultcategory->average_point_flg == 1) {
					$sheet->mergeCells('E' . $implementation_position . ':G' . $implementation_position + 3);
				} else {
					$sheet->mergeCells('E' . $implementation_position . ':G' . $implementation_position + 2);
				}

				//教科の取得
				$subjects = Subject::where('result_category_id', $resultcategory->id)->get();
				foreach ($subjects as $subjectkey => $subject) {
					$column_subject = Coordinate::stringFromColumnIndex($subject_horizontal); //G指定する

					if ($subject->subject_name == '2科/3科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "3科");
					} elseif ($subject->subject_name == '3科/5科平均') {
						$sheet->setCellValue($column_subject . $subject_position, "5科");
					} else {
						$sheet->setCellValue($column_subject . $subject_position, $subject->subject_name);
					}

					$merge_subject = Coordinate::stringFromColumnIndex($subject_horizontal + 2); //K→N
					$sheet->mergeCells($column_subject . $subject_position . ':' . $merge_subject . $subject_position);
					$subject_horizontal = $subject_horizontal + 3;

					//生徒成績取得
					$studentresults = StudentResult::where('student_no', $student->student_no)->where('year', $select_year)->where('grade', 12)->where('result_category_id', $resultcategory->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
					if ($studentresults->isNotEmpty()) {
						foreach ($studentresults as $resultkey => $studentresult) {
							$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
							$sheet->setCellValue($column_point . $point_position,  $studentresult->point);
							$sheet->getStyle($column_point . $point_position)->getFill()->setFillType('solid')->getStartColor()->setARGB('palegreen');
							$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
							$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
							$point_horizontal = $point_horizontal + 3;
						}
					} else {
						$column_point = Coordinate::stringFromColumnIndex($point_horizontal); //K指定する
						$merge_point = Coordinate::stringFromColumnIndex($point_horizontal + 2); //K→N
						$sheet->mergeCells($column_point . $point_position . ':' . $merge_point . $point_position + 1);
						$point_horizontal = $point_horizontal + 3;
					}
					if ($subject->subject_name == "2科/3科平均") {
						$average2to3[] = $column_point . $point_position;
					}
					if ($subject->subject_name == "3科/5科平均") {
						$average4to5[] = $column_point . $point_position;
					}
					//進路希望のシート用に取得
					if ($resultcategory->result_category_name == "Vもし") {
						if (strpos($implementation->implementation_name, "6月") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$vmoshi_6m_point = $column_point . $point_position;
							}
						} elseif (strpos($implementation->implementation_name, "8月") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$vmoshi_8m_point = $column_point . $point_position;
							}
						}
					}
					if ($resultcategory->result_category_name == "五ツ木模試") {
						if (strpos($implementation->implementation_name, "9月") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$itsuki_9m_point = $column_point . $point_position;
							}
						} elseif (strpos($implementation->implementation_name, "10月") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$itsuki_10m_point = $column_point . $point_position;
							}
						} elseif (strpos($implementation->implementation_name, "11月") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$itsuki_11m_point = $column_point . $point_position;
							}
						} elseif (strpos($implementation->implementation_name, "12月") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$itsuki_12m_point = $column_point . $point_position;
							}
						}
					}
					if (strpos($resultcategory->result_category_name, "実力テスト") !== false) {
						if (strpos($implementation->implementation_name, "2回") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$test_2_point = $column_point . $point_position;
							}
						} elseif (strpos($implementation->implementation_name, "3回") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$test_3_point = $column_point . $point_position;
							}
						} elseif (strpos($implementation->implementation_name, "4回") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$test_4_point = $column_point . $point_position;
							}
						} elseif (strpos($implementation->implementation_name, "5回") !== false) {
							if ($subject->subject_name == "3科/5科平均") {
								$test_5_point = $column_point . $point_position;
							}
						}
					}
				}
				if ($resultcategory->average_point_flg == 1) {
					$point_position = $point_position + 1;
					$subject_position = $subject_position + 1;
					$implementation_position = $implementation_position + 1;
					$header_position = $header_position + 1;
					$average_position = $header_position + 2;

					$sheet->setCellValue('H' . $header_position + 2, "平均");
					$sheet->mergeCells('H' . $header_position + 2 . ':J' . $header_position + 2);


					foreach ($subjects as $subjectkey => $subject) {
						$column_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal);
						$merge_average_point = Coordinate::stringFromColumnIndex($average_point_horizontal + 2);
						$averagepoints = AveragePoint::where('school_id', $student->school_id)->where('year', $select_year)->where('grade', 12)->where('result_category_id', $resultcategory->id)->where('implementation_no', $implementation->implementation_no)->where('subject_no', $subject->subject_no)->get();
						if ($averagepoints->isNotEmpty()) {
							foreach ($averagepoints as $averagepointkey => $averagepoint) {
								$sheet->setCellValue($column_average_point . $average_position,  $averagepoint->average_point);
								$sheet->mergeCells($column_average_point . $average_position . ':' . $merge_average_point . $average_position);
							}
						} else {
							$sheet->mergeCells($column_average_point . $average_position . ':' . $merge_average_point . $average_position);
						}
						$average_point_horizontal = $average_point_horizontal + 3;
						if (strpos($resultcategory->result_category_name, "実力テスト") !== false) {
							if (strpos($implementation->implementation_name, "2回") !== false) {
								if ($subject->subject_name == "3科/5科平均") {
									$test_2_average_point = $column_average_point . $average_position;
								}
							} elseif (strpos($implementation->implementation_name, "3回") !== false) {
								if ($subject->subject_name == "3科/5科平均") {
									$test_3_average_point = $column_average_point . $average_position;
								}
							} elseif (strpos($implementation->implementation_name, "4回") !== false) {
								if ($subject->subject_name == "3科/5科平均") {
									$test_4_average_point = $column_average_point . $average_position;
								}
							} elseif (strpos($implementation->implementation_name, "5回") !== false) {
								if ($subject->subject_name == "3科/5科平均") {
									$test_5_average_point = $column_average_point . $average_position;
								}
							}
						}
					}
				}
				$max_col = $subject_horizontal - 1; //右端用変数
				$max_col = Coordinate::stringFromColumnIndex($max_col);
				$max_row = $point_position; //一番最後の値セット位置
				$subject_max_row = $point_position; //一番最後の値セット位置

				$implementation_position = $implementation_position + 3; //実施回の値位置変更
				$header_position = $header_position + 3; //見出しや項目の表示の位置
				$subject_position = $subject_position + 3; //教科の値位置
				$subject_horizontal = 11; //教科の横移動用リセット
				$point_position = $point_position + 3; //点数の位置変更
				$point_horizontal = 11; //点数横位置リセット
				$average_point_horizontal = 11;
			}
			$sheet->mergeCells('A' . $resultcategory_position . ':D' . $max_row + 1);
			$sheet->getStyle('A' . $resultcategory_position . ':' . $max_col . $max_row + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$resultcategory_position = $header_position + 1;
			$point_position = $point_position + 1;
			$subject_position = $subject_position + 1;
			$header_position = $header_position + 1;
			$implementation_position = $implementation_position + 1;
		}

		//入試内申点
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		$max_col = $sheet->getHighestColumn(); //右端取得

		$sheet->setCellValue('A' . $max_row + 2, '入試内申点');
		$row = $max_row + 2; //セル幅広げる用
		$sheet->getStyle('A' . $max_row + 2)->getFont()->setSize(16)->setBold(true);
		$sheet->mergeCells('A' . $max_row + 2 . ':' . $max_col . $max_row + 2);
		$sheet->getStyle('A' . $max_row + 2 . ':' . $max_col . $max_row + 2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

		if ($school_area == 1) { //1なら大阪
			$sheet->setCellValue('A' . $max_row + 3, '※大阪公立高校(特別)は１年次の学年評定(45点)＋2年次の学年評定(45点)＋3年の学年評定(45点)×3＝225点満点(ベース点)');
			$examination_row1 = $max_row + 3; //セル幅広げる用
			$sheet->getStyle('A' . $max_row + 3)->getFont()->setSize(10)->setBold(true);
			$sheet->mergeCells('A' . $max_row + 3 . ':' . $max_col . $max_row + 3);
			$sheet->setCellValue('A' . $max_row + 4, '※大阪公立高校(一般)は１年次の学年評定(45点)×2＋2年次の学年評定(45点)×２＋3年の学年評定(45点)×6＝450点満点(ベース点)');
			$examination_row2 = $max_row + 4; //セル幅広げる用
			$sheet->getStyle('A' . $max_row + 4)->getFont()->setSize(10)->setBold(true);
			$sheet->mergeCells('A' . $max_row + 4 . ':' . $max_col . $max_row + 4);
		} else {
			$sheet->setCellValue('A' . $max_row + 3, '※奈良公立高校は2年次の平均評定(45点)＋3年1学期評定(45点)＋3年2学期評定(45点)＝135点満点(ベース点)');
			$examination_row1 = $max_row + 3; //セル幅広げる用
			$sheet->getStyle('A' . $max_row + 3)->getFont()->setSize(10)->setBold(true);
			$sheet->mergeCells('A' . $max_row + 3 . ':' . $max_col . $max_row + 3);
		}

		$examination_position = $max_row + 6;
		$examination_horizontal = 1; //A

		$subjects = Subject::where('result_category_id', 1)->get();


		if ($school_area == 1) { //1なら大阪
			$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
			$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
			$sheet->setCellValue($column_examination . $examination_position, '中学1年生');
			$sheet->mergeCells($column_examination . $examination_position . ':' . $merge_examination . $examination_position + 1);

			$sheet->setCellValue($column_examination . $examination_position + 2, '学年評定');
			$sheet->mergeCells($column_examination . $examination_position + 2 . ':' . $merge_examination . $examination_position + 3);

			$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
			$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
			$sheet->setCellValue($column_examination . $examination_position + 5, '中学2年生');
			$sheet->mergeCells($column_examination . $examination_position + 5 . ':' . $merge_examination . $examination_position + 6);

			$sheet->setCellValue($column_examination . $examination_position + 7, '学年評定');
			$sheet->mergeCells($column_examination . $examination_position + 7 . ':' . $merge_examination . $examination_position + 8);

			$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
			$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
			$sheet->setCellValue($column_examination . $examination_position + 10, '中学3年生');
			$sheet->mergeCells($column_examination . $examination_position + 10 . ':' . $merge_examination . $examination_position + 11);

			$sheet->setCellValue($column_examination . $examination_position + 12, '学年評定');
			$sheet->mergeCells($column_examination . $examination_position + 12 . ':' . $merge_examination . $examination_position + 13);
		} else {
			$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
			$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
			$sheet->setCellValue($column_examination . $examination_position, '中学2年生');
			$sheet->mergeCells($column_examination . $examination_position . ':' . $merge_examination . $examination_position + 1);

			$sheet->setCellValue($column_examination . $examination_position + 2, '学年評定');
			$sheet->mergeCells($column_examination . $examination_position + 2 . ':' . $merge_examination . $examination_position + 3);

			$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
			$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
			$sheet->setCellValue($column_examination . $examination_position + 5, '中学3年生①');
			$sheet->mergeCells($column_examination . $examination_position + 5 . ':' . $merge_examination . $examination_position + 6);

			$sheet->setCellValue($column_examination . $examination_position + 7, '学年評定');
			$sheet->mergeCells($column_examination . $examination_position + 7 . ':' . $merge_examination . $examination_position + 8);

			$column_examination = Coordinate::stringFromColumnIndex($examination_horizontal);
			$merge_examination = Coordinate::stringFromColumnIndex($examination_horizontal + 5);
			$sheet->setCellValue($column_examination . $examination_position + 10, '中学3年生②');
			$sheet->mergeCells($column_examination . $examination_position + 10 . ':' . $merge_examination . $examination_position + 11);

			$sheet->setCellValue($column_examination . $examination_position + 12, '学年評定');
			$sheet->mergeCells($column_examination . $examination_position + 12 . ':' . $merge_examination . $examination_position + 13);
		}
		$examination_subject_position = $max_row + 6;
		$examination_subject_horizontal = 7; //G

		$subjects = Subject::where('result_category_id', 1)->get();


		for ($i = 0; $i < 3; $i++) {
			if ($i == 0) {
				if ($school_area == 1) { //大阪
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $osaka_annual_rating[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $osak_annual_rating_five);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中１】定期!" . $osaka_annual_rating_nine);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$nine_total[] = $column_examination_subject . $examination_subject_position + 2;
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				} else { //奈良
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $nara_annual_rating2[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $nara_annual_rating_five2);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $nara_annual_rating_nine2);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
							$rating_position[] = $column_examination_subject . $examination_subject_position + 2;
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				}
			} elseif (($i == 1)) {
				if ($school_area == 1) { //大阪
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $osaka_annual_rating2[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $osak_annual_rating_five2);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中２】定期!" . $osaka_annual_rating_nine2);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$nine_total[] = $column_examination_subject . $examination_subject_position + 2;
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position + 5 . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				} else { //奈良
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中３】定期!" . $nara_annual_rating3[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中３】定期!" . $nara_annual_rating_five3[0]);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中３】定期!" . $nara_annual_rating_nine3[0]);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
							$rating_position[] = $column_examination_subject . $examination_subject_position + 2;
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position + 5 . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				}
			} else {
				if ($school_area == 1) { //大阪
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中３】定期!" . $osaka_annual_rating3[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中３】定期!" . $osak_annual_rating_five3);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中３】定期!" . $osaka_annual_rating_nine3);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$nine_total[] = $column_examination_subject . $examination_subject_position + 2;
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position + 10 . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				} else { //奈良
					foreach ($subjects as $subjectkey => $subject) {
						$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
						$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position, $subject->subject_name);
						$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
						$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中３】定期!" . $nara_annual_rating4[$subjectkey]);
						$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
						$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						if ($subjectkey == 4) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "5科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中３】定期!" . $nara_annual_rating_five3[1]);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
						}
						if ($subjectkey == 8) {
							$examination_subject_horizontal = $examination_subject_horizontal + 3;
							$column_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal);
							$merge_examination_subject = Coordinate::stringFromColumnIndex($examination_subject_horizontal + 2);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position, "9科目合計");
							$sheet->mergeCells($column_examination_subject . $examination_subject_position . ':' . $merge_examination_subject . $examination_subject_position + 1);
							$sheet->setCellValue($column_examination_subject . $examination_subject_position + 2, "=【中３】定期!" . $nara_annual_rating_nine3[1]);
							$sheet->getStyle(($column_examination_subject . $examination_subject_position + 2))->getNumberFormat()->setFormatCode('0');
							$sheet->mergeCells($column_examination_subject . $examination_subject_position + 2 . ':' . $merge_examination_subject . $examination_subject_position + 3);
							$rating_position[] = $column_examination_subject . $examination_subject_position + 2;
						}
						$examination_subject_horizontal = $examination_subject_horizontal + 3;
					}
					$sheet->getStyle($column_examination . $examination_position + 10 . ':' . $merge_examination_subject . $examination_subject_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				}
			}
			$examination_subject_horizontal = 7; //G
			$examination_subject_position = $examination_subject_position + 5;
		}

		//内申点の計算
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得


		if ($school_area == 1) { //大阪
			$osak_school_evaluation_point_position = $max_row + 2;
			$osak_school_evaluation_point_horizontal = 1; //A
			// dd($nine_total);
			//内申点特別計算
			$nine_total_special = "";
			for ($i = 0; $i < count($nine_total); $i++) {
				if ($i == 0) {
					$nine_total_special .= $nine_total[$i];
				} else {
					$nine_total_special .= "+" . $nine_total[$i];
				}
			}
			//内申点一般計算
			$nine_total_general = "";
			for ($i = 0; $i < count($nine_total); $i++) {
				if ($i == 0) {
					$nine_total_general .= $nine_total[$i];
				} elseif ($i == count($nine_total) - 1) {
					$nine_total_general .= "*2+" . $nine_total[$i] . "*6";
				} else {
					$nine_total_general .= "*2+" . $nine_total[$i];
				}
			}
			// dd($nine_total_general);
			//内申点特別
			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 5);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position, "内申点(特別)");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2, "=" . $nine_total_special . "*3");
			$school_osaka_evaluation_point = $column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2;
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			$nine_total_special_position = $column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2;

			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 7);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 12);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position, "Ⅰ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2, "=" . $nine_total_special_position . "*" . 0.6);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2))->getNumberFormat()->setFormatCode('0');

			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 14);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 19);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position, "Ⅱ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2, "=" . $nine_total_special_position . "*" . 0.8);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2))->getNumberFormat()->setFormatCode('0');
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 21);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 26);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position, "Ⅲ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2, "=" . $nine_total_special_position . "*" . 1);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2))->getNumberFormat()->setFormatCode('0');
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 28);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 33);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position, "Ⅳ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2, "=" . $nine_total_special_position . "*" . 1.2);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2))->getNumberFormat()->setFormatCode('0');
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 35);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 40);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position, "Ⅴ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2, "=" . $nine_total_special_position . "*" . 1.4);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2))->getNumberFormat()->setFormatCode('0');
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 2 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



			//内申点一般
			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 5);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5, "内申点(一般)");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7, "=" . $nine_total_general);
			$nine_total_general_position = $column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7;
			$school_osaka_evaluation_point2 = $column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7;
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7))->getNumberFormat()->setFormatCode('0');



			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 7);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 12);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5, "Ⅰ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7, "=" . $nine_total_general_position . "*" . 0.6);
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7))->getNumberFormat()->setFormatCode('0');



			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 14);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 19);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5, "Ⅱ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7, "=" . $nine_total_general_position . "*" . 0.8);
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7))->getNumberFormat()->setFormatCode('0');




			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 21);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 26);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5, "Ⅲ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7, "=" . $nine_total_general_position . "*" . 1);
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7))->getNumberFormat()->setFormatCode('0');




			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 28);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 33);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5, "Ⅳ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7, "=" . $nine_total_general_position . "*" . 1.2);
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7))->getNumberFormat()->setFormatCode('0');



			$column_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 35);
			$merge_osaka_school_evaluation_point = Coordinate::stringFromColumnIndex($osak_school_evaluation_point_horizontal + 40);
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5, "Ⅴ");
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 5 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 6)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7, "=" . $nine_total_general_position . "*" . 1.4);
			$sheet->mergeCells($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8);
			$sheet->getStyle($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7 . ':' . $merge_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 8)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			$sheet->getStyle(($column_osaka_school_evaluation_point . $osak_school_evaluation_point_position + 7))->getNumberFormat()->setFormatCode('0');
		} else { //奈良
			$nara_school_evaluation_point_position = $max_row + 2;
			$nara_school_evaluation_point_horizontal = 19; //S
			//内申点
			$column_nara_school_evaluation_point = Coordinate::stringFromColumnIndex($nara_school_evaluation_point_horizontal);
			$merge_nara_school_evaluation_point = Coordinate::stringFromColumnIndex($nara_school_evaluation_point_horizontal + 5);
			$sheet->setCellValue($column_nara_school_evaluation_point . $nara_school_evaluation_point_position, "内申点");
			$sheet->mergeCells($column_nara_school_evaluation_point . $nara_school_evaluation_point_position . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 1);
			$sheet->getStyle($column_nara_school_evaluation_point . $nara_school_evaluation_point_position . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$sheet->setCellValue($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2, "=SUM(" . $rating_position[0] . "," . $rating_position[1] . "," . $rating_position[2] . ")");
			$sheet->getStyle(($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2))->getNumberFormat()->setFormatCode('0');
			$school_nara_evaluation_point = $column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2;
			$sheet->mergeCells($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2 . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 3);
			$sheet->getStyle($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2 . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			//3科偏差値（平均）
			$column_nara_school_evaluation_point = Coordinate::stringFromColumnIndex($nara_school_evaluation_point_horizontal + 8);
			$merge_nara_school_evaluation_point = Coordinate::stringFromColumnIndex($nara_school_evaluation_point_horizontal + 13);
			$sheet->setCellValue($column_nara_school_evaluation_point . $nara_school_evaluation_point_position, "3科偏差値（平均）");
			$sheet->mergeCells($column_nara_school_evaluation_point . $nara_school_evaluation_point_position . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 1);
			$sheet->getStyle($column_nara_school_evaluation_point . $nara_school_evaluation_point_position . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$average2to3_total = "";
			for ($i = 0; $i < count($average2to3); $i++) {
				if ($i == 0) {
					$average2to3_total .= $average2to3[$i];
				} else {
					$average2to3_total .= "," . $average2to3[$i];
				}
			}
			// dd($average2to3_total);
			$sheet->setCellValue($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2, "=AVERAGE(" . $average2to3_total . ")");
			$sheet->getStyle(($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2))->getNumberFormat()->setFormatCode('0');

			$sheet->mergeCells($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2 . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 3);
			$sheet->getStyle($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2 . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


			// dd($average4to5);
			//5科偏差値（平均）
			$column_nara_school_evaluation_point = Coordinate::stringFromColumnIndex($nara_school_evaluation_point_horizontal + 16);
			$merge_nara_school_evaluation_point = Coordinate::stringFromColumnIndex($nara_school_evaluation_point_horizontal + 21);
			$sheet->setCellValue($column_nara_school_evaluation_point . $nara_school_evaluation_point_position, "5科偏差値（平均）");
			$sheet->mergeCells($column_nara_school_evaluation_point . $nara_school_evaluation_point_position . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 1);
			$sheet->getStyle($column_nara_school_evaluation_point . $nara_school_evaluation_point_position . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
			//値
			$average4to5_total = "";
			for ($i = 0; $i < count($average4to5); $i++) {
				if ($i == 0) {
					$average4to5_total .= $average4to5[$i];
				} else {
					$average4to5_total .= "," . $average4to5[$i];
				}
			}
			$sheet->setCellValue($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2, "=AVERAGE(" . $average4to5_total . ")");
			$sheet->getStyle(($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2))->getNumberFormat()->setFormatCode('0');

			$sheet->mergeCells($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2 . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 3);
			$sheet->getStyle($column_nara_school_evaluation_point . $nara_school_evaluation_point_position + 2 . ':' . $merge_nara_school_evaluation_point . $nara_school_evaluation_point_position + 3)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		}
		$col = Coordinate::stringFromColumnIndex(1); //A はじめのセル
		$max_col = $sheet->getHighestColumn(); //右端取得
		$max_col_remainder = Coordinate::columnIndexFromString($max_col);
		$max_col_remainder = $max_col_remainder + 20; //右端から余分にとる
		$max_col_remainder = Coordinate::stringFromColumnIndex($max_col_remainder); //string型に戻す
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得

		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setVertical(Align::VERTICAL_CENTER); //上下中央寄せ
		$sheet->getStyle('A1:' . $max_col_remainder . $max_row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER); //左右中央寄せ

		// //セル結合処理
		$sheet->mergeCells('A1:E1');
		$sheet->mergeCells('F1:H1');
		$sheet->mergeCells('I1:W1');
		$sheet->mergeCells('X1:AF1');
		$sheet->mergeCells('AG1:AO1');
		$sheet->mergeCells('A2:' . $max_col . '2');
		$sheet->getStyle('A2:' . $max_col . '2')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN); //外周に枠つける

		//セルの幅調整
		$i = 1; //カウント用
		$j = 3; //カウント用
		// dd($max_row);
		while ($col != $max_col_remainder) { //右端と一致するまで回る
			$col = Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($col)->setWidth(2.5); //セルの幅調整
			$i++;
		}

		while ($j < $max_row) {
			$sheet->getRowDimension($j)->setRowHeight(12.5); //セルの高さ
			$j++;
		}
		$sheet->getRowDimension($row)->setRowHeight(20.5); //セルの高さ
		// $sheet->getPageSetup()->setPrintArea('A1:' . $max_col_remainder . $max_row); //A1から最大範囲まで印刷する
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);

		//【中3】‐志望校シート-
		if ($school_area == 1) { //大阪
			$cloned_sheet = clone $spreadsheet->getSheetbyName("Osaka"); //複写元のシート
			$cloned_sheet->setTitle("【中3】‐志望校シート-"); //必ず事前にタイトル名を決めておく
			$spreadsheet->addSheet($cloned_sheet); //２つ目の引数を省略した場合は最後尾に追加される。
			// $spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');
			$sheet = $spreadsheet->getSheet(8);
			$sheet->setCellValue('N2', '大阪');
			$sheet->setCellValue('B4', $school_building->name);
			$sheet->setCellValue('J4', $student->surname . $student->name);
			$sheet->setCellValue('B39', "=【中３】もし!" . $school_osaka_evaluation_point); //内申点　特別
			$sheet->setCellValue('B42', "=【中３】もし!" . $school_osaka_evaluation_point2); //内申点　一般
			//直近模試偏差値
			$sheet->setCellValue('D40', "=【中３】もし!" . $vmoshi_6m_point); //6月Vもし
			$sheet->setCellValue('F40', "=【中３】もし!" . $vmoshi_8m_point); //8月Vもし
			$sheet->setCellValue('H40', "=【中３】もし!" . $itsuki_9m_point); //9月五ツ木
			$sheet->setCellValue('D43', "=【中３】もし!" . $itsuki_10m_point); //10月五ツ木
			$sheet->setCellValue('F43', "=【中３】もし!" . $itsuki_11m_point); //11月五ツ木
			$sheet->setCellValue('H43', "=【中３】もし!" . $itsuki_12m_point); //12月五ツ木
			//学力診断テスト/実力テスト点数
			$sheet->setCellValue('J41', "=【中３】もし!" . $test_2_point); //2回
			$sheet->setCellValue('K41', "=【中３】もし!" . $test_2_average_point); //2回平均

			$sheet->setCellValue('L41', "=【中３】もし!" . $test_3_point); //3回
			$sheet->setCellValue('M41', "=【中３】もし!" . $test_3_average_point); //3回平均

			$sheet->setCellValue('J44', "=【中３】もし!" . $test_4_point); //4回
			$sheet->setCellValue('K44', "=【中３】もし!" . $test_4_average_point); //4回平均

			$sheet->setCellValue('L44', "=【中３】もし!" . $test_5_point); //5回
			$sheet->setCellValue('M44', "=【中３】もし!" . $test_5_average_point); //5回平均




			$sel_index = $spreadsheet->getIndex($spreadsheet->getSheetByName('Osaka')); //シート番号を取得
			$spreadsheet->removeSheetByIndex($sel_index);
			$sel_index = $spreadsheet->getIndex($spreadsheet->getSheetByName('nara')); //シート番号を取得
			$spreadsheet->removeSheetByIndex($sel_index);
		} else { //奈良
			$cloned_sheet = clone $spreadsheet->getSheetbyName("nara"); //複写元のシート
			$cloned_sheet->setTitle("【中3】‐志望校シート-"); //必ず事前にタイトル名を決めておく
			$spreadsheet->addSheet($cloned_sheet); //２つ目の引数を省略した場合は最後尾に追加される。
			// $spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');
			$sheet = $spreadsheet->getSheet(8);
			$sheet->setCellValue('N2', '奈良');
			$sheet->setCellValue('B4', $school_building->name);
			$sheet->setCellValue('B4', $school_building->name);
			$sheet->setCellValue('J4', $student->surname . $student->name);
			$sheet->setCellValue('B39', "=【中３】もし!" . $school_nara_evaluation_point); //内申点　特別

			//直近模試偏差値
			$sheet->setCellValue('D40', "=【中３】もし!" . $vmoshi_6m_point); //6月Vもし
			$sheet->setCellValue('F40', "=【中３】もし!" . $vmoshi_8m_point); //8月Vもし
			$sheet->setCellValue('H40', "=【中３】もし!" . $itsuki_9m_point); //9月五ツ木
			$sheet->setCellValue('D43', "=【中３】もし!" . $itsuki_10m_point); //10月五ツ木
			$sheet->setCellValue('F43', "=【中３】もし!" . $itsuki_11m_point); //11月五ツ木
			$sheet->setCellValue('H43', "=【中３】もし!" . $itsuki_12m_point); //12月五ツ木
			//学力診断テスト/実力テスト点数
			$sheet->setCellValue('J41', "=【中３】もし!" . $test_2_point); //2回
			$sheet->setCellValue('K41', "=【中３】もし!" . $test_2_average_point); //2回平均

			$sheet->setCellValue('L41', "=【中３】もし!" . $test_3_point); //3回
			$sheet->setCellValue('M41', "=【中３】もし!" . $test_3_average_point); //3回平均

			$sheet->setCellValue('J44', "=【中３】もし!" . $test_4_point); //4回
			$sheet->setCellValue('K44', "=【中３】もし!" . $test_4_average_point); //4回平均

			$sheet->setCellValue('L44', "=【中３】もし!" . $test_5_point); //5回
			$sheet->setCellValue('M44', "=【中３】もし!" . $test_5_average_point); //5回平均


			$sel_index = $spreadsheet->getIndex($spreadsheet->getSheetByName('Osaka')); //シート番号を取得
			$spreadsheet->removeSheetByIndex($sel_index);
			$sel_index = $spreadsheet->getIndex($spreadsheet->getSheetByName('nara')); //シート番号を取得
			$spreadsheet->removeSheetByIndex($sel_index);
		}

		$col = Coordinate::stringFromColumnIndex(1); //A はじめのセル
		$max_col = $sheet->getHighestColumn(); //右端取得
		$max_col_remainder = Coordinate::columnIndexFromString($max_col);
		$max_col_remainder = $max_col_remainder + 20; //右端から余分にとる
		$max_col_remainder = Coordinate::stringFromColumnIndex($max_col_remainder); //string型に戻す
		$max_row = $sheet->getHighestRow(); //最終行（最下段）の取得
		//セルの幅調整
		$i = 1; //カウント用
		$j = 5; //カウント用
		while (
			$col != $max_col_remainder
		) { //右端と一致するまで回る
			$col = Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($col)->setWidth(7.5); //セルの幅調整
			$i++;
		}
		while ($j < $max_row) {
			$sheet->getRowDimension($j)->setRowHeight(14); //セルの高さ
			$j++;
		}

		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToWidth(1);


		//ファイル名用の姓と名を取得
		$student_surname = $student->surname;
		$student_name = $student->name;

		//ファイル名の作成
		$filename = 'c_result_' . $student_surname . '_' . $student_name . '.xlsx';

		// $filename = 'junior_high_school_student_result.xlsx';

		// ダウンロード
		ob_end_clean(); // this
		ob_start(); // and this
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		// $objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->setIncludeCharts(true);
		$writer->save('php://output');
	}
}
