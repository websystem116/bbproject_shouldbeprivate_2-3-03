<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '512M');

use App\Score;
use App\Student;
use App\SchoolBuilding;
use App\School;
use App\ResultCategory;
use App\Implementation;
use App\Subject;
use App\StudentResult;


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


class ScoreController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$year = date('Y', strtotime('-3 month')); //表示用
		//成績カテゴリーのセレクトリスト
		$resultcategorys = ResultCategory::get();
		$resultcategorys_select_list = $resultcategorys->mapWithKeys(function ($item) {
			return [$item['id'] => $item['id'] . "　" . $item['result_category_name']];
		});
		//実施回の取得
		$implementations = Implementation::get();
		$implementations_select_list = $implementations->mapwithKeys(function ($item) {
			return [$item['implementation_no'] => $item['implementation_name']];
		});
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

		$student_search['result_category_id'] = $request->get('result_category_id');
		$student_search['implementation_id'] = $request->get('implementation_id');
		$student_search['year'] = $request->get('year');
		$student_search['grade'] = $request->get('grade');
		$student_search['school_building_id'] = $request->get("school_building_id");
		$student_search['school_id'] = $request->get("school_id");

		//除外条件の取得
		$student_search['rest_flg'] = $request->get('rest_flg');
		$student_search['graduation_flg'] = $request->get('graduation_flg');
		$student_search['withdrawal_flg'] = $request->get('withdrawal_flg');

		if ($request->has('output')) { //試験別成績一覧の出力

			if (empty($student_search['result_category_id'])) {
				return redirect("/shinzemi/score")->with("message", "※成績区分の選択をしてください")->withInput();
			}
			if (empty($student_search['year'])) {
				return redirect("/shinzemi/score")->with("message", "※年度を確認してください。")->withInput();
			}
			//成績カテゴリー
			$resultcategorys = ResultCategory::where('id', $student_search['result_category_id'])->firstOrFail();
			//実施回の取得
			$implementations = Implementation::where('result_category_id', $resultcategorys->id)->get();

			// スプレッドシート作成
			$spreadsheet = new Spreadsheet();
			$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');

			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle('seiseki' . date('Ymd'));

			// 値セット位置
			$position = 1;
			$horizontal = 1;

			//見出し
			$column = Coordinate::stringFromColumnIndex($horizontal);
			$sheet->setCellValue($column . $position, '生徒番号');

			$horizontal = $horizontal + 1;
			$column = Coordinate::stringFromColumnIndex($horizontal);
			$sheet->setCellValue($column . $position, '生徒氏名');

			$horizontal = $horizontal + 1;
			$column = Coordinate::stringFromColumnIndex($horizontal);
			$sheet->setCellValue($column . $position, '年度');

			$horizontal = $horizontal + 1;
			$column = Coordinate::stringFromColumnIndex($horizontal);
			$sheet->setCellValue($column . $position, '学校');

			$horizontal = $horizontal + 1;
			$column = Coordinate::stringFromColumnIndex($horizontal);
			$sheet->setCellValue($column . $position, '学年');

			$horizontal = $horizontal + 1;
			$column = Coordinate::stringFromColumnIndex($horizontal);
			$sheet->setCellValue($column . $position, '校舎');

			$horizontal = $horizontal + 1;
			$column = Coordinate::stringFromColumnIndex($horizontal);
			$sheet->setCellValue($column . $position, '成績区分');

			$horizontal = $horizontal + 1;
			$column = Coordinate::stringFromColumnIndex($horizontal);
			$sheet->setCellValue($column . $position, '実施回');

			//教科の見出し
			$subjects = Subject::where('result_category_id', $resultcategorys->id)->get();
			foreach ($subjects as $subjectkey => $subject) {
				$horizontal = $horizontal + 1;
				$column = Coordinate::stringFromColumnIndex($horizontal);
				$sheet->setCellValue($column . $position, $subject->subject_name);
			}
			//教科の見出しend

			//休塾、卒塾、退塾者の除外
			$student_query = Student::query();

			if ($student_search['rest_flg'] == 1 && $student_search['graduation_flg'] == 0 && $student_search['withdrawal_flg'] == 0) { //チェック
				$student_query->whereNotNull('juku_rest_date');
			}else if($student_search['rest_flg'] == 1 && ($student_search['graduation_flg'] == 1 && $student_search['withdrawal_flg'] == 0)){
				$student_query->whereNotNull('juku_rest_date')->orWhereNotNull('juku_graduation_date');
			}else if($student_search['rest_flg'] == 1 && ($student_search['graduation_flg'] == 0 && $student_search['withdrawal_flg'] == 1)){
				$student_query->whereNotNull('juku_rest_date')->orWhereNotNull('juku_withdrawal_date');
			}else if($student_search['rest_flg'] == 1 && ($student_search['graduation_flg'] == 1 && $student_search['withdrawal_flg'] == 1)){
				$student_query->whereNotNull('juku_rest_date')->orWhereNotNull('juku_withdrawal_date')->orWhereNotNull('juku_graduation_date');
			}else if($student_search['rest_flg'] == 0 && ($student_search['graduation_flg'] == 1 && $student_search['withdrawal_flg'] == 1)){
				$student_query->whereNotNull('juku_withdrawal_date')->orWhereNotNull('juku_graduation_date');
			}else if($student_search['rest_flg'] == 0 && ($student_search['graduation_flg'] == 1 && $student_search['withdrawal_flg'] == 0)){
				$student_query->whereNotNull('juku_graduation_date');
			}else if($student_search['rest_flg'] == 0 && ($student_search['graduation_flg'] == 0 && $student_search['withdrawal_flg'] == 1)){
				$student_query->whereNotNull('juku_withdrawal_date');
			}

			//除外対象の生徒番号を取得
			if($student_search['withdrawal_flg'] == 1 || $student_search['graduation_flg'] == 1 || $student_search['rest_flg'] == 1){
				$student_nos = $student_query->pluck('student_no')->toArray();
			}

			//成績取得
			$query = StudentResult::query();
			$query->when(!empty($student_search['result_category_id']), function ($query) use ($student_search) {
				$query->where('result_category_id', $student_search['result_category_id']);
			})->when(!empty($student_search['implementation_id']), function ($query) use ($student_search) {
				return $query->where('implementation_no', $student_search['implementation_id']);
			})->when(!empty($student_search['year']), function ($query) use ($student_search) {
				return $query->where('year', $student_search['year']);
			})->when(!empty($student_search['grade']), function ($query) use ($student_search) {
				return $query->where('student_results.grade', '=', $student_search['grade']);
			})->when(!empty($student_search['school_building_id']), function ($query) use ($student_search) {
				return $query->join('students', 'student_results.student_no', '=', 'students.student_no')
					->where('students.school_building_id', $student_search['school_building_id']);
			})->when(!empty($student_search['school_id']) && empty($student_search['school_building_id']), function ($query) use ($student_search) {
				return $query->join('students', 'student_results.student_no', '=', 'students.student_no')
					->where('students.school_id', $student_search['school_id']);
			})->when(!empty($student_search['school_id']) && !empty($student_search['school_building_id']), function ($query) use ($student_search) {
				return $query->where('students.school_id', $student_search['school_id']);
			});

			if (!empty($student_search['school_building_id']) || !empty($student_search['school_id'])) {
				$student_results = $query->get();
			} else {
				$student_results = $query->join('students', 'students.student_no', '=', 'student_results.student_no')->get();
			}

			//$student_resultsに$student_nosのstudent_idがあれば除外する
			if (!empty($student_search['withdrawal_flg']) || !empty($student_search['graduation_flg']) || !empty($student_search['rest_flg'])) {
				$student_results = $student_results->whereNotIn('student_no', $student_nos);
			}

			$student_results = $student_results->unique()->sortBy('school_building_id');
			$position = 2;
			$horizontal = 1;
			$duplication_student_no = "";


			// student_resultsはcollection。collectionが空の場合は、count()で0が返る。
			if ($student_results->count() == 0) {
				return redirect("/shinzemi/score")->with("message", "※該当データが存在しません")->withInput();
			}
			foreach ($student_results as $student_results_key => $student_result) {
				$column = Coordinate::stringFromColumnIndex($horizontal);
				$sheet->setCellValue($column . $position, $student_result->student_no);

				$column = Coordinate::stringFromColumnIndex($horizontal + 1);
				if (empty($student_result->student->surname) && empty($student_result->student->name)) {
					$sheet->setCellValue($column . $position, "");
				} else {
					$sheet->setCellValue($column . $position, $student_result->student->surname . $student_result->student->name);
				}
				$column = Coordinate::stringFromColumnIndex($horizontal + 2);
				if (empty($student_result->year)) {
					$sheet->setCellValue($column . $position, "");
				} else {
					$sheet->setCellValue($column . $position, $student_result->year);
				}

				$column = Coordinate::stringFromColumnIndex($horizontal + 3);
				if (empty($student_result->school->name)) {
					$sheet->setCellValue($column . $position, "");
				} else {
					$sheet->setCellValue($column . $position, $student_result->school->name);
				}

				$column = Coordinate::stringFromColumnIndex($horizontal + 4);
				if (empty($student_result->grade)) {
					$sheet->setCellValue($column . $position, "");
				} else {
					$grade = StudentResult::where('student_no', $student_result->student_no)->where('year', $student_result->year)->first(['grade']);
					$sheet->setCellValue($column . $position, config('const.school_year')[$grade->grade]);
				}

				$column = Coordinate::stringFromColumnIndex($horizontal + 5);
				if (empty($student_result->student->school_building_id)) {
					$sheet->setCellValue($column . $position, "");
				} else {
					$sheet->setCellValue($column . $position, $student_result->student->schoolbuilding->name);
				}
				$column = Coordinate::stringFromColumnIndex($horizontal + 6);
				if (empty($student_result->result_category_id)) {
					$sheet->setCellValue($column . $position, "");
				} else {
					$sheet->setCellValue($column . $position, $student_result->result_category->result_category_name);
				}

				$column = Coordinate::stringFromColumnIndex($horizontal + 7);
				if (empty($student_result->implementation_no)) {
					$sheet->setCellValue($column . $position, "");
				} else {
					$sheet->setCellValue($column . $position, $student_result->implementation->implementation_name);
				}

				//教科取得
				$subjects = Subject::where('result_category_id', $student_result->result_category_id)->get();
				foreach ($subjects as $subjectkey => $subject) {
					//生徒成績取得
					$studentresults = StudentResult::where('student_no', $student_result->student_no)->where('year', $student_result->year)->where('result_category_id', $student_result->result_category_id)->where('implementation_no', $student_result->implementation_no)->where('subject_no', $subject->subject_no)->get();
					// dd($studentresult->toSql(), $studentresult->getBindings());
					foreach ($studentresults as $resultkey => $studentresult) {
						$column = Coordinate::stringFromColumnIndex($horizontal + 8 + $subjectkey);
						if (empty($studentresult->point)) {
							$sheet->setCellValue($column . $position, "");
						} else {
							$sheet->setCellValue($column . $position, $studentresult->point);
						}
					}
				}

				if ($duplication_student_no == $student_result->student_no) { //生徒Noが一致したら何もしない

				} else { //生徒Noが一致しなければ次のセルに移動
					$position = $position + 1;
					$horizontal = 1;
					$duplication_student_no = $student_result->student_no; //重複するので前回の生徒No取得する
				}
			}

			//出力指示
			$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

			$filename = 'seiseki' . date('Ymd') . '.xlsx';
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
		} else { //普通にindexページの表示
			return view("score.index", compact(
				"student_search",
				"schools_select_list",
				"schooolbuildings_select_list",
				"resultcategorys_select_list",
				"implementations_select_list",
				"year"
			));
		}

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
	 * @param  \App\Score  $score
	 * @return \Illuminate\Http\Response
	 */
	public function show(Score $score)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Score  $score
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Score $score)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Score  $score
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Score $score)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Score  $score
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Score $score)
	{
		//
	}
	/**
	 * Undocumented function
	 *
	 * @param [type] $id
	 * @return void
	 */
	public function get_implementations($id)
	{
		$resultCategory = ResultCategory::where('id', $id)->first();
		$implementations = Implementation::where('result_category_id', $resultCategory->id)->get();
		return $implementations;
	}
}
