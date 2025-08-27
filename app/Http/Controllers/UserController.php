<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Bank;
use App\BranchBank;
use App\JobDescription;
use App\OtherJobDescription;
use App\JobDescriptionWage;
use App\OtherJobDescriptionWage;
use App\SchoolBuilding;

class UserController extends Controller
{
    public function user_csv_export(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=ユーザーマスタ.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        $callback = function () {
            $createCsvFile = fopen('php://output', 'w');

            $columns = [
                '管理No',
                'ユーザーID',
                'E-Mail',
                'パスワード',
                '名前（姓）',
                '名前（名）',
                '名前（姓フリガナ）',
                '名前（名フリガナ）',
                '生年月日',
                '性別',
                '郵便番号',
                '住所1',
                '住所2',
                '住所3',
                '電話番号',
                '校舎',
                '職務',
                '職種',
                '退社日',
                '入社日',
                '摘要欄',
                '控除対象配偶者',
                '控除対象扶養親族数',
                '銀行コード',
                '銀行支店コード',
                '口座種別',
                '口座番号',
                '受取人名',
                '権限',
                '作成日',
                '更新日',
            ];

            mb_convert_variables('SJIS-win', 'UTF-8', $columns);

            fputcsv($createCsvFile, $columns);

            $employee = DB::table('employee');

            $employeeData = $employee
                ->select(['id', 'name', 'department'])
                ->get();

            foreach ($employeeData as $employee) {
                $csv = [
                    $employee->id,
                    $employee->name,
                    $employee->department,
                ];

                mb_convert_variables('SJIS-win', 'UTF-8', $csv);

                fputcsv($createCsvFile, $csv);
            }
            fclose($createCsvFile);
        };
        return response()->stream($callback, 200, $headers);
    }
}
