<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Product;
use App\DivisionCode;
//=======================================================================
class ProductsController extends Controller
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

        $division_codes = DivisionCode::all();
        // pluck
        $division_codes_array = $division_codes->pluck("name", "id");

        $query = Product::query();

        // 商品名
        $query->when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        });

        // 商品No
        $query->when($request->number, function ($query) use ($request) {
            $query->where('number', $request->number);
        });

        // 売上区分
        $query->when($request->division_code, function ($query) use ($request) {
            $query->where('division_code', $request->division_code);
        });

        // 集計区分
        $query->when($request->class_categories, function ($query) use ($request) {
            $query->where('tabulation', $request->class_categories);
        });

        $product = $query->paginate($perPage);

        return view("product.index", compact("product", "division_codes", "division_codes_array"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $division_codes = DivisionCode::all();

        return view("product.create", compact("division_codes"));
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
        $this->validate(
            $request,
            [
                "name" => "required|max:40", //string('name',40)->nullable()
                "name_short" => "nullable|max:10", //string('name_short',10)->nullable()
                "number" => "required|integer|unique:products",
                "description" => "nullable|max:80", //string('description',80)->nullable()
                "price" => "nullable|integer", //integer('price')->nullable()
                "tax_category" => "nullable", //string('tax_category')->nullable()
                "division_code" => "nullable|max:2", //string('division_code',2)->nullable()
                "item_no" => "nullable|max:2", //string('item_no',2)->nullable()
                "tabulation" => "nullable|max:2", //string('tabulation',2)->nullable()
            ],
            [
                "name.required" => "商品名を入力してください。",
                "number.required" => "商品Noを入力してください。",
                "number.integer" => "商品Noは数字で入力してください。",
                "number.unique" => "商品Noは既に登録されています。",
                "name.max" => "商品名は40文字以内で入力してください。",
                "name_short.max" => "商品名（略称）は10文字以内で入力してください。",
                "description.max" => "内容は80文字以内で入力してください。",
                "price.integer" => "価格は整数で入力してください。",
            ]
        );
        $requestData = $request->all();

        Product::create($requestData);

        return redirect("/shinzemi/product")->with("flash_message", "データが登録されました。");
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
        $product = Product::findOrFail($id);
        return view("product.show", compact("product"));
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


        $division_codes = DivisionCode::all();

        $product = Product::findOrFail($id);
        return view("product.edit", compact("product", "division_codes", "url_for_back"));
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
            "number" => "required|integer|unique:products,number," . $id,
            "description" => "nullable|max:80", //string('description',80)->nullable()
            "price" => "nullable|integer", //integer('price')->nullable()
            "tax_category" => "nullable", //string('tax_category')->nullable()
            "division_code" => "nullable|max:2", //string('division_code',2)->nullable()
            "item_no" => "nullable|max:2", //string('item_no',2)->nullable()
            "tabulation" => "nullable|max:2", //string('tabulation',2)->nullable()
        ], [
            "name.required" => "商品名を入力してください。",
            "name.max" => "商品名は40文字以内で入力してください。",
            "number.required" => "商品Noを入力してください。",
            "number.integer" => "商品Noは数字で入力してください。",
            "number.unique" => "商品Noは既に登録されています。",
            "name_short.max" => "商品名（略称）は10文字以内で入力してください。",
            "description.max" => "内容は80文字以内で入力してください。",
            "price.integer" => "価格は整数で入力してください。",
        ]);

        $requestData = $request->all();

        $product = Product::findOrFail($id);
        $product->update($requestData);

        // 同じページに戻る
        // $before_url = url()->previous();
        // return redirect($before_url)->with("flash_message", "データが更新されました。");

        $url = session("url");
        session()->forget("url");

        if (strpos($url, "product") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        }

        if (strpos($url, "product") == false) {
            return redirect("/shinzemi/product")->with("flash_message", "データが更新されました。");
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
        Product::destroy($id);

        return redirect("/shinzemi/product")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================