<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\User;
use App\Salary;
use App\DailySalary;
use App\SchoolBuilding;
use App\JobDescription;
use App\OtherJobDescription;
use App\TransportationExpense;
use App\DailyOtherSalary;
use App\JobDescriptionWage;
use App\SalaryProgress;
use App\SalaryDetail;
use App\OtherJobDescriptionWage;

//=======================================================================
class SalariesController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request)
{
    $queryParameters = $request->query();
    $queryString = http_build_query($queryParameters);

    $salary_search['conditions_flg'] = $request->get("conditions_flg");
    $salary_search['work_month'] = $request->get("work_month");
    $salary_search['work_date'] = $request->get("work_date");
    $salary_search['school_building'] = $request->get("school_building");
    $salary_search['school_building2'] = $request->get("school_building2");

    $perPage = 25;
    $job_descriptions = JobDescription::all();
    $school_buildings = SchoolBuilding::all()->pluck('name', 'id');
    $list = [];
    $work_month = "";
    $work_date = "";
    $min_work_date = DailySalary::min('work_date');
    $max_work_date = DailySalary::max('work_date');

    $daily_salary_count = 0;
    $daily_salary_info = ['firstItem' => 0, 'lastItem' => 0, 'total' => 0];

    if ($salary_search['conditions_flg'] != "") {
        if ($salary_search['conditions_flg'] == 1) {
            $daily_salary = DailySalary::where("work_month", "LIKE", $salary_search['work_month'])
                ->whereIn('user_id', function ($query) use ($salary_search) {
                    $query->from('users')
                        ->select('users.id')
                        ->when(!empty($salary_search['school_building']), function ($query) use ($salary_search) {
                            return $query->where('users.school_building', $salary_search['school_building']);
                        });
                })->get();

            $work_month = $salary_search['work_month'];
            $work_date = date('Y-m-d', strtotime($salary_search['work_month']));
        } else {
            $daily_salary = DailySalary::where("work_date", "LIKE", $salary_search['work_date'])
                ->when(!empty($salary_search['school_building2']), function ($query) use ($salary_search) {
                    return $query->where('school_building_id', $salary_search['school_building2']);
                })->get();
            $work_month = date('Y-m', strtotime($salary_search['work_date']));
            $work_date = date('Y-m-d', strtotime($salary_search['work_date']));
        }

        foreach ($daily_salary as $value) {
            if ($salary_search['conditions_flg'] == 2) {
                $list[$value->user_id]['transportation_expenses'] = TransportationExpense::where('work_date', $salary_search['work_date'])
                    ->where('user_id', $value->user_id)->sum('fare');
            }

            $list[$value->user_id]['user_id'] = $value->user->user_id;
            $list[$value->user_id]['id'] = $value->user_id;
            $list[$value->user_id]['name'] = $value->user->full_name;
            $list[$value->user_id]['work_date'] = $value->work_date;

            if ($salary_search['conditions_flg'] == 1) {
                $list[$value->user_id]['superior_approvals'][] = $value->superior_approval;
            } else {
                $list[$value->user_id]['superior_approval'] = $value->superior_approval == 1 ? '済' : '未';
            }

            foreach ($job_descriptions as $job_description) {
                if ($job_description->id == $value->job_description_id) {
                    if (isset($list[$value->user_id][$job_description->id])) {
                        $list[$value->user_id][$job_description->id] += $value->working_time;
                    } else {
                        $list[$value->user_id][$job_description->id] = $value->working_time;
                    }
                }
            }
        }

        foreach ($list as $key => $value) {
            // Still using Salary for these amounts (if that's where they're stored)
            $salaries = Salary::where('tightening_date', $work_month)->where('user_id', $key)->first();
            $list[$key]['other_payment_amount'] = $salaries->other_payment_amount ?? 0;
            $list[$key]['other_deduction_amount'] = $salaries->other_deduction_amount ?? 0;
            $list[$key]['year_end_adjustment'] = $salaries->year_end_adjustment ?? 0;
            if ($salary_search['conditions_flg'] == 1) {
                $list[$key]['transportation_expenses'] = $salaries->transportation_expenses ?? 0;
                if (in_array('0', $list[$key]['superior_approvals'])) {
                    $list[$key]['superior_approval'] = '未';
                } else {
                    $list[$key]['superior_approval'] = '済';
                }
            }

            $list[$key]['monthly_completion'] = ($salaries && $salaries->monthly_completion == 1) ? '済' : '未';
            $list[$key]['monthly_approval'] = ($salaries && $salaries->monthly_approval == 1) ? '済' : '未';
            $list[$key]['salary_approval'] = ($salaries && $salaries->salary_approval == 1) ? '済' : '未';
            $list[$key]['monthly_tightening'] = ($salaries && $salaries->monthly_tightening == 1) ? '済' : '未';

            // ✅ Get salary_confirmation from DailySalary
            $daily = DailySalary::where('work_month', $work_month)
                ->where('user_id', $key)
                ->first();
            $list[$key]['salary_confirmation'] = ($daily && $daily->salary_confirmation) ? '済' : '未';
        }

        $daily_salary_count = count($list);
        if ($daily_salary_count > 0) {
            $daily_salary_info['firstItem'] = 1;
            $daily_salary_info['lastItem'] = $daily_salary_count;
            $daily_salary_info['total'] = $daily_salary_count;
        }
    }

    return view("salary.index", compact("list", "job_descriptions", "school_buildings", "salary_search", 'work_month', 'work_date', 'queryString', 'daily_salary_count', 'daily_salary_info'));
}


	public function monthly_salary_index()
	{

		return view("salary.monthly_salary_index");
	}




	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{

		return view("salary.create");
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
			"code" => "nullable|max:4", //string('code',4)->nullable()
			"name" => "required|max:15", //string('name',15)->nullable()
			"name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
		]);
		$requestData = $request->all();

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
		return view("salary.show", compact("daily_salary"));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 *
	 * @return \Illuminate\View\View
	 */
	public function edit($id, $date, Request $request)
	{
		$url = url()->previous();
		session(["url" => $url]);

		$working_date = $request->get('working_date');
		if ($working_date == null) {
			$working_date = $date;
			if ($date == null) {
				$working_date = date('Y-m-d');
			}
		}
		$users = User::findOrFail($id);
		$daily_salaries = DailySalary::where("user_id", $id)->where("work_date", $working_date)->get();
		$transportation_expenses = TransportationExpense::where("user_id", $id)->where("work_date", $working_date)->get();
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		// $job_descriptions = JobDescription::all()->pluck('name', 'id');
		//プルダウンid=1～4のものだけ非表示
		$job_descriptions = JobDescription::where('id', '>', 4)->get()->pluck('name', 'id');
		$other_job_descriptions = OtherJobDescription::all()->pluck('name', 'id');
		$daily_other_salaries = DailyOtherSalary::where("user_id", $id)->where("work_date", $working_date)->get();
		$salary_progress = SalaryProgress::where('id', 1)->first();
		$processing_month = $salary_progress->new_monthly_processing_month;
		$readonly = false;
		$display_none = false;
		$disable = false;

		if ($processing_month >= date('Y-m', strtotime($working_date))) {
			$readonly = 'readonly';
			$disable = 'disabled';
			$display_none = true;
		}

		$daily_salaries_cnt = ($daily_salaries->count() == 0) ? 1 : $daily_salaries->count();
		$transportation_expenses_cnt = ($transportation_expenses->count() == 0) ? 1 : $transportation_expenses->count();
		$daily_other_salaries_cnt = ($daily_other_salaries->count() == 0) ? 1 : $daily_other_salaries->count();

		return view("salary.edit", compact("users", "working_date", "daily_salaries", "school_buildings", "transportation_expenses", "job_descriptions", "other_job_descriptions", "daily_other_salaries", "date", "readonly", "display_none", "disable"));
	}
	public function approval_edit($id, $date, Request $request)
	{

		$url = url()->previous();
		if (strpos($url, 'edit') === false) {
			session(["url" => $url]);
			$before_url = $url;
		} else {
			$before_url = session("url");
		}

		$working_date = $request->get('working_date');
		if ($working_date == null) {
			$working_date = $date;
			if ($date == null) {
				$working_date = date('Y-m-d');
			}
		}
		$users = User::findOrFail($id);
		$salaries = Salary::where("user_id", $id)->where("tightening_date", date("Y-m", strtotime($working_date)))->first();
		$daily_salaries = DailySalary::where("user_id", $id)->where("work_date", $working_date)->get();
		$transportation_expenses = TransportationExpense::where("user_id", $id)->where("work_date", $working_date)->get();
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		// $job_descriptions = JobDescription::all()->pluck('name', 'id');
		//プルダウンid=1～4のものだけ非表示
		$job_descriptions = JobDescription::where('id', '>', 4)->get()->pluck('name', 'id');
		$other_job_descriptions = OtherJobDescription::all()->pluck('name', 'id');
		$daily_other_salaries = DailyOtherSalary::where("user_id", $id)->where("work_date", $working_date)->get();
		$salary_progress = SalaryProgress::where('id', 1)->first();
		$processing_month = $salary_progress->new_monthly_processing_month;
		$readonly = false;
		$display_none = false;
		$disable = '';

		if ($processing_month >= date('Y-m', strtotime($working_date))) {
			$readonly = 'readonly';
			$disable = 'disabled';
			$display_none = true;
		}
		$daily_salaries_cnt = ($daily_salaries->count() == 0) ? 1 : $daily_salaries->count();
		$transportation_expenses_cnt = ($transportation_expenses->count() == 0) ? 1 : $transportation_expenses->count();
		$daily_other_salaries_cnt = ($daily_other_salaries->count() == 0) ? 1 : $daily_other_salaries->count();



		return view("salary.approval_edit", compact("users", "working_date", "daily_salaries", "school_buildings", "transportation_expenses", "job_descriptions", "other_job_descriptions", "daily_other_salaries", "date", "readonly", "display_none", "disable", "salaries", "before_url"));
	}
	public function deduction($id, $month)
	{
		$url = url()->previous();
		session(["url" => $url]);

		$users = User::findOrFail($id);
		$salary_progress = SalaryProgress::where('id', 1)->first();
		$processing_month = $salary_progress->new_monthly_processing_month;
		$readonly = false;
		$display_none = false;
		$disable = '';

		if ($processing_month >= $month) {
			$readonly = 'readonly';
			$disable = 'disabled';
			$display_none = true;
		}

		$salaries = Salary::where("user_id", $id)->where("tightening_date", $month)->first();
		return view("salary.deduction", compact("users", "month", "salaries", "readonly", "display_none", "disable"));
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
		// $this->validate($request, [
		// 	"code" => "nullable|max:4", //string('code',4)->nullable()
		// 	"name" => "required|max:15", //string('name',15)->nullable()
		// 	"name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
		// ]);
		$requestData = $request->all();
		$users = User::findOrFail($id);
		$job_description_wages = JobDescriptionWage::where('user_id', $id)->get();
		foreach ($job_description_wages as $job_description_wage) {
			$wages[$job_description_wage->job_description_id] = $job_description_wage->wage;
		}
		DailySalary::where("user_id", $id)->where("work_date", $requestData['working_date'])->delete();
		$job_description_cnt = is_countable($requestData["job_description"]) ? count($requestData["job_description"]) : 0;
		for ($i = 0; $i < $job_description_cnt; $i++) {
			if ($requestData['job_description'][$i]) {
				if (empty($payment_amount[$requestData['job_description'][$i]])) {
					$payment_amount[$requestData['job_description'][$i]] = $wages[$requestData['job_description'][$i]] * $requestData['working_time'][$i];
				}
				$daily_salaries[] = [
					"user_id" => $id,
					"work_month" => date('Y-m', strtotime($requestData['working_date'])),
					"work_date" => $requestData['working_date'],
					"school_building_id" => $requestData['school_building'][$i],
					"job_description_id" => $requestData['job_description'][$i],
					"working_time" => $requestData['working_time'][$i],
					"remarks" =>  $requestData['remarks'][$i],
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id

				];
			}
		}
		if (isset($daily_salaries)) {

			DailySalary::insert($daily_salaries);
		}
		$tranceportation_school_building_cnt = is_countable($requestData["tranceportation_school_building"]) ? count($requestData["tranceportation_school_building"]) : 0;

		TransportationExpense::where("user_id", $id)->where("work_date", $requestData['working_date'])->delete();
		for ($i = 0; $i < $tranceportation_school_building_cnt; $i++) {

			if ($requestData['tranceportation_school_building'][$i]) {
				$tranceportation_round_trip_flg = 0;

				if (!empty($requestData['tranceportation_round_trip_flg'])) {
					if (in_array($i, $requestData['tranceportation_round_trip_flg'])) {
						$tranceportation_round_trip_flg = 1;
					}
				}
				$tranceportations[] = [
					"user_id" => $id,
					"work_month" => date('Y-m', strtotime($requestData['working_date'])),
					"work_date" => $requestData['working_date'],
					"school_building" => $requestData['tranceportation_school_building'][$i],
					"route" => $requestData['tranceportation_route'][$i],
					"boarding_station" => $requestData['tranceportation_boarding_station'][$i],
					"get_off_station" => $requestData['tranceportation_get_off_station'][$i],
					"unit_price" => $requestData['tranceportation_unit_price'][$i],
					"round_trip_flg" => $tranceportation_round_trip_flg,
					"fare" => $requestData['tranceportation_fare'][$i],
					"remarks" =>  $requestData['tranceportation_remarks'][$i],
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id

				];
			}
		}
		if (isset($tranceportations)) {

			TransportationExpense::insert($tranceportations);
		}
		DailyOtherSalary::where("user_id", $id)->where("work_date", $requestData['working_date'])->delete();
		$other_job_description_cnt = is_countable($requestData["other_job_description"]) ? count($requestData["other_job_description"]) : 0;
		for ($i = 0; $i < $other_job_description_cnt; $i++) {
			if ($requestData['other_job_description'][$i]) {
				$other_performances[] = [
					"user_id" => $id,
					"work_month" => date('Y-m', strtotime($requestData['working_date'])),
					"work_date" => $requestData['working_date'],
					"school_building" => $requestData['other_school_building'][$i],
					"job_description" => $requestData['other_job_description'][$i],
					"remarks" =>  $requestData['other_remarks'][$i],
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id

				];
			}
		}
		if (isset($other_performances)) {
			DailyOtherSalary::insert($other_performances);
		}
		$monthly_daily_salaries = DailySalary::where("user_id", $id)->where("work_month", date('Y-m', strtotime($requestData['working_date'])))->get();
		$monthly_daily_other_salaries = DailyOtherSalary::where("user_id", $id)->where('work_month', date('Y-m', strtotime($requestData['working_date'])))->orderBy('user_id', 'asc')->get();
		$salary_exist = Salary::where("user_id", $id)->where("tightening_date", date('Y-m', strtotime($requestData['working_date'])))->exists();
		if (!$salary_exist) {
			$salary_data = [
				"user_id" => $id,
				"tightening_date" => date('Y-m', strtotime($requestData['working_date']))
			];
			Salary::create([
				"user_id" => $id,
				"tightening_date" => date('Y-m', strtotime($requestData['working_date']))
			]);
		}
		$salaries = Salary::where("user_id", $id)->where('tightening_date', date('Y-m', strtotime($requestData['working_date'])))->first();
		$job_description_wages = JobDescriptionWage::where("user_id", $id)->get();
		$other_job_description_wages = OtherJobDescriptionWage::where("user_id", $id)->get();
		foreach ($job_description_wages as $job_description_wage) {
			$wage = empty($job_description_wage->wage) ? 0 : $job_description_wage->wage;
			$wages[$job_description_wage->job_description_id] = $wage;
		}
		foreach ($other_job_description_wages as $other_job_description_wages) {
			$other_wage = empty($other_job_description_wages->wage) ? 0 : $other_job_description_wages->wage;
			$other_wages[$other_job_description_wages->other_job_description_id] = $other_wage;
		}
		foreach ($monthly_daily_salaries as $monthly_daily_salary) {
			$wage = empty($wages[$monthly_daily_salary->job_description_id]) ? 0 : $wages[$monthly_daily_salary->job_description_id];
			if (empty($salary_details_data[1][$monthly_daily_salary->job_description_id]['working_time'])) {
				$salary_details_data[1][$monthly_daily_salary->job_description_id]['working_time'] = $monthly_daily_salary->working_time;
			} else {
				$salary_details_data[1][$monthly_daily_salary->job_description_id]['working_time'] += $monthly_daily_salary->working_time;
			}
		}
		foreach ($salary_details_data[1] as $job_description_id => $salary_details_data_detail) {
			$working_time = ceil(($salary_details_data_detail['working_time'] * 10) / 60) / 10;
			// if(empty($wages[$user_id][$job_description_id])){
			//     echo $user_id."<br>";
			//     dd($job_description_id);
			// }
			$salary_details_data[1][$job_description_id]['payment_amount'] = $working_time * $wages[$job_description_id];
			if (empty($salary_data['salary'])) {
				$salary_data['salary'] = $working_time * $wages[$job_description_id];
			} else {
				$salary_data['salary'] += $working_time * $wages[$job_description_id];
			}
		}
		$transportation_expenses = TransportationExpense::where("user_id", $id)->where('work_month', date('Y-m', strtotime($requestData['working_date'])))->orderBy('user_id', 'asc')->get();

		foreach ($transportation_expenses as $transportation_expense) {
			$fare = $transportation_expense->unit_price;
			if ($transportation_expense->round_trip_flg == 1) {
				$fare = $fare * 2;
			}
			if (empty($salary_data['transportation_expenses'])) {
				$salary_data['transportation_expenses'] = $fare;
			} else {
				$salary_data['transportation_expenses'] += $fare;
			}
		}
		foreach ($monthly_daily_other_salaries as $monthly_daily_other_salary) {
			$wage = empty($other_wages[$monthly_daily_other_salary->job_description]) ? 0 : $other_wages[$monthly_daily_other_salary->job_description];

			if (empty($salary_details_data[2][$monthly_daily_other_salary->job_description]['payment_amount'])) {
				$salary_details_data[2][$monthly_daily_other_salary->job_description]['payment_amount'] = $wage;
			} else {
				$salary_details_data[2][$monthly_daily_other_salary->job_description]['payment_amount'] += $wage;
			}
			if (empty($salary_data['salary'])) {
				$salary_data['salary'] = $wage;
			} else {
				$salary_data['salary'] += $wage;
			}
		}
		$transportation_expenses = 0;
		$salary_sum = 0;

		if (!empty($salary_data['transportation_expenses'])) {
			$transportation_expenses = $salary_data['transportation_expenses'];
		}
		if (!empty($salary_data['salary'])) {
			$salary_sum = $salary_data['salary'];
		}
		if ($id != 91 && $id != 1190 && $id != 30 && $id != 1200) {
			if ($transportation_expenses > 15000) {
				$transportation_expenses = 15000;
			}
		}
		$salaries->salary = $salary_sum;
		$salaries->transportation_expenses = $transportation_expenses;
		$salaries->save();
		SalaryDetail::where('salary_id', $salaries->id)->delete();

		if (!empty($salary_details_data[1])) {
			foreach ($salary_details_data[1] as $job_description_id => $salary_details_data_detail) {
				$salaries_details_data[] = [
					'salary_id' => $salaries->id,
					'job_description_id' => $job_description_id,
					'payment_amount' => $salary_details_data_detail['payment_amount'],
					'hourly_wage' => $wages[$job_description_id],
					'description_division' => 1,
					'created_at' => date('Y-m-d h:i:s'),
					'updated_at' => date('Y-m-d h:i:s')
				];
			}
		}
		if (!empty($salary_details_data[2])) {
			foreach ($salary_details_data[2] as $job_description_id => $salary_details_data_detail) {
				$salaries_details_data[] = [
					'salary_id' =>  $salaries->id,
					'job_description_id' => $job_description_id,
					'payment_amount' => $salary_details_data_detail['payment_amount'],
					'hourly_wage' => $other_wages[$job_description_id],
					'description_division' => 2,
					'created_at' => date('Y-m-d h:i:s'),
					'updated_at' => date('Y-m-d h:i:s')
				];
			}
		}
		if (!empty($salaries_details_data)) {
			SalaryDetail::insert($salaries_details_data);
		}


		return redirect()->route('salary.edit', ['id' => $id, 'date' => $requestData['working_date']])->with("flash_message", "データが更新されました。");
	}



	public function approval_update(Request $request, $id)
	{
		// $this->validate($request, [
		// 	"code" => "nullable|max:4", //string('code',4)->nullable()
		// 	"name" => "required|max:15", //string('name',15)->nullable()
		// 	"name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
		// ]);
		$delete_flg = false;
		$requestData = $request->all();
		DailySalary::where("user_id", $id)->where("work_date", $requestData['working_date'])->delete();
		$job_description_cnt = is_countable($requestData["job_description"]) ? count($requestData["job_description"]) : 0;

		for ($i = 0; $i < $job_description_cnt; $i++) {
			if ($requestData['job_description'][$i]) {
				$superior_approval = 0;
				if (!empty($requestData['apploval_flg'])) {
					if (in_array($i, $requestData['apploval_flg'])) {
						$superior_approval = 1;
					}
				}
				$daily_salaries[] = [
					"user_id" => $id,
					"work_month" => date('Y-m', strtotime($requestData['working_date'])),
					"work_date" => $requestData['working_date'],
					"school_building_id" => $requestData['school_building'][$i],
					"job_description_id" => $requestData['job_description'][$i],
					"working_time" => $requestData['working_time'][$i],
					"remarks" =>  $requestData['remarks'][$i],
					'superior_approval' => $superior_approval,
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id

				];
			}
		}
		if (isset($daily_salaries)) {
			DailySalary::insert($daily_salaries);
		} else {
			$delete_flg = true;
		}

		TransportationExpense::where("user_id", $id)->where("work_date", $requestData['working_date'])->delete();
		$tranceportation_school_building_cnt = is_countable($requestData["tranceportation_school_building"]) ? count($requestData["tranceportation_school_building"]) : 0;

		for ($i = 0; $i < $tranceportation_school_building_cnt; $i++) {
			if ($requestData['tranceportation_school_building'][$i]) {
				$superior_approval = 0;
				if (!empty($requestData['tranceportation_apploval_flg'])) {

					if (in_array($i, $requestData['tranceportation_apploval_flg'])) {
						$superior_approval = 1;
					}
				}
				if (empty($transportation_expenses)) {
					$transportation_expenses = $requestData['tranceportation_fare'][$i];
				} else {
					$transportation_expenses += $requestData['tranceportation_fare'][$i];
				}
				$tranceportation_round_trip_flg = 0;

				if (!empty($requestData['tranceportation_round_trip_flg'])) {
					if (in_array($i, $requestData['tranceportation_round_trip_flg'])) {
						$tranceportation_round_trip_flg = 1;
					}
				}

				$tranceportations[] = [
					"user_id" => $id,
					"work_month" => date('Y-m', strtotime($requestData['working_date'])),
					"work_date" => $requestData['working_date'],
					'superior_approval' => $superior_approval,

					"school_building" => $requestData['tranceportation_school_building'][$i],
					"route" => $requestData['tranceportation_route'][$i],
					"boarding_station" => $requestData['tranceportation_boarding_station'][$i],
					"get_off_station" => $requestData['tranceportation_get_off_station'][$i],
					"unit_price" => $requestData['tranceportation_unit_price'][$i],
					"round_trip_flg" => $tranceportation_round_trip_flg,
					"fare" => $requestData['tranceportation_fare'][$i],
					"remarks" =>  $requestData['tranceportation_remarks'][$i],
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id
				];
			}
		}
		if (isset($tranceportations)) {
			TransportationExpense::insert($tranceportations);
		}
		DailyOtherSalary::where("user_id", $id)->where("work_date", $requestData['working_date'])->delete();
		$other_job_description_cnt = is_countable($requestData["other_job_description"]) ? count($requestData["other_job_description"]) : 0;
		for ($i = 0; $i < $other_job_description_cnt; $i++) {
			if ($requestData['other_job_description'][$i]) {
				$superior_approval = 0;
				if (!empty($requestData['other_apploval_flg'])) {
					if (in_array($i, $requestData['other_apploval_flg'])) {
						$superior_approval = 1;
					}
				}
				$other_performances[] = [
					"user_id" => $id,
					"work_month" => date('Y-m', strtotime($requestData['working_date'])),
					"work_date" => $requestData['working_date'],
					'superior_approval' => $superior_approval,
					"school_building" => $requestData['other_school_building'][$i],
					"job_description" => $requestData['other_job_description'][$i],
					"remarks" =>  $requestData['other_remarks'][$i],
					"creator" => Auth::user()->id,
					"updater" => Auth::user()->id
				];
			}
		}
		if (isset($other_performances)) {
			DailyOtherSalary::insert($other_performances);
		}
		$monthly_daily_salaries = DailySalary::where("user_id", $id)->where("work_month", date('Y-m', strtotime($requestData['working_date'])))->get();
		$monthly_daily_other_salaries = DailyOtherSalary::where("user_id", $id)->where('work_month', date('Y-m', strtotime($requestData['working_date'])))->orderBy('user_id', 'asc')->get();
		$salary_exist = Salary::where("user_id", $id)->where("tightening_date", date('Y-m', strtotime($requestData['working_date'])))->exists();
		if (!$salary_exist) {
			$salary_data = [
				"user_id" => $id,
				"tightening_date" => date('Y-m', strtotime($requestData['working_date']))
			];
			Salary::create([
				"user_id" => $id,
				"tightening_date" => date('Y-m', strtotime($requestData['working_date']))
			]);
		}
		$salaries = Salary::where("user_id", $id)->where('tightening_date', date('Y-m', strtotime($requestData['working_date'])))->first();
		$job_description_wages = JobDescriptionWage::where("user_id", $id)->get();
		$other_job_description_wages = OtherJobDescriptionWage::where("user_id", $id)->get();
		foreach ($job_description_wages as $job_description_wage) {
			$wage = empty($job_description_wage->wage) ? 0 : $job_description_wage->wage;
			$wages[$job_description_wage->job_description_id] = $wage;
		}
		foreach ($other_job_description_wages as $other_job_description_wages) {
			$other_wage = empty($other_job_description_wages->wage) ? 0 : $other_job_description_wages->wage;
			$other_wages[$other_job_description_wages->other_job_description_id] = $other_wage;
		}
		foreach ($monthly_daily_salaries as $monthly_daily_salary) {
			$wage = empty($wages[$monthly_daily_salary->job_description_id]) ? 0 : $wages[$monthly_daily_salary->job_description_id];
			if (empty($salary_details_data[1][$monthly_daily_salary->job_description_id]['working_time'])) {
				$salary_details_data[1][$monthly_daily_salary->job_description_id]['working_time'] = $monthly_daily_salary->working_time;
			} else {
				$salary_details_data[1][$monthly_daily_salary->job_description_id]['working_time'] += $monthly_daily_salary->working_time;
			}
		}
		foreach ($salary_details_data[1] as $job_description_id => $salary_details_data_detail) {
			$working_time = ceil(($salary_details_data_detail['working_time'] * 10) / 60) / 10;
			// if(empty($wages[$user_id][$job_description_id])){
			//     echo $user_id."<br>";
			//     dd($job_description_id);
			// }
			$salary_details_data[1][$job_description_id]['payment_amount'] = $working_time * $wages[$job_description_id];
			if (empty($salary_data['salary'])) {
				$salary_data['salary'] = $working_time * $wages[$job_description_id];
			} else {
				$salary_data['salary'] += $working_time * $wages[$job_description_id];
			}
		}
		$transportation_expenses = TransportationExpense::where("user_id", $id)->where('work_month', date('Y-m', strtotime($requestData['working_date'])))->orderBy('user_id', 'asc')->get();

		foreach ($transportation_expenses as $transportation_expense) {
			$fare = $transportation_expense->unit_price;
			if ($transportation_expense->round_trip_flg == 1) {
				$fare = $fare * 2;
			}
			if (empty($salary_data['transportation_expenses'])) {
				$salary_data['transportation_expenses'] = $fare;
			} else {
				$salary_data['transportation_expenses'] += $fare;
			}
		}
		foreach ($monthly_daily_other_salaries as $monthly_daily_other_salary) {
			$wage = empty($other_wages[$monthly_daily_other_salary->job_description]) ? 0 : $other_wages[$monthly_daily_other_salary->job_description];

			if (empty($salary_details_data[2][$monthly_daily_other_salary->job_description]['payment_amount'])) {
				$salary_details_data[2][$monthly_daily_other_salary->job_description]['payment_amount'] = $wage;
			} else {
				$salary_details_data[2][$monthly_daily_other_salary->job_description]['payment_amount'] += $wage;
			}
			if (empty($salary_data['salary'])) {
				$salary_data['salary'] = $wage;
			} else {
				$salary_data['salary'] += $wage;
			}
		}
		$transportation_expenses = 0;
		$salary_sum = 0;

		if (!empty($salary_data['transportation_expenses'])) {
			$transportation_expenses = $salary_data['transportation_expenses'];
		}
		if (!empty($salary_data['salary'])) {
			$salary_sum = $salary_data['salary'];
		}
		if ($id != 91 && $id != 1190 && $id != 30 && $id != 1200) {
			if ($transportation_expenses > 15000) {
				$transportation_expenses = 15000;
			}
		}
		$salaries->salary = $salary_sum;
		$salaries->transportation_expenses = $transportation_expenses;
		$salaries->save();
		SalaryDetail::where('salary_id', $salaries->id)->delete();
		if (!empty($salary_details_data[1])) {
			foreach ($salary_details_data[1] as $job_description_id => $salary_details_data_detail) {
				$salaries_details_data[] = [
					'salary_id' => $salaries->id,
					'job_description_id' => $job_description_id,
					'payment_amount' => $salary_details_data_detail['payment_amount'],
					'hourly_wage' => $wages[$job_description_id],
					'description_division' => 1,
					'created_at' => date('Y-m-d h:i:s'),
					'updated_at' => date('Y-m-d h:i:s')
				];
			}
		}
		if (!empty($salary_details_data[2])) {
			foreach ($salary_details_data[2] as $job_description_id => $salary_details_data_detail) {
				$salaries_details_data[] = [
					'salary_id' =>  $salaries->id,
					'job_description_id' => $job_description_id,
					'payment_amount' => $salary_details_data_detail['payment_amount'],
					'hourly_wage' => $other_wages[$job_description_id],
					'description_division' => 2,
					'created_at' => date('Y-m-d h:i:s'),
					'updated_at' => date('Y-m-d h:i:s')
				];
			}
		}
		if (!empty($salaries_details_data)) {
			SalaryDetail::insert($salaries_details_data);
		}



		return redirect("/shinzemi/salary")->with("flash_message", "データが更新されました。");
	}
	public function deduction_update(Request $request, $id)
	{
		$salaries = Salary::findOrFail($id);
		$request_salaries = $request->all();
		$data = [
			'other_payment_amount' => $request->payment,
			'other_payment_reason' => $request->payment_reason,
			'health_insurance' => $request->health_insurance,
			'welfare_pension' => $request->welfare_pension,
			'employment_insurance' => $request->employment_insurance,
			'municipal_tax' => $request->municipal_tax,
			'year_end_adjustment' => $request->year_end_adjustment
		];
		$salaries->update($data);

		// get session url
		$url = session("url");
		session()->forget("url");
		if (strpos($url, "salary") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/salary")->with("flash_message", "データが更新されました。");
		}

		// return redirect("/shinzemi/salary")->with("flash_message", "データが更新されました。");
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function monthly_tightening()
	{
		$update_column = [
			'monthly_tightening' => 1,
		];
		$update_column2 = [
			'new_monthly_processing_month' => date('Y-m', strtotime('-1 month')),
		];


		Salary::where('monthly_tightening', 0)
			->update($update_column);
		$salary_progress = SalaryProgress::findOrFail(1);

		$salary_progress->update($update_column2);

		return redirect("/shinzemi/home")->with("flash_message", "非常勤給与月締を実行しました。");
	}
	public function salary_approval(Request $request)
	{
		$requestData = $request->all();

		$salaries = Salary::whereIn('user_id', $requestData['salary_ids'])
			->where('tightening_date', $requestData['work_month'])
			->where(function ($query) {
				$query->whereNull('monthly_completion')
					->orWhere('monthly_completion', 0);
			})->get();
		$users = User::whereIn('id', $requestData['salary_ids'])
			->where(function ($query) use ($requestData) {
				$query->whereNull('description_column')
					->orWhere('description_column', 0)
					->orWhereNull('deductible_spouse')
					->orWhereNull('dependents_count')
					->orWhereNull('bank_id')
					->orWhereNull('branch_id')
					->orWhere('branch_id', "")
					->orWhereNull('account_type')
					->orWhere('account_type', "")
					->orWhereNull('account_number')
					->orWhere('account_number', "")
					->orWhereNull('recipient_name')
					->orWhere('recipient_name', "");
			})->get();
		$error_msg = "";
		$salary_error_users = array();
		$user_error_users = array();
		if ($salaries->count() > 0) {
			foreach ($salaries as $salary) {
				$salary_error_users[] = $salary->user->full_name;
			}
			$error_user = implode($salary_error_users);
			$error_msg .= "月末承認されていないユーザーが選択されています。\n";
			$error_msg .= "下記ユーザーの月末承認を行ってから給与承認を行ってください。\n";
			$error_msg .= $error_user . "\n\n";
		}
		if ($users->count() > 0) {
			$user_error_users = $users->pluck('full_name')->toArray();
			$error_user = implode($user_error_users);
			$error_msg .= "必要情報が登録されていないユーザーが選択されています。\n";
			$error_msg .= "下記ユーザーのユーザーマスターをご確認ください。\n";
			$error_msg .= $error_user . "\n";
		}
		if ($error_msg != "") {
			return back()->with("error_message", $error_msg);
		}
		$update_column = [
			'salary_approval' => 1,
		];

		Salary::whereIn('user_id', $requestData['salary_ids'])->where('tightening_date', $requestData['work_month'])
			->update($update_column);

		return back()->with("flash_message", "給与承認を実行しました。");
	}
	public function month_approval(Request $request)
	{
		$requestData = $request->all();
		$monthly_processing_month = $requestData['work_month'];
		$daily_salaries = DailySalary::whereIn('user_id', $requestData['salary_ids'])->where('work_month', $monthly_processing_month)->orderBy('user_id', 'asc')->get();
		$daily_other_salaries = DailyOtherSalary::whereIn('user_id', $requestData['salary_ids'])->where('work_month', $monthly_processing_month)->orderBy('user_id', 'asc')->get();
		$transportation_expenses = TransportationExpense::whereIn('user_id', $requestData['salary_ids'])->where('work_month', $monthly_processing_month)->orderBy('user_id', 'asc')->get();
		$job_description_wages = JobDescriptionWage::whereIn('user_id', $requestData['salary_ids'])->get();
		$other_job_description_wages = OtherJobDescriptionWage::whereIn('user_id', $requestData['salary_ids'])->get();
		$salaries = Salary::whereIn('user_id', $requestData['salary_ids'])->where('tightening_date', $monthly_processing_month)->get();
		foreach ($job_description_wages as $job_description_wage) {
			$wage = empty($job_description_wage->wage) ? 0 : $job_description_wage->wage;
			$wages[$job_description_wage->user_id][$job_description_wage->job_description_id] = $wage;
		}
		foreach ($other_job_description_wages as $other_job_description_wages) {
			$other_wage = empty($other_job_description_wages->wage) ? 0 : $other_job_description_wages->wage;
			$other_wages[$other_job_description_wages->user_id][$other_job_description_wages->other_job_description_id] = $other_wage;
		}

		foreach ($daily_salaries as $daily_salary) {
			$wage = empty($wages[$daily_salary->user_id][$daily_salary->job_description_id]) ? 0 : $wages[$daily_salary->user_id][$daily_salary->job_description_id];
			if (empty($salary_details_data[$daily_salary->user_id][1][$daily_salary->job_description_id]['working_time'])) {
				$salary_details_data[$daily_salary->user_id][1][$daily_salary->job_description_id]['working_time'] = $daily_salary->working_time;
			} else {
				$salary_details_data[$daily_salary->user_id][1][$daily_salary->job_description_id]['working_time'] += $daily_salary->working_time;
			}
		}
		foreach ($salary_details_data as $user_id => $salary_details_data_details) {

			foreach ($salary_details_data_details[1] as $job_description_id => $salary_details_data_detail) {
				$working_time = ceil(($salary_details_data_detail['working_time'] * 10) / 60) / 10;
				// if(empty($wages[$user_id][$job_description_id])){
				//     echo $user_id."<br>";
				//     dd($job_description_id);
				// }
				$salary_details_data[$user_id][1][$job_description_id]['payment_amount'] = $working_time * $wages[$user_id][$job_description_id];
				if (empty($salary_data[$user_id]['salary'])) {
					$salary_data[$user_id]['salary'] = $working_time * $wages[$user_id][$job_description_id];
				} else {
					$salary_data[$user_id]['salary'] += $working_time * $wages[$user_id][$job_description_id];
				}
			}
		}
		foreach ($transportation_expenses as $transportation_expense) {
			$fare = $transportation_expense->unit_price;
			if ($transportation_expense->round_trip_flg == 1) {
				$fare = $fare * 2;
			}
			if (empty($salary_data[$transportation_expense->user_id]['transportation_expenses'])) {
				$salary_data[$transportation_expense->user_id]['transportation_expenses'] = $fare;
			} else {
				$salary_data[$transportation_expense->user_id]['transportation_expenses'] += $fare;
			}
		}
		foreach ($daily_other_salaries as $daily_other_salary) {
			$wage = empty($other_wages[$daily_other_salary->user_id][$daily_other_salary->job_description]) ? 0 : $other_wages[$daily_other_salary->user_id][$daily_other_salary->job_description];

			if (empty($salary_details_data[$daily_other_salary->user_id][2][$daily_other_salary->job_description]['payment_amount'])) {
				$salary_details_data[$daily_other_salary->user_id][2][$daily_other_salary->job_description]['payment_amount'] = $wage;
			} else {
				$salary_details_data[$daily_other_salary->user_id][2][$daily_other_salary->job_description]['payment_amount'] += $wage;
			}
			if (empty($salary_data[$daily_other_salary->user_id]['salary'])) {
				$salary_data[$daily_other_salary->user_id]['salary'] = $wage;
			} else {
				$salary_data[$daily_other_salary->user_id]['salary'] += $wage;
			}
		}
		foreach ($salaries as $salary) {
			$salary_id[$salary->user_id] = $salary->id;
			$salary_ids[] = $salary->id;
			$transportation_expenses = 0;
			$salary_sum = 0;

			if (!empty($salary_data[$salary->user_id]['transportation_expenses'])) {
				$transportation_expenses = $salary_data[$salary->user_id]['transportation_expenses'];
			}
			if (!empty($salary_data[$salary->user_id]['salary'])) {
				$salary_sum = $salary_data[$salary->user_id]['salary'];
			}

			if ($salary->user_id != 91 && $salary->user_id != 1190 && $salary->user_id != 30 && $salary->user_id != 1200) {
				if ($transportation_expenses > 15000) {
					$transportation_expenses = 15000;
				}
			}
			$salary->salary = $salary_sum;
			$salary->transportation_expenses = $transportation_expenses;
			$salary->monthly_completion = 1;
			$salary->save();
		}
		SalaryDetail::whereIn('salary_id', $salary_ids)->delete();

		foreach ($salary_details_data as $user_id => $salary_details_data_details) {
			if (!empty($salary_details_data_details[1])) {
				foreach ($salary_details_data_details[1] as $job_description_id => $salary_details_data_detail) {
					$salaries_details_data[] = [
						'salary_id' => $salary_id[$user_id],
						'job_description_id' => $job_description_id,
						'payment_amount' => $salary_details_data_detail['payment_amount'],
						'hourly_wage' => $wages[$user_id][$job_description_id],
						'description_division' => 1,
						'created_at' => date('Y-m-d h:i:s'),
						'updated_at' => date('Y-m-d h:i:s')
					];
				}
			}
			if (!empty($salary_details_data_details[2])) {
				foreach ($salary_details_data_details[2] as $job_description_id => $salary_details_data_detail) {
					$salaries_details_data[] = [
						'salary_id' => $salary_id[$user_id],
						'job_description_id' => $job_description_id,
						'payment_amount' => $salary_details_data_detail['payment_amount'],
						'hourly_wage' => $other_wages[$user_id][$job_description_id],
						'description_division' => 2,
						'created_at' => date('Y-m-d h:i:s'),
						'updated_at' => date('Y-m-d h:i:s')
					];
				}
			}
		}
		if (!empty($salaries_details_data)) {
			SalaryDetail::insert($salaries_details_data);
		}

		// Salary::whereIn('user_id', $requestData['salary_ids'])->where('tightening_date', $requestData['work_month'])
		// 	->update($update_column);

		return back()->with("flash_message", "月末承認を実行しました。");
	}

	public function monthly_salary(Request $request)
	{
		$requestData = $request->all();
		$monthly_processing_month = $requestData['work_month'];
		$daily_salaries = DailySalary::where('work_month', $monthly_processing_month)->orderBy('user_id', 'asc')->get();
		$daily_other_salaries = DailyOtherSalary::where('work_month', $monthly_processing_month)->orderBy('user_id', 'asc')->get();
		$transportation_expenses = TransportationExpense::where('work_month', $monthly_processing_month)->orderBy('user_id', 'asc')->get();
		$job_descriptions = JobDescription::all();
		$job_description_wages = JobDescriptionWage::all();
		$other_job_description_wages = OtherJobDescriptionWage::all();
		$salaries = Salary::where('tightening_date', $monthly_processing_month)->get();
		foreach ($job_description_wages as $job_description_wage) {
			$wage = empty($job_description_wage->wage) ? 0 : $job_description_wage->wage;
			$wages[$job_description_wage->user_id][$job_description_wage->job_description_id] = $wage;
		}
		foreach ($other_job_description_wages as $other_job_description_wages) {
			$other_wage = empty($other_job_description_wages->wage) ? 0 : $other_job_description_wages->wage;
			$other_wages[$other_job_description_wages->user_id][$other_job_description_wages->other_job_description_id] = $other_wage;
		}

		foreach ($daily_salaries as $daily_salary) {
			$wage = empty($wages[$daily_salary->user_id][$daily_salary->job_description_id]) ? 0 : $wages[$daily_salary->user_id][$daily_salary->job_description_id];
			if (empty($salary_details_data_kari[$daily_salary->user_id][1][$daily_salary->job_description_id]['working_time'])) {
				$salary_details_data_kari[$daily_salary->user_id][1][$daily_salary->job_description_id]['working_time'] = $daily_salary->working_time;
			} else {
				$salary_details_data_kari[$daily_salary->user_id][1][$daily_salary->job_description_id]['working_time'] += $daily_salary->working_time;
			}
		}
		foreach ($salary_details_data_kari as $user_id => $salary_details_data_details) {

			foreach ($salary_details_data_details[1] as $job_description_id => $salary_details_data_detail) {
				$working_time = round(($salary_details_data_detail['working_time'] * 10) / 60) / 10;
				$salary_details_datas[$user_id][1][$job_description_id]['working_time'] = $working_time;
			}
		}
		foreach ($salaries as $salary) {
			$all_user_id[] = $salary->user_id;
			$salary_details = $salary->salary_detail;
			foreach ($salary_details as $salary_detail) {
				if ($salary_detail->payment_amount != 0) {
					$salary_details_datas[$salary->user_id][2][$salary_detail->job_description_id]['working_time'] = round($salary_detail->payment_amount / $salary_detail->hourly_wage, 2);
				} else {
					$salary_details_datas[$salary->user_id][2][$salary_detail->job_description_id]['working_time'] = 0;
				}
			}
		}
		// foreach ($salary_details_datas as $user_id => $salary_details_data) {
		// 	foreach ($salary_details_data[2] as $job_description_id => $salary_details_data_detail) {
		// 		dd($salary_details_data_detail['working_time']);
		// 	}
		// }
		$users = User::whereIn('id', $all_user_id)->get();


		// Salary::whereIn('user_id', $requestData['salary_ids'])->where('tightening_date', $requestData['work_month'])
		// 	->update($update_column);

		return view("salary.monthly_salary", compact("salary_details_datas", "users", "job_descriptions"));
	}


	public function month_approval_cancel(Request $request)
	{
		$update_column = [
			'monthly_completion' => 0,
		];
		$requestData = $request->all();

		Salary::whereIn('user_id', $requestData['salary_ids'])->where('tightening_date', $requestData['work_month'])
			->update($update_column);

		return back()->with("flash_message", "月末承認を実行しました。");
	}
	public function salary_approval_cancel(Request $request)
	{
		$update_column = [
			'salary_approval' => 0,
		];

		$requestData = $request->all();

		Salary::whereIn('user_id', $requestData['salary_ids'])->where('tightening_date', $requestData['work_month'])
			->update($update_column);

		return back()->with("flash_message", "給与承認を解除しました。");
	}
	public function destroy($id)
	{
		DailySalary::destroy($id);

		return redirect("/shinzemi/salary")->with("flash_message", "データが削除されました。");
	}
}
    //=======================================================================
