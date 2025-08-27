<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\BranchBank;
use App\Bank;

//=======================================================================
class BranchBanksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        $banks = Bank::all();

        $name = $request->get("name");
        $perPage = 25;

        if (!empty($request)) {

            $branch_bank = BranchBank::whereHas('bank', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%$request->name%");
            })->paginate($perPage);
        } else {
            $branch_bank = BranchBank::paginate($perPage);
        }

        return view("branch_bank.index", compact("branch_bank", "banks", "name"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $banks = Bank::all();
        return view("branch_bank.create", compact("banks"));
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
            "bank_id" => "required", //integer('bank_id')->nullable()
            "code" => "required|max:10", //string('code',10)->nullable()
            "name" => "required|max:15", //string('name',15)->nullable()
            "name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
            "zipcode" => "nullable|max:8", //string('zipcode',8)->nullable()
            "address" => "nullable|max:60", //string('address',60)->nullable()
            "tel" => "nullable|max:15", //string('tel',15)->nullable()

        ], [
            "bank_id.required" => "銀行名を選択してください。",
            "code.required" => "銀行支店コードを入力してください。",
            "code.max" => "銀行支店コードは10文字以内で入力してください。",
            "name.required" => "支店名を入力してください。",
            "name.max" => "支店名は15文字以内で入力してください。",
            "zipcode.max" => "郵便番号は8文字以内で入力してください。",
            "address.max" => "住所は60文字以内で入力してください。",
            "tel.max" => "電話番号は15文字以内で入力してください。",
        ]);
        $requestData = $request->all();

        BranchBank::create($requestData);

        return redirect("/shinzemi/branch_bank")->with("flash_message", "データが登録されました。");
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
        $branch_bank = BranchBank::findOrFail($id);
        return view("branch_bank.show", compact("branch_bank"));
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

        // $url = url()->previous();
        // session(["url" => $url]);

        $before_url = url()->previous();
        $current_url = url()->current();
        if ($before_url == $current_url) {
            // validationなどで戻ってきた場合
            $url_for_back = session()->get("url");
        } else {
            // 通常の遷移の場合
            $url_for_back = $before_url;
            session(["url" => $before_url]);
        }

        $branch_bank = BranchBank::findOrFail($id);
        $banks = Bank::all();

        return view("branch_bank.edit", compact("branch_bank", "banks", "url_for_back"));
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
            "bank_id" => "required", //integer('bank_id')->nullable()
            "code" => "required|max:10", //string('code',3)->nullable()
            "name" => "required|max:15", //string('name',15)->nullable()
            "name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
            "zipcode" => "nullable|max:8", //string('zipcode',8)->nullable()
            "address" => "nullable|max:60", //string('address',60)->nullable()
            "tel" => "nullable|max:15", //string('tel',15)->nullable()

        ], [
            "bank_id.required" => "銀行名を選択してください。",
            "code.required" => "銀行支店コードを入力してください。",
            "code.max" => "銀行支店コードは10文字以内で入力してください。",
            "name.required" => "支店名を入力してください。",
            "name.max" => "支店名は15文字以内で入力してください。",
            "zipcode.max" => "郵便番号は8文字以内で入力してください。",
            "address.max" => "住所は60文字以内で入力してください。",
            "tel.max" => "電話番号は15文字以内で入力してください。",
        ]);
        $requestData = $request->all();

        $branch_bank = BranchBank::findOrFail($id);
        $branch_bank->update($requestData);

        // get session url
        $url = session("url");
        session()->forget("url");

        if (strpos($url, "branch_bank") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        } else {
            return redirect("/shinzemi/branch_bank")->with("flash_message", "データが更新されました。");
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
        BranchBank::destroy($id);

        return redirect("/shinzemi/branch_bank")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================