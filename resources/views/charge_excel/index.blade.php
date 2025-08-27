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
            maxViewMode: 2,
        });
    });
</script>
@endpush

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"> 帳票出力 </div>
                <div class="panel-body">
                    <!--校舎別売上明細-->
                    {{ Form::open(['route' => 'charge_excel.export_school_building_sales', 'class' => 'form-horizontal']) }}
                    <div class="form-group row">
                        <div class="col-md-3">
                            {{ Form::submit('校舎別売上明細', ['class' => 'btn btn-primary']) }}
                        </div>
                        <div class="col-md-4">
                            {{ Form::select('school_building_id', $school_buildings, null, ['placeholder' => '校舎名', 'class' => 'select_search']) }}
                        </div>

                        <div class="col-md-2">
                            {{ Form::text('sale_month', null, ['placeholder' => '年月', 'class' => 'form-control form-name monthPick', 'readonly', 'style' => 'background-color:white']) }}

                        </div>
                    </div>
                    {{ Form::close() }}

                    <!--校舎別売上明細-->
                    <!--校舎別請求明細-->
                    {{ Form::open(['route' => 'charge_excel.export_school_building_charge', 'class' => 'form-horizontal']) }}
                    <div class="form-group row" style="display: flex;">
                        <div class="col-md-3">
                            {{ Form::submit('校舎別請求明細', ['class' => 'btn btn-primary']) }}
                        </div>
                        <div class="col-md-4">
                            {{ Form::select('school_building_id', $school_buildings, null, ['placeholder' => '校舎名', 'class' => 'select_search']) }}
                        </div>

                        <div class="col-md-2">
                            {{ Form::text('charge_month', null, ['placeholder' => '年月', 'class' => 'form-control form-name monthPick', 'readonly', 'style' => 'background-color:white']) }}
                        </div>

                        <div class="col-md-2" style="align-self: center;">
                            {{ Form::checkbox('payment_failed_flg', 1, false, ['class' => 'range', 'id' => 'payment_failed_flg1']) }}
                            {{ Form::label('payment_failed_flg1', '引落失敗者のみ出力', ['class' => 'custom-control-label']) }}
                        </div>
                    </div>
                    {{ Form::close() }}

                    <!--校舎別請求明細-->
                    <!--校舎別入金明細-->
                    {{ Form::open(['route' => 'charge_excel.export_school_building_payment', 'class' => 'form-horizontal']) }}
                    <div class="form-group row">
                        <div class="col-md-3">
                            {{ Form::submit('校舎別入金明細', ['class' => 'btn btn-primary']) }}
                        </div>

                        <div class="col-md-4">
                            {{ Form::select('school_building_id', $school_buildings, null, ['placeholder' => '校舎名', 'class' => 'select_search']) }}
                        </div>

                        <div class="col-md-2">
                            {{ Form::text('payment_month', null, ['placeholder' => '年月', 'class' => 'form-control form-name monthPick', 'readonly', 'style' => 'background-color:white']) }}
                        </div>

                        <div class="col-md-2">
                            {{ Form::select('pay_method', config('const.pay_method'), null, ['placeholder' => '入金方法', 'class' => 'range form-control']) }}
                        </div>
                    </div>
                    {{ Form::close() }}

                    <!--校舎別入金明細-->

                    <!--年間総売上-->
                    {{ Form::open(['route' => 'charge_excel.export_year_sales', 'class' => 'form-horizontal']) }}
                    <div class="form-group row">

                        <div class="col-md-3">
                            {{ Form::submit('年間総売上', ['class' => 'btn btn-primary']) }}
                        </div>

                        <div class="col-md-2">
                            {{ Form::number('year', date('Y'), ['placeholder' => '', 'class' => 'form-control']) }}
                        </div>

                    </div>
                    {{ Form::close() }}
                    <!--年間総売上-->

                    <!--月別売上明細-->
                    {{ Form::open(['route' => 'charge_excel.export_month_sales', 'class' => 'form-horizontal']) }}
                    <div class="form-group row">

                        <div class="col-md-3">
                            {{ Form::submit('月別売上明細', ['class' => 'btn btn-primary']) }}
                        </div>

                        <div class="col-md-5" style="display:flex;align-items: center;">

                            {{ Form::text('sale_month_start', null, ['placeholder' => '年月', 'class' => 'form-control form-name monthPick', 'readonly', 'style' => 'background-color:white']) }}

                            <div>〜</div>

                            {{ Form::text('sale_month_end', null, ['placeholder' => '年月', 'class' => 'form-control form-name monthPick', 'readonly', 'style' => 'background-color:white']) }}

                        </div>

                    </div>
                    {{ Form::close() }}

                    <!--月別売上明細-->
                    <!--銀行引落コンビニ振込等対照表-->
                    {{ Form::open(['route' => 'charge_excel.export_withdrawal', 'class' => 'form-horizontal']) }}
                    <div class="form-group row">
                        <div class="col-md-3">
                            {{ Form::submit('銀行引落コンビニ振込等対照表', ['class' => 'btn btn-primary']) }}
                        </div>
                        <div class="col-md-2">
                            {{ Form::number('year', date('Y'),  ['placeholder' => '年度', 'class' => 'form-control']) }}
                        </div>
                    </div>
                    {{ Form::close() }}

                    <!--銀行引落コンビニ振込等対照表-->
                    <!--入塾者初回売上チェック-->
                    {{ Form::open(['route' => 'charge_excel.export_first_sales', 'class' => 'form-horizontal']) }}
                    <div class="form-group row">

                        <div class="col-md-3">
                            {{ Form::submit('入塾者初回売上チェック', ['class' => 'btn btn-primary']) }}
                        </div>


                        <div class="col-md-5" style="display: flex;align-items: center;">

                            {{ Form::date('juku_start_date_start', null, ['placeholder' => '入塾日', 'class' => 'form-control']) }}

                            <div>
                                〜
                            </div>

                            {{ Form::date('juku_start_date_end', null, ['placeholder' => '入塾日', 'class' => 'form-control']) }}

                        </div>

                    </div>
                    {{ Form::close() }}

                    <!--入塾者初回売上チェック-->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection