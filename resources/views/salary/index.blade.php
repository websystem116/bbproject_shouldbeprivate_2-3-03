@extends('layouts.app')
@section('content')
    @push('css')
        <link href="{{ asset('css/bootstrap-datepicker3.css') }}" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
        <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.ja.min.js') }}"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>

        <script>
            $(function() {
                $('.select_search').select2({
                    language: "ja",
                    width: '300px'
                });
                $('.check_all').on("click", function() {
                    if ($('input[name="salary_ids[]"]:checked').length == 0) {
                        $('input[name="salary_ids[]"]').prop('checked', true);
                    } else {
                        $('input[name="salary_ids[]"]').prop('checked', false);
                    }
                });
            });
            $(function() {

                // monthPick
                var currentTime = new Date();
                var year = currentTime.getFullYear();
                var year2 = parseInt(year) + 10;

                $(".monthPick").datepicker({
                    autoclose: true,
                    language: 'ja',
                    clearBtn: true,
                    format: "yyyy-mm",
                    minViewMode: 1,
                    maxViewMode: 2
                });
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
                $(document).on('click', '.month_approval', function() {
                    var salary_id_cnt = $('input[name="salary_ids[]"]:checked').length;
                    if (salary_id_cnt == 0) {
                        alert('月末承認する情報を選択してください。');
                        return false;
                    }
                    if (!confirm('月末承認します。よろしいですか。')) {
                        return false;
                    } else {
                        var form = $(this).parents('form');
                        var action_url = "{{ route('salary.month_approval') }}";
                        form.attr('action', action_url);
                        form.submit();
                    }
                });

                $(document).on('click', '.month_approval_cancel', function() {
                    var salary_id_cnt = $('input[name="salary_ids[]"]:checked').length;
                    if (salary_id_cnt == 0) {
                        alert('月末承認を解除する情報を選択してください。');
                        return false;
                    }
                    if (!confirm('月末承認を解除します。よろしいですか。')) {
                        return false;
                    } else {
                        var form = $(this).parents('form');
                        var action_url = "{{ route('salary.month_approval_cancel') }}";
                        form.attr('action', action_url);
                        form.submit();
                    }
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

    <div class="container" style="width:95%">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">非常勤業務明細一覧</div>
                    <div class="panel-body">
                        @if (session('error_message'))
                            <div class="error_message alert alert-danger text-center py-3 my-0">
                                {!! nl2br(e(session('error_message'))) !!}
                            </div>
                        @endif


                        {{ Form::model($salary_search, ['route' => 'salary.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
                        <div class="container" style="width:95%">
                            <div class="row col-xs-12">
                                <div class="panel-group" id="sampleAccordion">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#sampleAccordion"
                                                    href="#sampleAccordionCollapse1">
                                                    ▽検索条件
                                                </a>
                                            </h3>
                                        </div>
                                        <div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <div class="row" style="margin-left: 32px;margin-top: 32px;">
                                                        <div class="col-xs-1 text-right">
                                                            {{ Form::radio('conditions_flg', '1', null, ['class' => 'custom-control-input', 'id' => 'conditions_flg1']) }}
                                                            {{ Form::label('conditions_flg1', '条件1', ['class' => 'custom-control-label']) }}
                                                        </div>
                                                        <div class="col-xs-2 text-right" style="width:100px;">
                                                            {{ Form::label('work_month', '年月', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-2">
                                                            {{ Form::text('work_month', null, ['placeholder' => '年月', 'class' => 'form-control form-name monthPick', 'readonly', 'style' => 'background-color:white']) }}
                                                        </div>
                                                        <div class="col-xs-2 text-right">
                                                            {{ Form::label('school_building', '校舎', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-3">
                                                            {{ Form::select('school_building', $school_buildings, null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <div class="row" style="margin-left: 32px;">
                                                        <div class="col-xs-1 text-right">
                                                            {{ Form::radio('conditions_flg', '2', null, ['class' => 'custom-control-input', 'id' => 'conditions_flg2']) }}
                                                            {{ Form::label('conditions_flg2', '条件2', ['class' => 'custom-control-label']) }}
                                                        </div>
                                                        <div class="col-xs-2 text-right" style="width:100px">
                                                            {{ Form::label('work_date', '授業実施日', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-2">
                                                            {{ Form::date('work_date', null, ['class' => 'form-control']) }}
                                                        </div>
                                                        <div class="col-xs-2 text-right">
                                                            {{ Form::label('school_building2', '授業実施校舎', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-3">
                                                            {{ Form::select('school_building2', $school_buildings, null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="text-center">
                                                                <button class="btn btn-primary"
                                                                    onclick="search()">検索</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div style="margin-top:8px">
                                        {{ $daily_salary_info['total'] }} 件中 {{ $daily_salary_info['firstItem'] }} -
                                        {{ $daily_salary_info['lastItem'] }} 件を表示
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-1 offset-md-1">
                                            <button type="button" class="btn btn-primary check_all">
                                                一括選択
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                                <br />
                                <br />

                                {{ Form::model($list, ['route' => 'salary.index', 'method' => 'POST', 'class' => 'form-horizontal']) }}

                                <div class="table">

                                    <table class="table table-borderless">
                                        <thead>

                                            <tr>
                                                <th>選択</th>
                                                <th>支給/控除</th>
                                                <th>ユーザーID</th>
                                                <th>アルバイト氏名</th>
                                                @foreach ($job_descriptions as $job_description)
                                                    <th>{{ $job_description->name }}<br>(時間)</th>
                                                @endforeach
                                                <th>その他支給額</th>
                                                <th>その他控除額</th>
                                                <th>年末調整</th>
                                                <th>交通費</th>
                                                <th>月末承認</th>
                                                <th>上長承認</th>
                                                <th>給与承認</th>
                                                <th>講師確認</th>
                                                <th>編集</th>
                                                {{-- <th>削除</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($list as $item)
                                                <tr>
                                                    {{-- 選択 --}}
                                                    <td> {{ Form::checkbox('salary_ids[]', $item['id'], false, ['class' => 'custom-control-input', 'id' => 'salary_ids']) }}
                                                    </td>
                                                    {{-- 支給/控除 --}}
                                                    <td><a href="{{ url('/shinzemi/salary/' . $item['id'] . '/deduction/' . $work_month) }}"
                                                            class="btn btn-primary btn-xs" title="Edit salary">支給/控除</a>
                                                    </td>
                                                    {{-- ユーザーID --}}
                                                    <td class="text-center">{{ $item['user_id'] }}</td>
                                                    {{-- アルバイト氏名 --}}
                                                    <td class="text-right">
                                                        {{ $item['name'] }}
                                                    </td>
                                                    {{-- 業務内容 --}}
                                                    @foreach ($job_descriptions as $job_description)
                                                        <td class="text-right">
                                                            @if (isset($item[$job_description->id]))
                                                                <!-- {{ $item[$job_description->id] ?? '0' }} -->
                                                                {{ floor(($item[$job_description->id] * 10) / 60) / 10 }}h
                                                            @else
                                                                0h
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    {{-- その他支給額 --}}
                                                    <td class="text-right">
                                                        {{ number_format($item['other_payment_amount']) ?? 0 }}
                                                    </td>
                                                    {{-- その他控除額 --}}
                                                    <td class="text-right">
                                                        {{ number_format($item['other_deduction_amount']) ?? 0 }}
                                                    </td>
                                                    {{-- 年末調整 --}}
                                                    <td class="text-right">
                                                        {{ number_format($item['year_end_adjustment']) ?? 0 }}
                                                    </td>
                                                    {{-- 交通費 --}}
                                                    <td class="text-right">
                                                        {{ number_format($item['transportation_expenses']) ?? 0 }}
                                                    </td>
                                                    {{-- 月末承認 --}}
                                                    <td>{{ $item['monthly_completion'] ?? '' }}</td>
                                                    {{-- 上長承認 --}}
                                                    <td>{{ $item['monthly_completion'] ?? '' }}</td>
                                                    {{-- 給与承認 --}}
                                                    <td>{{ $item['salary_approval'] ?? '' }}</td>
                                                    <td>
                                                        @if ($item['salary_confirmation'] == '済')
                                                            <span style="color: red;">✗</span>
                                                        @else
                                                            &nbsp;
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('/shinzemi/salary/' . $item['id'] . '/approval_edit/' . $work_date) }}"
                                                            class="btn btn-primary btn-xs" title="Edit salary">編集</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ Form::hidden('work_month', $work_month) }}
                                <div class="form-group row mb-0">
                                    <div class="col-md-1 offset-md-1">
                                        <button class="btn btn-primary salary_approval">
                                            給与承認
                                        </button>
                                    </div>
                                    <div class="col-md-2 offset-md-1">
                                        <button class="btn btn-primary salary_approval_cancel">
                                            給与承認解除
                                        </button>
                                    </div>
                                    <div class="col-md-2 offset-md-2">
                                        <button class="btn btn-primary  export_payslip">
                                            給与明細出力
                                        </button>
                                    </div>
                                    <div class="col-md-1 offset-md-2">
                                        <button class="btn btn-primary  month_approval">
                                            月末承認
                                        </button>
                                    </div>
                                    <div class="col-md-1 offset-md-2">
                                        <button class="btn btn-primary  month_approval_cancel">
                                            月末承認解除
                                        </button>
                                    </div>
                                    {{ Form::close() }}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endsection
