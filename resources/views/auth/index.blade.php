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
    // CSV出力ボタンのクリックイベント
    $('#csv-export-btn').on('click', function(e) {
        e.preventDefault();
        console.log('CSV export button clicked');
        
        // フォームを送信
        document.getElementById('csv-export-form').submit();
    });
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


});
</script>
@endpush
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">ユーザーマスタ</div>
                <div class="panel-body">

                    {{ Form::model($user_search, ['route' => 'register.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
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
                                            <div class="form-group">
                                                <div class="row">
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
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-xs-2 text-right">
                                                        {{ Form::label('employment_status', '職務', ['class' => 'control-label']) }}
                                                    </div>
                                                    <div class="col-xs-3">
                                                        {{ Form::select('employment_status', config('const.employment_status'), null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
                                                    </div>
                                                    <div class="col-xs-2 text-right">
                                                        {{ Form::label('occupation', '職種', ['class' => 'control-label']) }}
                                                    </div>
                                                    <div class="col-xs-3">
                                                        {{ Form::select('occupation', config('const.occupation'), null, ['placeholder' => '選択してください', 'class' => 'form-control select_search']) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-xs-2 text-right">
                                                        {{ Form::label('work_status', '勤務状況', ['class' => 'control-label']) }}
                                                    </div>
                                                    <div class="col-xs-3">
                                                        {{ Form::select('work_status', ['2' => '退職'], null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
                                                    </div>
                                                    <div class="col-xs-2 text-right">
                                                        {{ Form::label('user_id', 'ユーザーID', ['class' => 'control-label']) }}
                                                    </div>
                                                    <div class="col-xs-3">
                                                        {{ Form::text('user_id', null, ['placeholder' => '', 'class' => 'form-control','style' => 'width: 300px']) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">

                                                    <!-- 入社年の検索項目 -->
                                                    <div class="col-xs-2 text-right">
                                                        {{ Form::label('join_year', '入社年', ['class' => 'control-label']) }}
                                                    </div>
                                                    <!-- type date only year -->
                                                    <div class="col-xs-3">
                                                        {{ Form::select('join_year', $hire_date_list, null, ['class' => 'form-control select_search']) }}
                                                    </div>

                                                    <!-- 退社年の検索項目 -->
                                                    <div class="col-xs-2 text-right">
                                                        {{ Form::label('retire_year', '退社年月', ['class' => 'control-label']) }}
                                                    </div>
                                                    <!-- type date only year -->
                                                    <div class="col-xs-3">
                                                        {{ Form::text('retire_year', null, ['class' => 'form-control monthPick','style' => 'background-color:white;' , 'readonly']) }}
                                                    </div>

                                                    </div>
                                                    </div>

                                                <div class="form-group">
                                                    <div class="row">
                                                        <!-- 権限の検索項目 -->
                                                        <div class="col-xs-2 text-right">
                                                            {{ Form::label('roles', '権限', ['class' => 'control-label']) }}
                                                        </div>
                                                        <div class="col-xs-3">
                                                            {{ Form::select('roles', config('const.roles'), null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
                                                        </div>
                                                        <div class="col-xs-7">
                                                            <!-- 空白スペース -->
                                                        </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="text-center">
                                                        <input type="submit" class="btn btn-primary" value="検索">
                                                        <a href="{{ route('register.index') }}" class="btn btn-primary" style="color: white;">
                                                            リセット
                                                        </a>
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
                            @if (auth()->user()->roles == 1)
                            <a href="{{ route('register') }}" class="btn btn-success btn-sm" title="Add New bank">
                                新規追加
                            </a>
                            @endif

                            <!-- CSV出力ボタン -->
                            <button type="button" class="btn btn-success btn-sm" id="csv-export-btn" title="CSV Export" style="margin-left: 10px;">
                                CSV出力
                            </button>

                            <!-- CSV出力用の隠しフォーム -->
                            {{ Form::open(['route' => 'user.user_info_output', 'method' => 'POST', 'id' => 'csv-export-form', 'style' => 'display: none;']) }}
                            {{ Form::hidden('name', $user_search['name']) }}
                            {{ Form::hidden('school_building', $user_search['school_building']) }}
                            {{ Form::hidden('employment_status', $user_search['employment_status']) }}
                            {{ Form::hidden('occupation', $user_search['occupation']) }}
                            {{ Form::hidden('work_status', $user_search['work_status']) }}
                            {{ Form::hidden('user_id', $user_search['user_id']) }}
                            {{ Form::hidden('join_year', $user_search['join_year']) }}
                            {{ Form::hidden('retire_year', $user_search['retire_year']) }}
                            {{ Form::hidden('roles', $user_search['roles']) }}
                            {{ Form::close() }}
                            
                            <div style="margin-top:8px">{{ $user->total() }} 件中 {{ $user->firstItem() }} - {{ $user->lastItem() }} 件を表示</div>

                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>ユーザーID</th>
                                            <th>入社日</th>
                                            <th>退社日</th>
                                            <th>勤務状況</th>
                                            <th>名前</th>
                                            <th>校舎</th>
                                            <th>職務</th>
                                            <th>職種</th>
                                            @if (auth()->user()->roles == 1)
                                            <th>権限</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user as $item)
                                        <tr>

                                            <td>{{ $item->user_id }} </td>

                                            <td>{{ $item->hiredate ?? '' }}</td>

                                            <td>
                                                {{ $item->retirement_date ?? '' }}
                                            </td>

                                            <td>
                                                @if ($item->retirement_date)
                                                退職
                                                @else
                                                勤務
                                                @endif
                                            </td>

                                            <td>{{ $item->last_name }}{{ $item->first_name }} </td>

                                            <td>{{ $item->school_buildings->name ?? '' }} </td>
                                            <td>{{ config('const.employment_status')[$item->employment_status] }}
                                            </td>
                                            {{-- <td>{{ $item->employment_status }} </td> --}}
                                            <td>{{ config('const.occupation')[$item->occupation] ?? ''}}
                                            </td>
                                            @if (auth()->user()->roles == 1)
                                            <td>{{ config('const.roles')[$item->roles] ?? ''}}
                                            </td>
                                            @endif

                                            @if (auth()->user()->roles == 1)
                                            <td>
                                                <a href="{{ route('register.edit', ['id' => $item->id]) }}" class="btn btn-primary btn-xs" title="Edit user">編集</a>
                                            </td>

                                            <td>
                                                <!-- <form method="POST" action="/shinzemi/auth/{{ $item->id }}" class="form-horizontal" style="display:inline;"> -->
                                                <form method="POST" action="{{ route('register.destroy', ['id' => $item->id]) }}" class="form-horizontal" style="display:inline;">
                                                    {{ csrf_field() }}
                                                    <!-- {{ method_field('DELETE') }} -->
                                                    {{ method_field('PUT') }}
                                                    <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除しますか')">
                                                        削除
                                                    </button>
                                                </form>
                                            </td>

                                            @else
                                            <td><a href="{{ route('register.read', ['id' => $item->id]) }}" class="btn btn-primary btn-xs" title="Read user">閲覧</a>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="pagination-wrapper"> {!! $user->appends(request()->input())->links() !!} </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endsection