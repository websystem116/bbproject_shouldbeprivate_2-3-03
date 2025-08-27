<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

use App\User;
use App\Salary;
use App\SalaryDetail;
use App\SalaryProgress;
use App\SchoolBuilding;
use App\School;
use App\JobDescription;
use App\OtherJobDescription;
use App\JobDescriptionWage;
use App\OtherJobDescriptionWage;
use App\DailySalary;
use App\DailyOtherSalary;
use App\Company;
use App\IncomeTax;
use App\TransportationExpense;

class SalaryCalculationController extends Controller
{
    /****
     * 非常勤給与の再計算を行う
     * 
     */
    public function salalyCalculation()
    {
        $salary_progress = SalaryProgress::findOrFail(1);
        $new_monthly_processing_month = $salary_progress->new_monthly_processing_month;
        $monthly_processing_month = date("Y-m", strtotime("+1 month " . $new_monthly_processing_month . "-01"));
        $daily_salaries = DailySalary::where('work_month', $monthly_processing_month)->orderBy('user_id', 'asc')->get();
        $daily_other_salaries = DailyOtherSalary::where('work_month', $monthly_processing_month)->orderBy('user_id', 'asc')->get();
        $transportation_expenses = TransportationExpense::where('work_month', $monthly_processing_month)->orderBy('user_id', 'asc')->get();
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
            if ($transportation_expenses > 15000) {
                $transportation_expenses = 15000;
            }

            $salary->salary = $salary_sum;
            $salary->transportation_expenses = $transportation_expenses;
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
    }
}
