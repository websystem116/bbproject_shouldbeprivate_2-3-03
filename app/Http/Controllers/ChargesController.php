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
use App\ChargeDetail;
use App\Charge;
use App\Payment;
use App\ChargeProgress;

//=======================================================================
class ChargesController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request)
	{
		return view("charge.index");
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

		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();
		$charge_progress_update = ChargeProgress::where("id", $charge_progress->id);
		$before_charge_progress = ChargeProgress::where("sales_month", date("Y-m", strtotime("-1 month " . $charge_progress->sales_month . "-01")))->first();
		$data = ["charge_data_created_flg" => "1", "charge_data_created_date" => date("Y-m-d")];
		$charge_progress_update->update($data);
		unset($charge_progress_update);
		unset($data);


		$unupdate_charges = Charge::where('charge_month', $charge_progress->sales_month)->where('withdrawal_confirmed', 1)->get();
		foreach ($unupdate_charges as $unupdate_charge) {
			$confirmed_charge[] = $unupdate_charge->sales_number;
		}
		Charge::where('charge_month', $charge_progress->sales_month)->delete();
		ChargeDetail::where('sale_month', $charge_progress->sales_month)->delete();

		$sale = Sale::where('sale_month', $charge_progress->sales_month)
			->whereIn('sales_number', function ($query) {
				$query->from('sales_details')
					->select('sales_details.sales_number')
					->whereNotNull('product_id');
			})->get(); #退塾・休塾などの条件削除
		$sales_details = SalesDetail::where('created_at', '>=', $charge_progress->created_at)
			->where('sale_month', '<', $charge_progress->sales_month)->get();
		foreach ($sales_details as $sales_detail) {
			$before_sales_detail[$sales_detail->student_no][] = $sales_detail;
		}
		unset($sales_details);
		unset($sales_detail);
		$failed_sales_details = SalesDetail::whereNull('scrubed_month')
			->where('sale_month',  $before_charge_progress->sales_month)->get();
		foreach ($failed_sales_details as $failed_sales_detail) {
			$fails_student_nos[] = $failed_sales_detail->student_no;
		}
		if (!empty($fails_student_nos)) {
			$fails_student_no = array_unique($fails_student_nos);

			$failed_charges = Charge::whereNull('withdrawal_confirmed')
				->whereIn('student_no', $fails_student_no)
				->where('charge_month',  $before_charge_progress->sales_month)
				->where('sum', "<>",  0)->get();
			unset($fails_student_no);
			unset($fails_student_nos);
			foreach ($failed_charges as $failed_charge) {
				$fails_payment[$failed_charge->student_no] = $failed_charge->sum;
				if ($failed_charge->sum > 0) {
					$fails_student_nos[] = $failed_charge->student_no;
				}
			}
		}
		// if ($charge_progress->sales_month == "2024-06") {
		// 	$fails_student_nos[] = 05224341;
		// }

		// $sales_detail_fails = SalesDetail::whereNull('scrubed_month')->where('sale_month', "<>", $charge_progress->sales_month)->get();
		// foreach ($sales_detail_fails as $sales_detail_fail) {
		// 	if (empty($fails_payment[$sales_detail_fail->student_no])) {
		// 		$fails_payment[$sales_detail_fail->student_no] = $sales_detail_fail->subtotal;
		// 	} else {
		// 		$fails_payment[$sales_detail_fail->student_no] += $sales_detail_fail->subtotal;
		// 	}
		// 	if (!empty($fails_payment_month[$sales_detail_fail->student_no])) {
		// 		if ($fails_payment_month[$sales_detail_fail->student_no] > $sales_detail_fail->sale_month) {
		// 			$fails_payment_month[$sales_detail_fail->student_no] = $sales_detail_fail->sale_month;
		// 		}
		// 	} else {
		// 		$fails_payment_month[$sales_detail_fail->student_no] = $sales_detail_fail->sale_month;
		// 	}
		// 	if (!empty($fails_payment_month_all)) {
		// 		if ($fails_payment_month_all > $sales_detail_fail->sale_month) {
		// 			$fails_payment_month_all = $sales_detail_fail->sale_month;
		// 		}
		// 	} else {
		// 		$fails_payment_month_all = $sales_detail_fail->sale_month;
		// 	}
		// 	$fails_student_nos[] = $sales_detail_fail->student_no;
		// }
		if (!empty($fails_student_nos)) {
			$fails_student_no = array_unique($fails_student_nos);
			// unset($sales_detail_fails);
			$payments = Payment::whereIn('student_id', $fails_student_no)
				->where(function ($query) use ($charge_progress) {
					$query->whereNull('scrubed_month')->orWhere('scrubed_month',  $charge_progress->sales_month);
				})
				->where('sale_month', '>=', $before_charge_progress->sales_month)->where('created_at', '>', date("Y-m-d", strtotime(" -1 month " . $before_charge_progress->sales_month . "-21")))->get();
			$update_scrubed_month = [
				'scrubed_month' => $charge_progress->sales_month
			];
			Payment::whereIn('student_id', $fails_student_no)
				->where('sale_month', '>=', $before_charge_progress->sales_month)
				->whereNull('scrubed_month')
				->where('created_at', '>', date("Y-m-d", strtotime(" -1 month " . $before_charge_progress->sales_month . "-21")))
				->update($update_scrubed_month);

			foreach ($payments as $payment) {
				if (empty($scrubed_payment[$payment->student_id])) {
					$scrubed_payment[$payment->student_id] = $payment->payment_amount;
				} else {
					$scrubed_payment[$payment->student_id] += $payment->payment_amount;
				}
			}

			unset($payments);
		}
		// if ($charge_progress->sales_month == "2024-06") {
		// 	$scrubed_payment['05224341'] = 60500;
		// }

		$consumption_tax = (config('const.consumption_tax') / 100);

		$charge_params = [];
		foreach ($sale as $sale_value) {
			$sales_detail = $sale_value->sales_detail;
			$params = [];
			foreach ($sales_detail as $value) {
				$tax = 0;
				if ($value->product_price_display == 2) {
					$tax = floor($value->price * $consumption_tax);
				}
				# code...
				$params[] = [
					"student_no" => $value->student_no,
					"sale_month" => $value->sale_month,
					"product_id" => $value->product_id,
					"product_name" => $value->product_name,
					"product_price" => $value->product_price,
					"product_price_display" => $value->product_price_display,
					"price" => $value->price,
					"tax" => $tax,
					"subtotal" => $value->price + $tax,
					"remarks" => $value->remarks,
					"sales_number" => $value->sales_number,
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id,
					"created_at" => date("Y-m-d H:i:s"),
					"updated_at" => date("Y-m-d H:i:s")
				];
			}
			$before_tax_sum = 0;
			$before_price_sum = 0;
			$before_subtotal_sum = 0;

			if (!empty($before_sales_detail[$sale_value->student_no])) {
				foreach ($before_sales_detail[$sale_value->student_no] as $value) {
					$tax = 0;
					if ($value->product_price_display == 2) {
						$tax = floor($value->price * $consumption_tax);
					}
					# code...
					$params[] = [
						"student_no" => $value->student_no,
						"sale_month" => $sale_value->sale_month,
						"product_id" => $value->product_id,
						"product_name" => $value->product_name,
						"product_price" => $value->product_price,
						"product_price_display" => $value->product_price_display,
						"price" => $value->price,
						"tax" => $tax,
						"subtotal" => $value->price + $tax,
						"remarks" => $value->remarks,
						"sales_number" => $sale_value->sales_number,
						"creator" => Auth::user()->id,
						"updater" => Auth::user()->id,
						"created_at" => date("Y-m-d H:i:s"),
						"updated_at" => date("Y-m-d H:i:s")
					];
					$before_tax_sum += $tax;
					$before_price_sum += $value->price;
					$before_subtotal_sum += $value->price + $tax;
				}
			}
			ChargeDetail::insert($params);
			$carryover = 0;
			$prepaid = 0;
			if (!empty($fails_payment[$sale_value->student_no])) {
				$carryover = $fails_payment[$sale_value->student_no];
			}
			if (!empty($scrubed_payment[$sale_value->student_no])) {
				$prepaid = $scrubed_payment[$sale_value->student_no];
			}
			$sales_sum = $sale_value->sales_sum + $carryover - $prepaid;
			$withdrawal_confirmed = NULL;
			if (!empty($confirmed_charge)) {
				if (in_array($sale_value->sales_number, $confirmed_charge)) {
					$withdrawal_confirmed = 1;
				}
			}

			$charge_params[] = [
				"student_no" => $sale_value->student_no,
				"charge_month" => $sale_value->sale_month,
				"carryover" => $carryover,
				"month_sum" => $sale_value->sales_sum - $sale_value->tax + $before_price_sum,
				"month_tax_sum" => $sale_value->tax + $before_tax_sum,
				"prepaid" => $prepaid,
				"sum" => $sales_sum + $before_subtotal_sum,
				"withdrawal_confirmed" => $withdrawal_confirmed,
				"sales_number" => $sale_value->sales_number,
				"creator" => Auth::user()->id,
				"updater" => Auth::user()->id,
				"created_at" => date("Y-m-d H:i:s"),
				"updated_at" => date("Y-m-d H:i:s")
			];
		}
		Charge::insert($charge_params);

		return redirect("/shinzemi/home")->with("flash_message", "請求データを作成しました。");
	}
	public function charge_confirm()
	{
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();
		$charge_progress_update = ChargeProgress::where("id", $charge_progress->id);
		$data = ["charge_confirm_flg" => "1"];
		$charge_progress_update->update($data);
		return redirect("/shinzemi/home")->with("flash_message", "請求データを確定しました。");
	}
	public function charge_confirm_lift()
	{
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();
		$charge_progress_update = ChargeProgress::where("id", $charge_progress->id);
		$data = ["charge_confirm_flg" => "0"];
		$charge_progress_update->update($data);
		return redirect("/shinzemi/home")->with("flash_message", "請求データ確定を解除しました。");
	}
	public function charge_closing()
	{
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();
		$charge_progress_update = ChargeProgress::where("id", $charge_progress->id);
		$data = ["monthly_processing_date" => date("Y-m-d"), "new_monthly_processing_month" => date("Y-m")];
		$charge_progress_update->update($data);
		return redirect("/shinzemi/home")->with("flash_message", "月次締処理を確定しました。");
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
		$daily_salary = DailySalary::findOrFail($id);
		$schoolbuilding = SchoolBuilding::all()->pluck('name', 'id');
		$job_description = JobDescription::all()->pluck('name', 'id');

		return view("sales.edit", compact("daily_salary", "schoolbuilding", "job_description"));
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
			"code" => "nullable|max:4", //string('code',4)->nullable()
			"name" => "required|max:15", //string('name',15)->nullable()
			"name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
		]);
		$requestData = $request->all();

		$daily_salary = DailySalary::findOrFail($id);
		$daily_salary->update($requestData);

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
		DailySalary::destroy($id);

		return redirect("/shinzemi/sales")->with("flash_message", "データが削除されました。");
	}
}
    //=======================================================================
