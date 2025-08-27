<?php

namespace App\Http\Controllers;

use App\BeforeJukuSales;
use App\BeforeStudent;
use App\Product;
use App\Bank;
use App\BranchBank;
use App\Discount;
use App\SchoolBuilding;
use App\School;
use App\HighschoolCourse;
use Validator;

use Illuminate\Http\Request;

class BeforeJukuSalesController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});
		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		//商品のセレクトリスト
		$products = Product::get();
		$products_select_list = $products->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		//割引のセレクトリスト
		$discounts = Discount::get();
		$discounts_select_list = $discounts->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//検索の処理
		$query = BeforeStudent::query();
		$perPage = 25;


		//検索の値取得
		$student_search['id_start'] = $request->get("id_start");
		$student_search['id_end'] = $request->get("id_end");
		$student_search['student_no_start'] = $request->get("student_no_start");
		$student_search['student_no_end'] = $request->get("student_no_end");
		$student_search['surname'] = $request->get("surname");
		$student_search['name'] = $request->get("name");
		$student_search['surname_kana'] = $request->get("surname_kana");
		$student_search['name_kana'] = $request->get("name_kana");
		$student_search['school_id'] = $request->get("school_id");
		$student_search['grade_start'] = $request->get("grade_start");
		$student_search['grade_end'] = $request->get("grade_end");
		$student_search['school_building_id'] = $request->get("school_building_id");
		$student_search['product_select'] = $request->get("product_select");
		$student_search['juku_start_date'] = $request->get("juku_start_date");
		$student_search['payment_start_date'] = $request->get("payment_start_date");
		$student_search['payment_end_date'] = $request->get("payment_end_date");
		$student_search['sales_date'] = $request->get("sales_date");

		// 検索するテキストが入力されている場合のみ
		if (!empty($student_search['id_start']) && empty($student_search['id_end'])) {
			$query->where('before_students.id', $student_search['id_start']);
		}
		if (!empty($student_search['id_end']) && empty($student_search['id_start'])) {
			$query->where('before_students.id', $student_search['id_end']);
		}
		if (!empty($student_search['id_start']) && !empty($student_search['id_end'])) {
			$query->whereBetween('id', [$student_search['id_start'], $student_search['id_end']]);
		}
		if (!empty($student_search['student_no_start']) && empty($student_search['student_no_end'])) {
			$query->where('before_students.before_student_no', $student_search['student_no_start']);
		}
		if (!empty($student_search['student_no_end']) && empty($student_search['student_no_start'])) {
			$query->where('before_students.before_student_no', $student_search['student_no_end']);
		}
		if (!empty($student_search['student_no_start']) && !empty($student_search['student_no_end'])) {
			$query->whereBetween('before_students.before_student_no', [$student_search['student_no_start'], $student_search['student_no_end']]);
		}
		if (!empty($student_search['surname'])) {
			$query->where('surname', 'like', '%' . $student_search['surname'] . '%');
		}
		if (!empty($student_search['name'])) {
			$query->where('name', 'like', '%' . $student_search['name'] . '%');
		}
		if (!empty($student_search['surname_kana'])) {
			$query->where('surname_kana', 'like', '%' . $student_search['surname_kana'] . '%');
		}
		if (!empty($student_search['name_kana'])) {
			$query->where('name_kana', 'like', '%' . $student_search['name_kana'] . '%');
		}
		if (!empty($student_search['school_id'])) {
			$query->where('school_id', $student_search['school_id']);
		}
		if (!empty($student_search['grade_start']) && empty($student_search['grade_end'])) {
			$query->where('grade', $student_search['grade_start']);
		}
		if (!empty($student_search['grade_end']) && empty($student_search['grade_start'])) {
			$query->where('grade', $student_search['grade_end']);
		}
		if (!empty($student_search['grade_start']) && !empty($student_search['grade_end'])) {
			$query->whereBetween('grade', [$student_search['grade_start'], $student_search['grade_end']]);
		}
		if (!empty($student_search['school_building_id'])) {
			$query->where('before_students.school_building_id', $student_search['school_building_id']);
		}
		if (!empty($student_search['product_select'])) {
			$query->join('before_juku_sales', 'before_juku_sales.before_student_no', '=', 'before_students.before_student_no')
				->where('before_juku_sales.product_id', $student_search['product_select']);
		}
		//入金日
		if (!empty($student_search['payment_start_date']) && empty($student_search['payment_end_date'])) {
			if (!empty($student_search['product_select'])) {
				$query->where('before_juku_sales.payment_date', $student_search['payment_start_date']);
			} else {
				$query->join('before_juku_sales', 'before_juku_sales.before_student_no', '=', 'before_students.before_student_no')
					->where('before_juku_sales.payment_date', $student_search['payment_start_date']);
			}
		}
		if (!empty($student_search['payment_end_date']) && empty($student_search['payment_start_date'])) {
			return redirect("/shinzemi/before_juku_sales")->with("message", "※入金日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['payment_start_date']) && !empty($student_search['payment_end_date'])) {
			if (empty($student_search['product_select'])) {
				$query->join('before_juku_sales', 'before_juku_sales.before_student_no', '=', 'before_students.before_student_no')->whereBetween('before_juku_sales.payment_date', [$student_search['payment_start_date'], $student_search['payment_end_date']]);
			} else {
				$query->whereBetween('before_juku_sales.payment_date', [$student_search['payment_start_date'], $student_search['payment_end_date']]);
			}
		}
		//売上年月
		if (!empty($student_search['sales_date'])) {
			if (empty($student_search['product_select'])) {
				$query->join('before_juku_sales', 'before_juku_sales.before_student_no', '=', 'before_students.before_student_no')->where('sales_date', $student_search['sales_date']);
			} else {
				$query->where('before_juku_sales.sales_date', $student_search['sales_date']);
			}
		}

		$before_student = $query->paginate($perPage);

		return view("before_juku_sales.index", compact("before_student",  "student_search", "schools_select_list", "schooolbuildings_select_list", "products_select_list", "discounts_select_list"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		// $this->validate($request, [
		// 	'sales_date[]' => 'required|max:6',
		// 	'payment_date[]' => 'required',
		// 	'product_id[]' => 'required',
		// 	'price_after_discount[]' => 'required',
		// ], [
		// 	"sales_date[].required" => "売上年月を入力してください。",
		// 	"sales_date[].max" => "売上年月は6桁yyyymmで入力してください。",
		// 	"payment_date[].required" => "入金日を入力してください。",
		// 	"product_id[].required" => "商品を選択してください。",
		// 	"price_after_discount[].required" => "金額を入力してください。",

		// ]);
		//Validator
		// $validator = Validator::make($request->all(), [
		// 	'sales_date[]' => 'required|max:6',
		// 	'payment_date[]'  => 'required',
		// 	'product_id[]' => 'required',
		// 	'price_after_discount[]' => 'required'
		// ], [
		// 	"sales_date[].required" => "売上年月を入力してください。",
		// 	// "sales_date[].max" => "売上年月は6桁yyyymmで入力してください。",
		// 	"payment_date[].required" => "入金日を入力してください。",
		// 	"product_id[].required" => "商品を選択してください。",
		// 	"price_after_discount[].required" => "金額を入力してください。",
		// ]);

		// if ($validator->fails()) {
		// 	return redirect()->back()
		// 		->withInput()
		// 		->withErrors($validator);
		// }
		//Validator

		$requestDatas = $request->all();
		// dd($requestDatas);

		//入塾生徒前のIDの情報があれば削除
		BeforeJukuSales::where('before_student_no', $request->before_student_no)->delete();
		for ($i = 0; $i < count($requestDatas['product_id']); $i++) {
			$product_info = Product::where('id', $requestDatas['product_id'][$i])->first(); //商品の情報取得
			// dd($product_info['tax_category']);
			if ($product_info['tax_category'] == 2) { //商品が内税か外税か　1：内税、2：外税
				$tax_rate = (config('const.consumption_tax')); //税率取得 const.php
				// dd($tax_rate);
				$tax_amount =  $requestDatas['price_after_discount'][$i] * $tax_rate / 100;
				// dd($tax_amount);
				$subtotal = $requestDatas['price_after_discount'][$i] + $tax_amount;
				// dd($subtotal);
			} else {
				$tax_amount = 0;
				$subtotal = $requestDatas['price_after_discount'][$i];
			}

			$requestData = [
				'sales_date' => $requestDatas['sales_date'][$i],
				'payment_date' => $requestDatas['payment_date'][$i],
				'product_id' => $requestDatas['product_id'][$i],
				'price_after_discount' => $requestDatas['price_after_discount'][$i],
				'tax' => $tax_amount,
				'subtotal' => $subtotal,
				'note' => $requestDatas['note'][$i],
				'before_student_no' => $requestDatas['before_student_no'],
				'school_building_id' => $requestDatas['school_building_id'],
			];

			BeforeJukuSales::create($requestData);
		}
		// get session url
		$url = session("url");
		session()->forget("url");

		if (strpos($url, "before_juku_sales") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/before_juku_sales")->with("flash_message", "データが登録されました。");
		}
	}

	/**
	 *Functions started by Ajax before_juku_sales.js
	 *
	 * @param [type] $id
	 * @return $get_product_price
	 */
	// このメソッドをAjaxから実行したい
	public function get_product_price($id)
	{
		$get_product_price = Product::where('id', $id)->value('price');
		return $get_product_price;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\BeforeJukuSales  $beforeJukuSales
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//入塾前生徒情報取得
		// $before_student = BeforeStudent::findOrFail($id);
		$before_student = BeforeStudent::where('before_student_no', $id)->first();

		//入塾前生徒情報に紐づく売上情報の取得
		// $beforejukusales = BeforeStudent::find($id)->before_juku_sales;
		$beforejukusales = BeforeStudent::where('before_student_no', $id)->first('before_student_no')->before_juku_sales;

		//商品のセレクトリスト
		$products = Product::get();
		$products_select_list = $products->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});

		$url = url()->previous();
		// sessionにURLを保存
		session(["url" => $url]);
		return view("before_juku_sales.edit", compact("before_student", "beforejukusales", "products_select_list"));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\BeforeJukuSales  $beforeJukuSales
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, BeforeJukuSales $beforeJukuSales)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\BeforeJukuSales  $beforeJukuSales
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(BeforeJukuSales $beforeJukuSales)
	{
		//
	}
}
