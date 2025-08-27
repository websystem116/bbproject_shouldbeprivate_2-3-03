<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\HighschoolCourse;
use App\School;

//=======================================================================
class HighschoolCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        $schools = School::all();

        $name = $request->get("name");
        $perPage = 25;

        if (!empty($request)) {

            $highschool_course = HighschoolCourse::whereHas('school', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%$request->name%");
            })->paginate($perPage);
        } else {
            $highschool_course = HighschoolCourse::paginate($perPage);
        }
        return view("highschool_course.index", compact("highschool_course", "schools", "name"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $schools = School::all();
        return view("highschool_course.create", compact("schools"));
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
            "school_id" => "nullable|max:4", //string('school_id',4)->nullable()
            "name" => "required|max:20", //string('name',20)->nullable()
            "name_short" => "nullable|max:10", //string('name_short',10)->nullable()
        ], [
            "name.required" => "名称を入力してください。",
        ]);
        $requestData = $request->all();

        HighschoolCourse::create($requestData);

        return redirect("/shinzemi/highschool_course")->with("flash_message", "データが登録されました。");
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
        $highschool_course = HighschoolCourse::findOrFail($id);
        return view("highschool_course.show", compact("highschool_course"));
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

        $highschool_course = HighschoolCourse::findOrFail($id);
        $schools = School::all();

        return view("highschool_course.edit", compact("highschool_course", "schools"));
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
            "school_id" => "nullable|max:4", //string('school_id',4)->nullable()
            "name" => "required|max:20", //string('name',20)->nullable()
            "name_short" => "nullable|max:10", //string('name_short',10)->nullable()

        ]);
        $requestData = $request->all();

        $highschool_course = HighschoolCourse::findOrFail($id);
        $highschool_course->update($requestData);

        // get session url
        $url = session("url");
        session()->forget("url");

        if (strpos($url, "highschool_course") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        } else {
            return redirect("/shinzemi/highschool_course")->with("flash_message", "データが更新されました。");
        }

        // return redirect("/shinzemi/highschool_course")->with("flash_message", "データが更新されました。");
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
        HighschoolCourse::destroy($id);

        return redirect("/shinzemi/highschool_course")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================