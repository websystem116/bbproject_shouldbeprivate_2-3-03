<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\DivisionCode;

//=======================================================================
class DivisionCodesController extends Controller
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
            $division_code = DivisionCode::where("id", "LIKE", "%$keyword%")->orWhere("name", "LIKE", "%$keyword%")->paginate($perPage);
        } else {
            $division_code = DivisionCode::paginate($perPage);
        }
        return view("division_code.index", compact("division_code"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("division_code.create");
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
            "name" => "required|max:100", //string('name',100)->nullable()

        ], [
            "name.required" => "売上区分名を入力してください。",
            "name.max" => "売上区分名は100文字以内で入力してください。",
        ]);
        $requestData = $request->all();

        DivisionCode::create($requestData);

        return redirect("/shinzemi/division_code")->with("flash_message", "データが登録されました。");
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
        $division_code = DivisionCode::findOrFail($id);
        return view("division_code.show", compact("division_code"));
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

        $division_code = DivisionCode::findOrFail($id);

        return view("division_code.edit", compact("division_code"));
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
            "name" => "required|max:100", //string('name',100)->nullable()

        ], [
            "name.required" => "売上区分名は必須です。",
            "name.max" => "売上区分名は100文字以内で入力してください。",

        ]);
        $requestData = $request->all();

        $division_code = DivisionCode::findOrFail($id);
        $division_code->update($requestData);

        // get session url
        $url = session("url");
        session()->forget("url");

        if (strpos($url, "division_code") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        } else {
            return redirect("/shinzemi/division_code")->with("flash_message", "データが更新されました。");
        }

        // return redirect("/shinzemi/division_code")->with("flash_message", "データが更新されました。");
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
        DivisionCode::destroy($id);

        return redirect("/shinzemi/division_code")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================