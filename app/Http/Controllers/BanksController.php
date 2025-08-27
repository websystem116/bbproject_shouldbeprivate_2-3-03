<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Bank;

//=======================================================================
class BanksController extends Controller
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
            $bank = Bank::where("code", "LIKE", "%$keyword%")
                ->orWhere("name", "LIKE", "%$keyword%")
                ->orWhere("name_kana", "LIKE", "%$keyword%")
                ->paginate($perPage);
        } else {
            $bank = Bank::paginate($perPage);
        }

        return view("bank.index", compact("bank"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("bank.create");
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
            "code" => "nullable|max:4|unique:banks", //string('code',4)->nullable()
            "name" => "required|max:15", //string('name',15)->nullable()
            "name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
        ], [
            "code.max" => "銀行コードは4文字以内で入力してください。",
            "code.unique" => "入力された銀行コードは既に登録されています。",
            "name.required" => "銀行名を入力してください。",
            "name.max" => "銀行名は15文字以内で入力してください。",
        ]);

        $requestData = $request->all();

        Bank::create($requestData);

        return redirect("/shinzemi/bank")->with("flash_message", "データが登録されました。");
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
        $bank = Bank::findOrFail($id);
        return view("bank.show", compact("bank"));
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
        $before_url = url()->previous();
        $current_url = url()->current();
        if ($before_url == $current_url) {
            // validationなどで戻ってきた場合（編集から編集へ）
            $url_for_back = session()->get("url");
        } else {
            // 通常の遷移の場合(一覧から編集へ)
            $url_for_back = $before_url;
            session(["url" => $before_url]);
        }

        $bank = Bank::findOrFail($id);

        return view("bank.edit", compact("bank", "url_for_back",));
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
            "code" => "nullable|max:4|unique:banks,code,$id,id", //string('code',4)->nullable()
            "name" => "required|max:15", //string('name',15)->nullable()
            "name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
        ], [
            "code.max" => "銀行コードは4文字以内で入力してください。",
            "code.unique" => "既に登録されている銀行コードです。",
            "name.required" => "銀行名を入力してください。",
            "name.max" => "銀行名は15文字以内で入力してください。",
        ]);

        $requestData = $request->all();

        $bank = Bank::findOrFail($id);
        $bank->update($requestData);

        $url = session("url");
        if (empty($url)) {
            $url = route("bank.index");
        }
        return redirect($url)->with("flash_message", "データが更新されました。");
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
        Bank::destroy($id);

        return redirect("/shinzemi/bank")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================