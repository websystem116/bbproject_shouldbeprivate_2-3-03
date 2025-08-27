<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\QuestionnaireDecision;
use App\QuestionnaireContent;
use App\QuestionnaireScore;
use App\SubjectTeacher;
use App\SubjectTeachersHistorie;
//=======================================================================
class QuestionnaireDecisionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get("search");
        $perPage = 25;

        if (!empty($keyword)) {
            $questionnaire_decision = QuestionnaireDecision::where("id", "LIKE", "%$keyword%")->orWhere("questionnaire_contents_id", "LIKE", "%$keyword%")->orWhere("user_id", "LIKE", "%$keyword%")->orWhere("classroom_score", "LIKE", "%$keyword%")->orWhere("subject_score", "LIKE", "%$keyword%")->paginate($perPage);
        } else {
            $questionnaire_decision = QuestionnaireDecision::paginate($perPage);
        }
        return view("questionnaire_decision.index", compact("questionnaire_decision"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $questionnaire_contents_name = [];
        $questionnaire_contents = QuestionnaireContent::all();
        foreach ($questionnaire_contents as $questionnaire_content) {
            if ($questionnaire_content->questionnaire_decisions->isEmpty()) {
                $questionnaire_contents_name[] = $questionnaire_content;
            }
        }

        return view("questionnaire_decision.create", compact("questionnaire_contents_name"));
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
        // アンケート集計・確定ボタン押下時に,現時点での講師別アンケ―ト数値マスタをコピーする

        $this->validate($request, [
            // "questionnaire_contents_id" => "nullable|integer", //integer('questionnaire_contents_id')->nullable()
            // "user_id" => "nullable|digits:3", //integer('user_id',3)->nullable()
            // "classroom_score" => "nullable|digits:5", //integer('classroom_score',5)->nullable()
            // "subject_score" => "nullable|digits:5", //integer('subject_score',5)->nullable()
        ]);
        $questionnaire_scores = QuestionnaireScore::all();

        $requestData = $request->all();
        $questionnaire_content_id = $request->questionnaire_content_id;
        // $questionnaire_scoresをQuestionnaireDecisionにコピー
        foreach ($questionnaire_scores as $questionnaire_score) {
            if ($questionnaire_score->classroom_score !== "") {

                $questionnaire_decision = new QuestionnaireDecision();
                $questionnaire_decision->questionnaire_contents_id = $questionnaire_content_id;
                $questionnaire_decision->user_id = $questionnaire_score->user_id;
                $questionnaire_decision->classroom_score = $questionnaire_score->classroom_score;
                $questionnaire_decision->subject_score = $questionnaire_score->subject_score;
                $questionnaire_decision->save();
            } elseif ($questionnaire_score->subject_score !== "") {
                $questionnaire_decision = new QuestionnaireDecision();
                $questionnaire_decision->questionnaire_contents_id = $questionnaire_content_id;
                $questionnaire_decision->user_id = $questionnaire_score->user_id;
                $questionnaire_decision->classroom_score = $questionnaire_score->classroom_score;
                $questionnaire_decision->subject_score = $questionnaire_score->subject_score;
                $questionnaire_decision->save();
            }
        }


        $subject_teachers = SubjectTeacher::all();
        // $subject_teachersをSubjectTeachersHistorieにコピー
        foreach ($subject_teachers as $subject_teacher) {

            $subject_teachers_historie = new SubjectTeachersHistorie();

            $subject_teachers_historie->questionnaire_contents_id = $questionnaire_content_id;
            $subject_teachers_historie->school_year = $subject_teacher->school_year;
            $subject_teachers_historie->classification_code_class = $subject_teacher->classification_code_class;
            $subject_teachers_historie->item_no_class = $subject_teacher->item_no_class;
            $subject_teachers_historie->user_id = $subject_teacher->user_id;

            $subject_teachers_historie->save();
        }

        return redirect()->route("questionnaire_decision.create")->with("flash_message", "確定しました。");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $questionnaire_decision = QuestionnaireDecision::findOrFail($id);
        return view("questionnaire_decision.show", compact("questionnaire_decision"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $questionnaire_decision = QuestionnaireDecision::findOrFail($id);

        return view("questionnaire_decision.edit", compact("questionnaire_decision"));
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
            "questionnaire_contents_id" => "nullable|integer", //integer('questionnaire_contents_id')->nullable()
            "user_id" => "nullable|digits:3", //integer('user_id',3)->nullable()
            "classroom_score" => "nullable|digits:5", //integer('classroom_score',5)->nullable()
            "subject_score" => "nullable|digits:5", //integer('subject_score',5)->nullable()

        ]);
        $requestData = $request->all();

        $questionnaire_decision = QuestionnaireDecision::findOrFail($id);
        $questionnaire_decision->update($requestData);

        return redirect("/shinzemi/questionnaire_decision")->with("flash_message", "questionnaire_decision updated!");
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
        QuestionnaireDecision::destroy($id);

        return redirect("/shinzemi/questionnaire_decision")->with("flash_message", "questionnaire_decision deleted!");
    }
}
    //=======================================================================