<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Company;
use App\WithdrawalAccount;
use App\PayrollAccount;
use App\Bank;
use App\BranchBank;

//=======================================================================
class CompanysController extends Controller
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

        if (!empty($keyword)) {
            $company = Company::where("id", "LIKE", "%$keyword%")->orWhere("name", "LIKE", "%$keyword%")->orWhere("name_short", "LIKE", "%$keyword%")->orWhere("zipcode", "LIKE", "%$keyword%")->orWhere("address1", "LIKE", "%$keyword%")->orWhere("address2", "LIKE", "%$keyword%")->orWhere("address3", "LIKE", "%$keyword%")->orWhere("tel", "LIKE", "%$keyword%")->orWhere("fax", "LIKE", "%$keyword%")->orWhere("email", "LIKE", "%$keyword%")->paginate($perPage);
        } else {
            $company = Company::paginate($perPage);
        }
        return view("company.index", compact("company"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("company.create");
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
            "name" => "required|max:50", //string('name',50)->nullable()
            "name_short" => "nullable|max:50", //string('name_short',50)->nullable()
            "zipcode" => "nullable|max:8", //string('zipcode',8)->nullable()
            "address1" => "nullable|max:30", //string('address1',30)->nullable()
            "address2" => "nullable|max:30", //string('address2',30)->nullable()
            "address3" => "nullable|max:100", //string('address3',100)->nullable()
            "tel" => "nullable|max:15", //string('tel',15)->nullable()
            "fax" => "nullable|max:15", //string('fax',15)->nullable()
            "email" => "nullable|max:50", //string('email',50)->nullable()
        ], [
            "name.required" => "名前を入力してください。",
            "name.max" => "名前は50文字以内で入力してください。",
        ]);

        $requestData = $request->all();

        Company::create($requestData);

        return redirect("/shinzemi/company")->with("flash_message", "company added!");
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
        $company = Company::findOrFail($id);
        return view("company.show", compact("company"));
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
        $company = Company::findOrFail($id);

        $withdrawal_accounts = WithdrawalAccount::all();
        $payroll_accounts = PayrollAccount::all();


        $banks = Bank::all();
        $branch_banks = BranchBank::all();

        return view("company.edit", compact("company", "banks", "branch_banks", "withdrawal_accounts", "payroll_accounts"));
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
            "name" => "required|max:50", //string('name',50)->nullable()
            "name_short" => "nullable|max:50", //string('name_short',50)->nullable()
            "zipcode" => "nullable|max:8", //string('zipcode',8)->nullable()
            "address1" => "nullable|max:30", //string('address1',30)->nullable()
            "address2" => "nullable|max:30", //string('address2',30)->nullable()
            "address3" => "nullable|max:100", //string('address3',100)->nullable()
            "tel" => "nullable|max:15", //string('tel',15)->nullable()
            "fax" => "nullable|max:15", //string('fax',15)->nullable()
            "email" => "nullable|max:50", //string('email',50)->nullable()

            "consignor_code.*" => "nullable|integer",
            "consignor_name.*" => "nullable|max:50",
            "account_number.*" => "nullable|integer",

            "payroll_consignor_code.*" => "nullable|integer",
            "payroll_consignor_name.*" => "nullable|max:50",
            "payroll_account_number.*" => "nullable|integer",
        ], [
            "name.required" => "名前を入力してください。",
            "name.max" => "名前は50文字以内で入力してください。",
            "zipcode.max" => "郵便番号は8文字以内で入力してください。",
            "address1.max" => "住所1は30文字以内で入力してください。",
            "address2.max" => "住所2は30文字以内で入力してください。",
            "address3.max" => "住所3は100文字以内で入力してください。",
            "tel.max" => "電話番号は15文字以内で入力してください。",
            "fax.max" => "FAX番号は15文字以内で入力してください。",
            "email.max" => "メールアドレスは50文字以内で入力してください。",

            "consignor_code.*.integer" => "自動引落使用口座の委託者コードは数字で入力してください。",
            "consignor_name.*.max" => "自動引落使用口座の委託者名は50文字以内で入力してください。",
            "account_number.*.integer" => "自動引落使用口座の口座番号は数字で入力してください。",

            "payroll_consignor_code.*.integer" => "給与振込使用口座の委託者コードは数字で入力してください。",
            "payroll_consignor_name.*.max" => "給与振込使用口座の委託者名は50文字以内で入力してください。",
            "payroll_account_number.*.integer" => "給与自動振込使用口座の口座番号は数字で入力してください。",

        ]);
        $requestData = $request->all();

        $company = Company::findOrFail($id);
        $company->update($requestData);

        $withdrawal_accounts = WithdrawalAccount::all();
        foreach ($withdrawal_accounts as $withdrawal_account) {
            $withdrawal_account->delete();
        }

        $consignor_code = $request->get('consignor_code');
        $consignor_name = $request->get('consignor_name');
        $bank_id = $request->get('bank_id');
        $branch_bank_id = $request->get('branch_bank_id');
        $account_number = $request->get('account_number');
        $account_type_id = $request->get('account_type_id');

        if (is_countable($consignor_code)) {
            for ($i = 0; $i < count($consignor_code); $i++) {

                $withdrawal_account = new WithdrawalAccount;

                $withdrawal_account->create([

                    'consignor_code' => $consignor_code[$i],
                    'consignor_name' => $consignor_name[$i],
                    'bank_id' => $bank_id[$i],
                    'branch_bank_id' => $branch_bank_id[$i],
                    'account_number' => $account_number[$i],
                    'account_type_id' => $account_type_id[$i],

                ]);
            }
        }

        $payroll_accounts = PayrollAccount::all();
        foreach ($payroll_accounts as $payroll_account) {
            $payroll_account->delete();
        }

        $payroll_consignor_code = $request->get('payroll_consignor_code');
        $payroll_consignor_name = $request->get('payroll_consignor_name');
        $payroll_bank_id = $request->get('payroll_bank_id');
        $payroll_branch_bank_id = $request->get('payroll_branch_bank_id');
        $payroll_account_number = $request->get('payroll_account_number');
        $payroll_account_type_id = $request->get('payroll_account_type_id');

        if (is_countable($payroll_consignor_code)) {
            for ($i = 0; $i < count($payroll_consignor_code); $i++) {

                $payroll_account = new PayrollAccount;

                $payroll_account->create([

                    'consignor_code' => $payroll_consignor_code[$i],
                    'consignor_name' => $payroll_consignor_name[$i],
                    'bank_id' => $payroll_bank_id[$i],
                    'branch_bank_id' => $payroll_branch_bank_id[$i],
                    'account_number' => $payroll_account_number[$i],
                    'account_type_id' => $payroll_account_type_id[$i],

                ]);
            }
        }

        return redirect('company/1/edit')->with("flash_message", "会社情報を更新しました!");
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
        Company::destroy($id);

        return redirect("/shinzemi/company")->with("flash_message", "company deleted!");
    }
}
    //=======================================================================