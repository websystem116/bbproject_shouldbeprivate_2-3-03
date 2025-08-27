@extends('layouts.app')

@section('content')
    @push('css')
        <link href="{{ asset('css/register.css') }}" rel="stylesheet">
    @endpush
    <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
    <script>
    $(function () {
        $(".banks_select").change(function () {
            var id = $(this).val();
            var branch_banks_select = @json($branch_banks_select);
            console.log(branch_banks_select);
			count = $(branch_banks_select[id]).length;
            console.log(count);
            $('.branch_banks > option').remove();
			for (var i = 0; i < count; i++) {
				$('.branch_banks').append($('<option>').html(branch_banks_select[id][i]['display']).val(branch_banks_select[id][i]['value']));
				// console.log(results[i]['id']);
			}
        });
    });
    </script>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        ユーザー登録
                    </div>

                    <div class="panel-body">
                        {{ Form::open(['route' => 'register', 'class' => 'form-horizontal']) }}
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('user_id', 'ユーザーID', ['class' => 'col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>
                            <div class="col-md-6">
                                {!! Form::text('user_id', '', [
                                    'class' => 'form-require form-control',
                                    'id' => 'user_id',
                                    'placeholder' => 'ユーザーID',
                                ]) !!}

                                @error('user_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('password', 'パスワード', ['class' => 'col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-6">
                                {{ Form::text('password', '',['class' => 'form-require form-control', 'id' => 'password', 'placeholder' => 'パスワード']) }}

                                @error('password')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('last_name', 'ユーザー氏名', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-6">
                                {{ Form::text('last_name', null, ['class' => 'form-require form-control', 'id' => 'last_name', 'placeholder' => '姓']) }}

                                @error('last_name')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="first_name" class="col-md-4 col-form-label text-md-right"></label>

                            <div class="col-md-6">
                                {{ Form::text('first_name', null, ['class' => 'form-require form-control', 'id' => 'first_name', 'placeholder' => '名']) }}

                                @error('first_name')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('last_name_kana', 'ユーザー氏名（カナ）', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-6">
                                {{ Form::text('last_name_kana', null, ['class' => 'form-require form-control hira_change', 'id' => 'last_name_kana', 'placeholder' => 'セイ']) }}

                                @error('last_name_kana')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="first_name_kana" class="col-md-4 col-form-label text-md-right"></label>

                            <div class="col-md-6">
                                {{ Form::text('first_name_kana', null, ['class' => 'form-require form-control hira_change', 'id' => 'first_name_kana', 'placeholder' => 'メイ']) }}

                                @error('first_name_kana')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('birthday', '生年月日', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-6">
                                <!-- <input id="birthday" type="date" class="form-control" name="last_name_kana" required autocomplete="birthday"> -->
                                {{ Form::date('birthday', null, ['class' => 'form-require form-control', 'id' => 'birthday', 'placeholder' => '誕生日']) }}

                                @error('birthday')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('sex', '性別', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-3">
                                {{ Form::radio('sex', '1', false, ['class' => 'form-require custom-control-input', 'id' => 'sex1']) }}
                                {{ Form::label('sex1', '男性', ['class' => 'custom-control-label']) }}
                            </div>
                            <div class="col-md-3">
                                {{ Form::radio('sex', '2', false, ['class' => 'form-require custom-control-input', 'id' => 'sex2']) }}
                                {{ Form::label('sex2', '女性', ['class' => 'custom-control-label']) }}
                            </div>
                        </div>
                        <div class="form-group row">

                            <div class="col-md-4 col-form-label">
                                <label for=" post_code" class="text-md-right">郵便番号</label>
                                <font color="red">※必須</font>

                            </div>

                            <div class="col-md-6">
                                {{ Form::text('post_code', null, ['autocomplete' => 'off', 'onKeyUp' => "AjaxZip3.zip2addr(this,'','address1','address1')", 'class' => 'form-require form-control', 'id' => 'post_code', 'placeholder' => '郵便番号']) }}

                                @error('post_code')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address1" class="col-md-4 col-form-label text-md-right">住所1
                            </label>

                            <div class="col-md-6">
                                {{ Form::text('address1', null, ['class' => 'form-control', 'id' => 'address1', 'placeholder' => '住所1']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address2" class="col-md-4 col-form-label text-md-right">住所2
                            </label>

                            <div class="col-md-6">
                                {{ Form::text('address2', null, ['class' => 'form-control', 'id' => 'address2', 'placeholder' => '住所2']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address3" class="col-md-4 col-form-label text-md-right">住所3
                            </label>

                            <div class="col-md-6">
                                {{ Form::text('address3', null, ['class' => 'form-control', 'id' => 'address3', 'placeholder' => '住所3']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            </label>
                            <div class="col-md-4">
                                {{ Form::label('tel', '電話番号', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-6">
                                {{ Form::text('tel', null, ['class' => 'form-require form-control', 'id' => 'tel', 'placeholder' => '電話']) }}

                                @error('tel')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('email', 'Eメールアドレス', ['class' => ' col-form-label text-md-right']) }}
                            </div>

                            <div class="col-md-6">
                                {{ Form::email('email', '', ['class' => 'form-control', 'id' => 'email', 'placeholder' => 'Eメール']) }}

                                @error('email')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror


                            </div>
                        </div>
                        <div class="form-group row">

                            <div class="col-md-4">
                                <label for="hiredate" class="text-md-right">
                                    入社日
                                </label>
                            </div>

                            <div class="col-md-6" style="display:flex;">
                                {{ Form::date('hiredate', '', ['class' => 'form-control', 'id' => 'hiredate', 'placeholder' => '入社日']) }}
                                <label for=" retirement_date" class="col-md-4 col-form-label text-md-right"
                                    style="align-self: center;">
                                    退職日
                                </label>
                                {{ Form::date('retirement_date', '', ['class' => 'form-control', 'id' => 'retirement_date', 'placeholder' => '退社日']) }}


                            </div>

                        </div>



                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('school_building', '校舎', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-6">
                                {{ Form::select('school_building', $school_buildings, null, ['placeholder' => '選択してください', 'class' => 'form-require form-control', 'id' => 'school_building']) }}

                                @error('school_building')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('employment_status', '職務', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-6">
                                {!! Form::select('employment_status', config('const.employment_status'), null, [
                                    'placeholder' => '選択してください',
                                    'class' => 'form-require form-control',
                                    'id' => 'employment_status',
                                ]) !!}

                                @error('employment_status')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('occupation', '職業', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-6">
                                {{ Form::select('occupation', config('const.occupation'), null, ['placeholder' => '選択してください', 'class' => 'form-require form-control', 'id' => 'occupation']) }}

                                @error('occupation')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                {{ Form::label('roles', '権限', ['class' => ' col-form-label text-md-right']) }}
                                <font color="red">※必須</font>
                            </div>

                            <div class="col-md-6">
                                {{ Form::select('roles', config('const.roles'), null, ['placeholder' => '選択してください', 'class' => 'form-require form-control', 'id' => 'roles']) }}

                                @error('roles')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

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
                                <label for="office_wage"
                                    class="col-md-4 col-form-label text-md-right">{{ $item->name }}
                                    （時給）
                                </label>

                                <div class="col-md-6">
                                    {{ Form::number('wage[]', $user->job_description_wages[$item->id-1]->wage ?? '', ['class' => 'form-control', 'id' => 'wage']) }}
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
                                <label for="office_wage"
                                    class="col-md-4 col-form-label text-md-right">{{ $item->name }}
                                </label>

                                <div class="col-md-6">
                                    {{ Form::number('other_wage[]', $user->other_job_description_wages[$item->id - 1]->wage ?? '', ['class' => 'form-control', 'id' => 'wage']) }}
                                </div>
                            </div>
                        @endforeach
                        <hr>

                        <div class="form-group row">

                            <div class="col-md-4">
                                <label for="description_column" class="text-md-right">
                                    摘要欄
                                </label>
                                {{-- <font color="red" class="required" style="display: none;">
                                    ※必須
                                </font> --}}
                            </div>

                            <div class="col-md-6">
                                {{ Form::radio('description_column', 1, false, ['class' => 'custom-control-input', 'id' => 'description_column1']) }}
                                {{ Form::label('description_column1', '甲欄', ['class' => 'custom-control-label']) }}
                                {{ Form::radio('description_column', 2, false, ['class' => 'custom-control-input', 'id' => 'description_column2']) }}
                                {{ Form::label('description_column2', '乙欄', ['class' => 'custom-control-label']) }}

                                @error('description_column')
                                    <div class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="deductible_spouse" class="col-md-4 col-form-label text-md-right">控除対象配偶者
                            </label>

                            <div class="col-md-3">
                                {{ Form::radio('deductible_spouse', '0', false, ['class' => 'custom-control-input', 'id' => 'deductible_spouse1']) }}
                                {{ Form::label('deductible_spouse1', 'なし', ['class' => 'custom-control-label']) }}
                            </div>
                            <div class="col-md-3">
                                {{ Form::radio('deductible_spouse', '1', false, ['class' => 'custom-control-input', 'id' => 'deductible_spouse2']) }}
                                {{ Form::label('deductible_spouse2', 'あり', ['class' => 'custom-control-label']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="dependents_count" class="col-md-4 col-form-label text-md-right">控除対象扶養親族数
                            </label>

                            <div class="col-md-6" style="display:flex">
                                {{ Form::number('dependents_count', '', ['class' => 'form-control', 'id' => 'dependents_count']) }}
                                <div style="align-self: flex-end;">人</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bank_id" class="col-md-4 col-form-label text-md-right">銀行コード
                            </label>

                            <div class="col-md-6">
                                {{ Form::select('bank_id', $banks_selects, null, ['placeholder' => '選択してください', 'class' => 'form-control banks_select', 'id' => 'bank_id']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="branch_id" class="col-md-4 col-form-label text-md-right">支店コード
                            </label>

                            <div class="col-md-6">
                                {{ Form::select('branch_id', $branch_banks, null, ['placeholder' => '選択してください', 'class' => 'form-control branch_banks', 'id' => 'branch_id']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="account_type" class="col-md-4 col-form-label text-md-right">口座種別
                            </label>

                            <div class="col-md-6">
                                {{ Form::select('account_type', config('const.account_type'), null, ['placeholder' => '選択してください', 'class' => 'form-control', 'id' => 'account_type']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="account_number" class="col-md-4 col-form-label text-md-right">口座番号
                            </label>

                            <div class="col-md-6">
                                {{ Form::number('account_number', '', ['class' => 'form-control', 'id' => 'account_number']) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="recipient_name" class="col-md-4 col-form-label text-md-right">受取人名
                            </label>

                            <div class="col-md-6">
                                {{ Form::text('recipient_name', null, ['class' => 'form-control hira_change hankaku_kana_change', 'id' => 'recipient_name', 'placeholder' => '受取人名']) }}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    登録
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    {{-- <script>
        // jquery
        $(function() {

            // if charge employment_status and value is 3 then show class=required
            $('#employment_status').change(function() {
                if ($(this).val() == 3) {
                    $('.required').show();
                } else {
                    $('.required').hide();
                }
            });
        });
    </script> --}}
@endsection
