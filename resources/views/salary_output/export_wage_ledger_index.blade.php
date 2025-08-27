@extends('layouts.app')
@section('content')
@push('css')
<!-- <link href="css/bootstrap-datepicker3.css" rel="stylesheet"> -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- <script type="text/javascript" src="js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datepicker.ja.min.js"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>

<script>
$(function() {
    $('.select_search').select2({
        language: "ja",
        width: '300px'
    });
});
$(function() {
    $(document).on('click', '.check_all', function() {
        if ($('input[name="user_ids[]"]:checked').length == 0) {
            $('input[name="user_ids[]"]').prop('checked', true);
        } else {
            $('input[name="user_ids[]"]').prop('checked', false);
        }
    });

    // monthPick
    var currentTime = new Date();
    var year = currentTime.getFullYear();
    var year2 = parseInt(year) + 10;

    // $(".monthPick").datepicker({
    //     autoclose: true,
    //     language: 'ja',
    //     clearBtn: true,
    //     format: "yyyy-mm",
    //     minViewMode: 1,
    //     maxViewMode: 2
    // });

    $(document).on('click', '.salary_approval', function() {
        // $('input[name = "salary_ids"]: checked ').length;
        var salary_id_cnt = $('input[name="salary_ids[]"]:checked').length;
        if (salary_id_cnt == 0) {
            alert('給与承認する情報を選択してください。');
            return false;
        }
        if (!confirm('給与承認を行います。よろしいですか。')) {
            return false;
        } else {
            var form = $(this).parents('form');
            var action_url = "{{ route('salary.salary_approval') }}";
            form.attr('action', action_url);
            form.submit();
        }
    });
    $(document).on('click', '.salary_approval_cancel', function() {
        var salary_id_cnt = $('input[name="salary_ids[]"]:checked').length;
        if (salary_id_cnt == 0) {
            alert('給与承認を解除する情報を選択してください。');
            return false;
        }
        if (!confirm('給与承認を解除します。よろしいですか。')) {
            return false;
        } else {
            var form = $(this).parents('form');
            var action_url = "{{ route('salary.salary_approval_cancel') }}";
            form.attr('action', action_url);
            form.submit();
        }
    });
    $(document).on('click', '.export_payslip', function() {
        var salary_id_cnt = $('input[name="salary_ids[]"]:checked').length;
        if (salary_id_cnt == 0) {
            alert('給与明細を発行する情報を選択してください。');
            return false;
        }
        if (!confirm('給与明細を出力します。よろしいですか。')) {
            return false;
        } else {
            var form = $(this).parents('form');
            var action_url = "{{ route('salary_output.export_payslip') }}";
            form.attr('action', action_url);
            form.submit();
        }
    });

    // id="export"をclickしたら発動
    $(document).on('click', '#export', function() {
        //id="year"のvalueを取得
        const year = $('#year').val();
        if (year == '') {
            alert('年を選択してください。');
            event.preventDefault();
        }

        // checkboxのuser_ids[]に1つでもチェックが入っているか確認
        if ($('input[name="user_ids[]"]:checked').length == 0) {
            alert('ユーザーを選択してください。');
            event.preventDefault();
        }

        var form = $(this).parents('form');
            var action_url = "{{ route('salary_output.export_wage_ledger') }}";
            form.attr('action', action_url);
            form.submit();

    });




});

function search() {
    // radio conditions_flg value
    const conditions_flg = $('input[name="conditions_flg"]:checked').val();

    if (conditions_flg == undefined) {
        alert('条件を選択してください。');
        event.preventDefault();
    }
    if (conditions_flg == 1) {
        if ($('#work_month').val() == '') {
            alert('年月を入力してください。');
            event.preventDefault();
        }
    }
    if (conditions_flg == 2) {
        if ($('#work_date').val() == '') {
            alert('授業実施日を入力してください。');
            event.preventDefault();
        }
    }

}
</script>
@endpush
@if (session('error_message'))
<div class="error_message bg-danger text-white text-center py-3 my-0">
    {{ session('error_message') }}
</div>
@endif

<div class="container" style="margin-bottom: 64px;">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">賃金台帳出力</div>
                <div class="panel-body">


                    {{ Form::model($user_search, ['route' => 'salary_output.export_wage_ledger_index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
                    <div class="col-xs-11">
                        <div class="panel-group" id="sampleAccordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#sampleAccordion" href="#sampleAccordionCollapse1">
                                            ▽検索条件
                                        </a>
                                    </h3>
                                </div>
                                <div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="col-xs-2 text-right">
                                                {{ Form::label('name', '名前', ['class' => 'control-label']) }}
                                            </div>
                                            <div class="col-xs-3">
                                                {{ Form::select('name', $users_name, null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
                                            </div>
                                            <div class="col-xs-2 text-right">
                                                {{ Form::label('school_building', '校舎', ['class' => 'control-label']) }}
                                            </div>
                                            <div class="col-xs-3">
                                                {{ Form::select('school_building', $school_buildings, null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <div class="col-xs-2 text-right">
                                                {{ Form::label('occupation', '職種', ['class' => 'control-label']) }}
                                            </div>
                                            <div class="col-xs-3">
                                                {{ Form::select('occupation', config('const.occupation'), null, ['placeholder' => '選択してください', 'class' => 'form-control select_search']) }}
                                            </div>
                                            <div class="col-xs-2 text-right">
                                                {{ Form::label('user_id', 'ユーザーID', ['class' => 'control-label']) }}
                                            </div>
                                            <div class="col-xs-3">
                                                {{ Form::text('user_id', null, ['placeholder' => '', 'class' => 'form-control', 'style' => 'width: 300px']) }}
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <!-- 入社年の検索項目 -->
                                            <div class="col-xs-2 text-right">
                                                {{ Form::label('join_year', '入社年', ['class' => 'control-label']) }}
                                            </div>
                                            <!-- type date only year -->
                                            <div class="col-xs-3">
                                                {{ Form::select('join_year', $hire_date_list, null, ['class' => 'form-control select_search']) }}
                                            </div>

                                        </div>

                                        <div class="text-center">
                                            <input type="submit" class="btn btn-primary" value="検索">
                                            <a href="{{ route('salary_output.export_wage_ledger_index') }}" class="btn btn-primary" style="color: white;">
                                                リセット
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                        <br />

                        <button type="button" class="btn btn-primary check_all">
                            一括選択
                        </button>

                        <div style="margin-top:8px">{{ $users_info['users_total'] }} 件中
                            {{ $users_info['first_item'] }} - {{ $users_info['last_item'] }}
                            件を表示
                        </div>


                        {{ Form::close() }}
                    </div>


                    <br />
                    <br />

                    <div class="col-xs-12" style="padding-left:0px;padding-right:0px">


                        {{ Form::model($users, ['route' => 'salary_output.export_wage_ledger', 'method' => 'POST', 'class' => 'form-horizontal']) }}

                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>選択</th>
                                    <th>ユーザーID</th>
                                    <th>非常勤氏名</th>
                                    <th>校舎</th>
                                    {{-- <th>削除</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    {{-- 選択 --}}
                                    <td> {{ Form::checkbox('user_ids[]', $user['id'], false, ['class' => 'custom-control-input', 'id' => 'salary_ids']) }}
                                    </td>
                                    {{-- ユーザーID --}}
                                    <td>{{ $user['user_id'] }}</td>
                                    {{-- アルバイト氏名 --}}
                                    <td>
                                        {{ $user->full_name }}
                                    </td>
                                    <td>
                                        {{ $user->school_buildings->name }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="" style="display: flex;">
        <div class="text-right" style="align-self: center;">
            {{ Form::label('year', '年：', ['class' => 'control-label']) }}
        </div>
        <div class="">
            {{ Form::select('year', $year, date('Y') - 1, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
        </div>
        <div class="" style="margin-left:16px">
            <button id="export" class="btn btn-primary ">
                賃金台帳出力
            </button>
        </div>
    </div>
    {{ Form::close() }}
</div>
@endsection