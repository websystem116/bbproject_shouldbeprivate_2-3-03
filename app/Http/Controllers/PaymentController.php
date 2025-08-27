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
use App\ChargeProgress;
use App\Payment;

//=======================================================================
class PaymentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request)
	{
		$requestData = $request->all();
		//学校のセレクトリスト
		$schools_select_list = School::all()->mapWithKeys(function ($item) {
			return [$item->id => "{$item->id}　{$item->name}"];
		});
		//校舎のセレクトリスト
		$schooolbuildings_select_list = SchoolBuilding::all()->mapWithKeys(function ($item) {
			return [$item->id => "{$item->number}　{$item->name}"];
		});
		//商品のセレクトリスト
		$products_select_list = Product::all()->mapWithKeys(function ($item) {
			return [$item->id => "{$item->number}　{$item->name}"];
		});
		//割引のセレクトリスト
		$discounts_select_list = Discount::all()->mapWithKeys(function ($item) {
			return [$item->id => "{$item->id}　{$item->name}"];
		});

		// 学生を検索するクエリを作成
		$studentsQuery = Student::query();

		// 検索条件に応じてクエリを追加
		if ($request->has('search')) {

			// バリデーション
			//入塾日
			if (!empty($requestData['juku_start_date']) && empty($requestData['juku_end_date'])) {
				return redirect()
					->back()
					->with("message", "※入塾日の終了範囲を指定してください")
					->withInput();
			}
			if (!empty($requestData['juku_end_date']) && empty($requestData['juku_start_date'])) {
				return redirect()
					->back()
					->with("message", "※入塾日の開始範囲を指定してください")
					->withInput();
			}
			//卒塾日
			if (!empty($requestData['juku_graduation_start_date']) && empty($requestData['juku_graduation_end_date'])) {
				return redirect()
					->back()
					->with("message", "※卒塾日の終了範囲を指定してください")
					->withInput();
			}
			if (!empty($requestData['juku_graduation_end_date']) && empty($requestData['juku_graduation_start_date'])) {
				return redirect()
					->back()
					->with("message", "※卒塾日の開始範囲を指定してください")
					->withInput();
			}
			//復塾日
			if (!empty($requestData['juku_return_start_date']) && empty($requestData['juku_return_end_date'])) {
				return redirect()
					->back()
					->with("message", "※復塾日の終了範囲を指定してください")
					->withInput();
			}
			if (!empty($requestData['juku_return_end_date']) && empty($requestData['juku_return_start_date'])) {
				return redirect()
					->back()
					->with("message", "※復塾日の開始範囲を指定してください")
					->withInput();
			}
			//退塾日
			if (!empty($requestData['juku_withdrawal_start_date']) && empty($requestData['juku_withdrawal_end_date'])) {
				return redirect()
					->back()
					->with("message", "※退塾日の終了範囲を指定してください")
					->withInput();
			}
			if (!empty($requestData['juku_withdrawal_end_date']) && empty($requestData['juku_withdrawal_start_date'])) {
				return redirect()
					->back()
					->with("message", "※退塾日の開始範囲を指定してください")
					->withInput();
			}
			//休塾日
			if (!empty($requestData['juku_rest_start_date']) && empty($requestData['juku_rest_end_date'])) {
				return redirect()
					->back()
					->with("message", "※休塾日の終了範囲を指定してください")
					->withInput();
			}
			if (!empty($requestData['juku_rest_end_date']) && empty($requestData['juku_rest_start_date'])) {
				return redirect()
					->back()
					->with("message", "※休塾日の開始範囲を指定してください")
					->withInput();
			}

			// 管理No id_startとid_endの間の学生を検索する
			$studentsQuery->when(
				isset($requestData['id_start']) || isset($requestData['id_end']),
				function ($query) use ($requestData) {

					if ($requestData['id_start'] && $requestData['id_end']) {
						return $query->whereBetween('students.id', [$requestData['id_start'], $requestData['id_end']]);
					}

					if ($requestData['id_start']) {
						return $query->where('students.id', $requestData['id_start']);
					}

					if ($requestData['id_end']) {
						return $query->where('students.id', $requestData['id_end']);
					}
				}
			);

			// 生徒No　no_startとno_endの間の学生を検索する
			$studentsQuery->when(
				isset($requestData['no_start']) || isset($requestData['no_end']),
				function ($query) use ($requestData) {

					if ($requestData['no_start'] && $requestData['no_end']) {
						return $query->whereBetween(
							'students.student_no',
							[$requestData['no_start'], $requestData['no_end']]
						);
					}

					if ($requestData['no_start']) {
						return $query->where('students.student_no', $requestData['no_start']);
					}

					if ($requestData['no_end']) {
						return $query->where('students.student_no', $requestData['no_end']);
					}
				}
			);

			// 生徒氏名（姓）
			$studentsQuery->when(isset($requestData['surname']), function ($query, $last_name) use ($requestData) {
				return $query->where('surname', 'like', '%' . $requestData['surname'] . '%');
			});

			// 生徒氏名（名）
			$studentsQuery->when(isset($requestData['name']), function ($query, $first_name) use ($requestData) {
				return $query->where('name', 'like', '%' . $requestData['name'] . '%');
			});

			// 生徒氏名（姓）カナsurname_kana
			$studentsQuery->when(isset($requestData['surname_kana']), function ($query, $last_name_kana) use ($requestData) {
				return $query->where('surname_kana', 'like', '%' . $requestData['surname_kana'] . '%');
			});

			// 生徒氏名（名）カナname_kana
			$studentsQuery->when(isset($requestData['name_kana']), function ($query, $first_name_kana) use ($requestData) {
				return $query->where('name_kana', 'like', '%' . $requestData['name_kana'] . '%');
			});

			// 電話番号
			$studentsQuery->when(isset($requestData['phone']), function ($query, $phone) use ($requestData) {
				return $query->where('phone1', 'like', '%' . $requestData['phone'] . '%')
					->orWhere('phone2', 'like', '%' . $requestData['phone'] . '%');
			});

			// 生徒の学校
			$studentsQuery->when(isset($requestData['school_id']), function ($query, $school) use ($requestData) {
				return $query->where('school_id', $requestData['school_id']);
			});

			// 兄弟フラグ
			$studentsQuery->when(isset($requestData['brothers_flg']), function ($query) use ($requestData) {
				return $query->where('brothers_flg', $requestData['brothers_flg']);
			});

			// ひとり親家庭フラグ
			$studentsQuery->when(isset($requestData['fatherless_flg']), function ($query) use ($requestData) {
				return $query->where('fatherless_flg', $requestData['fatherless_flg']);
			});

			// rest_flg
			$studentsQuery->when(isset($requestData['rest_flg']), function ($query) use ($requestData) {
				return $query->WhereNotNull('juku_rest_date');
			});

			// graduation_flg whereNotNull('juku_graduation_date')
			$studentsQuery->when(isset($requestData['graduation_flg']), function ($query) use ($requestData) {
				return $query->WhereNotNull('juku_graduation_date');
			});

			// withdrawal_flg whereNotNull('juku_withdrawal_date')
			$studentsQuery->when(isset($requestData['withdrawal_flg']), function ($query) use ($requestData) {
				return $query->WhereNotNull('juku_withdrawal_date');
			});

			// temporary_flg
			$studentsQuery->when(isset($requestData['temporary_flg']), function ($query) use ($requestData) {
				return $query->where('temporary_flg', $requestData['temporary_flg']);
			});

			// 学年 grade_start grade_end
			$studentsQuery->when(
				isset($requestData['grade_start']) || isset($requestData['grade_end']),
				function ($query) use ($requestData) {

					if (isset($requestData['grade_start']) && isset($requestData['grade_end'])) {
						if (intval($requestData['grade_start']) && intval($requestData['grade_end'])) {
							return $query->whereBetween('grade', [intval($requestData['grade_start']), intval($requestData['grade_end'])]);
						}
					}

					if (isset($requestData['grade_start'])) {
						if (intval($requestData['grade_start'])) {
							return $query->where('grade', intval($requestData['grade_start']));
						}
					}

					if (isset($requestData['grade_end'])) {
						if (intval($requestData['grade_end'])) {
							return $query->where('grade', intval($requestData['grade_end']));
						}
					}
				}
			);

			// 校舎
			$studentsQuery->when(isset($requestData['school_building_id']), function ($query, $school_building) use ($requestData) {
				return $query->where('school_building_id', $requestData['school_building_id']);
			});

			// 受講情報
			$studentsQuery->with('juko_infos')
				->when(isset($requestData['product_select']), function ($query, $product) use ($requestData) {
					return $query->whereHas('juko_infos', function ($query) use ($requestData) {
						return $query->where('product_id', $requestData['product_select']);
					});
				});

			// 割引
			$studentsQuery->when(isset($requestData['discount_select']), function ($query, $discount) use ($requestData) {
				return $query->where('discount_id', $requestData['discount_select']);
			});

			// 進学先
			$studentsQuery->when(isset($requestData['suggested_school']), function ($query, $suggested_school) use ($requestData) {
				return $query->where('choice_private_school_name1', 'like', '%' . $requestData['suggested_school'] . '%')
					->orWhere('choice_private_school_name2', 'like', '%' . $requestData['suggested_school'] . '%')
					->orWhere('choice_private_school_name3', 'like', '%' . $requestData['suggested_school'] . '%')
					->orWhere('choice_private_school_name4', 'like', '%' . $requestData['suggested_school'] . '%')
					->orWhere('choice_private_school_name5', 'like', '%' . $requestData['suggested_school'] . '%');
			});


			//入塾日　juku_start_date　juku_end_date
			$studentsQuery->when(
				isset($requestData['juku_start_date']) && isset($requestData['juku_end_date']),
				function ($query) use ($requestData) {
					return $query->whereBetween('juku_start_date', [$requestData['juku_start_date'], $requestData['juku_end_date']]);
				}
			);

			//卒塾日 juku_graduation_start_date juku_graduation_end_date
			$studentsQuery->when(
				isset($requestData['juku_graduation_start_date']) && isset($requestData['juku_graduation_end_date']),
				function ($query) use ($requestData) {
					return $query->whereBetween('juku_graduation_date', [$requestData['juku_graduation_start_date'], $requestData['juku_graduation_end_date']])
						->whereNotNull('juku_graduation_date');
				}
			);
			if (isset($requestData['juku_graduation_start_date']) && isset($requestData['juku_graduation_end_date'])) {
				$requestData['graduation_flg'] = 1;
			}

			//復塾日 juku_return_start_date juku_return_end_date
			$studentsQuery->when(
				isset($requestData['juku_return_start_date']) && isset($requestData['juku_return_end_date']),
				function ($query) use ($requestData) {
					return $query->whereBetween('juku_return_date', [$requestData['juku_return_start_date'], $requestData['juku_return_end_date']]);
				}
			);

			//退塾日 juku_withdrawal_start_date juku_withdrawal_end_date
			$studentsQuery->when(
				isset($requestData['juku_withdrawal_start_date']) && isset($requestData['juku_withdrawal_end_date']),
				function ($query) use ($requestData) {
					return $query->whereBetween('juku_withdrawal_date', [$requestData['juku_withdrawal_start_date'], $requestData['juku_withdrawal_end_date']]);
				}
			);
			if (isset($requestData['juku_withdrawal_start_date']) && isset($requestData['juku_withdrawal_end_date'])) {
				$requestData['withdrawal_flg'] = 1;
			}

			//休塾日 juku_rest_start_date juku_rest_end_date
			$studentsQuery->when(
				isset($requestData['juku_rest_start_date']) && isset($requestData['juku_rest_end_date']),
				function ($query) use ($requestData) {
					return $query->whereBetween('juku_rest_date', [$requestData['juku_rest_start_date'], $requestData['juku_rest_end_date']]);
				}
			);
			if (isset($requestData['juku_rest_start_date']) && isset($requestData['juku_rest_end_date'])) {
				$requestData['rest_flg'] = 1;
			}


			$studentsQuery->when(
				empty($requestData['withdrawal_flg']) && empty($requestData['graduation_flg']) && empty($requestData['rest_flg']),
				function ($query) {
					return $query->whereNull('juku_rest_date')
						->whereNull('juku_graduation_date')
						->whereNull('juku_withdrawal_date');
				}
			);
		} else {
			$studentsQuery->whereNull('juku_rest_date')
				->whereNull('juku_graduation_date')
				->whereNull('juku_withdrawal_date');
		}


		$perPage = 25;
		$students = $studentsQuery->paginate($perPage);


		return view("payment.index", compact("requestData", "students", "schooolbuildings_select_list", "products_select_list", "discounts_select_list", "schools_select_list"));
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
		$this->validate($request, []);
		$requestData = $request->all();
		dd($requestData['payment_date']);

		DailySalary::create($requestData);

		return redirect("/shinzemi/salary")->with("flash_message", "データが登録されました。");
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
		$students = Student::where('id', $id)->first();
		$charge_progress = ChargeProgress::latest('monthly_processing_date')
			->first();
		$sales_detail_fails = SalesDetail::where('student_no', $students->student_no)->whereNull('scrubed_month')->where('sale_month', "<=", date('Y-m'))->get();
		$fails_payment = 0;
		foreach ($sales_detail_fails as $sales_detail_fail) {
			if (empty($fails_payment)) {
				$fails_payment = $sales_detail_fail->subtotal;
			} else {
				$fails_payment += $sales_detail_fail->subtotal;
			}
			if (!empty($fails_payment_month)) {
				if ($fails_payment_month > $sales_detail_fail->sale_month) {
					$fails_payment_month = $sales_detail_fail->sale_month;
				}
			} else {
				$fails_payment_month = $sales_detail_fail->sale_month;
			}
		}
		unset($sales_detail_fails);
		$scrubed_payment = 0;
		if (!empty($fails_payment_month)) {
			$payments = Payment::where('student_id', $students->student_no)->where('sale_month', '>=', $fails_payment_month)->where('sale_month', "<=", date('Y-m'))->get();

			foreach ($payments as $payment) {
				if (!empty($fails_payment_month)) {
					if ($payment->sale_month >= $fails_payment_month) {
						if (empty($scrubed_payment)) {
							$scrubed_payment = $payment->payment_amount;
						} else {
							$scrubed_payment += $payment->payment_amount;
						}
					}
				}
			}
		}
		$accrued_amount = number_format($fails_payment - $scrubed_payment);
		// テーブル内の一番後に登録されたsale_monthを取得
		$latest_sales_month = $charge_progress->sales_month;
		// 上で取得した値はyyyy-mmの形式。これに1ヵ月を足した値を取得
		$latest_sales_month_plus_one_month = date('Y-m', strtotime($latest_sales_month . '+1 month'));

		$payments = Payment::where('student_id', $students->student_no)
			->where('payment_date', '>', $charge_progress->monthly_processing_date)
			->get();

		$payments_last = Payment::where('student_id', $students->student_no)
			->where('payment_date', '>', $charge_progress->monthly_processing_date)
			->latest('id')
			->first();



		return view("payment.edit", compact('students', 'payments', 'payments_last', 'latest_sales_month_plus_one_month', 'accrued_amount'));
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
		$requestData = $request->all();
		$students = Student::where('id', $id)->first();
		$charge_progress = ChargeProgress::latest('monthly_processing_date')->first();
		$payments = Payment::where('student_id', $students->student_no)->where('payment_date', '>', $charge_progress->monthly_processing_date)->delete();

		$payment_cnt = is_countable($requestData['payment_date']) ? count($requestData['payment_date']) : 0;
		for ($i = 0; $i < $payment_cnt; $i++) {
			$data = [
				'student_id' => $students->student_no,
				'payment_date' => $requestData['payment_date'][$i],
				'sale_month' => $requestData['sale_month'][$i],
				'school_building_id' => $students->school_building_id,
				'payment_amount' => $requestData['price'][$i],
				'pay_method' => $requestData['division'][$i],
				'summary' => $requestData['remarks'][$i],
			];
			Payment::create($data);
		}
		return redirect("/shinzemi/payment")->with("flash_message", "データが更新されました。");
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

		return redirect("/shinzemi/payment")->with("flash_message", "データが削除されました。");
	}
}
    //=======================================================================