<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Discount;
use App\DiscountDetail;

use App\DivisionCode;

//=======================================================================
class DiscountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        // 売上区分マスタ
        $division_codes = DivisionCode::all();

        $keyword = $request->get("search");
        $perPage = 25;

        if (!empty($keyword)) {
            $discount = Discount::where("id", "LIKE", "%$keyword%")
                ->orWhere("name", "LIKE", "%$keyword%")

                ->paginate($perPage);
        } else {
            $discount = Discount::paginate($perPage);
        }
        return view("discount.index", compact("discount", "division_codes"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // 売上区分マスタ
        $division_codes = DivisionCode::all();

        return view("discount.create", compact("division_codes"));
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
            "name" => "required|max:40", //string('name',40)->nullable()
            "name_short" => "nullable|max:10", //string('name_short',10)->nullable()

            "discount_rate.*" => "nullable|integer",
        ], [
            "name.required" => "割引名を入力してください。",
            "name.max" => "割引名は40文字以内で入力してください。",
            "name_short.max" => "略名は10文字以内で入力してください。",
            "discount_rate.*.integer" => "割引率は整数で入力してください。",
        ]);
        $requestData = $request->all();
        // Discount::create($requestData);

        // 親ディスカウント登録
        $discount = new Discount;
        $discount->name = $request->name;
        $discount->name_short = $request->name_short;

        $discount->save();

        // 子ディスカウント登録
        $discount_rates = $requestData["discount_rate"];
        foreach ($discount_rates as $key => $value) {
            $discountdetail = new DiscountDetail();
            $discountdetail->discount_id = $discount->id;

            $discountdetail->division_code_id = $key;
            $discountdetail->discount_rate = $value;
            $discountdetail->save();
        }


        return redirect("/shinzemi/discount")->with("flash_message", "データが登録されました。");
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
        $discount = Discount::findOrFail($id);
        return view("discount.show", compact("discount"));
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


        $discount = Discount::findOrFail($id);

        $discountdetails = Discount::find($id)->discountdetails;

        // 売上区分マスタ
        $division_codes = DivisionCode::all();
        // $division_codesに売上区分名を追加
        foreach ($division_codes as $key => $division_code) {
            // discount_rateだけ取り出す
            $division_codes[$key]['discount_rate'] = $discountdetails->where("division_code_id", $division_code->id)->first()->discount_rate ?? 0;
        }


        return view("discount.edit", compact("division_codes", "discount", "discountdetails"));
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
            "name" => "required|max:40", //string('name',40)->nullable()
            "name_short" => "nullable|max:10", //string('name_short',10)->nullable()
            // "discount_rate_class" => "nullable|integer", //integer('discount_rate_class')->nullable()
            // "discount_rate_personal" => "nullable|integer", //integer('discount_rate_personal')->nullable()
            // "discount_rate_course" => "nullable|integer", //integer('discount_rate_course')->nullable()
            // "discount_rate_join" => "nullable|integer", //integer('discount_rate_join')->nullable()
            // "discount_rate_monthly" => "nullable|integer", //integer('discount_rate_monthly')->nullable()
            // "discount_rate_teachingmaterial" => "nullable|integer", //integer('discount_rate_teachingmaterial')->nullable()
            // "discount_rate_test" => "nullable|integer", //integer('discount_rate_test')->nullable()
            // "discount_rate_certification" => "nullable|integer", //integer('discount_rate_certification')->nullable()
            // "discount_rate_other" => "nullable|integer", //integer('discount_rate_other')->nullable()
        ], [
            "name.required" => "割引名は必須です。",
            "name.max" => "割引名は40文字以内で入力してください。",
            "name_short.max" => "略名は10文字以内で入力してください。",
        ]);

        $requestData = $request->all();


        // 割引親登録
        $discount = Discount::findOrFail($id);

        $discount->name = $requestData['name'];
        $discount->name_short = $requestData['name_short'];

        $discount->save();

        // 割引子登録

        // delete all data in DiscountDetail by $id
        $discountdetails = DiscountDetail::where("discount_id", $id)->get();
        foreach ($discountdetails as $discountdetail) {
            $discountdetail->delete();
        }

        $discount_rates = $requestData["discount_rate"];
        foreach ($discount_rates as $key => $value) {
            $discountdetail = new DiscountDetail();
            $discountdetail->discount_id = $id;
            $discountdetail->division_code_id = $key;
            $discountdetail->discount_rate = $value;
            $discountdetail->save();
        }


        // get session url
        $url = session("url");
        session()->forget("url");

        if (strpos($url, "discount") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        } else {
            return redirect("/shinzemi/discount")->with("flash_message", "データが更新されました。");
        }

        // return redirect("/shinzemi/discount")->with("flash_message", "データが更新されました。");
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
        Discount::destroy($id);

        return redirect("/shinzemi/discount")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================