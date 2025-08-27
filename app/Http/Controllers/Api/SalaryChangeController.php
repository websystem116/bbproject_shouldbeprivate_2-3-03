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

class SalaryChangeController extends Controller
{
    /****
     * 非常勤給与の再計算を行う
     * 
     */
    public function salalyChange()
    {
        $users = User::all();
        foreach ($users as $user) {
            if (!empty($user->school_buildings)) {
                $area_user_ids[$user->school_buildings->area][] = $user->user_id;
            }
        }

        foreach ($area_user_ids as $area => $area_user_id) {
            if ($area == 1) {
                $job_description_wage = JobDescriptionWage::whereIn('job_description_id', ['19', '20'])
                    ->whereIn('user_id', $area_user_id)->where('wage', '<', 1065)->update(['wage' => 1065]);
            }
            if ($area == 2) {
                $job_description_wage = JobDescriptionWage::whereIn('job_description_id', ['19', '20'])
                    ->whereIn('user_id', $area_user_id)->where('wage', '<', 940)->update(['wage' => 940]);
            }
        }
    }
}
