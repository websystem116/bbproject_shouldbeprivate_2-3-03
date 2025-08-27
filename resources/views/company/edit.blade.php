@extends('layouts.app')
@section('content')
<script src="{{ asset('/js/company.js') }}"></script>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">編集</div>
                <div class="panel-body">
                    <!-- <a href="{{ url('/shinzemi/home') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a> -->
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <form method="POST" action="{{ route('company.update', $company->id) }}" class="form-horizontal" id="form1">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}

                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">名前: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name" type="text" id="name" value="{{ $company->name }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name_short" class="col-md-4 control-label">略称: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name_short" type="text" id="name_short" value="{{ $company->name_short }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="zipcode" class="col-md-4 control-label">郵便番号: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="zipcode" type="text" id="zipcode" value="{{ $company->zipcode }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address1" class="col-md-4 control-label">住所1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="address1" type="text" id="address1" value="{{ $company->address1 }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address2" class="col-md-4 control-label">住所2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="address2" type="text" id="address2" value="{{ $company->address2 }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address3" class="col-md-4 control-label">住所3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="address3" type="text" id="address3" value="{{ $company->address3 }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tel" class="col-md-4 control-label">電話番号: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="tel" type="text" id="tel" value="{{ $company->tel }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fax" class="col-md-4 control-label">FAX番号: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="fax" type="text" id="fax" value="{{ $company->fax }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label">Eメールアドレス: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="email" type="text" id="email" value="{{ $company->email }}">
                            </div>
                        </div>

                        <hr>

                        <div id="withdrawal_accounts_to">

                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">自動引落使用口座</label>
                                <div class="form-group">

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-success add-input-withdrawal">+</button>
                                        <button type="button" class="btn btn-sm btn-danger withdrawal-delete">×</button>
                                    </div>

                                </div>
                            </div>

                            @foreach($withdrawal_accounts as $key => $withdrawal_account)

                            <div class="withdrawal_accounts" style="margin-top: 40px;">


                                <div class="form-group">
                                    <label for="consignor_code" class="col-md-4 control-label">委託者コード: </label>
                                    <div class="col-md-3">
                                        <input class="form-control" name="consignor_code[]" type="text" id="consignor_code" value="{{ $withdrawal_account->consignor_code ?? '' }}" required="required">
                                    </div>

                                    <label for="consignor_name" class="col-md-2 control-label">委託者名: </label>
                                    <div class="col-md-3">
                                        <input class="form-control" name="consignor_name[]" type="text" id="consignor_name" value="{{ $withdrawal_account->consignor_name ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label for="bank_id" class="col-md-4 control-label">銀行: </label>
                                    <div class="col-md-3">
                                        <select class="form-control" name="bank_id[]">
                                            <option value="">-</option>

                                            @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}" @if ($withdrawal_account->bank_id == $bank->id) selected @endif>
                                                {{ $bank->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <label for="branch_bank_id" class="col-md-2 control-label">銀行支店: </label>
                                    <div class="col-md-3">
                                        <select class="form-control" name="branch_bank_id[]">
                                            <option value="">-</option>

                                            @foreach ($branch_banks as $branch_bank)
                                            <option data-bank="{{ $branch_bank->bank_id }}" value="{{ $branch_bank->id }}" @if ($withdrawal_account->branch_bank_id == $branch_bank->id) selected @endif>
                                                {{ $branch_bank->name }}
                                                @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="account_number" class="col-md-4 control-label">口座番号: </label>
                                    <div class="col-md-3">
                                        <input class="form-control" name="account_number[]" type="text" id="account_number" value="{{ $withdrawal_account->account_number ?? '' }}">
                                    </div>

                                    <label for="account_type_id" class="col-md-2 control-label">口座種別: </label>
                                    <div class="col-md-3">
                                        <select class="form-control" name="account_type_id[]">

                                            @foreach (config('const.account_type') as $key => $value)
                                            <option value="{{ $key }}" @if ($withdrawal_account->account_type_id == $key) selected @endif>
                                                {{ $value }}
                                            </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </div>

                        <hr>

                        <div id="payroll_accounts_to">

                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">給与自動振込使用口座</label>
                                <div class="form-row">
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-success add-input-payroll">+</button>
                                        <button type="button" class="btn btn-sm btn-danger payroll-delete">×</button>
                                    </div>
                                </div>
                            </div>

                            @foreach($payroll_accounts as $key => $payroll_account)

                            <div class="payroll_accounts" style="margin-top: 40px;">


                                <div class="form-group">
                                    <label for="consignor_code" class="col-md-4 control-label">委託者コード: </label>
                                    <div class="col-md-3">
                                        <input class="form-control" name="payroll_consignor_code[]" type="text" id="consignor_code" value="{{ $payroll_account->consignor_code ?? '' }}" required="required">
                                    </div>

                                    <label for="consignor_name" class="col-md-2 control-label">委託者名: </label>
                                    <div class="col-md-3">
                                        <input class="form-control" name="payroll_consignor_name[]" type="text" id="consignor_name" value="{{ $payroll_account->consignor_name ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="bank_id" class="col-md-4 control-label">銀行: </label>
                                    <div class="col-md-3">
                                        <select class="form-control" name="payroll_bank_id[]">
                                            <option value="">-</option>

                                            @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}" @if ($payroll_account->bank_id == $bank->id) selected @endif>
                                                {{ $bank->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <label for="branch_bank_id" class="col-md-2 control-label">銀行支店: </label>
                                    <div class="col-md-3">
                                        <select class="form-control" name="payroll_branch_bank_id[]">
                                            <option value="">-</option>

                                            @foreach ($branch_banks as $branch_bank)
                                            <option data-bank="{{ $branch_bank->bank_id }}" value="{{ $branch_bank->id }}" @if ($payroll_account->branch_bank_id == $branch_bank->id) selected @endif>
                                                {{ $branch_bank->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="account_number" class="col-md-4 control-label">口座番号: </label>
                                    <div class="col-md-3">
                                        <input class="form-control" name="payroll_account_number[]" type="text" id="account_number" value="{{ $payroll_account->account_number ?? '' }}">
                                    </div>

                                    <label for="account_type_id" class="col-md-2 control-label">口座種別: </label>
                                    <div class="col-md-3">
                                        <select class="form-control" name="payroll_account_type_id[]">
                                            @foreach (config('const.account_type') as $key => $value)
                                            <option value="{{ $key }}" @if ($payroll_account->account_type_id == $key) selected @endif>
                                                {{ $value }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                            @endforeach

                        </div>


                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                <input class="btn btn-primary" type="submit" value="更新">
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<template id="withdrawal_accounts">

    <div class="withdrawal_accounts" style="margin-top: 40px;">
        <div class="form-group">
            <label for="consignor_code" class="col-md-4 control-label">委託者コード: </label>
            <div class="col-md-3">
                <input class="form-control" name="consignor_code[]" type="text" id="consignor_code" value="" required="required">
            </div>

            <label for="consignor_name" class="col-md-2 control-label">委託者名: </label>
            <div class="col-md-3">
                <input class="form-control" name="consignor_name[]" type="text" id="consignor_name" value="">
            </div>
        </div>

        <div class="form-group">
            <label for="bank_id" class="col-md-4 control-label">銀行: </label>
            <div class="col-md-3">
                <select class="form-control" name="bank_id[]">
                    <option value="">-</option>

                    @foreach ($banks as $bank)
                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>

            <label for="branch_bank_id" class="col-md-2 control-label">銀行支店: </label>
            <div class="col-md-3">
                <select class="form-control" name="branch_bank_id[]">
                    <option value="">-</option>

                    @foreach ($branch_banks as $branch_bank)
                    <option data-bank="{{ $branch_bank->bank_id }}" value="{{ $branch_bank->id }}">{{ $branch_bank->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="account_number" class="col-md-4 control-label">口座番号: </label>
            <div class="col-md-3">
                <input class="form-control" name="account_number[]" type="text" id="account_number" value="">
            </div>

            <label for="account_type_id" class="col-md-2 control-label">口座種別: </label>
            <div class="col-md-3">

                <select class="form-control" name="account_type_id[]">
                    @foreach (config('const.account_type') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
</template>

<template id="payroll_accounts">

    <div class="payroll_accounts" style="margin-top: 40px;">

        <div class="form-group">
            <label for="consignor_code" class="col-md-4 control-label">委託者コード: </label>
            <div class="col-md-3">
                <input class="form-control" name="payroll_consignor_code[]" type="text" id="consignor_code" value="" required="required">
            </div>

            <label for="consignor_name" class="col-md-2 control-label">委託者名: </label>
            <div class="col-md-3">
                <input class="form-control" name="payroll_consignor_name[]" type="text" id="consignor_name" value="">
            </div>
        </div>

        <div class="form-group">
            <label for="bank_id" class="col-md-4 control-label">銀行: </label>
            <div class="col-md-3">
                <select class="form-control" name="payroll_bank_id[]">
                    <option value="">-</option>
                    @foreach ($banks as $bank)
                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>

            <label for="branch_bank_id" class="col-md-2 control-label">銀行支店: </label>
            <div class="col-md-3">
                <select class="form-control" name="payroll_branch_bank_id[]">
                    <option value="">-</option>
                    @foreach ($branch_banks as $branch_bank)
                    <option data-bank="{{ $branch_bank->bank_id }}" value="{{ $branch_bank->id }}">{{ $branch_bank->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="account_number" class="col-md-4 control-label">口座番号: </label>
            <div class="col-md-3">
                <input class="form-control" name="payroll_account_number[]" type="text" id="account_number" value="">
            </div>

            <label for="account_type_id" class="col-md-2 control-label">口座種別: </label>
            <div class="col-md-3">
                <select class="form-control" name="payroll_account_type_id[]">
                    @foreach (config('const.account_type') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

</template>