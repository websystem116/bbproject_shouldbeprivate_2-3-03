<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\School;

//=======================================================================
class SchoolsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->get("name");
        $perPage = 25;

        if (!empty($name)) {
            $school = School::where("name", "LIKE", "%$name%")
                // ->orWhere("name", "LIKE", "%$keyword%")
                // ->orWhere("name_short", "LIKE", "%$keyword%")
                // ->orWhere("school_classification", "LIKE", "%$keyword%")
                // ->orWhere("university_classification", "LIKE", "%$keyword%")
                ->paginate($perPage);
        } else {
            $school = School::paginate($perPage);
        }

        return view("school.index", compact("school", "name"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("school.create");
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
            "name" => "required|max:20", //string('name',20)->nullable()
            "name_short" => "nullable|max:10", //string('name_short',10)->nullable()
            "school_classification" => "nullable|max:2", //string('school_classification',2)->nullable()
            "university_classification" => "nullable|max:2", //string('university_classification',2)->nullable()
        ], [
            "name.required" => "学校名は必須です。",
            "name.max" => "学校名は20文字以内で入力してください。",
        ]);
        $requestData = $request->all();

        School::create($requestData);

        return redirect("/shinzemi/school")->with("flash_message", "データが登録されました。");
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
        $school = School::findOrFail($id);
        return view("school.show", compact("school"));
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

        $school = School::findOrFail($id);
        return view("school.edit", compact("school"));
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
            "name" => "required|max:20", //string('name',20)->nullable()
            "name_short" => "nullable|max:10", //string('name_short',10)->nullable()
            "school_classification" => "nullable|max:2", //string('school_classification',2)->nullable()
            "university_classification" => "nullable|max:2", //string('university_classification',2)->nullable()

        ], [
            "name.required" => "学校名は必須です。",
            "name.max" => "学校名は20文字以内で入力してください。",
        ]);

        $requestData = $request->all();

        $school = School::findOrFail($id);
        $school->update($requestData);

        // get session url
        $url = session("url");
        session()->forget("url");

        if (strpos($url, "school") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        } else {
            return redirect("/shinzemi/school")->with("flash_message", "データが更新されました。");
        }

        // return redirect("/shinzemi/school")->with("flash_message", "データが更新されました。");
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
        School::destroy($id);

        return redirect("/shinzemi/school")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================