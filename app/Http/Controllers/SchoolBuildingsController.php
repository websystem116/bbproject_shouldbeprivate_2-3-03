<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\SchoolBuilding;

//=======================================================================
class SchoolBuildingsController extends Controller
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
            $school_building = SchoolBuilding::where("id", "LIKE", "%$keyword%")
                ->orWhere("name", "LIKE", "%$keyword%")

                ->paginate($perPage);
        } else {
            $school_building = SchoolBuilding::paginate($perPage);
        }

        // 更新後にページネーションのページを保持する
        // dd($school_building->appends(['search' => $keyword]));

        return view("school_building.index", compact("school_building"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("school_building.create");
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
            "number" => "required|integer|unique:school_buildings",
            "name" => "required|max:20", //string('name',20)->nullable()
            "name_short" => "nullable|max:10", //string('name_short',10)->nullable()
            " zipcode" => "nullable|max:8", //string(' zipcode',8)->nullable()
            "address1" => "nullable|max:30", //string('address1',30)->nullable()
            "address2" => "nullable|max:30", //string('address2',30)->nullable()
            "address3" => "nullable|max:30", //string('address3',30)->nullable()
            "tel" => "nullable|max:15", //string('tel',15)->nullable()
            "fax" => "nullable|max:15", //string('fax',15)->nullable()
            "email" => "nullable|max:50", //string('email',50)->nullable()
        ], [
            "number.required" => "Noは必須です。",
            "number.unique" => "Noは既に登録されています。",
            "name.required" => "校舎名は必須項目です。",
            "name.max" => "校舎名は20文字以内で入力してください。",
        ]);
        $requestData = $request->all();

        SchoolBuilding::create($requestData);

        return redirect("/shinzemi/school_building")->with("flash_message", "データが登録されました。");
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
        $school_building = SchoolBuilding::findOrFail($id);
        return view("school_building.show", compact("school_building"));
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

        $school_building = SchoolBuilding::findOrFail($id);

        return view("school_building.edit", compact("school_building"));
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
            "number" => "required|integer|unique:school_buildings,number,$id",
            "name" => "required|max:20", //string('name',20)->nullable()
            "name_short" => "nullable|max:10", //string('name_short',10)->nullable()
            "zipcode" => "nullable|max:8", //string(' zipcode',8)->nullable()
            "address1" => "nullable|max:30", //string('address1',30)->nullable()
            "address2" => "nullable|max:30", //string('address2',30)->nullable()
            "address3" => "nullable|max:30", //string('address3',30)->nullable()
            "tel" => "nullable|max:15", //string('tel',15)->nullable()
            "fax" => "nullable|max:15", //string('fax',15)->nullable()
            "email" => "nullable|max:50", //string('email',50)->nullable()

        ], [
            "number.required" => "Noは必須です。",
            "number.unique" => "Noは既に登録されています。",
            "name.required" => "校舎名は必須項目です。",
            "name.max" => "校舎名は20文字以内で入力してください。",
        ]);
        $requestData = $request->all();

        $school_building = SchoolBuilding::findOrFail($id);
        $school_building->update($requestData);


        // get session url
        $url = session("url");
        session()->forget("url");

        if (strpos($url, "school_building") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        } else {
            return redirect("/shinzemi/school_building")->with("flash_message", "データが更新されました。");
        }

        // return redirect($url)->with("flash_message", "データが更新されました。");

        // return redirect("/shinzemi/school_building")->with("flash_message", "データが更新されました。");
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
        SchoolBuilding::destroy($id);

        return redirect("/shinzemi/school_building")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================