<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\QuestionnaireScore;
use App\User;
use App\SchoolBuilding;

//=======================================================================
class QuestionnaireScoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $perPage = 25;

        // $users = User::paginate($perPage);
        $users_for_select = User::all();

        $keyword = $request->get("search");

        if (!empty($keyword)) {
            $questionnaire_score = QuestionnaireScore::Join('users', 'users.id', '=', 'questionnaire_scores.user_id')
                ->Where("users.id", "=", $keyword)
                ->paginate($perPage);

            $users = User::where("id", "=", $keyword)->paginate($perPage);
        } else {
            $questionnaire_score = QuestionnaireScore::paginate($perPage);

            $users = User::paginate($perPage);
        }
        return view("questionnaire_score.index", compact("questionnaire_score", "users", "users_for_select"));
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

        return view("questionnaire_score.create", compact("users", "school_buildings"));
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
            // "user_id" => "nullable|digits:3", //integer('user_id',3)->nullable()
            // "classroom_score" => "nullable|digits:5", //integer('classroom_score',5)->nullable()
            // "subject_score" => "nullable|digits:5", //integer('subject_score',5)->nullable()
        ]);
        $requestDatas = $request->all();

        QuestionnaireScore::where('user_id', '=', $requestDatas['user_id'])->delete();

        for ($i = 0; $i < count($requestDatas['school_building_id']); $i++) {
            $requestData = ['classroom_score' => $requestDatas['classroom_score'][$i], 'subject_score' => $requestDatas['subject_score'][$i], 'school_building_id' => $requestDatas['school_building_id'][$i], 'user_id' => $requestDatas['user_id']];
            QuestionnaireScore::create($requestData);
        }

        return redirect("/shinzemi/questionnaire_score")->with("flash_message", "データが登録されました。");
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
        $questionnaire_score = QuestionnaireScore::findOrFail($id);
        return view("questionnaire_score.show", compact("questionnaire_score"));
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
        $url = url()->previous();
        // sessionにURLを保存
        session(["url" => $url]);

        $school_buildings = SchoolBuilding::all();
        foreach ($school_buildings as $key => $school_building) {
            $questionnaire_scores[$key] = QuestionnaireScore::where('user_id', '=', $id)->where('school_building_id', '=', $school_building->id)->get()->first();
            if (!$questionnaire_scores[$key]) {
                $questionnaire_scores[$key] = new QuestionnaireScore();
            }
            $questionnaire_scores[$key]['school_building_key'] = $school_building->id;
            $questionnaire_scores[$key]['school_building_name'] = $school_building->name;
        }

        // $questionnaire_scores = QuestionnaireScore::where('user_id', '=', $id)->get();
        $user = User::find($id);

        return view("questionnaire_score.edit", compact("questionnaire_scores", "school_buildings", "id", "user"));
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
            // "user_id" => "nullable|digits:3", //integer('user_id',3)->nullable()
            // "classroom_score" => "nullable|digits:5", //integer('classroom_score',5)->nullable()
            // "subject_score" => "nullable|digits:5", //integer('subject_score',5)->nullable()

            "test.*.classroom_score" => "nullable|max:4|regex:/^[0-9]+(\.[0-9]{1,2})?$/",
            "test.*.subject_score" => "nullable|max:4|regex:/^[0-9]+(\.[0-9]{1,2})?$/",

        ], [
            "test.*.classroom_score.max" => "教室数補正値は3桁以内で入力してください。",
            "test.*.subject_score.max" => "教科数補正値は3桁以内で入力してください。",
        ]);

        $requestDatas = $request->all();

        QuestionnaireScore::where('user_id', '=', $requestDatas['user_id'])->delete();

        foreach ($requestDatas['test'] as $index => $value) {
            $questionnairescores[] = $value;
        }

        User::find($requestDatas['user_id'])
            ->questionnaire_scores()
            ->createMany($questionnairescores);

        // get session url
        $url = session("url");
        session()->forget("url");

        if (strpos($url, "questionnaire_score") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        } else {
            return redirect("/shinzemi/questionnaire_score")->with("flash_message", "データが更新されました。");
        }

        // return redirect("/shinzemi/questionnaire_score")->with("flash_message", "データが更新されました。");
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
        QuestionnaireScore::destroy($id);

        return redirect("/shinzemi/questionnaire_score")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================