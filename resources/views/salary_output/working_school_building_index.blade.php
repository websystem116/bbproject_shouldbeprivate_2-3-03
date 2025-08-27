@extends('layouts.app')
@section('content')
    @push('css')
        <link href="{{ asset('/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
    @endpush

    @push('scripts')
        <script type="text/javascript" src="{{ asset('/js/bootstrap-datepicker.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/bootstrap-datepicker.ja.min.js') }}"></script>

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
                    <div class="panel-heading">校舎別非常勤給与一覧出力(勤務校舎)</div>
                    <div class="panel-body">


                        <div class="form-group row">
                            {{ Form::open(['route' => ['salary_output.export_worked_school_building_salary_list'], 'method' => 'POST', 'class' => 'form-horizontal']) }}
                            <div class="col-md-1 col-form-label m-0">
                                {{ Form::label('year_month', '年度') }}
                            </div>
                            <div class="col-md-2 m-0">
                                {{ Form::text('year_month', null, ['class' => 'form-control monthPick', 'readonly', 'id' => 'year', 'placeholder' => '年月', 'style' => 'background-color:white']) }}
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-md-2 ">
                                {{ Form::submit('校舎別非常勤給与一覧出力', ['class' => 'btn btn-primary', 'onfocus' => 'this.blur();']) }}
                            </div>
                        </div> <br />
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
