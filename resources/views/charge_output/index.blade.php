@extends('layouts.app')
@section('content')
    @push('css')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
        <link href="{{ asset('css/sales_index.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-datepicker3.css') }}" rel="stylesheet">
    @endpush
    @push('scripts')
        <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.ja.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
        <script>
            $(function() {
                $(".monthPick").datepicker({
                    autoclose: true,
                    language: 'ja',
                    clearBtn: true,
                    format: "yyyy-mm",
                    minViewMode: 1,
                    maxViewMode: 2
                });

                $('.select_search').select2({
                    language: "ja",
                    width: '300px'
                });
                $('.select_search_grade').select2({
                    language: "ja",
                    width: '80px'
                });
                $('.check_all').on("click", function() {
                    if ($('input[name="charge_ids[]"]:checked').length == 0) {
                        $('input[name="charge_ids[]"]').prop('checked', true);
                    } else {
                        $('input[name="charge_ids[]"]').prop('checked', false);
                    }
                });
                $(document).on('click', '.export', function() {
                    var charge_ids_cnt = $('input[name="charge_ids[]"]:checked').length;
                    if (charge_ids_cnt == 0) {
                        alert('請求書を出力する対象を選択してください。');
                        return false;
                    }
                    var form = $(this).parents('form');
                    // HTTPメソッドをPOSTに設定
                    form.attr('method', 'POST');
                    // フォームのアクションを更新
                    form.attr('action', "{{ route('charge_excel.export_charge') }}");
                    // CSRFトークンを追加
                    form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
                    // フォームを送信
                    form.submit();
                });
                $(document).on('click', '#convenience_store_flg1', function() {
                    if ($(this).prop('checked')) {
                        $('#convenience_store_flg').val(1);
                    } else {
                        $('#convenience_store_flg').val(0);
                    }
                    console.log($('#convenience_store_flg').val());
                });
            });
            array = [1, 2, 3, 4, 5];
            console.log(array);
        </script>
    @endpush
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請求書出力</div>
                    <div class="panel-body">

                        {{ Form::model($student_search, ['route' => 'charge_output.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
                        <div class="container">
                            <div class="row col-xs-11">
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
                                                    <div class="row">
                                                        <div class="col-xs-2 text-right">
                                                            {{ Form::label('month', '対象年月', ['class' => 'control-label ']) }}
                                                        </div>
                                                        <div class="col-xs-4">
                                                            {{ Form::text('month', null, ['class' => 'form-control form-name monthPick', 'readonly', 'style' => 'background-color:white']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-xs-2 text-right">
                                                            {{ Form::label('name', '生徒氏名', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-4">
                                                            {{ Form::text('last_name', null, ['placeholder' => '姓', 'class' => 'form-control form-name']) }}
                                                        </div>
                                                        <div class="col-xs-4">
                                                            {{ Form::text('first_name', null, ['placeholder' => '名', 'class' => 'form-control form-name']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-xs-2 text-right">
                                                            {{ Form::label('school_building', '学年', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-3">
                                                            {{ Form::select('school_year_start', config('const.school_year'), null, ['placeholder' => '選択', 'class' => 'select_search_grade']) }}
                                                            〜
                                                            {{ Form::select('school_year_end', config('const.school_year'), null, ['placeholder' => '選択', 'class' => 'select_search_grade']) }}
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
                                                    <div class="row">
                                                        <div class="col-xs-2 text-right">
                                                            {{ Form::label(' school', '学校', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-3">
                                                            {{ Form::select('school', $schools, null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
                                                        </div>
                                                        <div class="col-xs-2 text-right">
                                                            {{ Form::label('product', '商品', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-3">
                                                            {{ Form::select('product', $products, null, ['placeholder' => '選択してください', 'class' => 'form-control select_search']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-xs-2 text-right">
                                                            {{ Form::label('discount', '割引', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-3">
                                                            {{ Form::select('discount', $discounts, null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-xs-3 text-right">
                                                            {{ Form::radio('brothers_flg', '1', null, ['class' => 'custom-control-input ', 'id' => 'brothers_flg1']) }}
                                                            {{ Form::label('brothers_flg1', '生徒全員', ['class' => 'custom-control-label']) }}
                                                        </div>
                                                        <div class="col-xs-3 text-right">
                                                            {{ Form::radio('brothers_flg', '2', null, ['class' => 'custom-control-input ', 'id' => 'brothers_flg2']) }}
                                                            {{ Form::label('brothers_flg2', '兄弟姉妹が塾生にいる生徒のみ', ['class' => 'custom-control-label']) }}
                                                        </div>
                                                        <div class="col-xs-3">
                                                            {{ Form::radio('brothers_flg', '3', null, ['class' => 'custom-control-input ', 'id' => 'brothers_flg3']) }}
                                                            {{ Form::label('brothers_flg3', '兄弟姉妹が塾生にいない生徒のみ', ['class' => 'custom-control-label']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                {{ Form::hidden('convenience_store_flg', null, ['class' => 'custom-control-input', 'id' => 'convenience_store_flg']) }}

                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="text-center">
                                                            <button class="btn btn-primary">検索</button>
                                                            <button type="button" class="btn btn-primary" name="reset"
                                                                value="reset">
                                                                <a href="{{ route('charge_output.index') }}"
                                                                    style="color: white;">リセット</a>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                                <br />
                                <br />
                                <div style="display: flex;">

                                    <button type="button" class="btn btn-primary check_all">
                                        一括選択
                                    </button>
                                </div>
                                {{ Form::open(['route' => 'charge_excel.export_charge', 'class' => 'form-horizontal', 'id' => 'charge-form']) }}
                                <div style="margin-top:8px;text-align:right">
                                    {{ Form::checkbox('convenience_store_flg', '1', $student_search['convenience_store_flg'], ['class' => 'custom-control-input', 'id' => 'convenience_store_flg1']) }}
                                    {{ Form::label('convenience_store_flg1', 'コンビニ振込請求書', ['class' => 'custom-control-label']) }}
                                </div>

                                <div style="margin-top:8px">
                                    {{ $charges_info['charges_total'] }} 件中 {{ $charges_info['charges_first_item'] }} -
                                    {{ $charges_info['charges_last_item'] }} 件を表示
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>選択</th>
                                                <th>生徒氏名</th>
                                                <th>学年</th>
                                                <th>校舎名</th>
                                                <th>学校名</th>
                                                <th>商品名</th>
                                                <th>詳細</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($charges as $item)
                                                <tr>
                                                    {{-- 選択 --}}
                                                    <td> {{ Form::checkbox('charge_ids[]', $item->id, false, ['class' => 'custom-control-input', 'id' => 'charge_ids']) }}
                                                    </td>
                                                    {{-- 生徒氏名 --}}
                                                    <td>{{ $item->student->surname ?? '' }}{{ $item->student->name ?? '' }}
                                                    </td>
                                                    {{-- 学年 --}}
                                                    <td>{{ config('const.school_year')[$item->student->grade] }}</td>
                                                    {{-- 校舎名 --}}
                                                    <td>{{ $item->student->schoolbuilding->name ?? '' }}</td>
                                                    {{-- 学校名 --}}
                                                    <td>{{ $item->student->school->name ?? '' }}</td>
                                                    {{-- 商品名 --}}
                                                    <td>{{ $item->charge_detail[0]->product->name ?? '' }}</td>
                                                    {{-- 詳細 --}}
                                                    <td>
                                                        <a href="{{ route('invoice.index', ['student_no' => $item->student->student_no]) }}"
                                                            class="btn btn-primary">詳細</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{-- <div class="form-group row mb-0">
								<div class="col-md-6 offset-md-4">
									{{ Form::select('invoice_comment', $invoice_comment, ['class' => 'form-control']) }}
								</div>
							</div> --}}
                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button class="btn btn-primary export">
                                            出力
                                        </button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
