@extends('layouts.app')
@section('content')
@push('css')
<link href="{{ asset('css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.ja.min.js') }}"></script>
<script>
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
});
</script>
@endpush
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">非常勤給与振込データ作成</div>
                <div class="panel-body">
                    <div class="form-group row">
                        {{ Form::open(['route' => ['salary_output.export_salary'], 'method' => 'GET', 'class' => 'form-horizontal']) }}
                        <div class="col-md-1 col-form-label m-0">
                            {{ Form::label('year_month', '年月') }}
                        </div>
                        <div class="col-md-2 m-0">
                            {{ Form::text('year_month', null, ['class' => 'form-control monthPick', 'readonly', 'id' => 'year', 'placeholder' => '年月', 'style' => 'background-color:white']) }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-1 col-form-label m-0">
                            {{ Form::label('date', '振込日') }}
                        </div>

                        <div class="col-md-2 m-0">
                            {{ Form::date('date', null, ['class' => 'form-control', '', 'id' => 'year', 'placeholder' => '', 'style' => 'background-color:white']) }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2 ">
                            {{ Form::submit('出力', ['class' => 'btn btn-primary', 'onfocus' => 'this.blur();','onclick="return confirm(\'非常勤給与振込データを出力しますか？\')"']) }}
                        </div>
                    </div>
                    <br />
                    <br />
                    @if (session('error'))
                    <div class="message bg-warning text-warning text-center py-3 my-0">
                        {{ session('error') }}<br>
                        {{ session('error_user') }}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection