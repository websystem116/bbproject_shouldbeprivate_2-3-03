<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\SubjectTeacher;
use App\User;
use App\SchoolBuilding;

//=======================================================================
class SubjectTeachersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        $users = User::all();
        $school_buildings = SchoolBuilding::all();

        $user_id = $request->get("user_id");
        $school_building_id = $request->get("school_building_id");
        $school_year_search = $request->get("school_year_search");

        $subject_search = $request->get("subject_search");
        $alphabet_search = $request->get("alphabet_search");

        $perPage = 25;

        $query = SubjectTeacher::query();

        $query->when($user_id, function ($query, $user_id) {
            return $query->where('user_id', "$user_id");
        });

        $query->when($school_building_id, function ($query, $school_building_id) {
            return $query->where('school_building_id', "$school_building_id");
        });

        $query->when($school_year_search, function ($query, $school_year_search) {
            return $query->where('school_year', "$school_year_search");
        });

        $query->when($subject_search, function ($query, $subject_search) {
            return $query->where('classification_code_class', "$subject_search");
        });

        $query->when($alphabet_search, function ($query, $alphabet_search) {
            return $query->where('item_no_class', "$alphabet_search");
        });


        $subject_teacher = $query->paginate($perPage);


        return view("subject_teacher.index", compact("subject_teacher", "users", "school_buildings", "user_id", "school_building_id", "school_year_search", "subject_search", "alphabet_search"));
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
        return view("subject_teacher.create", compact("users", "school_buildings"));
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
            "school_building_id" => "required",
            "school_year" => "required|max:2", //string('school_year',2)->nullable()
            "classification_code_class" => "required|max:4", //string('classification_code_class',4)->nullable()
            "item_no_class" => "required|max:2", //string('item_no_class',2)->nullable()
            "user_id" => "required", //integer('user_id',3)->nullable()
        ], [
            "school_building_id.required" => "校舎を選択してください。",
            "school_year.required" => "学年を選択してください。",
            "classification_code_class.required" => "科目を選択してください。",
            "item_no_class.required" => "クラスを選択してください。",
            "user_id.required" => "講師を選択してください。",
        ]);

        $requestData = $request->all();

        SubjectTeacher::create($requestData);

        return redirect("/shinzemi/subject_teacher")->with("flash_message", "データが登録されました。");
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
        $subject_teacher = SubjectTeacher::findOrFail($id);
        return view("subject_teacher.show", compact("subject_teacher"));
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

        // Users get
        $users = User::all();
        // SchoolBuilding get
        $school_buildings = SchoolBuilding::all();

        $subject_teacher = SubjectTeacher::findOrFail($id);

        return view("subject_teacher.edit", compact("subject_teacher", "users", "school_buildings"));
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
            "school_year" => "nullable|max:2", //string('school_year',2)->nullable()
            "classification_code_class" => "nullable|max:4", //string('classification_code_class',4)->nullable()
            "item_no_class" => "nullable|max:2", //string('item_no_class',2)->nullable()
            "user_id" => "nullable", //integer('user_id',3)->nullable()

        ]);
        $requestData = $request->all();

        $subject_teacher = SubjectTeacher::findOrFail($id);
        $subject_teacher->update($requestData);

        // get session url
        $url = session("url");
        session()->forget("url");

        if (strpos($url, "subject_teacher") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        } else {
            return redirect("/shinzemi/subject_teacher")->with("flash_message", "データが更新されました。");
        }

        // return redirect("/shinzemi/subject_teacher")->with("flash_message", "データが更新されました。");
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
        SubjectTeacher::destroy($id);

        return redirect("/shinzemi/subject_teacher")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================