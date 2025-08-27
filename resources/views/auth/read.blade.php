@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">ユーザー登録</div>

                    <div class="panel-body">
                        <!-- <form method="POST" class="form-horizontal" action="{{ route('register') }}"> -->
                        {{ Form::model($user, ['route' => ['register.update', ['id' => $user->id]], 'method' => 'PUT', 'class' => 'form-horizontal']) }}
                        <div class="form-group row">
                            <!-- <label for="user_id" class="col-md-4 col-form-label text-md-right">
                                                                                                                                                                                                                                       ユーザーID:
                                                                                                                                                                                                                                      </label> -->
                            {{ Form::label('user_id', 'ユーザーID', ['class' => 'col-md-4 col-form-label text-md-right']) }}
                            <div class="col-md-6">
                                <!-- <input id="user_id" type="text" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ old('user_id') }}" user_id autocomplete="username" autofocus> -->
                                {!! Form::text('user_id', null, [
                                    'class' => 'form-control',
                                    'id' => 'user_id',
                                    'readonly',
                                    'placeholder' => 'ユーザーID',
                                ]) !!}
                                @error('user_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">パスワード</label>

                            <div class="col-md-6">
                                <!-- <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password"> -->
                                {{ Form::text('password', null, ['class' => 'form-control', 'id' => 'password', 'readonly', 'placeholder' => 'パスワード']) }}

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label for="last_name" class="col-md-4 col-form-label text-md-right">ユーザー氏名</label>

                            <div class="col-md-6">
                                <!-- <input id="last_name" type="text" class="form-control" name="last_name" required autocomplete="last_name"> -->
                                {{ Form::text('last_name', null, ['class' => 'form-control', 'id' => 'last_name', 'readonly', 'placeholder' => '姓']) }}

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="first_name" class="col-md-4 col-form-label text-md-right"></label>

                            <div class="col-md-6">
                                <!-- 名<input id="first_name" type="text" class="form-control" name="first_name" required autocomplete="first_name"> -->
                                {{ Form::text('first_name', null, ['class' => 'form-control', 'readonly', 'id' => 'first_name', 'placeholder' => '名']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="last_name_kana" class="col-md-4 col-form-label text-md-right">ユーザー氏名（カナ）</label>

                            <div class="col-md-6">
                                <!-- セイ<input id="last_name_kana" type="text" class="form-control" name="last_name_kana" required autocomplete="last_name_kana"> -->
                                {{ Form::text('last_name_kana', null, ['class' => 'form-control', 'readonly', 'id' => 'last_name_kana', 'placeholder' => 'セイ']) }}

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="first_name_kana" class="col-md-4 col-form-label text-md-right"></label>

                            <div class="col-md-6">
                                <!-- メイ<input id="first_name_kana" type="text" class="form-control" name="first_name_kana" required autocomplete="first_name_kana"> -->
                                {{ Form::text('first_name_kana', null, ['class' => 'form-control', 'readonly', 'id' => 'first_name_kana', 'placeholder' => 'メイ']) }}

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="birthday" class="col-md-4 col-form-label text-md-right">生年月日</label>

                            <div class="col-md-6">
                                <!-- <input id="birthday" type="date" class="form-control" name="last_name_kana" required autocomplete="birthday"> -->
                                {{ Form::date('birthday', null, ['class' => 'form-control', 'id' => 'birthday', 'readonly', 'placeholder' => '誕生日']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sex" class="col-md-4 col-form-label text-md-right">性別
                            </label>

                            <div class="col-md-3">
                                <!-- <input id="sex" type="radio" class="form-control" name="sex" required value="1">男性 -->
                                {{ Form::radio('sex', '1', false, ['class' => 'custom-control-input', 'disabled', 'id' => 'sex1']) }}
                                {{ Form::label('sex1', '男性', ['class' => 'custom-control-label']) }}
                            </div>
                            <div class="col-md-3">
                                <!-- <input id="sex" type="radio" class="form-control" name="sex" required value="2">女性 -->
                                {{ Form::radio('sex', '2', false, ['class' => 'custom-control-input', 'disabled', 'id' => 'sex2']) }}
                                {{ Form::label('sex2', '女性', ['class' => 'custom-control-label']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="post_code" class="col-md-4 col-form-label text-md-right">郵便番号
                            </label>

                            <div class="col-md-6">
                                <!-- <input id="post_code" type="text" class="form-control" name="post_code" required autocomplete="post_code"> -->
                                {{ Form::text('post_code', null, ['class' => 'form-control', 'id' => 'post_code', 'readonly', 'placeholder' => '郵便番号']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address1" class="col-md-4 col-form-label text-md-right">住所1
                            </label>

                            <div class="col-md-6">
                                <!-- <input id="address1" type="text" class="form-control" name="address1" required autocomplete="address1"> -->
                                {{ Form::text('address1', null, ['class' => 'form-control', 'id' => 'address1', 'readonly', 'placeholder' => '住所1']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address2" class="col-md-4 col-form-label text-md-right">住所2
                            </label>

                            <div class="col-md-6">
                                <!-- <input id="address2" type="text" class="form-control" name="address2" required autocomplete="address2"> -->
                                {{ Form::text('address2', null, ['class' => 'form-control', 'id' => 'address2', 'readonly', 'placeholder' => '住所2']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address3" class="col-md-4 col-form-label text-md-right">住所3
                            </label>

                            <div class="col-md-6">
                                <!-- <input id="address3" type="text" class="form-control" name="address3" required autocomplete="address3"> -->
                                {{ Form::text('address3', null, ['class' => 'form-control', 'id' => 'address3', 'readonly', 'placeholder' => '住所3']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tel" class="col-md-4 col-form-label text-md-right">電話番号
                            </label>

                            <div class="col-md-6">
                                <!-- <input id="tel" type="text" class="form-control" name="tel" required autocomplete="tel"> -->
                                {{ Form::text('tel', null, ['class' => 'form-control', 'id' => 'tel', 'readonly', 'placeholder' => '電話']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Eメールアドレス
                            </label>

                            <div class="col-md-6">
                                <!-- <input id="post_code" type="email" class="form-control" name="email" required autocomplete="email"> -->
                                {{ Form::email('email', null, ['class' => 'form-control', 'id' => 'email', 'readonly', 'placeholder' => 'Eメール']) }}

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hiredate" class="col-md-3 col-form-label text-md-right">入社日
                            </label>

                            <div class="col-md-3">
                                <!-- <input id="hiredate" type="date" class="form-control" name="hiredate" required autocomplete="hiredate"> -->
                                {{ Form::date('hiredate', null, ['class' => 'form-control', 'id' => 'hiredate', 'readonly', 'placeholder' => '入社日']) }}
                            </div>
                            <label for="retirement_date" class="col-md-3 col-form-label text-md-right">退職日
                            </label>

                            <div class="col-md-3">
                                <!-- <input id="retirement_date" type="date" class="form-control" name="retirement_date" required autocomplete="hiredate"> -->
                                {{ Form::date('retirement_date', null, ['class' => 'form-control', 'readonly', 'id' => 'retirement_date', 'placeholder' => '退社日']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="school_building" class="col-md-4 col-form-label text-md-right">校舎
                            </label>

                            <div class="col-md-6">
                                {{ Form::select('school_building', $school_buildings, null, ['placeholder' => '選択してください', 'class' => 'form-control', 'disabled', 'id' => 'school_building']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="employment_status" class="col-md-4 col-form-label text-md-right">職務
                            </label>

                            <div class="col-md-6">
                                {{ Form::select('employment_status', config('const.employment_status'), null, [
                                    'placeholder' => '選択してください',
                                    'class' => 'form-control',
                                    'disabled',
                                    'id' => 'employment_status',
                                ]) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="occupation" class="col-md-4 col-form-label text-md-right">職業
                            </label>

                            <div class="col-md-6">
                                {{ Form::select('occupation', config('const.occupation'), null, ['placeholder' => '選択してください', 'class' => 'form-control', 'disabled', 'id' => 'occupation']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('roles', '権限', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>


                            <div class="col-md-6">
                                {{ Form::select('roles', config('const.roles'), null, ['placeholder' => '選択してください', 'class' => 'form-control', 'disabled', 'id' => 'roles']) }}
                            </div>
                        </div>

                        <hr>
                        <div class="form-group row">
                            <div class="col-md-3 col-form-label text-md-right">
                                <<業務内容>>
                            </div>
                        </div>
                        @foreach ($job_description as $item)
                        <div class="form-group row">
                            <label for="office_wage" class="col-md-4 col-form-label text-md-right">{{ $item->name }}（時給）
                            </label>
    
                            <div class="col-md-6">
                                {{ Form::number('wage[]', $job_description_wage_values[$item->id] ?? '', ['class' => 'form-control','disabled']) }}
                            </div>
                        </div>
                        @endforeach
                        <hr>
                        <div class="form-group row">
                            <div class="col-md-3 col-form-label text-md-right">
                                <<その他実績>>
                            </div>
                        </div>
    
                        @foreach ($other_job_description as $item)
                        <div class="form-group row">
                            <label for="office_wage" class="col-md-4 col-form-label text-md-right">{{ $item->name }}
                            </label>
    
                            <div class="col-md-6">
                                {{ Form::number('other_wage[]', $other_job_description_wage_values[$item->id] ?? '', ['class' => 'form-control','disabled']) }}
                            </div>
                        </div>
                        @endforeach
                        <hr>
                            <div class="form-group row">
                            <label for="description_column" class="col-md-4 col-form-label text-md-right">摘要欄
                            </label>

                            <div class="col-md-3">
                                {{ Form::radio('description_column', 1, false, ['class' => 'custom-control-input', 'disabled', 'id' => 'description_column1']) }}
                                {{ Form::label('description_column1', '甲欄', ['class' => 'custom-control-label']) }}
                            </div>
                            <div class="col-md-3">
                                {{ Form::radio('description_column', 2, false, ['class' => 'custom-control-input', 'disabled', 'id' => 'description_column2']) }}
                                {{ Form::label('description_column2', '乙欄', ['class' => 'custom-control-label']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="deductible_spouse" class="col-md-4 col-form-label text-md-right">控除対象配偶者
                            </label>

                            <div class="col-md-3">
                                {{ Form::radio('deductible_spouse', '1', false, ['class' => 'custom-control-input', 'disabled', 'id' => 'deductible_spouse1']) }}
                                {{ Form::label('deductible_spouse1', 'なし', ['class' => 'custom-control-label']) }}
                            </div>
                            <div class="col-md-3">
                                {{ Form::radio('deductible_spouse', '2', false, ['class' => 'custom-control-input', 'disabled', 'id' => 'deductible_spouse2']) }}
                                {{ Form::label('deductible_spouse2', 'あり', ['class' => 'custom-control-label']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dependents_count" class="col-md-4 col-form-label text-md-right">控除対象扶養親族数
                            </label>

                            <div class="col-md-6">
                                {{ Form::number('dependents_count', null, ['class' => 'form-control', 'readonly', 'id' => 'dependents_count']) }}人
                            </div>
                        </div>
                        {{-- <div class="form-group row">
                            <label for="bank_id" class="col-md-4 col-form-label text-md-right">銀行コード
                            </label>

                            <div class="col-md-6">
                                {{ Form::select('bank_id', $banks_selects, null, ['placeholder' => '選択してください', 'class' => 'form-control', 'disabled', 'id' => 'bank_id']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="branch_id" class="col-md-4 col-form-label text-md-right">支店コード
                            </label>

                            <div class="col-md-6">
                                {{ Form::select('branch_id', $branch_banks, null, ['placeholder' => '選択してください', 'class' => 'form-control', 'disabled', 'id' => 'branch_id']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="account_type" class="col-md-4 col-form-label text-md-right">口座種別
                            </label>
                            <div class="col-md-6">
                                {{ Form::select('account_type', config('const.account_type'), null, ['placeholder' => '選択してください', 'disabled', 'class' => 'form-control', 'id' => 'account_type']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="account_number" class="col-md-4 col-form-label text-md-right">口座番号
                            </label>

                            <div class="col-md-6">
                                {{ Form::text('account_number', null, ['class' => 'form-control', 'readonly', 'id' => 'account_number']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="recipient_name" class="col-md-4 col-form-label text-md-right">受取人名
                            </label>

                            <div class="col-md-6">
                                {{ Form::text('recipient_name', null, ['class' => 'form-control', 'readonly', 'id' => 'recipient_name', 'placeholder' => '受取人名']) }}
                            </div>
                        </div>
 --}}
                        {{ Form::close() }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
