<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
use App\Payment;
use App\Bank;
use App\BranchBank;

use App\Charge;
use App\ChargeProgress;
use App\InvoiceComment;

//=======================================================================
class ChargeOutputController extends Controller
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
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		$schools = School::all()->pluck('name', 'id');
		$products = Product::all()->pluck('name', 'id');
		$discounts = Discount::all()->pluck('name', 'id');
		$invoice_comment = InvoiceComment::all()->pluck('abbreviation', 'id');
		$search_names = [];
		$student_search['month'] = $request->get("month");
		$student_search['last_name'] = $request->get("last_name");
		$student_search['first_name'] = $request->get("first_name");
		$student_search['school_year_start'] = $request->get("school_year_start");
		$student_search['school_year_end'] = $request->get("school_year_end");
		$student_search['school_building'] = $request->get("school_building");
		$student_search['school'] = $request->get("school");
		$student_search['product'] = $request->get("product");
		$student_search['discount'] = $request->get("discount");
		$student_search['brothers_flg'] = $request->get("brothers_flg");
		$student_search['convenience_store_flg'] = $request->get("convenience_store_flg");
		if ($request->get("brothers_flg") == 1)
			$student_search['brothers_flg'] = "";
		$charges = [];
		if (!empty($student_search['month']) || !empty($student_search['last_name']) || !empty($student_search['first_name']) || !empty($student_search['school_year_start']) || !empty($student_search['school_year_end']) || !empty($student_search['school_building']) || !empty($student_search['school']) || !empty($student_search['product']) || !empty($student_search['discount']) || !empty($student_search['brothers_flg'])) {
			$charges = Charge::whereIn('student_no', function ($query) use ($student_search) {
				$query->from('students')
					->select('students.student_no')
					->when(!empty($student_search['last_name']), function ($query) use ($student_search) {
						return $query->where('students.surname', 'like', '%' . $student_search['last_name'] . '%');
					})->when(!empty($student_search['first_name']), function ($query) use ($student_search) {
						return $query->where('students.name', 'like', '%' . $student_search['first_name'] . '%');
					})->when(!empty($student_search['school_year_start']) && !empty($student_search['school_year_end']), function ($query) use ($student_search) {
						return $query->whereBetween('students.grade', [$student_search['school_year_start'], $student_search['school_year_end']]);
					})->when(!empty($student_search['school_building']), function ($query) use ($student_search) {
						return $query->where('students.school_building_id', $student_search['school_building']);
					})->when(!empty($student_search['school']), function ($query) use ($student_search) {
						return $query->where('students.school_id', $student_search['school']);
					})->when(!empty($student_search['discount']), function ($query) use ($student_search) {
						return $query->where('students.discount_id', $student_search['discount']);
					})->when(!empty($student_search['brothers_flg']), function ($query) use ($student_search) {
						if ($student_search['brothers_flg'] == 2) {
							return $query->where('students.brothers_flg', 1);
						} else {
							return $query->where('students.brothers_flg', '<>', 1);
						}
					});
			})->whereIn(
				'sales_number',
				function ($query) use ($student_search) {
					$query->from('charge_details')
						->select('charge_details.sales_number')->when(!empty($student_search['product']), function ($query) use ($student_search) {
							return $query->where('charge_details.product_id', $student_search['product']);
						});
				}
			)->when(!empty($student_search['month']), function ($query) use ($student_search) {
				return $query->where('charge_month', $student_search['month']);
			})->with('student')->get()->sort(function ($first, $second) {
				if ($first->student->school_building_id == $second->student->school_building_id) {
					if ($first->student->grade == $second->student->grade) {
						return $first->student->juku_start_date < $second->student->juku_start_date ? -1 : 1;
					}
					return $first->student->grade < $second->student->grade ? -1 : 1;
				}
				return $first->student->school_building_id < $second->student->school_building_id ? -1 : 1;
			});

			// 総件数を取得
			$charges_count = $charges->count();
			// firstItem() lastItem() total() が使えないので、ここで計算
			if ($charges_count > 0) {
				$charges_info['charges_first_item'] = 1;
				$charges_info['charges_last_item'] = $charges_count;
				$charges_info['charges_total'] = $charges_count;
			} else {
				$charges_info['charges_first_item'] = 0;
				$charges_info['charges_last_item'] = 0;
				$charges_info['charges_total'] = 0;
			}
			// 選択された年月から、コンビニ振込フラグの多数決を取る
			$majorityFlag = 0; // デフォルトは0（コンビニ振込なし）
			if (!empty($student_search['month'])) {
				$majorityFlag = Charge::where('charge_month', $student_search['month'])
					->select('convenience_store_flg', DB::raw('count(*) as total'))
					->groupBy('convenience_store_flg')
					->orderByDesc('total')
					->value('convenience_store_flg') ?? 0; // 存在しない場合は0
			}
			$student_search['convenience_store_flg'] = $majorityFlag;
		} else {
			$charges_info['charges_first_item'] = 0;
			$charges_info['charges_last_item'] = 0;
			$charges_info['charges_total'] = 0;
		}
		return view("charge_output.index", compact("charges", "school_buildings", "invoice_comment", "products", "schools", "discounts", "student_search",  "charges_info"));
	}
	public function nanto_import_index(Request $request)
	{
		return view("charge_output.nanto_import_index");
	}
	public function risona_import_index(Request $request)
	{
		return view("charge_output.risona_import_index");
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

	public function nanto_index()
	{
		return view("charge_output.nanto_index");
	}
	public function risona_index()
	{
		// charge_output.index_nantoを表示
		return view("charge_output.risona_index");
	}

	public function export_nanto(Request $request)
	{

		$request = $request->all();
		$date = $request['date'];

		// 南都WEB出力
		function mb_str_pad($input, $pad_length, $pad_string = " ", $pad_style = STR_PAD_RIGHT, $encoding = "UTF-8")
		{
			$mb_pad_length = strlen($input) - mb_strlen($input, $encoding) + $pad_length;
			return str_pad($input, $mb_pad_length, $pad_string, $pad_style);
		}
		$str = "19100000056437ｼﾝｶﾞｸｾﾞﾐﾅｰﾙ                             ";
		$str .= date('md', strtotime($date));
		$str .= "0162ﾅﾝﾄｷﾞﾝｺｳ       100ｶﾞｸｴﾝﾏｴｼﾃﾝ     10205083                 \r\n";
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();
		$charge_progress_update = ChargeProgress::where("id", $charge_progress->id);
		$data = ["withdrawal_nanto_date" => $date];
		$charge_progress_update->update($data);

		$charges = Charge::whereIn(
			'student_no',
			function ($query) {
				$query->from('students')
					->select('students.student_no')
					->where('students.payment_methods', '1')
					->whereNull('students.juku_withdrawal_date')
					->whereNull('students.juku_graduation_date')
					->whereNull('students.juku_rest_date');
			}
		)->where("charge_month", $charge_progress->sales_month)->get();
		$banks = Bank::all();
		foreach ($banks as $bank) {
			$bank_info[$bank->code]['name_kana'] = $bank->name_kana;
			$bank_info[$bank->code]['id'] = $bank->id;
		}
		$branch_banks = BranchBank::all();
		foreach ($branch_banks as $branch_bank) {
			$banks_branch_bank[$branch_bank->bank_id][$branch_bank->code]['name_kana'] = $branch_bank->name_kana;
		}
		$cnt = 0;
		$total_sum = 0;
		foreach ($charges as $value) {
			$student = $value->student;
			if ($value->sum > 0) {
				if (!empty($student->bank_id)) {
					if (empty($banks_branch_bank[$bank_info[$student->bank_id]['id']][$student->branch_code]['name_kana'])) {
						dd($student->id);
					}
					$bank_code = $student->bank_id;
					$bank_name_kana = $bank_info[$student->bank_id]['name_kana'];
					$branch_bank_code = $student->branch_code;
					$branch_bank_name_kana = $banks_branch_bank[$bank_info[$student->bank_id]['id']][$student->branch_code]['name_kana'];
					$bank_number = $student->bank_number;
					$bank_name_kana = mb_convert_kana($bank_name_kana, 'k', 'utf-8');
					$bank_name_kana = mb_convert_kana($bank_name_kana, 'h', 'utf-8');
					$branch_bank_name_kana = mb_convert_kana($branch_bank_name_kana, 'k', 'utf-8');
					$branch_bank_name_kana = mb_convert_kana($branch_bank_name_kana, 'h', 'utf-8');
					$bank_name_kana = mb_str_pad($bank_name_kana, 15);
					$branch_bank_name_kana = mb_str_pad($branch_bank_name_kana, 15);
					$name = mb_convert_kana($student->bank_holder, 'ks', 'utf-8');
					$bank_type = config("const.account_type")[$student->bank_type];
					switch ($bank_type) {
						case '普通':
							$bank_type_no = 1;
							break;
						case '当座':
							$bank_type_no = 2;
							break;

						default:
							# code...
							break;
					}
					$name = mb_convert_kana($name, 'h', 'utf-8');
					$name = mb_str_pad($name, 30);
					$str .= "2";
					$str .= $bank_code . $bank_name_kana;
					$str .= $branch_bank_code . $branch_bank_name_kana; //
					$str .= "    "; //
					$str .= $bank_type_no; //預金種目
					$str .= str_pad($bank_number, 7, "0", STR_PAD_LEFT); //口座番号
					$str .= $name;
					if ($value->sum > 0) {
						$sum = $value->sum;
					} else {
						$sum = 0; //請求金額
					}
					$str .= str_pad($sum, 10, "0", STR_PAD_LEFT); //請求金額

					$str .= "1";
					$str .= str_pad($student->student_no, 20, "0", STR_PAD_LEFT); //顧客番号？？？？
					$str .= "0"; //
					$str .= "        "; //
					$str .= "\r\n"; //
					$total_sum += $sum;
					$cnt++;
				}
			}
		}
		$str .= "8"; //
		$str .= str_pad($cnt, 6, "0", STR_PAD_LEFT); //
		$str .= str_pad($total_sum, 12, "0", STR_PAD_LEFT); //
		$str .= str_repeat('0', 6);
		$str .= str_repeat('0', 12);
		$str .= str_repeat('0', 6);
		$str .= str_repeat('0', 12);
		$str .= str_repeat(' ', 65);
		$str .= "\r\n"; //
		$str .= "9"; //
		$str .= str_repeat(' ', 119);

		// $filename = "南都銀行出力用" . date("Y-m", strtotime('+1 month'));

		$fileName = "南都銀行出力用" . date("Y-m", strtotime('+1 month'));
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename=' . $fileName);
		echo mb_convert_encoding($str, "SJIS", "UTF-8");  //←UTF-8のままで良ければ不要です。
		exit;
	}
	public function export_risona(Request $request)
	{

		$request = $request->all();
		$date = $request['date'];
		function mb_str_pad2($input, $pad_length, $pad_string = " ", $pad_style = STR_PAD_RIGHT, $encoding = "UTF-8")
		{
			$mb_pad_length = strlen($input) - mb_strlen($input, $encoding) + $pad_length;
			return str_pad($input, $mb_pad_length, $pad_string, $pad_style);
		}
		$str = "19100000022388ｼﾝｶﾞｸｾﾞﾐﾅｰﾙ                             ";
		$str .= date('md', strtotime($date));
		$str .= "0010ﾘｿﾅｷﾞﾝｺｳ       050ｼﾝﾅﾗｴｲｷﾞｮｳﾌﾞ   10241393                 \r\n";
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();
		$charge_progress_update = ChargeProgress::where("id", $charge_progress->id);
		$data = ["withdrawal_risona_date" => $date];
		$charge_progress_update->update($data);

		$charges = Charge::whereIn(
			'student_no',
			function ($query) {
				$query->from('students')
					->select('students.student_no')
					->where('students.payment_methods', '2')
					->whereNull('students.juku_withdrawal_date')
					->whereNull('students.juku_graduation_date')
					->whereNull('students.juku_rest_date');
			}
		)->where("charge_month", $charge_progress->sales_month)->get();
		$banks = Bank::all();
		foreach ($banks as $bank) {
			$bank_info[$bank->code]['name_kana'] = $bank->name_kana;
			$bank_info[$bank->code]['id'] = $bank->id;
		}
		$branch_banks = BranchBank::all();
		foreach ($branch_banks as $branch_bank) {
			$banks_branch_bank[$branch_bank->bank_id][$branch_bank->code]['name_kana'] = $branch_bank->name_kana;
		}

		$cnt = 0;
		$total_sum = 0;
		foreach ($charges as $value) {
			$student = $value->student;
			if ($value->sum > 0) {
				if (!empty($student->bank_id)) {
					$bank_code = $student->bank_id;
					$bank_name_kana = $bank_info[$student->bank_id]['name_kana'];
					$branch_bank_code = $student->branch_code;
					$branch_bank_name_kana = $banks_branch_bank[$bank_info[$student->bank_id]['id']][$student->branch_code]['name_kana'];
					$bank_number = $student->bank_number;
					$bank_name_kana = mb_convert_kana($bank_name_kana, 'k', 'utf-8');
					$bank_name_kana = mb_convert_kana($bank_name_kana, 'h', 'utf-8');
					$branch_bank_name_kana = mb_convert_kana($branch_bank_name_kana, 'k', 'utf-8');
					$branch_bank_name_kana = mb_convert_kana($branch_bank_name_kana, 'h', 'utf-8');
					$bank_name_kana = mb_str_pad2($bank_name_kana, 15);
					$branch_bank_name_kana = mb_str_pad2($branch_bank_name_kana, 15);
					$name = mb_convert_kana($student->bank_holder, 'ks', 'utf-8');
					$bank_type = config("const.account_type")[$student->bank_type];
					switch ($bank_type) {
						case '普通':
							$bank_type_no = 1;
							break;
						case '当座':
							$bank_type_no = 2;
							break;

						default:
							# code...
							break;
					}
					$name = mb_convert_kana($name, 'h', 'utf-8');
					$name = mb_str_pad2($name, 30);
					$str .= "2";
					$str .= $bank_code . $bank_name_kana;
					$str .= $branch_bank_code . $branch_bank_name_kana; //
					$str .= "    "; //
					$str .= $bank_type_no; //預金種目
					$str .= str_pad($bank_number, 7, "0", STR_PAD_LEFT); //口座番号
					$str .= $name;
					if ($value->sum > 0) {
						$sum = $value->sum;
					} else {
						$sum = 0; //請求金額
					}
					$str .= str_pad($sum, 10, "0", STR_PAD_LEFT); //請求金額
					$str .= "1";
					$str .= str_pad($student->student_no, 20, "0", STR_PAD_LEFT); //顧客番号？？？？
					$str .= "0"; //
					$str .= "        "; //
					$str .= "\r\n"; //
					$total_sum += $sum;
					$cnt++;
				}
			}
		}
		$str .= "8"; //
		$str .= str_pad($cnt, 6, "0", STR_PAD_LEFT); //
		$str .= str_pad($total_sum, 12, "0", STR_PAD_LEFT); //
		$str .= str_repeat('0', 6);
		$str .= str_repeat('0', 12);
		$str .= str_repeat('0', 6);
		$str .= str_repeat('0', 12);
		$str .= str_repeat(' ', 65);
		$str .= "\r\n"; //
		$str .= "9"; //
		$str .= str_repeat(' ', 119);
		// $filename = "/var/www/html/shinzemi/storage/app/public/りそな銀行出力用" . date("Y-m", strtotime('+1 month'));
		$fileName = "りそな銀行出力用" . date("Y-m", strtotime('+1 month'));
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename=' . $fileName);
		echo mb_convert_encoding($str, "SJIS", "UTF-8");  //←UTF-8のままで良ければ不要です。
		exit;
		// file_put_contents($filename, $str);
	}
	public function import_nanto(Request $request)
	{
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();
		// $charge_progress = ChargeProgress::where('id', '10')->first();
		$school_buildings = Student::where('payment_methods', 1)->get()->pluck('school_building_id', 'student_no');
		Storage::putFileAs('/upFiles', $request->file('nanto_file'), 'nanto_file.txt');
		$sample_data = Storage::get('/upFiles/nanto_file.txt');
		$sample_data = mb_convert_encoding($sample_data, "utf-8", "sjis-win");
		$data = explode("\r\n", $sample_data);
		$data_cnt = is_countable($data) ? count($data) : 0;
		$payment_data = array();
		$student_nos = array();
		for ($i = 0; $i < $data_cnt; $i++) {
			if ($i == 0) {
			} elseif ($i == ($data_cnt - 1)) {
			} elseif ($i == ($data_cnt - 2)) {
			} elseif ($i == ($data_cnt - 3)) {
			} else {
				$ng_flg = mb_substr($data[$i], 111, 1);
				if ($ng_flg !== "") {
					if ($ng_flg == "0") {
						$student_no = mb_substr($data[$i], 103, 8);
						$student_nos[] = $student_no;
						$payment_amount = (int)mb_substr($data[$i], 80, 10);
						if (!empty($school_buildings[$student_no])) {
							$school_building_id = $school_buildings[$student_no];
							$payment_data[] = [
								'student_id' => $student_no,
								'sale_month' => $charge_progress->sales_month,
								'school_building_id' => $school_building_id,
								'payment_date' => $charge_progress->withdrawal_nanto_date,
								'payment_amount' => $payment_amount,
								'pay_method' => 3,
								'summary' => "",
								'scrubed_month' => date("Y-m"),
								'creator' => Auth::user()->id,
								'updater' => Auth::user()->id,
								'created_at' => date("Y-m-d H:i:s"),
								'updated_at' => date("Y-m-d H:i:s")
							];
						}
					}
				}
			}
		}
		$ok_cnt = is_countable($student_nos) ? count($student_nos) : 0;
		if ($ok_cnt > 0) {
			$sales_details = SalesDetail::whereIn('student_no', $student_nos)->whereNull('scrubed_month')->where('sale_month', '<=', $charge_progress->sales_month);
			$sales_data = [
				'charged_month' => date("Y-m"),
				'scrubed_month' => date("Y-m"),
				'updater' => Auth::user()->id
			];
			$sales_details->update($sales_data);
			$charges = Charge::whereIn('student_no', $student_nos)->where('charge_month', $charge_progress->sales_month);
			$charge_data = ['withdrawal_confirmed' => '1'];
			$charges->update($charge_data);

			Payment::insert($payment_data);
		}
		$charge_progress_update = ChargeProgress::where('id', $charge_progress->id);
		$update_data = [
			'withdrawal_import_nanto_date' => date("Y-m-d"),
			'updater' => Auth::user()->id
		];
		$charge_progress_update->update($update_data);

		return redirect("/shinzemi/home")->with("flash_message", "引落結果が反映されました。");
	}
	public function import_risona(Request $request)
	{
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();
		// $charge_progress = ChargeProgress::where('id', '10')->first();
		$school_buildings = Student::where('payment_methods', 2)->get()->pluck('school_building_id', 'student_no');

		Storage::putFileAs('/upFiles', $request->file('risona_file'), 'risona_file.txt');
		$sample_data = Storage::get('/upFiles/risona_file.txt');
		$sample_data = mb_convert_encoding($sample_data, "utf-8", "sjis-win");
		$data = explode("\r\n", $sample_data);
		$data_cnt = is_countable($data) ? count($data) : 0;
		$payment_data = array();
		$student_nos = array();

		for ($i = 0; $i < $data_cnt; $i++) {
			if ($i == 0) {
				// １行目
			} elseif ($i == ($data_cnt - 1)) {
			} elseif ($i == ($data_cnt - 2)) {
			} elseif ($i == ($data_cnt - 3)) {
			} else {
				$ng_flg = mb_substr($data[$i], 111, 1);
				if ($ng_flg !== "") {
					if ($ng_flg == "0") {
						$student_no = mb_substr($data[$i], 103, 8);
						$student_nos[] = $student_no;
						$payment_amount = (int)mb_substr($data[$i], 80, 10);
						if (!empty($school_buildings[$student_no])) {
							$school_building_id = $school_buildings[$student_no];
							$payment_data[] = [
								'student_id' => $student_no,
								'sale_month' => $charge_progress->sales_month,
								'school_building_id' => $school_building_id,
								'payment_date' => $charge_progress->withdrawal_nanto_date,
								'payment_amount' => $payment_amount,
								'pay_method' => 3,
								'summary' => "",
								'scrubed_month' => date("Y-m"),
								'creator' => Auth::user()->id,
								'updater' => Auth::user()->id,
								'created_at' => date("Y-m-d H:i:s"),
								'updated_at' => date("Y-m-d H:i:s")
							];
						}
					}
				}
			}
		}
		$ok_cnt = is_countable($student_nos) ? count($student_nos) : 0;
		if ($ok_cnt > 0) {
			$sales_details = SalesDetail::whereIn('student_no', $student_nos)->whereNull('scrubed_month')->where('sale_month', '<=', $charge_progress->sales_month);
			$sales_data = [
				'charged_month' => date("Y-m"),
				'scrubed_month' => date("Y-m"),
				'updater' => Auth::user()->id
			];
			$sales_details->update($sales_data);
			$charges = Charge::whereIn('student_no', $student_nos)->where('charge_month', $charge_progress->sales_month);
			$charge_data = ['withdrawal_confirmed' => '1'];
			$charges->update($charge_data);

			Payment::insert($payment_data);
		}
		$charge_progress_update = ChargeProgress::where('id', $charge_progress->id);
		$update_data = [
			'withdrawal_import_risona_date' => date("Y-m-d"),
			'updater' => Auth::user()->id
		];
		$charge_progress_update->update($update_data);

		return redirect("/shinzemi/home")->with("flash_message", "データが登録されました。");
	}
	public function charge_confirm_lift()
	{
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();

		$data = ["charge_confirm_flg" => "0"];
		$charge_progress->update($data);
	}
	public function charge_processing()
	{
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();

		$data = ["monthly_processing_date" => date('Y-m-d')];
		$charge_progress->update($data);
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
