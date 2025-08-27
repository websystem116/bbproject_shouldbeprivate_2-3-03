<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

use App\ChargeProgress;
use App\Charge;
use App\ChargeDetail;
use App\Sale;
use App\SalesDetail;
use App\Payment;

class ChargeCalculationController extends Controller
{
    /****
     * 非常勤給与の再計算を行う
     * 
     */
    public function chargeCalculation()
    {
        $sale_month = "2023-06";
        $charge_progress = ChargeProgress::where('sales_month', $sale_month)->first();
        unset($sale_month);
        $before_charge_progress = ChargeProgress::where("sales_month", date("Y-m", strtotime("-1 month " . $charge_progress->sales_month . "-01")))->first();
        $edit_student_no = "05223360";
        $sale = Sale::where('sale_month', $charge_progress->sales_month)
            ->where('student_no', $edit_student_no)
            ->whereIn('sales_number', function ($query) {
                $query->from('sales_details')
                    ->select('sales_details.sales_number')
                    ->whereNotNull('product_id');
            })->get(); #退塾・休塾などの条件削除
        $sales_details = SalesDetail::where('created_at', '>=', $charge_progress->created_at)
            ->where('student_no', $edit_student_no)
            ->where('sale_month', '<', $charge_progress->sales_month)->get();
        foreach ($sales_details as $sales_detail) {
            $before_sales_detail[$sales_detail->student_no][] = $sales_detail;
        }
        unset($sales_details);
        unset($sales_detail);
        $failed_charges = Charge::whereNull('withdrawal_confirmed')
            ->where('student_no', $edit_student_no)
            ->where('charge_month',  $before_charge_progress->sales_month)->get();
        foreach ($failed_charges as $failed_charge) {
            $fails_payment[$failed_charge->student_no] = $failed_charge->sum;
            $fails_student_nos[] = $failed_charge->student_no;
        }
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
            $payments = Payment::whereIn('student_id', $fails_student_no)->where('created_at', '>=', $before_charge_progress->withdrawal_nanto_date)->where('created_at', '<=', $charge_progress->withdrawal_nanto_date)->get();

            foreach ($payments as $payment) {
                if (empty($scrubed_payment[$payment->student_id])) {
                    $scrubed_payment[$payment->student_id] = $payment->payment_amount;
                } else {
                    $scrubed_payment[$payment->student_id] += $payment->payment_amount;
                }
            }

            unset($payments);
        }
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
                    "creator" => 0,
                    "updater" => 0,
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
                        "creator" => 0,
                        "updater" => 0,
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
            $charge_params[] = [
                "student_no" => $sale_value->student_no,
                "charge_month" => $sale_value->sale_month,
                "carryover" => $carryover,
                "month_sum" => $sale_value->sales_sum - $sale_value->tax + $before_price_sum,
                "month_tax_sum" => $sale_value->tax + $before_tax_sum,
                "prepaid" => $prepaid,
                "sum" => $sales_sum + $before_subtotal_sum,
                "sales_number" => $sale_value->sales_number,
                // "creator" => Auth::user()->id,
                // "updater" => Auth::user()->id,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ];
        }
        Charge::insert($charge_params);
    }
}
