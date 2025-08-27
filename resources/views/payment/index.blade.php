@extends('layouts.app')
@section('content')
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<link href="{{ asset('css/sales_index.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script>
    $(function() {
        $('.select_search').select2({
            language: "ja",
            width: '150px'
        });
        $('.select_search2').select2({
            language: "ja",
            width: '150px'
        });
        $('.select_search3').select2({
            language: "ja",
            width: '150px'
        });
        $('.select_search_grade').select2({
            language: "ja",
            width: '80px'
        });
    });
</script>
@endpush
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">コンビニ振込等登録</div>
                <div class="panel-body">

                    {{ Form::open(['route' => 'payment.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
                    <div class="container">
                        <div class="row col-xs-11">
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

                                            <div class="form-group" style="display:flex;">
                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('number', '管理No', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="">
                                                    {{ Form::number('id_start',$requestData['id_start'] ?? null, ['placeholder' => '管理No', 'class' => 'form-control ']) }}
                                                </div>

                                                <div class="text-center">
                                                    {{ Form::label('wave', '～', ['class' => 'control-label','style' => 'padding-right:8px;padding-left:8px;']) }}
                                                </div>

                                                <div class="">
                                                    {{ Form::number('id_end', $requestData['id_end'] ?? null, ['placeholder' => '管理No', 'class' => 'form-control ']) }}
                                                </div>

                                            </div>

                                            <div class="form-group" style="display:flex;">

                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('number', '生徒No', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="">
                                                    {{ Form::number('no_start',$requestData['no_start'] ?? null, ['placeholder' => '生徒No', 'class' => 'form-control ']) }}
                                                </div>

                                                <div class="text-center">
                                                    {{ Form::label('wave', '～', ['class' => 'control-label','style' => 'padding-right:8px;padding-left:8px;']) }}
                                                </div>

                                                <div class="">
                                                    {{ Form::number('no_end', $requestData['no_end'] ?? null, ['placeholder' => '生徒No', 'class' => 'form-control ']) }}
                                                </div>

                                            </div>

                                            <div class="form-group" style="display:flex;">

                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('name', '生徒氏名', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="" style="width:15%;">
                                                    {{ Form::text('surname',$requestData['surname'] ?? null, ['placeholder' => '姓', 'class' => 'form-control']) }}
                                                </div>

                                                <div class="" style="width:15%;padding-left: 8px;">
                                                    {{ Form::text('name', $requestData['name'] ?? null, ['placeholder' => '名', 'class' => 'form-control']) }}
                                                </div>

                                                <div class="" style="width:15%;padding-left: 8px;">
                                                    {{ Form::text('surname_kana', $requestData['surname_kana'] ?? null, ['placeholder' => '姓カナ', 'class' => 'form-control']) }}
                                                </div>

                                                <div class="" style="width:15%;padding-left: 8px;">
                                                    {{ Form::text('name_kana', $requestData['name_kana'] ?? null, ['placeholder' => '名カナ', 'class' => 'form-control']) }}
                                                </div>

                                            </div>

                                            <div class="form-group" style="display:flex;">
                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('phone', '電話番号', ['class' => 'control-label']) }}
                                                </div>
                                                <div class="">
                                                    {{ Form::text('phone', $requestData['phone'] ?? null, ['placeholder' => '電話番号', 'class' => 'form-control']) }}
                                                </div>
                                                <div class="text-left" style="padding-left:16px;">
                                                    {{ Form::label('school_name', '学校名', ['class' => 'control-label ']) }}
                                                </div>
                                                <div class="" style="padding-left:16px;">
                                                    {{ Form::select('school_id',$schools_select_list,$requestData['school_id'] ?? null, ['placeholder' => '選択してください', 'class' => 'form-control select_search2']) }}
                                                </div>

                                                <div style="display:flex;flex-wrap: wrap;padding-left: 24px;">

                                                    <div class="" style="width:33%">
                                                        <label>
                                                            {{Form::checkbox('brothers_flg', '1',$requestData['brothers_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'brothers_flg'])}}兄弟姉妹
                                                        </label>
                                                    </div>

                                                    <div class="" style="width:33%">
                                                        <label>
                                                            {{Form::checkbox('fatherless_flg', '1',$requestData['fatherless_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'fatherless_flg'])}}ひとり親家庭
                                                        </label>
                                                    </div>

                                                    <div class="" style="width:33%">
                                                        <label>
                                                            {{Form::checkbox('temporary_flg', '1', $requestData['temporary_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'temporary_flg'])}}仮入塾
                                                        </label>
                                                    </div>

                                                    <div class="" style="width:33%">
                                                        <label>
                                                            {{Form::checkbox('rest_flg', '1', $requestData['rest_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'rest_flg'])}}休塾者
                                                        </label>
                                                    </div>

                                                    <div class="" style="width:33%">
                                                        <label>
                                                            {{Form::checkbox('graduation_flg', '1', $requestData['graduation_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'graduation_flg'])}}卒塾者
                                                        </label>
                                                    </div>

                                                    <div class="" style="width:33%">
                                                        <label>
                                                            {{Form::checkbox('withdrawal_flg', '1', $requestData['withdrawal_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'withdrawal_flg'])}}退塾者
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="form-group" style="display:flex;">

                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('greade', '学年', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="">
                                                    {{ Form::select('grade_start', config('const.school_year'),$requestData['grade_start'] ?? null,['placeholder' => '選択してください', 'class' => 'form-control select_search2']) }}
                                                </div>

                                                <div class="text-center">
                                                    {{ Form::label('wave', '～', ['class' => 'control-label','style' => 'padding-right:8px;padding-left:8px;']) }}
                                                </div>

                                                <div class="">
                                                    {{ Form::select('grade_end', config('const.school_year'), $requestData['grade_end'] ?? null,['placeholder' => '選択してください', 'class' => 'form-control select_search2']) }}
                                                </div>

                                                <div class="text-left" style="padding-left:100px;">
                                                    {{ Form::label('school_building', '校舎名', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="" style="padding-left:16px;">
                                                    {{ Form::select('school_building_id',$schooolbuildings_select_list,$requestData['school_building_id'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
                                                </div>
                                            </div>

                                            <div class="form-group" style="display:flex;">
                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('product_select', '商品名', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="">
                                                    {{ Form::select('product_select',$products_select_list,$requestData['product_select'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
                                                </div>

                                                <div class="text-left" style="padding-left:280px;">
                                                    {{ Form::label('discount_select', '割引', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="" style="padding-left:32px;">
                                                    {{ Form::select('discount_select',$discounts_select_list,$requestData['discount_select'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
                                                </div>

                                            </div>

                                            <div class="form-group" style="display:flex;">
                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('suggested_school', '進学先', ['class' => 'control-label']) }}
                                                </div>
                                                <div class="">
                                                    {{ Form::text('suggested_school', $requestData['suggested_school'] ?? null, ['placeholder' => '進学先名', 'class' => 'form-control form-name']) }}
                                                </div>
                                            </div>

                                            <div class="form-group" style="display:flex;">
                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('juku_start_date', '入塾日', ['class' => 'control-label']) }}
                                                </div>
                                                <div class="">
                                                    {{ Form::date('juku_start_date', $requestData['juku_start_date'] ?? null, ['class' => 'form-control', 'id' => 'juku_start_date']) }}

                                                </div>
                                                <div class="text-center">
                                                    {{ Form::label('wave', '～', ['class' => 'control-label','style' => 'padding-right:8px;padding-left:8px;']) }}
                                                </div>
                                                <div class="">
                                                    {{Form::date('juku_end_date', $requestData['juku_end_date'] ?? null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                </div>
                                                <div class="text-left" style="padding-left: 130px;">
                                                    {{ Form::label('juku_start_date', '卒塾日', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="" style="padding-left:16px;">
                                                    {{Form::date('juku_graduation_start_date', $requestData['juku_graduation_start_date'] ?? null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                </div>
                                                <div class="text-center">
                                                    {{ Form::label('wave', '～', ['class' => 'control-label','style' => 'padding-right:8px;padding-left:8px;']) }}
                                                </div>
                                                <div class="">
                                                    {{Form::date('juku_graduation_end_date', $requestData['juku_graduation_end_date'] ?? null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                </div>
                                            </div>

                                            <div class="form-group" style="display:flex;">
                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('juku_start_date', '復塾日', ['class' => 'control-label']) }}
                                                </div>
                                                <div class="">
                                                    {{Form::date('juku_return_start_date', $requestData['juku_return_start_date'] ?? null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                </div>
                                                <div class="text-center">
                                                    {{ Form::label('wave', '～', ['class' => 'control-label','style' => 'padding-right:8px;padding-left:8px;']) }}
                                                </div>
                                                <div class="">
                                                    {{Form::date('juku_return_end_date', $requestData['juku_return_end_date'] ?? null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                </div>
                                            </div>

                                            <div class="form-group" style="display:flex;">
                                                <div class="text-left" style="width:15%;padding-left: 16px;">
                                                    {{ Form::label('juku_withdrawal_date', '退塾日', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="">
                                                    {{Form::date('juku_withdrawal_start_date',$requestData['juku_withdrawal_start_date'] ?? null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                </div>

                                                <div class="text-center">
                                                    {{ Form::label('wave', '～', ['class' => 'control-label','style' => 'padding-right:8px;padding-left:8px;']) }}
                                                </div>

                                                <div class="">
                                                    {{Form::date('juku_withdrawal_end_date',$requestData['juku_withdrawal_end_date'] ?? null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                </div>

                                                <div class="text-left" style="padding-left: 130px;">
                                                    {{ Form::label('juku_rest_date', '休塾日', ['class' => 'control-label']) }}
                                                </div>

                                                <div class="" style="padding-left:16px;">
                                                    {{Form::date('juku_rest_start_date', $requestData['juku_rest_start_date'] ?? null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                </div>

                                                <div class="text-center">
                                                    {{ Form::label('wave', '～', ['class' => 'control-label','style' => 'padding-right:8px;padding-left:8px;']) }}
                                                </div>

                                                <div class="">
                                                    {{Form::date('juku_rest_end_date', $requestData['juku_rest_end_date'] ?? null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="text-center">
                                                        <button name="search" class="btn btn-primary" value="true">検索</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                            {{-- <a href="{{ url("salary/create") }}" class="btn btn-success btn-sm" title="Add New salary">
                            新規追加
                            </a> --}}

                            {{-- <form method="GET" action="{{ url("salary") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search...">
                                <span class="input-group-btn">
                                    <button class="btn btn-info" type="submit">
                                        <span>検索</span>
                                    </button>
                                </span>
                            </div>
                            </form> --}}


                            <br />
                            <br />

                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>生徒氏名</th>
                                            <th>学年</th>
                                            <th>校舎名</th>
                                            <th>編集</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $item)
                                        <tr>
                                            {{-- 生徒氏名 --}}
                                            <td>{{ $item->surname . $item->name }}</td>
                                            {{-- 学年 --}}
                                            <td>{{ config('const.school_year')[$item->grade] ?? '' }}</td>
                                            {{-- 校舎名 --}}
                                            <td>{{ $item->schoolbuilding->name ?? '' }}</td>
                                            <td><a href="{{ url('/shinzemi/payment/' . $item->id . '/edit') }}" title="Edit payment" class="btn btn-primary btn-xs">編集</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination-wrapper">
                                {!! $students->appends(request()->input())->links() !!}
                            </div>

                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection