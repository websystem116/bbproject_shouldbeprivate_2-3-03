<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Sale;
use App\SalesDetail;
use App\SchoolBuilding;
use App\School;
use App\JobDescription;
use App\DailySalary;
use App\Student;
use App\Product;
use App\Discount;
use App\DiscountDetail;
use App\ChargeProgress;

//=======================================================================
class SalesController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request)
	{
		$keyword = $request->get("search");
		// $perPage = 2500;
		$charge_progress = ChargeProgress::latest('id')->first();
		$sales_month = $charge_progress->sales_month;

		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		$schools = School::all()->pluck('name', 'id');
		$products = Product::all()->pluck('name', 'id');

		$products_for_list = Product::get();
		$products_select_list = $products_for_list->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});

		$discounts = Discount::all()->pluck('name', 'id');

		$search_names = [];
		$student_search['last_name'] = $request->get("last_name");
		$student_search['first_name'] = $request->get("first_name");
		$student_search['school_year_start'] = $request->get("school_year_start");
		$student_search['school_year_end'] = $request->get("school_year_end");
		$student_search['school_year_range'][] = intval($request->get("school_year_start"));
		$student_search['school_year_range'][] = intval($request->get("school_year_end"));
		$student_search['school_building'] = $request->get("school_building");
		$student_search['school'] = $request->get("school");
		$student_search['product'] = $request->get("product");
		$student_search['discount'] = $request->get("discount");
		$student_search['brothers_flg'] = $request->get("brothers_flg");
		$student_search['not_enrolled_flg'] = $request->get("not_enrolled_flg");
		$student_search['work_month'] = $request->get("work_month");


		// 検索時のみ表示
		if ($request->search == 1) {
			$sales = Sale::whereIn('student_no', function ($query) use ($student_search) {
				$query->from('students')
					->select('students.student_no')
					->when(!empty($student_search['last_name']), function ($query) use ($student_search) {
						return $query->where('students.surname', 'like', '%' . $student_search['last_name'] . '%');
					})->when(!empty($student_search['first_name']), function ($query) use ($student_search) {
						return $query->where('students.name', 'like', '%' . $student_search['first_name'] . '%');
					})->when(!empty($student_search['school_year_start']) && !empty($student_search['school_year_end']), function ($query) use ($student_search) {
						return $query->whereBetween('grade', $student_search['school_year_range']);
					})->when(!empty($student_search['school_building']), function ($query) use ($student_search) {
						return $query->where('students.school_building_id', $student_search['school_building']);
					})->when(!empty($student_search['school']), function ($query) use ($student_search) {
						return $query->where('students.school_id', $student_search['school']);
					})->when(!empty($student_search['discount']), function ($query) use ($student_search) {
						return $query->where('students.discount_id', $student_search['discount']);
					})->when(!empty($student_search['brothers_flg']), function ($query) use ($student_search) {
						return $query->where('students.brothers_flg', $student_search['brothers_flg']);
					})->when(!empty($student_search['not_enrolled_flg']), function ($query) {
						return $query->where(function ($query) {
							$query->orWhereNotNull('juku_withdrawal_date')
								->orWhereNotNull('juku_graduation_date')
								->orWhereNotNull('juku_rest_date');
						});
					});
			})->whereIn('sales_number', function ($query) use ($student_search) {
				$query->from('sales_details')
					->select('sales_details.sales_number')
					->when(!empty($student_search['product']), function ($query) use ($student_search) {
						return $query->where('sales_details.product_id', $student_search['product']);
					});
			})->when(!empty($student_search['work_month']), function ($query) use ($student_search) {
				return $query->where('sale_month', $student_search['work_month']);
			})->when(empty($student_search['work_month']), function ($query) use ($sales_month) {
				return $query->where('sale_month', $sales_month);
			})->get();
		} else {
			// 未検索時は、レコードがないとエラーになるので、空のコレクションを渡す
			$sales = Sale::where("id", 0)->get();
		}

		$sales_count = $sales->count();

		// $sales firstItem() lastItem() total() が使えないので、ここで計算
		$sales_first_item = 0;
		$sales_last_item = 0;
		$sales_total = 0;
		if ($sales_count > 0) {
			$sales_count_info['sales_first_item'] = 1;
			$sales_count_info['sales_last_item'] = $sales_count;
			$sales_count_info['sales_total'] = $sales_count;
		} else {
			$sales_count_info['sales_first_item'] = 0;
			$sales_count_info['sales_last_item'] = 0;
			$sales_count_info['sales_total'] = 0;
		}
		return view("sales.index", compact("sales", "school_buildings", "schools", "discounts", "products", "student_search", "sales_count", "sales_count_info", "products_select_list", "sales_month"));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\View\View
	 */
	// public function create()
	// {



	// 	return view("salary.create");
	// }
	public function store(Request $request)
	{
		$this->validate($request, [
			"code" => "nullable|max:4", //string('code',4)->nullable()
			"name" => "required|max:15", //string('name',15)->nullable()
			"name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
		]);
		$requestData = $request->all();

		DailySalary::create($requestData);

		return redirect("/shinzemi/salary")->with("flash_message", "データが登録されました。");
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function data_migration()
	{
		$charge_progress = ChargeProgress::latest('id')->first();
		$sales_month = $charge_progress->sales_month . "-01";
		$Data = [
			"sales_data_created_flg" => "1",
			"sales_data_created_date" => date('Y-m-d'),
			"sales_month" => date('Y-m', strtotime("+1 month " . $sales_month)),
			"creator" => Auth::user()->id,
			"updater" => Auth::user()->id
		];
		ChargeProgress::create($Data);
		$student = Student::where("grade", '!=', '16')->whereNull('juku_graduation_date')->whereNull('juku_rest_date')->whereNull('juku_withdrawal_date')->get(); #退塾・休塾などの条件必要あり
		$sale_params = [];
		foreach ($student as $student_value) {

			$juko_infos = $student_value->juko_infos;
			$params = [];
			$consumption_tax = (config('const.consumption_tax') / 100);
			$tax_sum = 0;
			$sales_sum = 0;

			foreach ($juko_infos as $value) {
				# code...
				$discounted_price = $value->product->price;
				if ($student_value->discount_id != "") {
					$discount = DiscountDetail::where('division_code_id', $value->product->division_code)->where('discount_id', $student_value->discount_id)->first();
					if ($discount) {
						$discount_rate = $discount->discount_rate;
						if ($discount_rate) {
							$discounted_price = floor($value->product->price * (1 - ($discount_rate / 100)));
						}
					}
				}
				if ($value->product->tax_category == 1) {
					$tax = 0;
					$discounted_price = $discounted_price;
				} elseif ($value->product->tax_category == 2) {
					$tax = floor($discounted_price * $consumption_tax);
					$discounted_price = $discounted_price;
				}

				$params[] = [
					"student_id" => $value->student_id,
					"student_no" => $value->student_no,
					"sale_month" => date('Y-m', strtotime("+1 month " . $sales_month)),
					"product_id" => $value->product_id,
					"sales_number" => $value->student_no . date('Ym', strtotime("+1 month " . $sales_month)),
					"product_name" => $value->product->name,
					"product_free" => date('m月分', strtotime("+1 month " . $sales_month)),
					"product_price" => $value->product->price,
					"product_price_display" => $value->product->tax_category,
					"sales_category" => $value->product->division_code,
					"price" => $discounted_price,
					"tax" => $tax,
					"subtotal" => $tax + $discounted_price,
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id
				];
				$tax_sum += $tax;
				$sales_sum += floor($discounted_price * (1 + $consumption_tax));
			}
			SalesDetail::insert($params);
			$existed_sales = Sale::where("sales_number", $value->student_no . date('Ym', strtotime("+1 month " . $sales_month)));
			if ($existed_sales->exists()) {
				$existed_sale = $existed_sales->first();
				$existed_sales_details = $existed_sale->sales_detail;
				$price_sumed = $existed_sales_details->sum('price');
				$tax_sumed = $existed_sales_details->sum('tax');
				$tax_sum += $tax_sumed;
				$sales_sum += $price_sumed;

				$existed_sale_params = [
					"tax" => $tax_sum,
					"sales_sum" => $sales_sum,
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id
				];
				$existed_sale->update($existed_sale_params);
			} else {
				$sale_params[] = [
					"student_id" => $student_value->id,
					"student_no" => $student_value->student_no,
					"sale_month" => date('Y-m', strtotime("+1 month " . $sales_month)),
					"school_building_id" => $student_value->school_building_id,
					"school_id" => $student_value->school_id,
					"school_year" => $student_value->grade,
					"brothers_flg" => $student_value->brother_flg,
					"discount_id" => $student_value->discount_id,
					"tax" => $tax_sum,
					"sales_sum" => $sales_sum,
					"sales_number" => $student_value->student_no . date('Ym', strtotime("+1 month " . $sales_month)),
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id
				];
			}
		}
		Sale::insert($sale_params);

		return redirect("/shinzemi/home")->with("flash_message", "売上データを作成しました。");
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
		$daily_salary = DailySalary::findOrFail($id);
		return view("sales.show", compact("daily_salary"));
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
		session(["url" => $url]);
		$charge_progress = ChargeProgress::latest('id')->first();
		$sales_month = $charge_progress->sales_month;
		$created_at = $charge_progress->created_at;
		$sale = Sale::findOrFail($id);
		$schoolbuilding = SchoolBuilding::all()->pluck('name', 'id');
		$job_description = JobDescription::all()->pluck('name', 'id');
		$sales_details = array();
		if ($sale->sale_month == $sales_month) {
			$sales_details = SalesDetail::where('student_no', $sale->student_no)
				->where('created_at', '>=', $created_at)
				->where('sale_month', '<', $sales_month)->get();
		}
		//商品のセレクトリスト
		$products = Product::get();
		$products_select_list = $products->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		// $products_select_list = Product::all()->pluck('name', 'id');

		return view("sales.edit", compact("sale", "schoolbuilding", "job_description", "products_select_list", "sales_month", "sales_details"));
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
		// 個別生徒の売上データを更新する

		$requestData = $request->all();
		$charge_progress = ChargeProgress::latest('id')->first();
		$sales_month = $charge_progress->sales_month;
		$created_at = $charge_progress->created_at;

		$sales = Sale::findOrFail($id);
		SalesDetail::where('sales_number', $sales->sales_number)->delete();
		SalesDetail::where('student_no', $sales->student_no)
			->where('created_at', '>=', $created_at)
			->where('sale_month', '<', $sales_month)->delete();

		$sales_detail_cnt = is_countable($requestData['sale_month']) ? count($requestData['sale_month']) : 0;

		$student = Student::where('student_no', $sales->student_no);
		$student->update(['note' => $requestData['note']]);

		for ($i = 0; $i < $sales_detail_cnt; $i++) {
			if (!empty($requestData['product_id'][$i])) {
				$product = Product::where('id', $requestData['product_id'][$i])->first();
				$tax = 0;

				if ($product->tax_category == 2) {
					$tax = floor($requestData['price'][$i] * (config('const.consumption_tax') / 100));
				}
				if (empty($requestData['remarks'][$i])) {
					$requestData['remarks'][$i] = "";
				}
				$sales_details[] = [
					"student_no" => $sales->student_no,
					"sale_month" => $requestData['sale_month'][$i],
					"sales_date" => date("Y-m-d"),
					"product_id" => $requestData['product_id'][$i],
					"product_name" => $product->name . $requestData['product_free'][$i],
					"product_free" => $requestData['product_free'][$i],
					"product_price" => $product->price,
					"product_price_display" => $product->tax_category,
					"sales_category" =>  $product->division_code,
					"price" => $requestData['price'][$i],
					"tax" => $tax,
					"sales_number" => $sales->student_no . str_replace("-", "", $requestData['sale_month'][$i]),
					"subtotal" => $requestData['price'][$i] + $tax,
					"remarks" => $requestData['remarks'][$i],
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id,
					"created_at" => date('Y-m-d H:i:s'),
					"updated_at" => date('Y-m-d H:i:s')

				];
				if (empty($tax_sums[$requestData['sale_month'][$i]])) {
					$tax_sums[$requestData['sale_month'][$i]] = 0;
				}
				if (empty($sales_sums[$requestData['sale_month'][$i]])) {
					$sales_sums[$requestData['sale_month'][$i]] = 0;
				}
				$current_sale_month = $sales->sale_month;
				$student_no = $sales->student_no;
				$school_building = $sales->school_building_id;
				$school_id = $sales->school_id;
				$school_year = $sales->school_year;
				$brothers_flg = $sales->brothers_flg;
				$discount_id = $sales->discount_id;
				$tax_sums[$requestData['sale_month'][$i]] += $tax;
				$sales_sums[$requestData['sale_month'][$i]] += $requestData['price'][$i] + $tax;
			}
		}
		if (!empty($sales_details)) {
			SalesDetail::insert($sales_details);
		}
		unset($sale);
		foreach ($tax_sums as $sale_month => $tax_sum) {
			$sales = Sale::where('sale_month', $sale_month)->where('student_no', $student_no);
			$sales_sum = $sales_sums[$sale_month];

			if ($sales->exists()) {

				if ($sale_month != $current_sale_month) {
					$price_sumed = $sales->first()->sales_detail->sum('price');
					$tax_sumed = $sales->first()->sales_detail->sum('tax');
					$tax_sum += $tax_sumed;
					$sales_sum += $price_sumed;
				}
				$sale_params = [
					"tax" => $tax_sum,
					"sales_sum" => $sales_sum,
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id
				];
				$sales->update($sale_params);
			} else {
				$sale_params = [
					"student_no" => $student_no,
					"sale_month" => $sale_month,
					"school_building_id" => $school_building,
					"school_id" => $school_id,
					"school_year" => $school_year,
					"brothers_flg" => $brothers_flg,
					"discount_id" => $discount_id,
					"tax" => $tax_sum,
					"sales_sum" => $sales_sum,
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id,
					"sales_number" => $student_no . str_replace("-", "", $sale_month),
					"created_at" => date('Y-m-d H:i:s'),
					"updated_at" => date('Y-m-d H:i:s')

				];

				Sale::insert($sale_params);
			}
		}
		// get session url
		$url = session("url");
		session()->forget("url");
		if (strpos($url, "sales") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/sales")->with("flash_message", "データが更新されました。");
		}

		return redirect("/shinzemi/sales")->with("flash_message", "データが更新されました。");
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

		$sales = Sale::findOrFail($id);
		SalesDetail::where('sales_number', $sales->sales_number)->delete();
		Sale::where('id', $id)->delete();

		return redirect("/shinzemi/sales")->with("flash_message", "売上データが削除されました。");
	}


	/**
	 *Functions started by Ajax
	 *
	 * @param [type] $id
	 * @return $get_product_price
	 */
	public function get_product_price($id)
	{
		$get_product_price = Product::where('id', $id)->value('price');
		return $get_product_price;
	}


	public function bulk_store(Request $request)
	{
		// 選択済講座ID
		$selected_product = $request->input('selected_product');
		//  選択済講座IDから講座情報を取得
		$product_info = Product::where('id', $selected_product)->first();

		// 売上年月
		$sale_month_date = $request->input('sale_month_date');

		// 選択された生徒達のid
		$checked_students = $request->input('checked');

		// 消費税率をconfigから取得。商品の値段に消費税を加算するため
		// if ($product_info->price > 0) {
		// 	$tax = $product_info->price * (config('const.consumption_tax') / 100);
		// 	$tax = (int)$tax;
		// } else {
		// 	$tax = 0;
		// }
		$students = Student::whereIn('student_no', $checked_students)->get();
		$consumption_tax = (config('const.consumption_tax') / 100);


		// 売上明細に登録する配列を作成
		$sales_details = [];
		foreach ($students as $student) {
			$discounted_price = $product_info->price;

			if ($student->discount_id != "") {
				$discount = DiscountDetail::where('division_code_id', $product_info->division_code)->where('discount_id', $student->discount_id)->first();
				if ($discount) {
					$discount_rate = $discount->discount_rate;
					if ($discount_rate) {
						$discounted_price = floor($product_info->price * (1 - ($discount_rate / 100)));
					}
				}
			}
			if ($product_info->tax_category == 1) {
				$tax = 0;
				$discounted_price = $discounted_price;
			} elseif ($product_info->tax_category == 2) {
				$tax = floor($discounted_price * $consumption_tax);
				$discounted_price = $discounted_price;
			}

			$sales = 0;

			$sales_details[] = [
				"student_no" => $student->student_no,
				"sale_month" => $sale_month_date,
				"sales_date" => date("Y-m-d"),
				"product_id" => $product_info->id,
				"product_name" => $product_info->name,
				"product_price" => $product_info->price,
				"product_price_display" => $product_info->tax_category,
				"sales_category" =>  $product_info->division_code,
				"price" => $discounted_price,
				"tax" => $tax,
				"sales_number" => $student->student_no . str_replace("-", "", $sale_month_date),
				"subtotal" => $discounted_price + $tax,
				"remarks" => "",
				"creator" => Auth::user()->id,
				"updater" => Auth::user()->id,
				"created_at" => date('Y-m-d H:i:s'),
				"updated_at" => date('Y-m-d H:i:s')

			];


			// 売上用に講座の値段と消費税を計算
			$sales = $discounted_price + $tax;

			// Salesに既に登録されているsales_sumとtaxに加算する

			// 数字に変換
			$sales = (int)$sales;


			// 既存の売上データに講座の消費税と売上額を加算
			Sale::where('student_no', $student->student_no)->where('sale_month', $sale_month_date)
				->update([
					'tax' => DB::raw('tax + ' . $tax),
					'sales_sum' => DB::raw('sales_sum + ' . $sales),
				]);
		}

		// 売上明細に登録
		if (!empty($sales_details)) {
			SalesDetail::insert($sales_details);
		}

		$url = url()->previous();
		if (strpos($url, "sales") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/sales")->with("flash_message", "データが更新されました。");
		}
	}

	public function bulk_delete(Request $request)
	{

		// 選択済講座ID
		$selected_product = $request->input('selected_product_for_delete');
		//  選択済講座IDから講座情報を取得
		$product_info = Product::where('id', $selected_product)->first();

		// 売上年月
		$sale_month_date = $request->input('sale_month_date_for_delete');

		// 選択された生徒達のid
		$checked_students = $request->input('checked');

		$students = Student::whereIn('student_no', $checked_students)->get();
		$consumption_tax = (config('const.consumption_tax') / 100);

		foreach ($students as $student) {
			$discounted_price = $product_info->price;
			if ($student->discount_id != "") {
				$discount = DiscountDetail::where('division_code_id', $product_info->division_code)->where('discount_id', $student->discount_id)->first();
				if ($discount) {
					$discount_rate = $discount->discount_rate;
					if ($discount_rate) {
						$discounted_price = floor($product_info->price * (1 - ($discount_rate / 100)));
					}
				}
			}
			if ($product_info->tax_category == 1) {
				$tax = 0;
				$discounted_price = $discounted_price;
			} elseif ($product_info->tax_category == 2) {
				$tax = floor($discounted_price * $consumption_tax);
				$discounted_price = $discounted_price;
			}			// 売上明細から削除
			$sale_for_delete = SalesDetail::where('student_no', $student->student_no)->where('sale_month', $sale_month_date)->where('product_id', $product_info->id)->first();

			if ($sale_for_delete) {
				$sale_for_delete->delete();
				// 売上用に講座の値段と消費税を計算
				$sales = $discounted_price + $tax;
				// Salesに既に登録されているsales_sumとtaxに加算する
				// 数字に変換
				$sales = (int)$sales;

				// 既存の売上データから講座の消費税と売上額を減算
				Sale::where('student_no', $student->student_no)->where('sale_month', $sale_month_date)
					->update([
						'tax' => DB::raw('tax - ' . $tax),
						'sales_sum' => DB::raw('sales_sum - ' . $sales),
					]);
			}
			$sales_detail_exists = SalesDetail::where('student_no', $student->student_no)->where('sale_month', $sale_month_date)->exists();

			if (!$sales_detail_exists) {
				Sale::where('student_no', $student->student_no)->where('sale_month', $sale_month_date)->delete();
			}
		}

		$url = url()->previous();
		if (strpos($url, "sales") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/sales")->with("flash_message", "データが更新されました。");
		}
	}
}
    //=======================================================================