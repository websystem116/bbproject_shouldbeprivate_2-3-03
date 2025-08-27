<?php

namespace App\Http\Controllers;

use Auth;

use App\JukoInfo;
use App\Student;
use App\Product;
use App\Bank;
use App\BranchBank;
use App\ChargeProgress;
use App\Discount;
use App\SchoolBuilding;
use App\School;
use App\HighschoolCourse;
use App\Sale;
use App\SalesDetail;

use Illuminate\Http\Request;

class JukoInfoController extends Controller
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
		$query = Student::query();
		$perPage = 25;

		//検索の値取得
		$student_search['id_start'] = $request->get("id_start");
		$student_search['id_end'] = $request->get("id_end");
		$student_search['no_start'] = $request->get("no_start");
		$student_search['no_end'] = $request->get("no_end");
		$student_search['surname'] = $request->get("surname");
		$student_search['name'] = $request->get("name");
		$student_search['surname_kana'] = $request->get("surname_kana");
		$student_search['name_kana'] = $request->get("name_kana");
		$student_search['phone'] = $request->get("phone");
		$student_search['school_id'] = $request->get("school_id");
		$student_search['brothers_flg'] = $request->get("brothers_flg");
		$student_search['fatherless_flg'] = $request->get("fatherless_flg");
		$student_search['rest_flg'] = $request->get("rest_flg");
		$student_search['graduation_flg'] = $request->get("graduation_flg");
		$student_search['withdrawal_flg'] = $request->get("withdrawal_flg");
		$student_search['temporary_flg'] = $request->get("temporary_flg");
		$student_search['grade_start'] = $request->get("grade_start");
		$student_search['grade_end'] = $request->get("grade_end");
		$student_search['school_building_id'] = $request->get("school_building_id");
		$student_search['product_select'] = $request->get("product_select");
		$student_search['discount_select'] = $request->get("discount_select");
		$student_search['juku_start_date'] = $request->get("juku_start_date");
		$student_search['juku_end_date'] = $request->get("juku_end_date");
		$student_search['juku_graduation_start_date'] = $request->get("juku_graduation_start_date");
		$student_search['juku_graduation_end_date'] = $request->get("juku_graduation_end_date");
		$student_search['juku_return_start_date'] = $request->get("juku_return_start_date");
		$student_search['juku_return_end_date'] = $request->get("juku_return_end_date");
		$student_search['juku_withdrawal_start_date'] = $request->get("juku_withdrawal_start_date");
		$student_search['juku_withdrawal_end_date'] = $request->get("juku_withdrawal_end_date");
		$student_search['juku_rest_start_date'] = $request->get("juku_rest_start_date");
		$student_search['juku_rest_end_date'] = $request->get("juku_rest_end_date");

		// 検索するテキストが入力されている場合のみ
		if (!empty($student_search['id_start']) && empty($student_search['id_end'])) {
			$query->where('students.id', $student_search['id_start']);
		}
		if (!empty($student_search['id_end']) && empty($student_search['id_start'])) {
			$query->where('students.id', $student_search['id_end']);
		}
		if (!empty($student_search['id_start']) && !empty($student_search['id_end'])) {
			$query->whereBetween('students.id', [$student_search['id_start'], $student_search['id_end']]);
		}
		if (
			!empty($student_search['no_start']) && empty($student_search['no_end'])
		) {
			$query->where('students.student_no', $student_search['no_start']);
		}
		if (!empty($student_search['no_end']) && empty($student_search['no_start'])) {
			$query->where('students.student_no', $student_search['no_end']);
		}
		if (!empty($student_search['no_start']) && !empty($student_search['no_end'])) {
			$query->whereBetween('students.student_no', [$student_search['no_start'], $student_search['no_end']]);
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
		if (!empty($student_search['phone'])) {
			$query->where('phone1', 'like', '%' . $student_search['phone'] . '%')->orWhere('phone2', 'like', '%' . $student_search['phone'] . '%');
		}
		if (!empty($student_search['school_id'])) {
			$query->where('school_id', $student_search['school_id']);
		}
		if (!empty($student_search['brothers_flg'])) {
			$query->where('brothers_flg', $student_search['brothers_flg']);
		}
		if (!empty($student_search['fatherless_flg'])) {
			$query->where('fatherless_flg', $student_search['fatherless_flg']);
		}
		if (!empty($student_search['rest_flg'])) {
			$query->WhereNotNull('juku_rest_date');
		}
		if (!empty($student_search['graduation_flg'])) {
			$query->WhereNotNull('juku_graduation_date');
		}
		if (!empty($student_search['withdrawal_flg'])) {
			$query->whereNotNull('juku_withdrawal_date');
		}
		if (!empty($student_search['temporary_flg'])) {
			$query->where('temporary_flg', $student_search['temporary_flg']);
		}
		if (!empty($student_search['grade_start']) && empty($student_search['grade_end'])) {
			$query->where('grade', $student_search['grade_start']);
		}
		if (!empty($student_search['grade_end']) && empty($student_search['grade_start'])) {
			$query->where('grade', $student_search['grade_end']);
		}
		if (!empty($student_search['grade_start']) && !empty($student_search['grade_end'])) {
			$student_search['grade_start'] = intval($student_search['grade_start']);
			$student_search['grade_end'] = intval($student_search['grade_end']);
			$query->WhereBetween('grade', [$student_search['grade_start'], $student_search['grade_end']]);
		}
		if (!empty($student_search['school_building_id'])) {
			$query->where('school_building_id', $student_search['school_building_id']);
		}
		if (!empty($student_search['product_select'])) {
			$query->join('juko_infos', 'juko_infos.student_no', '=', 'students.student_no')
				->where('juko_infos.product_id', $student_search['product_select']);
		}
		if (!empty($student_search['discount_select'])) {
			$query->where('discount_id', $student_search['discount_select']);
		}
		//入塾日
		if (!empty($student_search['juku_start_date']) && empty($student_search['juku_end_date'])) {
			$dt_juku_start_date = $student_search['juku_start_date'];
			return redirect("/shinzemi/juko_info")->with("message", "※入塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_end_date']) && empty($student_search['juku_start_date'])) {
			return redirect("/shinzemi/juko_info")->with("message", "※入塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_start_date']) && !empty($student_search['juku_end_date'])) {
			$query->whereBetween('juku_start_date', [$student_search['juku_start_date'], $student_search['juku_end_date']]);
		}
		//卒塾日
		if (!empty($student_search['juku_graduation_start_date']) && empty($student_search['juku_graduation_end_date'])) {
			return redirect("/shinzemi/juko_info")->with("message", "※卒塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_graduation_end_date']) && empty($student_search['juku_graduation_start_date'])) {
			return redirect("/shinzemi/juko_info")->with("message", "※卒塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_graduation_start_date']) && !empty($student_search['juku_graduation_end_date'])) {
			$student_search['graduation_flg'] = 1;
			$query->whereBetween('juku_graduation_date', [$student_search['juku_graduation_start_date'], $student_search['juku_graduation_end_date']]);
		}

		//復塾日
		if (!empty($student_search['juku_return_start_date']) && empty($student_search['juku_return_end_date'])) {
			return redirect("/shinzemi/juko_info")->with("message", "※復塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_return_end_date']) && empty($student_search['juku_return_start_date'])) {
			return redirect("/shinzemi/juko_info")->with("message", "※復塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_return_start_date']) && !empty($student_search['juku_return_end_date'])) {
			$query->whereBetween('juku_return_date', [$student_search['juku_return_start_date'], $student_search['juku_return_end_date']]);
		}

		//退塾日
		if (!empty($student_search['juku_withdrawal_start_date']) && empty($student_search['juku_withdrawal_end_date'])) {
			return redirect("/shinzemi/juko_info")->with("message", "※退塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_withdrawal_end_date']) && empty($student_search['juku_withdrawal_start_date'])) {
			return redirect("/shinzemi/juko_info")->with("message", "※退塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_withdrawal_start_date']) && !empty($student_search['juku_withdrawal_end_date'])) {
			$student_search['withdrawal_flg'] = 1;
			$query->whereBetween('juku_withdrawal_date', [$student_search['juku_withdrawal_start_date'], $student_search['juku_withdrawal_end_date']]);
		}

		//休塾日
		if (!empty($student_search['juku_rest_start_date']) && empty($student_search['juku_rest_end_date'])) {
			return redirect("/shinzemi/juko_info")->with("message", "※休塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_rest_end_date']) && empty($student_search['juku_rest_start_date'])) {
			return redirect("/shinzemi/juko_info")->with("message", "※休塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_rest_start_date']) && !empty($student_search['juku_rest_end_date'])) {
			$student_search['rest_flg'] = 1;
			$query->whereBetween('juku_rest_date', [$student_search['juku_rest_start_date'], $student_search['juku_rest_end_date']]);
		}

		if ($student_search['withdrawal_flg'] == 1) { //卒塾チェック
			$query->whereNotNull('juku_withdrawal_date');
		}
		if ($student_search['graduation_flg'] == 1) { //退塾チェック
			$query->whereNotNull('juku_graduation_date');
		}
		if ($student_search['rest_flg'] == 1) {
			$query->whereNotNull('juku_rest_date')->whereNull('juku_graduation_date')->whereNull('juku_withdrawal_date');
		}
		if (empty($student_search['withdrawal_flg']) && empty($student_search['graduation_flg']) && empty($student_search['rest_flg'])) { //在塾生徒
			$query->whereNull('juku_rest_date')->whereNull('juku_graduation_date')->whereNull('juku_withdrawal_date');
		}

		$student = $query->paginate($perPage);

		return view("juko_info.index", compact("student", "student_search", "schools_select_list", "schooolbuildings_select_list", "products_select_list", "discounts_select_list"));
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
		$requestDatas = $request->all();

		JukoInfo::where('student_no', $request->student_no)->delete();
		for ($i = 0; $i < count($requestDatas['product_id']); $i++) {
			$requestData = ['product_id' => $requestDatas['product_id'][$i], 'student_id' => $requestDatas['student_id'], 'student_no' => $requestDatas['student_no']];
			JukoInfo::create($requestData);
		}
		//受講情報の備考をstudentsに書き込む
		$student = Student::where('student_no', $request->student_no);
		$student->update(['note' => $requestDatas['note']]);

		$charge_progress_id = ChargeProgress::max('id');
		$charge_progress = ChargeProgress::where('id', $charge_progress_id)->first();
		if (empty($charge_progress->monthly_processing_date)) {
			//売上データが作成されていたら
			$create_flg = Sale::where('student_no', $request->student_no)->where('sale_month', $charge_progress->sales_month)->exists();
			if (!$create_flg) {
				//売上データにその生徒のデータがなかったら
				$student = Student::where('student_no', $request->student_no)->first(); #退塾・休塾などの条件必要あり
				$params = [];
				$consumption_tax = (config('const.consumption_tax') / 100);
				$tax_sum = 0;
				$sales_sum = 0;
				$juko_infos = $student->juko_infos;
				foreach ($juko_infos as $value) {
					# code...
					if ($value->product->tax_category == 1) {
						$tax = 0;
						$discounted_price = $value->product->price;
					} elseif ($value->product->tax_category == 2) {
						$tax = floor($value->product->price * $consumption_tax);
						$discounted_price = $value->product->price;
					}
					if ($student->discount_id != "") {
						$discount_detail = $student->discount->discountdetails;
						$discount = $discount_detail->where('division_code_id', $value->product->division_code)->first();
						if ($discount) {
							$discount_rate = $discount->discount_rate;
							if ($discount_rate) {
								if ($value->product->tax_category == 1) {
									$discounted_price = floor(($value->product->price - $tax) * (1 - ($discount_rate / 100)));
								} elseif ($value->product->tax_category == 2) {
									$discounted_price = floor($value->product->price * (1 - ($discount_rate / 100)));
								}
								if ($value->product->tax_category == 1) {
									$tax = 0;
								} elseif ($value->product->tax_category == 2) {
									$tax = floor($discounted_price * $consumption_tax);
								}

							}
						}
					}
					$params[] = [
						"student_id" => $value->student_id,
						"student_no" => $value->student_no,
						"sale_month" => date('Y-m', strtotime($charge_progress->sales_month."-01")),
						"product_id" => $value->product_id,
						"sales_number" => $value->student_no . date('Ym', strtotime($charge_progress->sales_month."-01")),
						"product_name" => $value->product->name,
						"product_price" => $value->product->price,
						"product_price_display" => $value->product->tax_category,
						"sales_category" => $value->product->division_code,
						"price" => $discounted_price,
						"tax" => $tax,
						"subtotal" => $discounted_price+$tax,
						"creator" => Auth::user()->id,
						"updater" => Auth::user()->id
					];
					$tax_sum += $tax;
					$sales_sum += floor($discounted_price * (1 + $consumption_tax));
				}
				SalesDetail::insert($params);

				$sale_params[] = [
					"student_id" => $student->id,
					"student_no" => $student->student_no,
					"sale_month" => date('Y-m', strtotime($charge_progress->sales_month."-01")),
					"school_building_id" => $student->school_building_id,
					"school_id" => $student->school_id,
					"school_year" => $student->grade,
					"brothers_flg" => $student->brother_flg,
					"discount_id" => $student->discount_id,
					"tax" => $tax_sum,
					"sales_sum" => $sales_sum,
					"sales_number" => $student->student_no . date('Ym', strtotime($charge_progress->sales_month."-01")),
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id
				];
				Sale::insert($sale_params);
			}
		}
		// get session url
		$url = session("url");
		session()->forget("url");

		if (strpos($url, "juko_info") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/juko_info")->with("flash_message", "データが更新されました。");
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\JukoInfo  $jukoInfo
	 * @return \Illuminate\Http\Response
	 */
	public function show(JukoInfo $jukoInfo)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id student_no
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{

		$url = url()->previous();
		// sessionにURLを保存
		session(["url" => $url]);
		$id = sprintf('%08d', $id);
		//生徒情報取得
		$student = Student::where('student_no', $id)->first();
		// $student = sprintf('%08d', $student);
		// dd($student);
		//生徒情報に紐づく受講情報を取得
		$jukoinfos = Student::where('student_no', $id)->first('student_no')->juko_infos;
		// dd($jukoinfos);


		//商品のセレクトリスト
		$products = Product::get();
		$products_select_list = $products->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});


		return view("juko_info.edit", compact("student", "jukoinfos", "products_select_list"));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\JukoInfo  $jukoInfo
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  $id = student_np
	 * @param  \Illuminate\Http\Request
	 */
	public function product_delete(Request $request, $id)
	{
		JukoInfo::where('student_no', $id)->delete();
		$url = url()->previous();
		if (strpos($url, "juko_info") !== false) {
			return redirect($url)->with("flash_message", "データが削除されました。");
		} else {
			return redirect("/shinzemi/juko_info")->with("flash_message", "データが削除されました。");
		}
	}

	public function bulk_store(Request $request)
	{
		// 選択済講座ID
		$selected_product = $request->input('selected_product');

		// 選択された生徒達のno
		$checked_students = $request->input('checked');

		foreach ($checked_students as $checked_student) {
			// juko_infoモデルにデータを登録
			$requestData = ['product_id' => $selected_product, 'student_no' => $checked_student];

			JukoInfo::create($requestData);
		}

		$url = url()->previous();
		if (strpos($url, "juko_info") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/juko_info")->with("flash_message", "データが更新されました。");
		}
	}

	public function bulk_delete(Request $request)
	{

		// 選択済講座ID
		$selected_product = $request->input('selected_product_for_delete');

		// 選択された生徒達のid
		$checked_students = $request->input('checked');


		foreach ($checked_students as $checked_student) {
			// juko_infoモデルにデータを登録
			$requestData = ['product_id' => $selected_product, 'student_no' => $checked_student];

			$juko_info = JukoInfo::where('student_no', $requestData['student_no'])->where('product_id', $requestData['product_id'])->first();
			if ($juko_info) {
				$juko_info->delete();
			}
		}

		$url = url()->previous();
		if (strpos($url, "juko_info") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/juko_info")->with("flash_message", "データが更新されました。");
		}
	}
}
