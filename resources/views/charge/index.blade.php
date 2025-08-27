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
                    width: '300px'
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
                    <div class="panel-heading">請求書出力</div>
                    <div class="panel-body">

                        {{ Form::model($student_search, ['route' => 'charge.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
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
                                                            {{ Form::select('school_year', config('const.school_year'), null, ['placeholder' => '選択', 'class' => 'select_search_grade']) }}
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
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="text-center">
                                                            <button class="btn btn-primary">検索</button>
                                                            {{-- <button class="btn">クリア</button> --}}
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
                                                <th>生徒No</th>
                                                <th>生徒氏名</th>
                                                <th>学年</th>
                                                <th>校舎名</th>
                                                <th>編集</th>
                                                <th>削除</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($students as $item)
                                                <tr>
                                                    {{-- 生徒氏名 --}}
                                                    <td>{{ $item->surname . $item->name }}</td>
                                                    {{-- 学年 --}}
                                                    <td>{{ $item->grade }}</td>
                                                    {{-- 校舎名 --}}
                                                    <td>{{ $item->schoolbuilding->name ?? '' }}</td>
                                                    <td><a href="{{ url('/shinzemi/charge/' . $item->id . '/edit') }}"
                                                            title="Edit sales" class="btn btn-primary btn-xs">編集</a>
                                                    </td>
                                                    <td>
                                                        <form method="POST" action="/charge/{{ $item->id }}"
                                                            class="form-horizontal" style="display:inline;">
                                                            {{ csrf_field() }}

                                                            {{ method_field('DELETE') }}
                                                            <button type="submit" class="btn btn-danger btn-xs"
                                                                title="Delete User"
                                                                onclick="return confirm('Confirm delete')">
                                                                削除
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="pagination-wrapper"> {!! $sales->appends(['search' => Request::get('search')])->render() !!} </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
