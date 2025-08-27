<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\QuestionnaireResultsDetail;
use App\QuestionnaireContent;
use App\SchoolBuilding;
use App\User;
use App\QuestionnaireEverySubject;
use App\SubjectTeacher;
use App\QuestionnaireDecision;


//laravel excel
// use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\questionnaire_results_detailsExport;

// use phpspreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpParser\Node\Stmt\Foreach_;

// 罫線引きたい
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;


//python 実行用
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use Illuminate\Support\Facades\Storage;



//=======================================================================
class QuestionnaireResultsDetailsController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request)
	{

		$school_buildings = SchoolBuilding::all();
		$questionnaire_contents = QuestionnaireContent::all();

		$management_code = $request->get("management_code");
		$questionnaire_content_id = $request->get("questionnaire_content_id");
		$school_building_id = $request->get("school_building_id");
		$school_year_search = $request->get("school_year_search");

		$perPage = 25;

		if (!empty($management_code || $questionnaire_content_id || $school_building_id || $school_year_search)) {

			$questionnaire_results_detail = DB::table("questionnaire_results_details")
				->leftJoin("questionnaire_contents", "questionnaire_contents.id", "=", "questionnaire_results_details.questionnaire_content_id")
				->leftJoin("school_buildings", "school_buildings.id", "=", "questionnaire_results_details.school_building_id")

				->when($management_code, function ($query) use ($management_code) {
					$query->where('management_code', '=',  $management_code);
				})

				->when($questionnaire_content_id, function ($query) use ($questionnaire_content_id) {
					$query->where('questionnaire_content_id', '=',  $questionnaire_content_id);
				})

				->when($school_building_id, function ($query) use ($school_building_id) {
					$query->where('school_building_id', '=',  $school_building_id);
				})

				->when($school_year_search, function ($query) use ($school_year_search) {
					$query->where('school_year_id', '=',  $school_year_search);
				})

				->select("*")->addSelect("questionnaire_results_details.id")
				->addSelect("questionnaire_results_details.created_at")
				->addSelect("questionnaire_results_details.updated_at")

				// order is latest
				->orderBy("questionnaire_results_details.id", "desc")

				->paginate($perPage);
		} else {

			$questionnaire_results_detail = DB::table("questionnaire_results_details")
				->leftJoin("questionnaire_contents", "questionnaire_contents.id", "=", "questionnaire_results_details.questionnaire_content_id")
				->leftJoin("school_buildings", "school_buildings.id", "=", "questionnaire_results_details.school_building_id")

				->select("*")->addSelect("questionnaire_results_details.id")
				->addSelect("questionnaire_results_details.created_at")
				->addSelect("questionnaire_results_details.updated_at")

				->orderBy("questionnaire_results_details.id", "desc")


				->paginate($perPage);
		}


		return view("questionnaire_results_detail.index", compact("questionnaire_results_detail", "school_buildings", "questionnaire_contents"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		$users = User::all();
		$school_buildings = SchoolBuilding::all();
		$questionnaire_contents = QuestionnaireContent::all();


		return view("questionnaire_results_detail.create", compact("users", "school_buildings", "questionnaire_contents"));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			"management_code" => "nullable|max:11", //integer('management_code')->nullable()

			// "management_code" => "nullable|digits:2", //integer('management_code',2)->nullable()
			// "questionnaire_content_id" => "nullable|digits:2", //integer('questionnaire_content_id',2)->nullable()
			// "school_building_id" => "nullable|digits:3", //integer('school_building_id',3)->nullable()
			// "school_year_id" => "nullable|digits:2", //integer('school_year_id',2)->nullable()
		], [
			"management_code.max" => "アンケートNoは11桁以内で入力してください。	",

		]);

		$requestData = $request->all();

		$questionnaireeverysubject = new QuestionnaireResultsDetail($requestData);
		$questionnaireeverysubject->save();
		// get last_insert_id
		$last_insert_id = $questionnaireeverysubject->id;

		//英語
		if ($request->alphabet_id_1) {

			$questionnaireeverysubject_e = new QuestionnaireEverySubject();
			$questionnaireeverysubject_e->questionnaire_results_details_id = $last_insert_id;
			$questionnaireeverysubject_e->alphabet_id = $request->alphabet_id_1;
			$questionnaireeverysubject_e->subject_id = 1;
			$questionnaireeverysubject_e->user_id = $request->user_id_1;

			$questionnaireeverysubject_e->question1 = $request->question_1_1;
			$questionnaireeverysubject_e->question2 = $request->question_2_1;
			$questionnaireeverysubject_e->question3 = $request->question_3_1;
			$questionnaireeverysubject_e->question4 = $request->question_4_1;
			$questionnaireeverysubject_e->question5 = $request->question_5_1;
			$questionnaireeverysubject_e->question6 = $request->question_6_1;
			$questionnaireeverysubject_e->question7 = $request->question_7_1;

			$questionnaireeverysubject_e->save();
		}

		//理科
		if ($request->alphabet_id_2) {
			$questionnaireeverysubject_s = new QuestionnaireEverySubject();
			$questionnaireeverysubject_s->questionnaire_results_details_id = $last_insert_id;
			$questionnaireeverysubject_s->alphabet_id = $request->alphabet_id_2;
			$questionnaireeverysubject_s->subject_id = 2;
			$questionnaireeverysubject_s->user_id = $request->user_id_2;
			$questionnaireeverysubject_s->question1 = $request->question_1_2;
			$questionnaireeverysubject_s->question2 = $request->question_2_2;
			$questionnaireeverysubject_s->question3 = $request->question_3_2;
			$questionnaireeverysubject_s->question4 = $request->question_4_2;
			$questionnaireeverysubject_s->question5 = $request->question_5_2;
			$questionnaireeverysubject_s->question6 = $request->question_6_2;
			$questionnaireeverysubject_s->question7 = $request->question_7_2;

			$questionnaireeverysubject_s->save();
		}

		//数学
		if ($request->alphabet_id_3) {
			$questionnaireeverysubject_m = new QuestionnaireEverySubject();
			$questionnaireeverysubject_m->questionnaire_results_details_id = $last_insert_id;
			$questionnaireeverysubject_m->alphabet_id = $request->alphabet_id_3;
			$questionnaireeverysubject_m->subject_id = 3;
			$questionnaireeverysubject_m->user_id = $request->user_id_3;
			$questionnaireeverysubject_m->question1 = $request->question_1_3;
			$questionnaireeverysubject_m->question2 = $request->question_2_3;
			$questionnaireeverysubject_m->question3 = $request->question_3_3;
			$questionnaireeverysubject_m->question4 = $request->question_4_3;
			$questionnaireeverysubject_m->question5 = $request->question_5_3;
			$questionnaireeverysubject_m->question6 = $request->question_6_3;
			$questionnaireeverysubject_m->question7 = $request->question_7_3;

			$questionnaireeverysubject_m->save();
		}

		//国語
		if ($request->alphabet_id_4) {

			$questionnaireeverysubject_j = new QuestionnaireEverySubject();
			$questionnaireeverysubject_j->questionnaire_results_details_id = $last_insert_id;
			$questionnaireeverysubject_j->alphabet_id = $request->alphabet_id_4;
			$questionnaireeverysubject_j->subject_id = 4;
			$questionnaireeverysubject_j->user_id = $request->user_id_4;
			$questionnaireeverysubject_j->question1 = $request->question_1_4;
			$questionnaireeverysubject_j->question2 = $request->question_2_4;
			$questionnaireeverysubject_j->question3 = $request->question_3_4;
			$questionnaireeverysubject_j->question4 = $request->question_4_4;
			$questionnaireeverysubject_j->question5 = $request->question_5_4;
			$questionnaireeverysubject_j->question6 = $request->question_6_4;
			$questionnaireeverysubject_j->question7 = $request->question_7_4;

			$questionnaireeverysubject_j->save();
		}

		//社会
		if ($request->alphabet_id_5) {

			$questionnaireeverysubject_so = new QuestionnaireEverySubject();
			$questionnaireeverysubject_so->questionnaire_results_details_id = $last_insert_id;
			$questionnaireeverysubject_so->alphabet_id = $request->alphabet_id_5;
			$questionnaireeverysubject_so->subject_id = 5;
			$questionnaireeverysubject_so->user_id = $request->user_id_5;
			$questionnaireeverysubject_so->question1 = $request->question_1_5;
			$questionnaireeverysubject_so->question2 = $request->question_2_5;
			$questionnaireeverysubject_so->question3 = $request->question_3_5;
			$questionnaireeverysubject_so->question4 = $request->question_4_5;
			$questionnaireeverysubject_so->question5 = $request->question_5_5;
			$questionnaireeverysubject_so->question6 = $request->question_6_5;
			$questionnaireeverysubject_so->question7 = $request->question_7_5;

			$questionnaireeverysubject_so->save();
		}

		//その他
		if ($request->alphabet_id_6) {

			$questionnaireeverysubject_o = new QuestionnaireEverySubject();
			$questionnaireeverysubject_o->questionnaire_results_details_id = $last_insert_id;
			$questionnaireeverysubject_o->alphabet_id = $request->alphabet_id_6;
			$questionnaireeverysubject_o->subject_id = 6;
			$questionnaireeverysubject_o->user_id = $request->user_id_6;
			$questionnaireeverysubject_o->question1 = $request->question_1_6;
			$questionnaireeverysubject_o->question2 = $request->question_2_6;
			$questionnaireeverysubject_o->question3 = $request->question_3_6;
			$questionnaireeverysubject_o->question4 = $request->question_4_6;
			$questionnaireeverysubject_o->question5 = $request->question_5_6;
			$questionnaireeverysubject_o->question6 = $request->question_6_6;
			$questionnaireeverysubject_o->question7 = $request->question_7_6;

			$questionnaireeverysubject_o->save();
		}

		return redirect("/shinzemi/questionnaire_results_detail")->with("flash_message", "更新しました。");
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 *
	 * @return \Illuminate\View\View
	 */
	// public function show($id)
	// {
	// 	//$questionnaire_results_detail = QuestionnaireResultsDetail::findOrFail($id);

	// 	// ----------------------------------------------------
	// 	// -- QueryBuilder: SELECT [questionnaire_results_details]--
	// 	// ----------------------------------------------------
	// 	$questionnaire_results_detail = DB::table("questionnaire_results_details")
	// 		->leftJoin("questionnaire_contents", "questionnaire_contents.id", "=", "questionnaire_results_details.questionnaire_content_id")
	// 		->leftJoin("school_buildings", "school_buildings.id", "=", "questionnaire_results_details.school_building_id")
	// 		->select("*")->addSelect("questionnaire_results_details.id")->where("questionnaire_results_details.id", $id)->first();
	// 	return view("questionnaire_results_detail.show", compact("questionnaire_results_detail"));
	// }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 *
	 * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$url = url()->previous();
		// sessionにURLを保存
		session(["url" => $url]);

		$users = User::all();
		$school_buildings = SchoolBuilding::all();
		$questionnaire_contents = QuestionnaireContent::all();

		$questionnaire_results_detail = QuestionnaireResultsDetail::find($id);

		$questionnaire_every_subjects_e = QuestionnaireEverySubject::where('questionnaire_results_details_id', '=', $id)->where('subject_id', '=', '1')->first();
		if (!$questionnaire_every_subjects_e) {
			$questionnaire_every_subjects_e = new QuestionnaireEverySubject();
		}

		$questionnaire_every_subjects_s = QuestionnaireEverySubject::where('questionnaire_results_details_id', '=', $id)->where('subject_id', '=', '2')->first();
		if (!$questionnaire_every_subjects_s) {
			$questionnaire_every_subjects_s = new QuestionnaireEverySubject();
		}


		$questionnaire_every_subjects_m = QuestionnaireEverySubject::where('questionnaire_results_details_id', '=', $id)->where('subject_id', '=', '3')->first();
		if (!$questionnaire_every_subjects_m) {
			$questionnaire_every_subjects_m = new QuestionnaireEverySubject();
		}

		$questionnaire_every_subjects_j = QuestionnaireEverySubject::where('questionnaire_results_details_id', '=', $id)->where('subject_id', '=', '4')->first();
		if (!$questionnaire_every_subjects_j) {
			$questionnaire_every_subjects_j = new QuestionnaireEverySubject();
		}

		$questionnaire_every_subjects_so = QuestionnaireEverySubject::where('questionnaire_results_details_id', '=', $id)->where('subject_id', '=', '5')->first();
		if (!$questionnaire_every_subjects_so) {
			$questionnaire_every_subjects_so = new QuestionnaireEverySubject();
		}

		$questionnaire_every_subjects_o = QuestionnaireEverySubject::where('questionnaire_results_details_id', '=', $id)->where('subject_id', '=', '6')->first();
		if (!$questionnaire_every_subjects_o) {
			$questionnaire_every_subjects_o = new QuestionnaireEverySubject();
		}

		return view("questionnaire_results_detail.edit", compact("questionnaire_results_detail", "users", "school_buildings", "questionnaire_contents", "questionnaire_every_subjects_e", "questionnaire_every_subjects_s", "questionnaire_every_subjects_m", "questionnaire_every_subjects_j", "questionnaire_every_subjects_so", "questionnaire_every_subjects_o"));
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
			"management_code" => "nullable|max:11", //integer('management_code')->nullable()
			// "questionnaire_content_id" => "nullable|digits:2", //integer('questionnaire_content_id',2)->nullable()
			// "school_building_id" => "nullable|digits:3", //integer('school_building_id',3)->nullable()
			// "school_year_id" => "nullable|digits:2", //integer('school_year_id',2)->nullable()
			// "alphabet_id_1" => "nullable|digits:2", //integer('alphabet_id_1',2)->nullable()
			// "subject_id_1" => "nullable|digits:2", //integer('subject_id_1',2)->nullable()
			// "user_id_1" => "nullable|integer", //integer('user_id_1')->nullable()

		], [
			"management_code.max" => "アンケートNoは11桁以内で入力してください。	",
		]);
		$requestData = $request->all();

		$questionnaire_results_detail = QuestionnaireResultsDetail::findOrFail($id);
		$questionnaire_results_detail->school_building_id = $request->school_building_id;
		$questionnaire_results_detail->management_code = $request->management_code;
		$questionnaire_results_detail->questionnaire_content_id = $request->questionnaire_content_id;
		$questionnaire_results_detail->school_year_id = $request->school_year_id;

		//save
		$questionnaire_results_detail->save();

		//一旦削除
		QuestionnaireEverySubject::where('questionnaire_results_details_id', '=', $id)->delete();

		//英語
		if ($request->alphabet_id_1) {

			$questionnaireeverysubject_e = new QuestionnaireEverySubject();
			$questionnaireeverysubject_e->questionnaire_results_details_id = $id;
			$questionnaireeverysubject_e->alphabet_id = $request->alphabet_id_1;
			$questionnaireeverysubject_e->subject_id = 1;
			$questionnaireeverysubject_e->user_id = $request->user_id_1;
			$questionnaireeverysubject_e->question1 = $request->question_1_1;
			$questionnaireeverysubject_e->question2 = $request->question_2_1;
			$questionnaireeverysubject_e->question3 = $request->question_3_1;
			$questionnaireeverysubject_e->question4 = $request->question_4_1;
			$questionnaireeverysubject_e->question5 = $request->question_5_1;
			$questionnaireeverysubject_e->question6 = $request->question_6_1;
			$questionnaireeverysubject_e->question7 = $request->question_7_1;

			$questionnaireeverysubject_e->save();
		}

		//理科
		if ($request->alphabet_id_2) {
			$questionnaireeverysubject_s = new QuestionnaireEverySubject();
			$questionnaireeverysubject_s->questionnaire_results_details_id = $id;
			$questionnaireeverysubject_s->alphabet_id = $request->alphabet_id_2;
			$questionnaireeverysubject_s->subject_id = 2;
			$questionnaireeverysubject_s->user_id = $request->user_id_2;
			$questionnaireeverysubject_s->question1 = $request->question_1_2;
			$questionnaireeverysubject_s->question2 = $request->question_2_2;
			$questionnaireeverysubject_s->question3 = $request->question_3_2;
			$questionnaireeverysubject_s->question4 = $request->question_4_2;
			$questionnaireeverysubject_s->question5 = $request->question_5_2;
			$questionnaireeverysubject_s->question6 = $request->question_6_2;
			$questionnaireeverysubject_s->question7 = $request->question_7_2;

			$questionnaireeverysubject_s->save();
		}

		//数学
		if ($request->alphabet_id_3) {

			$questionnaireeverysubject_m = new QuestionnaireEverySubject();
			$questionnaireeverysubject_m->questionnaire_results_details_id = $id;
			$questionnaireeverysubject_m->alphabet_id = $request->alphabet_id_3;
			$questionnaireeverysubject_m->subject_id = 3;
			$questionnaireeverysubject_m->user_id = $request->user_id_3;
			$questionnaireeverysubject_m->question1 = $request->question_1_3;
			$questionnaireeverysubject_m->question2 = $request->question_2_3;
			$questionnaireeverysubject_m->question3 = $request->question_3_3;
			$questionnaireeverysubject_m->question4 = $request->question_4_3;
			$questionnaireeverysubject_m->question5 = $request->question_5_3;
			$questionnaireeverysubject_m->question6 = $request->question_6_3;
			$questionnaireeverysubject_m->question7 = $request->question_7_3;

			$questionnaireeverysubject_m->save();
		}

		//国語
		if ($request->alphabet_id_4) {

			$questionnaireeverysubject_j = new QuestionnaireEverySubject();

			$questionnaireeverysubject_j->questionnaire_results_details_id = $id;
			$questionnaireeverysubject_j->alphabet_id = $request->alphabet_id_4;
			$questionnaireeverysubject_j->subject_id = 4;
			$questionnaireeverysubject_j->user_id = $request->user_id_4;
			$questionnaireeverysubject_j->question1 = $request->question_1_4;
			$questionnaireeverysubject_j->question2 = $request->question_2_4;
			$questionnaireeverysubject_j->question3 = $request->question_3_4;
			$questionnaireeverysubject_j->question4 = $request->question_4_4;
			$questionnaireeverysubject_j->question5 = $request->question_5_4;
			$questionnaireeverysubject_j->question6 = $request->question_6_4;
			$questionnaireeverysubject_j->question7 = $request->question_7_4;

			$questionnaireeverysubject_j->save();
		}

		//社会
		if ($request->alphabet_id_5) {

			$questionnaireeverysubject_so = new QuestionnaireEverySubject();
			$questionnaireeverysubject_so->questionnaire_results_details_id = $id;
			$questionnaireeverysubject_so->alphabet_id = $request->alphabet_id_5;
			$questionnaireeverysubject_so->subject_id = 5;
			$questionnaireeverysubject_so->user_id = $request->user_id_5;
			$questionnaireeverysubject_so->question1 = $request->question_1_5;
			$questionnaireeverysubject_so->question2 = $request->question_2_5;
			$questionnaireeverysubject_so->question3 = $request->question_3_5;
			$questionnaireeverysubject_so->question4 = $request->question_4_5;
			$questionnaireeverysubject_so->question5 = $request->question_5_5;
			$questionnaireeverysubject_so->question6 = $request->question_6_5;
			$questionnaireeverysubject_so->question7 = $request->question_7_5;

			$questionnaireeverysubject_so->save();
		}

		//その他
		if ($request->alphabet_id_6) {

			$questionnaireeverysubject_o = new QuestionnaireEverySubject();
			$questionnaireeverysubject_o->questionnaire_results_details_id = $id;
			$questionnaireeverysubject_o->alphabet_id = $request->alphabet_id_6;
			$questionnaireeverysubject_o->subject_id = 6;
			$questionnaireeverysubject_o->user_id = $request->user_id_6;
			$questionnaireeverysubject_o->question1 = $request->question_1_6;
			$questionnaireeverysubject_o->question2 = $request->question_2_6;
			$questionnaireeverysubject_o->question3 = $request->question_3_6;
			$questionnaireeverysubject_o->question4 = $request->question_4_6;
			$questionnaireeverysubject_o->question5 = $request->question_5_6;
			$questionnaireeverysubject_o->question6 = $request->question_6_6;
			$questionnaireeverysubject_o->question7 = $request->question_7_6;

			$questionnaireeverysubject_o->save();
		}

		// get session url
		$url = session("url");
		session()->forget("url");

		if (strpos($url, "questionnaire_results_detail") !== false) {
			return redirect($url)->with("flash_message", "アンケート結果を更新しました");
		} else {
			return redirect("/shinzemi/questionnaire_results_detail")->with("flash_message", "アンケート結果を更新しました");
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
		QuestionnaireResultsDetail::destroy($id);

		return redirect("/shinzemi/questionnaire_results_detail")->with("flash_message", "削除完了しました!");
	}

	//Export Excel

	//講師別ランキング表（質問1～7の計7項目）教科毎で取得
	// get $id
	public function export($id, Request $request)
	{

		$requestdata = $request->all();
		$employment_status = $requestdata['employment_status'];

		switch ($employment_status) {
			case 1:
				$users = User::whereIn('employment_status', [1, 2, 3])->get();
				break;
			case 2:
				// 社員のみ（正社員、契約社員）
				$users = User::whereIn('employment_status', [1, 2])->get();
				break;
			case 3:
				// 非常勤のみ(アルバイト)
				$users = User::where('employment_status', 3)->get();
				break;
			default:
				$users = User::whereIn('roles', [1, 2, 3])->get();
				break;
		}

		$questionnaire_content_id = $id;
		$questionnaire_content = QuestionnaireContent::findOrFail($questionnaire_content_id);
		$year_month = $questionnaire_content->month;
		if ($year_month) {
			$date = explode('-', $year_month);
			$year = $date[0];
			$month = $date[1];
		}

		//質問１のランキングを取得
		$questionnaire_results_details = QuestionnaireResultsDetail::where('questionnaire_content_id', $questionnaire_content_id)->get();

		//idだけ取得
		$questionnaire_results_details_ids = $questionnaire_results_details->pluck('id');

		#################################################
		# 質問１
		#################################################
		// 今回の対象アンケートの質問1の回答のみ取得（値が0以上のアンケートのみ取得）

		// 今回の対象アンケートの質問1の回答のみ取得（値が0以上のアンケートのみ取得）
		// 指定された講師カテゴリ（全体、社員、非常勤）を抽出
		$questionnaire_every_subjects_1 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)
			->where('question1', '>', '0')
			->where('question1', '!=', '99')
			->whereIn('user_id', $users->pluck('id'))->get();

		//講師別にグループ化（質問１のアンケート結果）
		$subject_id_1_records = $questionnaire_every_subjects_1->groupBy('user_id');


		// 講師別の質問1の合計値を取得
		$subject_id_1_sum_score_every_users = $subject_id_1_records->map(function ($item) {

			$item['sum_question1'] = round($item->sum('question1') / $item->count(), 2);
			$item['groupby_user_id'] = $item->avg('user_id');

			return $item;
		});


		//点数が高い講師順に並び変える
		$subject_id_1_sum_score_every_users_desc = $subject_id_1_sum_score_every_users->sortBy('sum_question1');

		##################################################
		#質問２
		##################################################
		// 今回の対象のアンケートの質問2の回答のみ取得（選択肢0以上のアンケートのみ取得）

		$questionnaire_every_subjects_2 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)
			->where('question2', '>', '0')
			->where('question2', '!=', '99')
			->whereIn('user_id', $users->pluck('id'))->get();


		//講師別にグループ化（質問２のアンケート結果）
		$subject_id_2_records = $questionnaire_every_subjects_2->groupBy('user_id');

		// 講師別の質問2の合計値を取得
		$subject_id_2_sum_score_every_users = $subject_id_2_records->map(function ($item) {

			$item['sum_question2'] = round($item->sum('question2') / $item->count(), 2);
			$item['groupby_user_id'] = $item->avg('user_id');

			return $item;
		});

		//点数が高い講師順に並び変える
		$subject_id_2_sum_score_every_users_desc = $subject_id_2_sum_score_every_users->sortBy('sum_question2');

		##################################################
		#質問３
		##################################################
		// 今回の対象のアンケートの質問3の回答のみ取得（選択肢0以上のアンケートのみ取得）
		// $questionnaire_every_subjects_3 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)->where('question3', '>', '0')->get();

		$questionnaire_every_subjects_3 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)
			->where('question3', '>', '0')
			->where('question3', '!=', '99')
			->whereIn('user_id', $users->pluck('id'))->get();

		//講師別にグループ化（質問３のアンケート結果）
		$subject_id_3_records = $questionnaire_every_subjects_3->groupBy('user_id');

		// 講師別に質問3の合計値を取得
		$subject_id_3_sum_score_every_users = $subject_id_3_records->map(function ($item) {

			$item['sum_question3'] = round($item->sum('question3') / $item->count(), 2);
			$item['groupby_user_id'] = $item->avg('user_id');

			return $item;
		});

		//sort by sum_question3 desc 点数が高い講師順に並び変える
		$subject_id_3_sum_score_every_users_desc = $subject_id_3_sum_score_every_users->sortBy('sum_question3');

		##################################################
		#質問４
		##################################################
		// 今回の対象のアンケートの質問4の回答のみ取得（選択肢0以上のアンケートのみ取得）
		// $questionnaire_every_subjects_4 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)->where('question4', '>', '0')->get();

		$questionnaire_every_subjects_4 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)
			->where('question4', '>', '0')
			->where('question4', '!=', '99')
			->whereIn('user_id', $users->pluck('id'))->get();

		//講師別にグループ化（質問４のアンケート結果）
		$subject_id_4_records = $questionnaire_every_subjects_4->groupBy('user_id');

		// 講師別の質問4の合計値を取得
		$subject_id_4_sum_score_every_users = $subject_id_4_records->map(function ($item) {

			$item['sum_question4'] = round($item->sum('question4') / $item->count(), 2);
			$item['groupby_user_id'] = $item->avg('user_id');

			return $item;
		});

		//sort by sum_question4 desc 点数が高い講師順に並び変える
		$subject_id_4_sum_score_every_users_desc = $subject_id_4_sum_score_every_users->sortBy('sum_question4');

		##################################################
		#質問５
		##################################################
		// 今回の対象のアンケートの質問5の回答のみ取得（選択肢0以上のアンケートのみ取得）
		// $questionnaire_every_subjects_5 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)->where('question5', '>', '0')->get();

		$questionnaire_every_subjects_5 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)
			->where('question5', '>', '0')
			->where('question5', '!=', '99')
			->whereIn('user_id', $users->pluck('id'))->get();

		//講師別にグループ化（質問５のアンケート結果）
		$subject_id_5_records = $questionnaire_every_subjects_5->groupBy('user_id');

		// 講師別の質問5の合計値を取得
		$subject_id_5_sum_score_every_users = $subject_id_5_records->map(function ($item) {

			$item['sum_question5'] = round($item->sum('question5') / $item->count(), 2);
			$item['groupby_user_id'] = $item->avg('user_id');

			return $item;
		});

		//sort by sum_question5 desc 点数が高い講師順に並び変える
		$subject_id_5_sum_score_every_users_desc = $subject_id_5_sum_score_every_users->sortBy('sum_question5');

		##################################################
		#質問６
		##################################################
		// 今回の対象のアンケートの質問6の回答のみ取得（選択肢0以上のアンケートのみ取得）
		// $questionnaire_every_subjects_6 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)->where('question6', '>', '0')->get();

		$questionnaire_every_subjects_6 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)
			->where('question6', '>', '0')
			->where('question6', '!=', '99')
			->whereIn('user_id', $users->pluck('id'))->get();

		//講師別にグループ化（質問６のアンケート結果）
		$subject_id_6_records = $questionnaire_every_subjects_6->groupBy('user_id');

		// 講師別の質問6の合計値を取得
		$subject_id_6_sum_score_every_users = $subject_id_6_records->map(function ($item) {

			$item['sum_question6'] = round($item->sum('question6') / $item->count(), 2);

			$item['groupby_user_id'] = $item->avg('user_id');

			return $item;
		});

		//sort by sum_question6 desc 点数が高い講師順に並び変える
		$subject_id_6_sum_score_every_users_desc = $subject_id_6_sum_score_every_users->sortBy('sum_question6');

		###############################################
		#質問7
		###############################################
		// 今回の対象のアンケートの質問7の回答のみ取得（選択肢0以上のアンケートのみ取得）
		// $questionnaire_every_subjects_7 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)->where('question7', '>', '0')->get();

		$questionnaire_every_subjects_7 = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)
			->where('question7', '>', '0')
			->where('question7', '!=', '99')
			->whereIn('user_id', $users->pluck('id'))->get();


		//講師別にグループ化（質問７のアンケート結果）
		$subject_id_7_records = $questionnaire_every_subjects_7->groupBy('user_id');

		// 講師別の質問7の合計値を取得
		$subject_id_7_sum_score_every_users = $subject_id_7_records->map(function ($item) {

			$item['sum_question7'] = round($item->sum('question7') / $item->count(), 2);

			$item['groupby_user_id'] = $item->avg('user_id');

			return $item;
		});

		//sort by sum_question7 desc 点数が高い講師順に並び変える
		$subject_id_7_sum_score_every_users_desc = $subject_id_7_sum_score_every_users->sortBy('sum_question7');


		//Excel出力
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/ranking.xlsx'); //template.xlsx 読込

		$sheet = $spreadsheet->getActiveSheet();

		// 年月出力
		$sheet->setCellValue('B2', $questionnaire_content->title);

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		];

		#################################################
		# 質問1出力
		#################################################
		$i = 5;
		foreach ($subject_id_1_sum_score_every_users_desc as $value) {

			// 講師名の取得
			$user = User::find($value['groupby_user_id']);

			// 算出点数
			$sum_question1 = $value['sum_question1'];

			// 罫線つける
			$sheet->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);

			if ($user) {
				$sheet->setCellValue('C' . $i, $user->last_name . $user->first_name . '先生 ' . $sum_question1);

				$sheet->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);
				$i++;
			}
		}

		#################################################
		# 質問2出力
		#################################################
		$i = 5;
		foreach ($subject_id_2_sum_score_every_users_desc as $value) {


			$user = User::find($value['groupby_user_id']);

			$sum_question2 = $value['sum_question2'];

			if ($user) {
				$sheet->setCellValue('D' . $i, $user->last_name . $user->first_name . '先生 ' . $sum_question2);

				$i++;
			}
		}

		#################################################
		# 質問3出力
		#################################################
		$i = 5;
		foreach ($subject_id_3_sum_score_every_users_desc as $value) {

			$user = User::find($value['groupby_user_id']);

			$sum_question3 = $value['sum_question3'];

			if ($user) {
				$sheet->setCellValue('E' . $i, $user->last_name . $user->first_name  . '先生 ' . $sum_question3);

				$i++;
			}
		}

		#################################################
		# 質問4出力
		#################################################
		$i = 5;
		foreach ($subject_id_4_sum_score_every_users_desc as $value) {

			$user = User::find($value['groupby_user_id']);

			$sum_question4 = $value['sum_question4'];

			if ($user) {
				$sheet->setCellValue('F' . $i, $user->last_name  . $user->first_name . '先生 ' . $sum_question4);

				$i++;
			}
		}

		#################################################
		# 質問5出力
		#################################################
		$i = 5;
		foreach ($subject_id_5_sum_score_every_users_desc as $value) {

			$user = User::find($value['groupby_user_id']);

			$sum_question5 = $value['sum_question5'];

			if ($user) {
				$sheet->setCellValue('G' . $i, $user->last_name  . $user->first_name  . '先生 ' . $sum_question5);
				$i++;
			}
		}

		#################################################
		# 質問6出力
		#################################################
		$i = 5;
		foreach ($subject_id_6_sum_score_every_users_desc as $value) {

			$user = User::find($value['groupby_user_id']);

			$sum_question6 = $value['sum_question6'];

			if ($user) {
				$sheet->setCellValue('H' . $i, $user->last_name  . $user->first_name  . '先生 ' . $sum_question6);
				$i++;
			}
		}

		#################################################
		# 質問7出力
		#################################################
		$i = 5;
		foreach ($subject_id_7_sum_score_every_users_desc as $value) {

			$user = User::find($value['groupby_user_id']);

			$sum_question7 = $value['sum_question7'];

			if ($user) {
				$sheet->setCellValue('I' . $i, $user->last_name  . $user->first_name  . '先生 ' . $sum_question7);
				$i++;
			}
		}

		// ランキング順位出力
		$lastRow = $sheet->getHighestRow();

		$ranking = 1;
		// insert ranking column(from:B5 to $lastRow)
		for ($i = 5; $i <= $lastRow; $i++) {
			$sheet->setCellValue('B' . $i, $ranking . "位");

			$ranking++;
		}



		$filename = $questionnaire_content->title . "ランキング表.xlsx";

		ob_end_clean();
		ob_start();

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

	//講師別一覧
	public function export_every_teachers($id, Request $request)
	{

		$requestdata = $request->all();
		$employment_status = $requestdata['employment_status'];

		switch ($employment_status) {
			case 1:
				$users = User::whereIn('employment_status', [1, 2, 3])->get();
				break;
			case 2:
				// 社員のみ（正社員、契約社員）
				$users = User::whereIn('employment_status', [1, 2])->get();
				break;
			case 3:
				// 非常勤のみ
				$users = User::where('employment_status', 3)->get();
				break;
			default:
				$users = User::whereIn('employment_status', [1, 2, 3])->get();
				break;
		}

		//Excelテンプレート読み込み
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/export_every_teachers.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();

		$questionnaire_content_id = $id;
		$questionnaire_content = QuestionnaireContent::findOrFail($questionnaire_content_id);
		$year_month = $questionnaire_content->month;

		if ($year_month) {
			$date = explode('-', $year_month);
			$year = $date[0];
			$month = $date[1];
		}

		// 年月挿入
		$sheet->setCellValue("B2", $questionnaire_content->title);

		$questionnaire_decisions = QuestionnaireDecision::where('questionnaire_contents_id', $questionnaire_content_id)->get();

		$questionnaire_results_details = QuestionnaireResultsDetail::where('questionnaire_content_id', $questionnaire_content_id)->get();
		//idだけ取得
		$questionnaire_results_details_ids = $questionnaire_results_details->pluck('id');

		// 今回の対象のアンケートだけ取得
		$questionnaire_every_subjects = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)->get();

		// 初期化
		$questionnaire_every_subjects_user_id = array();
		$questionnaire_every_subjects_user_id['username'] = array();

		$i = 5;

		// 指定されたカテゴリーの講師のみ出力（全体、社員、非常勤のみ）
		foreach ($users as $user) {

			// 罫線を引く
			$start_height_position = $i;

			$questionnaire_every_subjects_user_id['username'][$user->last_name] = array();

			foreach ($questionnaire_every_subjects as $key => $questionnaire_every_subject) {
				// 今回の対象のアンケートで該当講師の回答があるものだけ取得
				if ($questionnaire_every_subject->user_id == $user->id) {
					$questionnaire_every_subjects_user_id['username'][$user->last_name][] = $questionnaire_every_subject;
				}
			}

			// 12/1 add
			if (!$questionnaire_every_subjects_user_id['username'][$user->last_name]) {
				continue;
			}

			// 講師毎の今回のアンケートで集計・確定した教室数評価値（複数）と教科数評価値（複数）の平均値を取得。
			$classroom_score_avg = $questionnaire_decisions->where('user_id', $user->id)->where('classroom_score', '>', 0)->avg('classroom_score');
			$subject_score_avg = $questionnaire_decisions->where('user_id', $user->id)->where('subject_score', '>', 0)->avg('subject_score');

			// 講師毎のquestion1の合計を取得
			$question1_total = array_sum(array_column(
				array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
					// return $x['question1'] > 0;

					// 0以上か99ではないか
					return $x['question1'] > 0 && $x['question1'] != 99;
				}),
				'question1'
			));
			$question1_array = array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
				// return $x['question1'] > 0;

				return $x['question1'] > 0 && $x['question1'] != 99;
			});
			if (is_countable($question1_array) && count($question1_array) > 0 && $question1_total > 0) {
				$question1_avg = round($question1_total / count($question1_array), 2);
				unset($question1_total);
				unset($question1_array);
			} else {
				$question1_avg = 0;
			}

			// 講師毎のquestion2の合計を取得
			$question2_total = array_sum(array_column(
				array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
					// return $x['question2'] > 0;

					return $x['question2'] > 0 && $x['question2'] != 99;
				}),
				'question2'
			));
			$question2_array = array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
				// return $x['question2'] > 0;

				return $x['question2'] > 0 && $x['question2'] != 99;
			});
			if (is_countable($question2_array) && $question2_total > 0) {
				$question2_avg = round($question2_total / count($question2_array), 2);
				unset($question2_total);
				unset($question2_array);
			} else {
				$question2_avg = 0;
			}

			// 講師毎のquestion3の合計を取得
			$question3_total = array_sum(array_column(
				array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
					// return $x['question3'] > 0;

					return $x['question3'] > 0 && $x['question3'] != 99;
				}),
				'question3'
			));
			$question3_array = array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
				// return $x['question3'] > 0;

				return $x['question3'] > 0 && $x['question3'] != 99;
			});
			if (is_countable($question3_array) && is_countable($question3_array) && $question3_total > 0) {
				$question3_avg = round($question3_total / count($question3_array), 2);
				unset($question3_total);
				unset($question3_array);
			} else {
				$question3_avg = 0;
			}

			// 講師毎のquestion4の合計を取得
			$question4_total = array_sum(array_column(
				array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
					// return $x['question4'] > 0;

					return $x['question4'] > 0 && $x['question4'] != 99;
				}),
				'question4'
			));
			$question4_array = array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
				// return $x['question4'] > 0;

				return $x['question4'] > 0 && $x['question4'] != 99;
			});
			if (is_countable($question4_array) && count($question4_array) > 0 && $question4_total > 0) {
				$question4_avg = round($question4_total / count($question4_array), 2);
				unset($question4_total);
				unset($question4_array);
			} else {
				$question4_avg = 0;
			}

			// 講師毎のquestion5の合計を取得
			$question5_total = array_sum(array_column(
				array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
					// return $x['question5'] > 0;

					return $x['question5'] > 0 && $x['question5'] != 99;
				}),
				'question5'
			));
			$question5_array = array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
				// return $x['question5'] > 0;

				return $x['question5'] > 0 && $x['question5'] != 99;
			});
			if (is_countable($question5_array) && count($question5_array) > 0 && $question5_total > 0) {
				$question5_avg = round($question5_total / count($question5_array), 2);
				unset($question5_total);
				unset($question5_array);
			} else {
				$question5_avg = 0;
			}

			// 講師毎のquestion6の合計を取得
			$question6_total = array_sum(array_column(
				array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
					// return $x['question6'] > 0;

					return $x['question6'] > 0 && $x['question6'] != 99;
				}),
				'question6'
			));
			$question6_array = array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
				// return $x['question6'] > 0;

				return $x['question6'] > 0 && $x['question6'] != 99;
			});
			if (is_countable($question6_array) && count($question6_array) > 0 && $question6_total > 0) {
				$question6_avg = round($question6_total / count($question6_array), 2);
				unset($question6_total);
				unset($question6_array);
			} else {
				$question6_avg = 0;
			}

			// 講師毎のquestion7の合計を取得
			$question7_total = array_sum(array_column(
				array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
					// return $x['question7'] > 0;

					return $x['question7'] > 0 && $x['question7'] != 99;
				}),
				'question7'
			));
			$question7_array = array_filter($questionnaire_every_subjects_user_id['username'][$user->last_name], function ($x) {
				// return $x['question7'] > 0;

				return $x['question7'] > 0 && $x['question7'] != 99;
			});
			if (is_countable($question7_array) && count($question7_array) > 0 && $question7_total > 0) {
				$question7_avg = round($question7_total / count($question7_array), 2);
				unset($question7_total);
				unset($question7_array);
			} else {
				$question7_avg = 0;
			}

			// Excel書き込み処理
			$sheet->setCellValue('B' . $i, $user->last_name  . $user->first_name);
			$sheet->setCellValue('C' . $i, "質問1");
			$sheet->setCellValue('D' . $i, "質問2");
			$sheet->setCellValue('E' . $i, "質問3");
			$sheet->setCellValue('F' . $i, "質問4");
			$sheet->setCellValue('G' . $i, "質問5");
			$sheet->setCellValue('H' . $i, "質問6");
			$sheet->setCellValue('I' . $i, "質問7");

			// write border outline and inner line
			$styleArray = [
				'borders' => [
					'allBorders' => [
						'borderStyle' => Border::BORDER_THIN,
						'color' => ['argb' => '000000'],
					],
				],
			];
			$sheet->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);

			$i = $i + 2;

			$sheet->setCellValue('C' . $i, $question1_avg);
			$sheet->setCellValue('D' . $i, $question2_avg);
			$sheet->setCellValue('E' . $i, $question3_avg);
			$sheet->setCellValue('F' . $i, $question4_avg);
			$sheet->setCellValue('G' . $i, $question5_avg);
			$sheet->setCellValue('H' . $i, $question6_avg);
			$sheet->setCellValue('I' . $i, $question7_avg);

			$sheet->setCellValue('B' . $i, "全体");

			$sheet->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);

			// 教室数補正値の行へ書き込み
			$i = $i + 1;
			$sheet->setCellValue('B' . $i, "教室数補正値");
			$sheet->setCellValue('C' . $i, $classroom_score_avg);
			$sheet->setCellValue('D' . $i, $classroom_score_avg);
			$sheet->setCellValue('E' . $i, $classroom_score_avg);
			$sheet->setCellValue('F' . $i, $classroom_score_avg);
			$sheet->setCellValue('G' . $i, $classroom_score_avg);
			$sheet->setCellValue('H' . $i, $classroom_score_avg);
			$sheet->setCellValue('I' . $i, $classroom_score_avg);

			$sheet->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);

			// 教科数補正値の行へ書き込み
			$i = $i + 1;
			$sheet->setCellValue('B' . $i, "教科数補正値");
			$sheet->setCellValue('C' . $i, $subject_score_avg);
			$sheet->setCellValue('D' . $i, $subject_score_avg);
			$sheet->setCellValue('E' . $i, $subject_score_avg);
			$sheet->setCellValue('F' . $i, $subject_score_avg);
			$sheet->setCellValue('G' . $i, $subject_score_avg);
			$sheet->setCellValue('H' . $i, $subject_score_avg);
			$sheet->setCellValue('I' . $i, $subject_score_avg);

			$sheet->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);

			// 補正後の行へ書き込み
			$i = $i + 1;
			$sheet->setCellValue('B' . $i, "補正後");
			$sheet->setCellValue('C' . $i, $question1_avg - $classroom_score_avg - $subject_score_avg);
			$sheet->setCellValue('D' . $i, $question2_avg - $classroom_score_avg - $subject_score_avg);
			$sheet->setCellValue('E' . $i, $question3_avg - $classroom_score_avg - $subject_score_avg);
			$sheet->setCellValue('F' . $i, $question4_avg - $classroom_score_avg - $subject_score_avg);
			$sheet->setCellValue('G' . $i, $question5_avg - $classroom_score_avg - $subject_score_avg);
			$sheet->setCellValue('H' . $i, $question6_avg - $classroom_score_avg - $subject_score_avg);
			$sheet->setCellValue('I' . $i, $question7_avg - $classroom_score_avg - $subject_score_avg);

			$sheet->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);

			// 質問別補正値の行へ書き込み
			$i = $i + 1;
			$sheet->setCellValue('B' . $i, "質問別補正値");
			$sheet->setCellValue('C' . $i, $questionnaire_content->question1_compensation);
			$sheet->setCellValue('D' . $i, $questionnaire_content->question2_compensation);
			$sheet->setCellValue('E' . $i, $questionnaire_content->question3_compensation);
			$sheet->setCellValue('F' . $i, $questionnaire_content->question4_compensation);
			$sheet->setCellValue('G' . $i, $questionnaire_content->question5_compensation);
			$sheet->setCellValue('H' . $i, $questionnaire_content->question6_compensation);
			$sheet->setCellValue('I' . $i, $questionnaire_content->question7_compensation);

			$sheet->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);

			// 集計の行へ書き込み
			$i = $i + 1;
			$sheet->setCellValue('B' . $i, "集計");
			$sheet->setCellValue('C' . $i, ($question1_avg - $classroom_score_avg - $subject_score_avg) * $questionnaire_content->question1_compensation);
			$sheet->setCellValue('D' . $i, ($question2_avg - $classroom_score_avg - $subject_score_avg) * $questionnaire_content->question2_compensation);
			$sheet->setCellValue('E' . $i, ($question3_avg - $classroom_score_avg - $subject_score_avg) * $questionnaire_content->question3_compensation);
			$sheet->setCellValue('F' . $i, ($question4_avg - $classroom_score_avg - $subject_score_avg) * $questionnaire_content->question4_compensation);
			$sheet->setCellValue('G' . $i, ($question5_avg - $classroom_score_avg - $subject_score_avg) * $questionnaire_content->question5_compensation);
			$sheet->setCellValue('H' . $i, ($question6_avg - $classroom_score_avg - $subject_score_avg) * $questionnaire_content->question6_compensation);
			$sheet->setCellValue('I' . $i, ($question7_avg - $classroom_score_avg - $subject_score_avg) * $questionnaire_content->question7_compensation);

			$sheet->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);

			$end_height_position = $i;
			// ユーザー毎の罫線を引く
			$objStyle = $sheet->getStyle('B' . $start_height_position . ':' . 'I' . $end_height_position);
			$objBorders = $objStyle->getBorders();
			$objBorders->getTop()->setBorderStyle(Border::BORDER_THIN);
			$objBorders->getBottom()->setBorderStyle(Border::BORDER_THIN);
			$objBorders->getLeft()->setBorderStyle(Border::BORDER_THIN);
			$objBorders->getRight()->setBorderStyle(Border::BORDER_THIN);

			$i = $i + 2;
		}

		$filename = $questionnaire_content->title . '講師別一覧.xlsx';

		ob_end_clean();
		ob_start();

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

	//講師別評価点一覧
	public function export_teacher_evaluation($id, Request $request)
	{
		$employment_status = $request['employment_status'];

		switch ($employment_status) {
			case 1:
				$users = User::whereIn('employment_status', [1, 2, 3])->get();
				break;
			case 2:
				// 社員のみ（正社員、契約社員）
				$users = User::whereIn('employment_status', [1, 2])->get();
				break;
			case 3:
				// 非常勤のみ
				$users = User::where('employment_status', 3)->get();
				break;
			default:
				$users = User::whereIn('employment_status', [1, 2, 3])->get();
				break;
		}

		$questionnaire_content_id = $id;
		$questionnaire_content = QuestionnaireContent::findOrFail($questionnaire_content_id);
		$year_month = $questionnaire_content->month;

		if ($year_month) {
			$date = explode('-', $year_month);
			$year = $date[0];
			$month = $date[1];
		}

		$questionnaire_content_ids = [$questionnaire_content_id];
		$questionnaire_results_details = QuestionnaireResultsDetail::whereIn('questionnaire_content_id', $questionnaire_content_ids)->get();

		//idだけ取得
		$questionnaire_results_details_ids = $questionnaire_results_details->pluck('id');

		// 今回の対象のアンケート詳細だけ取得
		// $questionnaire_every_subjects = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)->get();
		$questionnaire_every_subjects = QuestionnaireEverySubject::whereIn('questionnaire_results_details_id', $questionnaire_results_details_ids)->whereIn('user_id', $users->pluck('id'))->get();


		//講師別にグループ化(全質問）英語とか国語とか)
		$records = $questionnaire_every_subjects->groupBy('user_id');

		$sum_score_every_users = $records->map(function ($item) {

			$count = 0;

			$question1_total = $item->filter(function ($item) {
				// return $item->question1 > 0;

				// 0より大きいものかつ99ではないものを取得
				return $item->question1 > 0 && $item->question1 != 99;
			});

			// 講師別の質問1の合計値を取得してアンケート数で割る
			if (is_countable($question1_total) && count($question1_total) > 0) {
				$question1_avg = $question1_total->sum('question1') / count($question1_total);
				$count += 1;
			} else {
				$question1_avg = 0;
			}

			$question2_total = $item->filter(function ($item) {
				// return $item->question2 > 0;

				return $item->question2 > 0 && $item->question2 != 99;
			});

			// 講師別の質問2の合計値を取得してアンケート数で割る
			if (is_countable($question2_total) && count($question2_total) > 0) {
				$question2_avg = $question2_total->sum('question2') / count($question2_total);
				$count += 1;
			} else {
				$question2_avg = 0;
			}

			$question3_total = $item->filter(function ($item) {
				// return $item->question3 > 0;

				return $item->question3 > 0 && $item->question3 != 99;
			});

			// 講師別の質問3の合計値を取得してアンケート数で割る
			if (is_countable($question3_total) && count($question3_total) > 0) {
				$question3_avg = $question3_total->sum('question3') / count($question3_total);
				$count += 1;
			} else {
				$question3_avg = 0;
			}

			$question4_total = $item->filter(function ($item) {
				// return $item->question4 > 0;

				return $item->question4 > 0 && $item->question4 != 99;
			});

			// 講師別の質問4の合計値を取得してアンケート数で割る
			if (is_countable($question4_total) && count($question4_total) > 0) {
				$question4_avg = $question4_total->sum('question4') / count($question4_total);
				$count += 1;
			} else {
				$question4_avg = 0;
			}

			$question5_total = $item->filter(function ($item) {
				// return $item->question5 > 0;

				return $item->question5 > 0 && $item->question5 != 99;
			});

			// 講師別の質問5の合計値を取得してアンケート数で割る
			if (is_countable($question5_total) && count($question5_total) > 0) {
				$question5_avg = $question5_total->sum('question5') / count($question5_total);
				$count += 1;
			} else {
				$question5_avg = 0;
			}

			$question6_total = $item->filter(function ($item) {
				// return $item->question6 > 0;

				return $item->question6 > 0 && $item->question6 != 99;
			});

			// 講師別の質問6の合計値を取得してアンケート数で割る
			if (is_countable($question6_total) && count($question6_total) > 0) {
				$question6_avg = $question6_total->sum('question6') / count($question6_total);
				$count += 1;
			} else {
				$question6_avg = 0;
			}

			$question7_total = $item->filter(function ($item) {
				// return $item->question7 > 0;

				return $item->question7 > 0 && $item->question7 != 99;
			});

			// 講師別の質問7の合計値を取得してアンケート数で割る
			if (is_countable($question7_total) && count($question7_total) > 0) {
				$question7_avg = $question7_total->sum('question7') / count($question7_total);
				$count += 1;
			} else {
				$question7_avg = 0;
			}

			// 講師ID代入
			$item['groupby_user_id'] = $item->avg('user_id');

			// 質問１～７の平均値を代入
			if ($count > 0) {
				$item['sum_question'] = round(($question1_avg + $question2_avg + $question3_avg + $question4_avg + $question5_avg + $question6_avg + $question7_avg) / $count, 2);
			} else {
				$item['sum_question'] = 0;
			}

			return $item;
		});

		$sum_score_every_users = $sum_score_every_users->sortBy('groupby_user_id');

		//Excel出力
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/export_teacher_evaluation.xlsx'); //template.xlsx 読込

		$sheet = $spreadsheet->getActiveSheet();

		// タイトル挿入
		$sheet->setCellValue("B2", $questionnaire_content->title);

		$i = 5;
		foreach ($sum_score_every_users as $value) {

			// ユーザー情報取得
			$user = User::find($value['groupby_user_id']);

			if ($user) {
				$sum_question = $value['sum_question'];

				$sheet->setCellValue('B' . $i, $user->id);
				$sheet->setCellValue('C' . $i, $user->last_name . $user->first_name);
				$sheet->setCellValue('D' . $i, $user->school_buildings->name);
				$sheet->setCellValue('E' . $i, $sum_question);

				// 罫線,外側、内側設定
				$sheet->getStyle('B' . $i . ':E' . $i + 1)->applyFromArray([
					'borders' => [
						'outline' => [
							'borderStyle' => Border::BORDER_THIN,
							'color' => ['argb' => '000000'],
						],
						'inside' => [
							'borderStyle' => Border::BORDER_THIN,
							'color' => ['argb' => '000000'],
						],
					],
				]);

				// ユーザーが存在する場合のみ、iをインクリメント
				$i++;
			}
		}

		$filename = $questionnaire_content->title . '講師別評価点一覧.xlsx';

		ob_end_clean();
		ob_start();

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

	//教室別
	public function export_every_classroom($id, Request $request)
	{

		$questionnaire_content_id = $id;
		$questionnaire_content = QuestionnaireContent::findOrFail($questionnaire_content_id);

		$year_month = $questionnaire_content->month;

		if ($year_month) {
			$date = explode('-', $year_month);
			$year = $date[0];
			$month = $date[1];
		}

		$school_buildings = SchoolBuilding::all();

		//Excel出力
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/export_every_classroom.xlsx'); //template.xlsx 読込

		$sheet = $spreadsheet->getActiveSheet();

		// タイトル
		$sheet->setCellValue("B2", $questionnaire_content->title);

		$i = 3;

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		];

		// 校舎毎のアンケートデータを取得
		foreach ($school_buildings as $school_building) {

			$start_height_position = $i;

			$question1_123_count = 0;
			$question2_123_count = 0;
			$question3_123_count = 0;
			$question4_123_count = 0;
			$question5_123_count = 0;
			$question6_123_count = 0;
			$question7_123_count = 0;

			$average_question1 = 0;
			$average_question2 = 0;
			$average_question3 = 0;
			$average_question4 = 0;
			$average_question5 = 0;
			$average_question6 = 0;
			$average_question7 = 0;

			$average_chu1 = 0;
			$average_chu2 = 0;
			$average_chu3 = 0;

			$sheet->setCellValue('B' . $i, $school_building->name);
			$sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);

			$i = $i + 1;

			$sheet->setCellValue('B' . $i, '学年');
			$sheet->setCellValue('C' . $i, '質問1');
			$sheet->setCellValue('D' . $i, '質問2');
			$sheet->setCellValue('E' . $i, '質問3');
			$sheet->setCellValue('F' . $i, '質問4');
			$sheet->setCellValue('G' . $i, '質問5');
			$sheet->setCellValue('H' . $i, '質問6');
			$sheet->setCellValue('I' . $i, '質問7');
			$sheet->setCellValue('J' . $i, '平均');

			// 罫線引く
			$sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);

			$i = $i + 1;

			// 中1のアンケートデータを取得
			$questionnaire_results_details_1 = QuestionnaireResultsDetail::where('questionnaire_content_id', $questionnaire_content_id)
				->where('school_building_id', $school_building->id)
				->where('school_year_id', 10)
				->with('questionnaire_every_subjects')->get();

			$question1_count[$i] = array();

			$sheet->setCellValue('B' . $i, '中1');

			$question1_chu1_avg = 0;
			$question2_chu1_avg = 0;
			$question3_chu1_avg = 0;
			$question4_chu1_avg = 0;
			$question5_chu1_avg = 0;
			$question6_chu1_avg = 0;
			$question7_chu1_avg = 0;

			foreach ($questionnaire_results_details_1 as $questionnaire_results_detail_1) {

				// 質問1の平均値取得
				$question1_chu1_avg = round(
					$questionnaire_results_detail_1->questionnaire_every_subjects->where('question1', '>', '0')
						->where('question1', '!=', '99')
						->avg('question1'),
					2
				);
				$sheet->setCellValue('C' . $i, $question1_chu1_avg);
				if ($question1_chu1_avg > 0) {
					$question1_count[$i][] = $question1_chu1_avg;
				}

				$question2_chu1_avg =  round(
					$questionnaire_results_detail_1->questionnaire_every_subjects->where('question2', '>', '0')
						->where('question2', '!=', '99')
						->avg('question2'),
					2
				);
				$sheet->setCellValue('D' . $i, $question2_chu1_avg);
				if ($question2_chu1_avg > 0) {
					$question1_count[$i][] = $question2_chu1_avg;
				}

				$question3_chu1_avg =  round(
					$questionnaire_results_detail_1->questionnaire_every_subjects->where('question3', '>', '0')
						->where('question3', '!=', '99')
						->avg('question3'),
					2
				);
				$sheet->setCellValue('E' . $i, $question3_chu1_avg);
				if ($question3_chu1_avg > 0) {
					$question1_count[$i][] = $question3_chu1_avg;
				}

				$question4_chu1_avg =  round(
					$questionnaire_results_detail_1->questionnaire_every_subjects->where('question4', '>', '0')
						->where('question4', '!=', '99')
						->avg('question4'),
					2
				);
				$sheet->setCellValue('F' . $i, $question4_chu1_avg);
				if ($question4_chu1_avg > 0) {
					$question1_count[$i][] = $question4_chu1_avg;
				}

				$question5_chu1_avg =  round(
					$questionnaire_results_detail_1->questionnaire_every_subjects->where('question5', '>', '0')
						->where('question5', '!=', '99')
						->avg('question5'),
					2
				);
				$sheet->setCellValue('G' . $i, $question5_chu1_avg);
				if ($question5_chu1_avg  > 0) {
					$question1_count[$i][] = $question5_chu1_avg;
				}

				$question6_chu1_avg =  round(
					$questionnaire_results_detail_1->questionnaire_every_subjects->where('question6', '>', '0')
						->where('question6', '!=', '99')
						->avg('question6'),
					2
				);
				$sheet->setCellValue('H' . $i, $question6_chu1_avg);
				if ($question6_chu1_avg > 0) {
					$question1_count[$i][] = $question6_chu1_avg;
				}
				$question7_chu1_avg =  round(
					$questionnaire_results_detail_1->questionnaire_every_subjects->where('question7', '>', '0')
						->where('question7', '!=', '99')
						->avg('question7'),
					2
				);
				$sheet->setCellValue('I' . $i, $question7_chu1_avg);
				if ($question7_chu1_avg > 0) {
					$question1_count[$i][] = $question7_chu1_avg;
				}
			}

			if ($question1_count[$i]) {
				if (is_countable($question1_count[$i])) {
					$sheet->setCellValue('J' . $i, round(array_sum($question1_count[$i]) / count($question1_count[$i]), 2));
					$average_chu1 = round(array_sum($question1_count[$i]) / count($question1_count[$i]), 2);
				}
			}

			$sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);

			$i = $i + 1;

			// 中2のアンケートデータを取得
			$questionnaire_results_details_2 = QuestionnaireResultsDetail::where('questionnaire_content_id', $questionnaire_content_id)->where('school_building_id', $school_building->id)->where('school_year_id', 11)->with('questionnaire_every_subjects')->get();

			$question2_count[$i] = array();

			$sheet->setCellValue('B' . $i, '中2');

			$question1_chu2_avg = 0;
			$question2_chu2_avg = 0;
			$question3_chu2_avg = 0;
			$question4_chu2_avg = 0;
			$question5_chu2_avg = 0;
			$question6_chu2_avg = 0;
			$question7_chu2_avg = 0;

			foreach ($questionnaire_results_details_2 as $questionnaire_results_detail_2) {

				// question1の平均値取得
				$question1_chu2_avg = round(
					$questionnaire_results_detail_2->questionnaire_every_subjects->where('question1', '>', '0')
						->where('question1', '!=', '99')
						->avg('question1'),
					2
				);
				$sheet->setCellValue('C' . $i, $question1_chu2_avg);
				if ($question1_chu2_avg > 0) {
					$question2_count[$i][] = $question1_chu2_avg;
				}

				$question2_chu2_avg =  round(
					$questionnaire_results_detail_2->questionnaire_every_subjects->where('question2', '>', '0')
						->where('question2', '!=', '99')
						->avg('question2'),
					2
				);
				$sheet->setCellValue('D' . $i, $question2_chu2_avg);
				if ($question2_chu2_avg > 0) {
					$question2_count[$i][] = $question2_chu2_avg;
				}

				$question3_chu2_avg =  round(
					$questionnaire_results_detail_2->questionnaire_every_subjects->where('question3', '>', '0')
						->where('question3', '!=', '99')
						->avg('question3'),
					2
				);
				$sheet->setCellValue('E' . $i, $question3_chu2_avg);
				if ($question3_chu2_avg > 0) {
					$question2_count[$i][] = $question3_chu2_avg;
				}

				$question4_chu2_avg =  round(
					$questionnaire_results_detail_2->questionnaire_every_subjects->where('question4', '>', '0')
						->where('question4', '!=', '99')
						->avg('question4'),
					2
				);
				$sheet->setCellValue('F' . $i, $question4_chu2_avg);
				if ($question4_chu2_avg > 0) {
					$question2_count[$i][] = $question4_chu2_avg;
				}

				$question5_chu2_avg =  round(
					$questionnaire_results_detail_2->questionnaire_every_subjects->where('question5', '>', '0')
						->where('question5', '!=', '99')
						->avg('question5'),
					2
				);
				$sheet->setCellValue('G' . $i, $question5_chu2_avg);
				if ($question5_chu2_avg > 0) {
					$question2_count[$i][] = $question5_chu2_avg;
				}

				$question6_chu2_avg =  round(
					$questionnaire_results_detail_2->questionnaire_every_subjects->where('question6', '>', '0')
						->where('question6', '!=', '99')
						->avg('question6'),
					2
				);
				$sheet->setCellValue('H' . $i, $question6_chu2_avg);
				if ($question6_chu2_avg > 0) {
					$question2_count[$i][] = $question6_chu2_avg;
				}

				$question7_chu2_avg =  round(
					$questionnaire_results_detail_2->questionnaire_every_subjects->where('question7', '>', '0')
						->where('question7', '!=', '99')
						->avg('question7'),
					2
				);
				$sheet->setCellValue('I' . $i, $question7_chu2_avg);
				if ($question7_chu2_avg > 0) {
					$question2_count[$i][] = $question7_chu2_avg;
				}
			}

			if ($question2_count[$i]) {
				if (is_countable($question2_count[$i])) {
					$sheet->setCellValue('J' . $i, round(array_sum($question2_count[$i]) / count($question2_count[$i]), 2));
					$average_chu2 = round(array_sum($question2_count[$i]) / count($question2_count[$i]), 2);
				}
			}

			$sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);

			$i = $i + 1;

			// 中3のアンケートデータを取得
			$questionnaire_results_details_3 = QuestionnaireResultsDetail::where('questionnaire_content_id', $questionnaire_content_id)->where('school_building_id', $school_building->id)->where('school_year_id', 12)->with('questionnaire_every_subjects')->get();

			$question3_count[$i] = array();

			$sheet->setCellValue('B' . $i, '中3');

			$question1_chu3_avg = 0;
			$question2_chu3_avg = 0;
			$question3_chu3_avg = 0;
			$question4_chu3_avg = 0;
			$question5_chu3_avg = 0;
			$question6_chu3_avg = 0;
			$question7_chu3_avg = 0;

			foreach ($questionnaire_results_details_3 as $questionnaire_results_detail_3) {

				// question1の平均値取得
				$question1_chu3_avg = round(
					$questionnaire_results_detail_3->questionnaire_every_subjects->where('question1', '>', '0')
						->where('question1', '!=', '99')
						->avg('question1'),
					2
				);
				$sheet->setCellValue('C' . $i, $question1_chu3_avg);
				if ($question1_chu3_avg > 0) {
					$question3_count[$i][] = $question1_chu3_avg;
				}

				$question2_chu3_avg =  round(
					$questionnaire_results_detail_3->questionnaire_every_subjects->where('question2', '>', '0')
						->where('question2', '!=', '99')
						->avg('question2'),
					2
				);
				$sheet->setCellValue('D' . $i, $question2_chu3_avg);
				if ($question2_chu3_avg > 0) {
					$question3_count[$i][] = $question2_chu3_avg;
				}

				$question3_chu3_avg =  round(
					$questionnaire_results_detail_3->questionnaire_every_subjects->where('question3', '>', '0')
						->where('question3', '!=', '99')
						->avg('question3'),
					2
				);
				$sheet->setCellValue('E' . $i, $question3_chu3_avg);
				if ($question3_chu3_avg > 0) {
					$question3_count[$i][] = $question3_chu3_avg;
				}

				$question4_chu3_avg =  round(
					$questionnaire_results_detail_3->questionnaire_every_subjects->where('question4', '>', '0')
						->where('question4', '!=', '99')
						->avg('question4'),
					2
				);
				$sheet->setCellValue('F' . $i, $question4_chu3_avg);
				if ($question4_chu3_avg > 0) {
					$question3_count[$i][] = $question4_chu3_avg;
				}

				$question5_chu3_avg =  round(
					$questionnaire_results_detail_3->questionnaire_every_subjects->where('question5', '>', '0')
						->where('question5', '!=', '99')
						->avg('question5'),
					2
				);
				$sheet->setCellValue('G' . $i, $question5_chu3_avg);
				if ($question5_chu3_avg > 0) {
					$question3_count[$i][] = $question5_chu3_avg;
				}

				$question6_chu3_avg =  round(
					$questionnaire_results_detail_3->questionnaire_every_subjects->where('question6', '>', '0')
						->where('question6', '!=', '99')
						->avg('question6'),
					2
				);
				$sheet->setCellValue('H' . $i, $question6_chu3_avg);
				if ($question6_chu3_avg > 0) {
					$question3_count[$i][] = $question6_chu3_avg;
				}

				$question7_chu3_avg =  round(
					$questionnaire_results_detail_3->questionnaire_every_subjects->where('question7', '>', '0')
						->where('question7', '!=', '99')
						->avg('question7'),
					2
				);
				$sheet->setCellValue('I' . $i, $question7_chu3_avg);
				if ($question7_chu3_avg > 0) {
					$question3_count[$i][] = $question7_chu3_avg;
				}
			}

			if ($question3_count[$i]) {
				if (is_countable($question3_count[$i])) {
					$sheet->setCellValue('J' . $i, round(array_sum($question3_count[$i]) / count($question3_count[$i]), 2));
					$average_chu3 = round(array_sum($question3_count[$i]) / count($question3_count[$i]), 2);
				}
			}

			$sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);

			$i = $i + 1;
			$sheet->setCellValue('B' . $i, "平均");

			// 質問1の平均
			$question1_chu123 = $question1_chu1_avg + $question1_chu2_avg + $question1_chu3_avg;
			if ($question1_chu1_avg > 0)
				$question1_123_count += 1;
			if ($question1_chu2_avg > 0)
				$question1_123_count += 1;
			if ($question1_chu3_avg > 0)
				$question1_123_count += 1;

			if ($question1_123_count > 0) {
				$sheet->setCellValue('C' . $i, round($question1_chu123 / $question1_123_count, 2));
				$average_question1 = round($question1_chu123 / $question1_123_count, 2);
			}

			unset($question1_123_count);

			// 質問2の平均
			$question2_chu123 = $question2_chu1_avg + $question2_chu2_avg + $question2_chu3_avg;
			if ($question2_chu1_avg > 0)
				$question2_123_count += 1;
			if ($question2_chu2_avg > 0)
				$question2_123_count += 1;
			if ($question2_chu3_avg > 0)
				$question2_123_count += 1;

			if ($question2_123_count > 0) {
				$sheet->setCellValue('D' . $i, round($question2_chu123 / $question2_123_count, 2));
				$average_question2 = round($question2_chu123 / $question2_123_count, 2);
			}

			unset($question2_123_count);

			// 質問3の平均
			$question3_chu123 = $question3_chu1_avg + $question3_chu2_avg + $question3_chu3_avg;
			if ($question3_chu1_avg > 0)
				$question3_123_count += 1;
			if ($question3_chu2_avg > 0)
				$question3_123_count += 1;
			if ($question3_chu3_avg > 0)
				$question3_123_count += 1;

			if ($question3_123_count > 0) {
				$sheet->setCellValue('E' . $i, round($question3_chu123 / $question3_123_count, 2));
				$average_question3 = round($question3_chu123 / $question3_123_count, 2);
			}

			unset($question3_123_count);

			// 質問4の平均
			$question4_chu123 = $question4_chu1_avg + $question4_chu2_avg + $question4_chu3_avg;
			if ($question4_chu1_avg > 0)
				$question4_123_count += 1;
			if ($question4_chu2_avg > 0)
				$question4_123_count += 1;
			if ($question4_chu3_avg > 0)
				$question4_123_count += 1;

			if ($question4_123_count > 0) {
				$sheet->setCellValue('F' . $i, round($question4_chu123 / $question4_123_count, 2));
				$average_question4 = round($question4_chu123 / $question4_123_count, 2);
			}

			unset($question4_123_count);

			// 質問5の平均
			$question5_chu123 = $question5_chu1_avg + $question5_chu2_avg + $question5_chu3_avg;
			if ($question5_chu1_avg > 0)
				$question5_123_count += 1;
			if ($question5_chu2_avg > 0)
				$question5_123_count += 1;
			if ($question5_chu3_avg > 0)
				$question5_123_count += 1;

			if ($question5_123_count > 0) {
				$sheet->setCellValue('G' . $i, round($question5_chu123 / $question5_123_count, 2));
				$average_question5 = round($question5_chu123 / $question5_123_count, 2);
			}

			unset($question5_123_count);


			// 質問6の平均
			$question6_chu123 = $question6_chu1_avg + $question6_chu2_avg + $question6_chu3_avg;
			if ($question6_chu1_avg > 0)
				$question6_123_count += 1;
			if ($question6_chu2_avg > 0)
				$question6_123_count += 1;
			if ($question6_chu3_avg > 0)
				$question6_123_count += 1;

			if ($question6_123_count > 0) {
				$sheet->setCellValue('H' . $i, round($question6_chu123 / $question6_123_count, 2));
				$average_question6 = round($question6_chu123 / $question6_123_count, 2);
			}

			unset($question6_123_count);

			// 質問7の平均
			$question7_chu123 = $question7_chu1_avg + $question7_chu2_avg + $question7_chu3_avg;
			if ($question7_chu1_avg > 0)
				$question7_123_count += 1;
			if ($question7_chu2_avg > 0)
				$question7_123_count += 1;
			if ($question7_chu3_avg > 0)
				$question7_123_count += 1;

			if ($question7_123_count > 0) {
				$sheet->setCellValue('I' . $i, round($question7_chu123 / $question7_123_count, 2));
				$average_question7 = round($question7_chu123 / $question7_123_count, 2);
			}

			unset($question7_123_count);

			// 平均の平均
			$average_chu123 = $average_question1 + $average_question2 + $average_question3 + $average_question4 + $average_question5 + $average_question6 + $average_question7;

			$average_chu123_count = 0;
			if ($average_question1 > 0)
				$average_chu123_count += 1;
			if ($average_question2 > 0)
				$average_chu123_count += 1;
			if ($average_question3 > 0)
				$average_chu123_count += 1;
			if ($average_question4 > 0)
				$average_chu123_count += 1;
			if ($average_question5 > 0)
				$average_chu123_count += 1;
			if ($average_question6 > 0)
				$average_chu123_count += 1;
			if ($average_question7 > 0)
				$average_chu123_count += 1;

			if ($average_chu123_count > 0)
				$sheet->setCellValue('J' . $i, round($average_chu123 / $average_chu123_count, 2));

			$sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);

			$i = $i + 1;
			$sheet->setCellValue('B' . $i, "補正値");
			$sheet->setCellValue('C' . $i, $questionnaire_content->question1_compensation);
			$sheet->setCellValue('D' . $i, $questionnaire_content->question2_compensation);
			$sheet->setCellValue('E' . $i, $questionnaire_content->question3_compensation);
			$sheet->setCellValue('F' . $i, $questionnaire_content->question4_compensation);
			$sheet->setCellValue('G' . $i, $questionnaire_content->question5_compensation);
			$sheet->setCellValue('H' . $i, $questionnaire_content->question6_compensation);
			$sheet->setCellValue('I' . $i, $questionnaire_content->question7_compensation);

			$sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);

			$i = $i + 1;
			$sheet->setCellValue('B' . $i, "集計値");
			$sheet->setCellValue('C' . $i, $average_question1 * $questionnaire_content->question1_compensation);
			$sheet->setCellValue('D' . $i, $average_question2 * $questionnaire_content->question2_compensation);
			$sheet->setCellValue('E' . $i, $average_question3 * $questionnaire_content->question3_compensation);
			$sheet->setCellValue('F' . $i, $average_question4 * $questionnaire_content->question4_compensation);
			$sheet->setCellValue('G' . $i, $average_question5 * $questionnaire_content->question5_compensation);
			$sheet->setCellValue('H' . $i, $average_question6 * $questionnaire_content->question6_compensation);
			$sheet->setCellValue('I' . $i, $average_question7 * $questionnaire_content->question7_compensation);


			$end_height_position = $i;

			// ユーザー毎の罫線を引く
			$objStyle = $sheet->getStyle('B' . $start_height_position . ':' . 'J' . $end_height_position);
			// ボーダーオブジェクト取得([B2]セル)
			$objBorders = $objStyle->getBorders();
			// ボーダー全て(Top, Bottom, Left, Right)を「細い線:BORDER_THIN」に設定
			$objBorders->getTop()->setBorderStyle(Border::BORDER_THIN);
			// $objBorders->getTop()->setColor(new Color(Color::COLOR_THIN));   //  例として色設定
			$objBorders->getBottom()->setBorderStyle(Border::BORDER_THIN);
			$objBorders->getLeft()->setBorderStyle(Border::BORDER_THIN);
			$objBorders->getRight()->setBorderStyle(Border::BORDER_THIN);

			$sheet->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);

			$i = $i + 1;
		}

		$filename = $questionnaire_content->title . '教室別一覧.xlsx';

		ob_end_clean();
		ob_start();

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

	//import pdf
	public function import()
	{
		$questionnaire_contents = array();

		$school_buildings = SchoolBuilding::all();
		$questionnaire_contents_collection = QuestionnaireContent::all();
		foreach ($questionnaire_contents_collection as $questionnaire_content) {
			if ($questionnaire_content->questionnaire_decisions->isEmpty()) {
				$questionnaire_contents[] = $questionnaire_content;
			}
		}

		return view('questionnaire_import.create', compact('school_buildings', 'questionnaire_contents'));
	}

	//store import jpg
	public function store_import(Request $request)
	{

		$request->validate([
			// 	'file' => 'required|file|mimes:pdf',
			'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:50000',
		]);

		$school_building_id = $request->school_building_id;
		$questionnaire_content_id = $request->questionnaire_content_id;
		$file = $request->file('file');

		if (!is_null($file)) {

			// 事前にjpgファイルを削除する
			$dir = 'upFiles';
			$files = Storage::disk('local')->files($dir);
			foreach ($files as $file_info) {
				Storage::disk('local')->delete($file_info);
			}

			date_default_timezone_set('Asia/Tokyo');

			// 指定ディレクトリにファイルを保存
			$fileName = 'an1.pdf';

			$dir = 'upFiles';
			$file->storeAs($dir, $fileName, ['disk' => 'local']);
		}

		// アップロードしたPDFを読み込んで、画像に変換する
		$process2 = new Process("pdftoppm -jpeg -scale-to 3506 /var/www/html/shinzemi/storage/app/upFiles/an1.pdf /var/www/html/shinzemi/storage/app/upFiles/ancake ");
		$process2->setTimeout(1600);
		$process2->run();

		if (!$process2->isSuccessful()) {
			throw new ProcessFailedException($process2);
		}

		// 画像読み取り用のpythonファイルを実行する
		$process = new Process("python3 /var/www/html/shinzemi/ancake/aupy2.py");
		$process->setTimeout(1600);
		$process->run();

		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
		}

		// 実行したpythonファイルの結果を取得する
		$data = $process->getOutput();
		$json = json_decode($data, true);

		return view('questionnaire_import.edit', compact('data', 'json', 'school_building_id', 'questionnaire_content_id'));
	}

	public function confirm_import(Request $request)
	{
	}

	public function index_export()
	{

		$questionnaire_contents = QuestionnaireContent::all();

		return view('questionnaire_output.index', compact('questionnaire_contents'));
	}

	public function stores(Request $request)
	{

		$this->validate($request, [
			// "management_code" => "nullable|digits:2", //integer('management_code',2)->nullable()
			// "questionnaire_content_id" => "nullable|digits:2", //integer('questionnaire_content_id',2)->nullable()
			// "school_building_id" => "nullable|digits:3", //integer('school_building_id',3)->nullable()
			// "school_year_id" => "nullable|digits:2", //integer('school_year_id',2)->nullable()

			// "alphabet_id_1" => "nullable|digits:2", //integer('alphabet_id_1',2)->nullable()
			// "subject_id_1" => "nullable|digits:2", //integer('subject_id_1',2)->nullable()
			// "user_id_1" => "nullable|integer", //integer('user_id_1')->nullable()
			// "question_1_1" => "nullable|digits:3", //integer('question_1_1',3)->nullable()
			// "question_2_1" => "nullable|digits:3", //integer('question_2_1',3)->nullable()
			// "question_3_1" => "nullable|digits:3", //integer('question_3_1',3)->nullable()
			// "question_4_1" => "nullable|digits:3", //integer('question_4_1',3)->nullable()
			// "question_5_1" => "nullable|digits:3", //integer('question_5_1',3)->nullable()
			// "question_6_1" => "nullable|digits:3", //integer('question_6_1',3)->nullable()
			// "question_7_1" => "nullable|digits:3", //integer('question_7_1',3)->nullable()

			// "alphabet_id_2" => "nullable|digits:2", //integer('alphabet_id_2',2)->nullable()
			// "subject_id_2" => "nullable|digits:2", //integer('subject_id_2',2)->nullable()
			// "user_id_2" => "nullable|digits:2", //integer('user_id_2',2)->nullable()
			// "question_1_2" => "nullable|digits:3", //integer('question_1_2',3)->nullable()
			// "question_2_2" => "nullable|digits:3", //integer('question_2_2',3)->nullable()
			// "question_3_2" => "nullable|digits:3", //integer('question_3_2',3)->nullable()
			// "question_4_2" => "nullable|digits:3", //integer('question_4_2',3)->nullable()
			// "question_5_2" => "nullable|digits:3", //integer('question_5_2',3)->nullable()
			// "question_6_2" => "nullable|digits:3", //integer('question_6_2',3)->nullable()
			// "question_7_2" => "nullable|digits:3", //integer('question_7_2',3)->nullable()

			// "alphabet_id_3" => "nullable|digits:2", //integer('alphabet_id_3',2)->nullable()
			// "subject_id_3" => "nullable|digits:2", //integer('subject_id_3',2)->nullable()
			// "user_id_3" => "nullable|digits:2", //integer('user_id_3',2)->nullable()
			// "question_1_3" => "nullable|digits:3", //integer('question_1_3',3)->nullable()
			// "question_2_3" => "nullable|digits:3", //integer('question_2_3',3)->nullable()
			// "question_3_3" => "nullable|digits:3", //integer('question_3_3',3)->nullable()
			// "question_4_3" => "nullable|digits:3", //integer('question_4_3',3)->nullable()
			// "question_5_3" => "nullable|digits:3", //integer('question_5_3',3)->nullable()
			// "question_6_3" => "nullable|digits:3", //integer('question_6_3',3)->nullable()
			// "question_7_3" => "nullable|digits:3", //integer('question_7_3',3)->nullable()

			// "alphabet_id_4" => "nullable|digits:2", //integer('alphabet_id_4',2)->nullable()
			// "subject_id_4" => "nullable|digits:2", //integer('subject_id_4',2)->nullable()
			// "user_id_4" => "nullable|integer", //integer('user_id_4')->nullable()
			// "question_1_4" => "nullable|digits:3", //integer('question_1_4',3)->nullable()
			// "question_2_4" => "nullable|digits:3", //integer('question_2_4',3)->nullable()
			// "question_3_4" => "nullable|digits:3", //integer('question_3_4',3)->nullable()
			// "question_4_4" => "nullable|digits:3", //integer('question_4_4',3)->nullable()
			// "question_5_4" => "nullable|digits:3", //integer('question_5_4',3)->nullable()
			// "question_6_4" => "nullable|digits:3", //integer('question_6_4',3)->nullable()
			// "question_7_4" => "nullable|digits:3", //integer('question_7_4',3)->nullable()

			// "alphabet_id_5" => "nullable|digits:2", //integer('alphabet_id_5',2)->nullable()
			// "subject_id_5" => "nullable|digits:2", //integer('subject_id_5',2)->nullable()
			// "user_id_5" => "nullable|digits:3", //integer('user_id_5',3)->nullable()
			// "question_1_5" => "nullable|digits:3", //integer('question_1_5',3)->nullable()
			// "question_2_5" => "nullable|digits:3", //integer('question_2_5',3)->nullable()
			// "question_3_5" => "nullable|digits:3", //integer('question_3_5',3)->nullable()
			// "question_4_5" => "nullable|digits:3", //integer('question_4_5',3)->nullable()
			// "question_5_5" => "nullable|digits:3", //integer('question_5_5',3)->nullable()
			// "question_6_5" => "nullable|digits:3", //integer('question_6_5',3)->nullable()
			// "question_7_5" => "nullable|integer", //integer('question_7_5')->nullable()
			// "Creator" => "nullable|digits:3", //integer('Creator',3)->nullable()
			// "Updater" => "nullable|digits:3", //integer('Updater',3)->nullable()

		]);
		$requestData = $request->all();
		$school_building_id = $request->school_building_id;

		// アンケート1枚ずつ保存

		foreach ($requestData['array'] as $value) {
			########################################################################################
			#QuestionnaireResultsDetail保存
			########################################################################################

			$questionnaireresultsdetail = new QuestionnaireResultsDetail();

			$questionnaireresultsdetail->questionnaire_content_id = $request->questionnaire_content_id;

			$questionnaireresultsdetail->management_code = $value['id'];

			// 校舎
			$questionnaireresultsdetail->school_building_id = $school_building_id;

			// 学年
			$questionnaireresultsdetail->school_year_id = $value['school_year'];

			$questionnaireresultsdetail->save();

			$last_insert_id = $questionnaireresultsdetail->id;
			########################################################################################
			#各科目のアンケート結果を保存
			########################################################################################
			################################################################################
			#英語
			################################################################################
			$questionnaireeverysubject = new QuestionnaireEverySubject();

			$questionnaireeverysubject->questionnaire_results_details_id = $last_insert_id;
			// 科目
			$questionnaireeverysubject->subject_id = 1;
			// クラス
			$questionnaireeverysubject->alphabet_id = $value['class_1'];
			###############################################################
			#校舎、学年、科目、クラスを元に講師IDを取得
			##############################################################
			// 英語のクラスが0の場合は、英語のアンケート結果を保存しない
			if ($questionnaireeverysubject->alphabet_id) {

				$query = SubjectTeacher::where('school_building_id', $school_building_id)
					->where('school_year', $value['school_year'])
					->where('classification_code_class', 1)
					->where('item_no_class', $value['class_1'])
					->first();

				if ($query) {
					$user_id = ($query->user_id);
					$questionnaireeverysubject->user_id = $user_id;
				}


				$questionnaireeverysubject->question1 = $value['question_1_1'] ? $value['question_1_1'] : 0;
				$questionnaireeverysubject->question2 = $value['question_2_1'] ? $value['question_2_1'] : 0;
				$questionnaireeverysubject->question3 = $value['question_3_1'] ? $value['question_3_1'] : 0;
				$questionnaireeverysubject->question4 = $value['question_4_1'] ? $value['question_4_1'] : 0;
				$questionnaireeverysubject->question5 = $value['question_5_1'] ? $value['question_5_1'] : 0;
				$questionnaireeverysubject->question6 = $value['question_6_1'] ? $value['question_6_1'] : 0;
				$questionnaireeverysubject->question7 = $value['question_7_1'] ? $value['question_7_1'] : 0;

				$questionnaireeverysubject->save();
			}

			################################################################################
			#理科
			################################################################################
			$questionnaireeverysubject = new QuestionnaireEverySubject();

			$questionnaireeverysubject->questionnaire_results_details_id = $last_insert_id;
			// 理科
			$questionnaireeverysubject->subject_id = 2;
			$questionnaireeverysubject->alphabet_id = $value['class_2'];

			if ($questionnaireeverysubject->alphabet_id) {

				// 校舎、学年、科目、クラスを元に講師IDを取得(学年とクラスが取得出来てないと駄目。)
				$query = SubjectTeacher::where('school_building_id', $school_building_id)
					->where('school_year', $value['school_year'])
					->where('classification_code_class', 2)
					->where('item_no_class', $value['class_2'])
					->first();

				if ($query) {

					$user_id = ($query->user_id);
					$questionnaireeverysubject->user_id = $user_id;
				}

				$questionnaireeverysubject->question1 = $value['question_1_2'] ? $value['question_1_2'] : 0;
				$questionnaireeverysubject->question2 = $value['question_2_2'] ? $value['question_2_2'] : 0;
				$questionnaireeverysubject->question3 = $value['question_3_2'] ? $value['question_3_2'] : 0;
				$questionnaireeverysubject->question4 = $value['question_4_2'] ? $value['question_4_2'] : 0;
				$questionnaireeverysubject->question5 = $value['question_5_2'] ? $value['question_5_2'] : 0;
				$questionnaireeverysubject->question6 = $value['question_6_2'] ? $value['question_6_2'] : 0;
				$questionnaireeverysubject->question7 = $value['question_7_2'] ? $value['question_7_2'] : 0;

				$questionnaireeverysubject->save();
			}

			################################################################################
			#数学
			################################################################################
			$questionnaireeverysubject = new QuestionnaireEverySubject();

			$questionnaireeverysubject->questionnaire_results_details_id = $last_insert_id;
			// 数学
			$questionnaireeverysubject->subject_id = 3;
			$questionnaireeverysubject->alphabet_id = $value['class_3'];

			if ($questionnaireeverysubject->alphabet_id) {

				// 校舎、学年、科目、クラスを元に講師IDを取得(学年とクラスが取得出来てないと駄目。)
				$query = SubjectTeacher::where('school_building_id', $school_building_id)
					->where('school_year', $value['school_year'])
					->where('classification_code_class', 3)
					->where('item_no_class', $value['class_3'])
					->first();

				if ($query) {
					$user_id = ($query->user_id);
					$questionnaireeverysubject->user_id = $user_id;
				}

				$questionnaireeverysubject->question1 = $value['question_1_3'] ? $value['question_1_3'] : 0;
				$questionnaireeverysubject->question2 = $value['question_2_3'] ? $value['question_2_3'] : 0;
				$questionnaireeverysubject->question3 = $value['question_3_3'] ? $value['question_3_3'] : 0;
				$questionnaireeverysubject->question4 = $value['question_4_3'] ? $value['question_4_3'] : 0;
				$questionnaireeverysubject->question5 = $value['question_5_3'] ? $value['question_5_3'] : 0;
				$questionnaireeverysubject->question6 = $value['question_6_3'] ? $value['question_6_3'] : 0;
				$questionnaireeverysubject->question7 = $value['question_7_3'] ? $value['question_7_3'] : 0;

				$questionnaireeverysubject->save();
			}
			################################################################################
			#国語
			################################################################################
			$questionnaireeverysubject = new QuestionnaireEverySubject();

			$questionnaireeverysubject->questionnaire_results_details_id = $last_insert_id;
			// 国語
			$questionnaireeverysubject->subject_id = 4;
			$questionnaireeverysubject->alphabet_id = $value['class_4'];

			if ($questionnaireeverysubject->alphabet_id) {

				// 校舎、学年、科目、クラスを元に講師IDを取得(学年とクラスが取得出来てないと駄目。)
				$query = SubjectTeacher::where('school_building_id', $school_building_id)
					->where('school_year', $value['school_year'])
					->where('classification_code_class', 4)
					->where('item_no_class', $value['class_4'])
					->first();

				if ($query) {

					$user_id = ($query->user_id);
					$questionnaireeverysubject->user_id = $user_id;
				}

				$questionnaireeverysubject->question1 = $value['question_1_4'] ? $value['question_1_4'] : 0;
				$questionnaireeverysubject->question2 = $value['question_2_4'] ? $value['question_2_4'] : 0;
				$questionnaireeverysubject->question3 = $value['question_3_4'] ? $value['question_3_4'] : 0;
				$questionnaireeverysubject->question4 = $value['question_4_4'] ? $value['question_4_4'] : 0;
				$questionnaireeverysubject->question5 = $value['question_5_4'] ? $value['question_5_4'] : 0;
				$questionnaireeverysubject->question6 = $value['question_6_4'] ? $value['question_6_4'] : 0;
				$questionnaireeverysubject->question7 = $value['question_7_4'] ? $value['question_7_4'] : 0;

				$questionnaireeverysubject->save();
			}
			################################################################################
			#社会
			################################################################################
			$questionnaireeverysubject = new QuestionnaireEverySubject();

			$questionnaireeverysubject->questionnaire_results_details_id = $last_insert_id;
			// 社会
			$questionnaireeverysubject->subject_id = 5;
			$questionnaireeverysubject->alphabet_id = $value['class_5'];

			if ($questionnaireeverysubject->alphabet_id) {

				// 校舎、学年、科目、クラスを元に講師IDを取得(学年とクラスが取得出来てないと駄目。)
				$query = SubjectTeacher::where('school_building_id', $school_building_id)
					->where('school_year', $value['school_year'])
					->where('classification_code_class', 5)
					->where('item_no_class', $value['class_5'])
					->first();

				if ($query) {
					$user_id = ($query->user_id);
					$questionnaireeverysubject->user_id = $user_id;
				}

				$questionnaireeverysubject->question1 = $value['question_1_5'] ? $value['question_1_5'] : 0;
				$questionnaireeverysubject->question2 = $value['question_2_5'] ? $value['question_2_5'] : 0;
				$questionnaireeverysubject->question3 = $value['question_3_5'] ? $value['question_3_5'] : 0;
				$questionnaireeverysubject->question4 = $value['question_4_5'] ? $value['question_4_5'] : 0;
				$questionnaireeverysubject->question5 = $value['question_5_5'] ? $value['question_5_5'] : 0;
				$questionnaireeverysubject->question6 = $value['question_6_5'] ? $value['question_6_5'] : 0;
				$questionnaireeverysubject->question7 = $value['question_7_5'] ? $value['question_7_5'] : 0;

				$questionnaireeverysubject->save();
			}

			################################################################################
			#その他
			################################################################################
			$questionnaireeverysubject = new QuestionnaireEverySubject();

			$questionnaireeverysubject->questionnaire_results_details_id = $last_insert_id;
			// その他
			$questionnaireeverysubject->subject_id = 6;
			$questionnaireeverysubject->alphabet_id = $value['class_6'];

			if ($questionnaireeverysubject->alphabet_id) {

				// 校舎、学年、科目、クラスを元に講師IDを取得(学年とクラスが取得出来てないと駄目。)
				$query = SubjectTeacher::where('school_building_id', $school_building_id)
					->where('school_year', $value['school_year'])
					->where('classification_code_class', 6)
					->where('item_no_class', $value['class_6'])
					->first();

				if ($query) {
					$user_id = ($query->user_id);
					$questionnaireeverysubject->user_id = $user_id;
				}

				$questionnaireeverysubject->question1 = $value['question_1_6'] ? $value['question_1_6'] : 0;
				$questionnaireeverysubject->question2 = $value['question_2_6'] ? $value['question_2_6'] : 0;
				$questionnaireeverysubject->question3 = $value['question_3_6'] ? $value['question_3_6'] : 0;
				$questionnaireeverysubject->question4 = $value['question_4_6'] ? $value['question_4_6'] : 0;
				$questionnaireeverysubject->question5 = $value['question_5_6'] ? $value['question_5_6'] : 0;
				$questionnaireeverysubject->question6 = $value['question_6_6'] ? $value['question_6_6'] : 0;
				$questionnaireeverysubject->question7 = $value['question_7_6'] ? $value['question_7_6'] : 0;

				$questionnaireeverysubject->save();
			}
		}

		return redirect("/shinzemi/questionnaire_results_detail")->with("flash_message", "インポート完了しました");
	}


	//=======================================================================
}