@extends('layouts.app')
@section('content')
    @push('css')
        <link href="{{ asset('/css/salary_edit.css') }}" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="{{ asset('/js/salary_edit.js') }}"></script>
        <script>
            $(function() {
                $(document).on("change", ".tranceportation_round_trip_flg", function() {
                    if ($(this).prop('checked')) {
                        var tranceportation_unit_price = $(this).parent().parent().find(
                            ".tranceportation_unit_price");
                        var tranceportation_unit_price_value = tranceportation_unit_price.val();
                        var tranceportation_fare = $(this).parent().parent().find(
                            ".tranceportation_fare");
                        var tranceportation_fare_value = tranceportation_fare.val();
                        if (tranceportation_unit_price_value != "") {
                            tranceportation_fare.val(tranceportation_unit_price_value * 2);
                        }
                    } else {
                        var tranceportation_fare = $(this).parent().parent().find(
                            ".tranceportation_fare");
                        tranceportation_fare.val("");

                    }
                });
                $(document).on("change", ".tranceportation_unit_price", function() {
                    var tranceportation_round_trip_flg = $(this).parent().parent().find(
                        ".tranceportation_round_trip_flg");
                    if (tranceportation_round_trip_flg.prop('checked')) {
                        var tranceportation_unit_price_value = $(this).val();
                        var tranceportation_fare = $(this).parent().parent().find(".tranceportation_fare");
                        if (tranceportation_unit_price_value != "") {
                            tranceportation_fare.val(tranceportation_unit_price_value * 2);
                        }
                    }
                });


            });
        </script>
    @endpush
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">非常勤勤怠登録</div>
                    <div class="panel-body">

                        <!-- <a href="{{ url('/shinzemi/salary') }}" title="Back">
                                                                <button class="btn btn-warning btn-xs">戻る</button>
                                                            </a> -->

                        <a href="{{ url()->previous() }}" title="Back">
                            <button class="btn btn-warning btn-xs">戻る</button>
                        </a>
                        <a href="{{ route('register.myedit') }}" class="btn btn-success btn-sm" title="ユーザー情報">
                            ユーザー情報
                        </a>
                        <br />
                        <br />
                        {{ Form::model($users, ['route' => ['salary.edit', $users->id, $date], 'method' => 'GET', 'class' => 'form-horizontal']) }}
                        <div class="form-group">
                            <label for="code" class="col-md-1 control-label">ユーザーID: </label>
                            <div class="col-md-1 disp-value">
                                {{ $users->user_id }}
                            </div>

                            <label for="code" class="col-md-1 control-label">非常勤氏名: </label>
                            <div class="col-md-1 disp-value">
                                {{ $users->full_name }}
                            </div>
                            <label for="code" class="col-md-1 control-label">校舎名: </label>
                            <div class="col-md-2 disp-value">
                                {{ $users->school_buildings->name }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="code" class="col-md-1 control-label">出勤日: </label>
                            <div class="col-md-2 disp-value">
                                {{ Form::date('working_date', $working_date, ['class' => 'date form-control']) }}
                            </div>
                            <div class="col-md-6 offset-md-4 disp-value">
                                <button type="submit" class="btn btn-primary ">
                                    検索
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        {{ Form::model($users, ['route' => ['salary.update', $users->id], 'method' => 'PUT', 'class' => 'form-horizontal']) }}
                        {{ csrf_field() }}
                        {{ Form::hidden('working_date', $working_date) }}
                        <div class="panel panel-default">
                            <div class="panel-heading">業務実績
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">選択</th>
                                        <th scope="col">実施校舎</th>
                                        <th scope="col">業務内容</th>
                                        <th scope="col">時間(分)</th>
                                        <th scope="col">備考</th>
                                    </tr>
                                </thead>
                                <tbody class='performance_tr'>
                                    @if ($daily_salaries->count() == 0)
                                        {{ Form::hidden('performance_cnt', 1) }}
                                        <tr class='performance_row'>
                                            <td>
                                                {{ Form::checkbox('apploval_flg[]', '1', false, ['class' => 'form-control checkbox_size custom-control-input select_flg', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('school_building[]', $school_buildings, false, ['placeholder' => '選択してください', 'class' => 'form-control  school_building', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('job_description[]', $job_descriptions, false, ['placeholder' => '選択してください', 'class' => 'form-control  job_description', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::number('working_time[]', null, ['class' => 'form-control  working_time', $readonly]) }}

                                            </td>
                                            <td>
                                                {{ Form::textarea('remarks[]', null, ['class' => 'form-control remarks', 'placeholder' => '備考', 'rows' => '1', $readonly]) }}
                                            </td>
                                        </tr>
                                    @else
                                        {{ Form::hidden('performance_cnt', $daily_salaries->count()) }}
                                        @foreach ($daily_salaries as $index => $value)
                                            @if ($index == 0)
                                                <tr class='performance_row'>
                                                @else
                                                <tr class='performance_add_row'>
                                            @endif
                                            <td>
                                                {{ Form::checkbox('select_flg[]', '1', false, ['class' => 'form-control checkbox_size custom-control-input select_flg', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('school_building[]', $school_buildings, $value->school_building_id, ['placeholder' => '選択してください', 'class' => 'form-control  school_building', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('job_description[]', $job_descriptions, $value->job_description_id, ['placeholder' => '選択してください', 'class' => 'form-control  job_description', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::number('working_time[]', $value->working_time, ['class' => 'form-control  working_time', $disable]) }}

                                            </td>
                                            <td>
                                                {{ Form::textarea('remarks[]', $value->remarks ?? '', ['class' => 'form-control remarks', 'placeholder' => '備考', 'rows' => '1', $disable]) }}
                                            </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @if ($display_none == false)
                                <div class="panel-footer">
                                    {{ Form::button('追加', ['class' => 'btn btn-success add-performance-row']) }}
                                    {{ Form::button('削除', ['class' => 'btn btn-danger delete-performance-row']) }}
                                </div>
                            @endif

                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">交通費</div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">選択</th>
                                        <th scope="col">校舎</th>
                                        <th scope="col">路線名</th>
                                        <th scope="col">乗車駅</th>
                                        <th scope="col">降車駅</th>
                                        <th scope="col">単価(円)</th>
                                        <th scope="col">往復</th>
                                        <th scope="col">運賃(円)</th>
                                        <th scope="col">備考</th>
                                    </tr>
                                </thead>
                                <tbody class='tranceportation_tr'>
                                    @if ($transportation_expenses->count() == 0)
                                        {{ Form::hidden('tranceportation_cnt', 1) }}
                                        <tr class='tranceportation_row'>
                                            <td>
                                                {{ Form::checkbox('tranceportation_select_flg[]', '1', false, ['class' => 'form-control checkbox_size custom-control-input tranceportation_select_flg', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('tranceportation_school_building[]', $school_buildings, false, ['placeholder' => '選択してください', 'class' => 'form-control  form_text  tranceportation_school_building', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::text('tranceportation_route[]', null, ['class' => 'form-control form_text tranceportation_route', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::text('tranceportation_boarding_station[]', null, ['class' => 'form-control  form_text tranceportation_boarding_station', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::text('tranceportation_get_off_station[]', null, ['class' => 'form-control  form_text tranceportation_get_off_station', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::number('tranceportation_unit_price[]', null, ['class' => 'form-control form_price  tranceportation_unit_price', 'max' => '100000', 'min' => '0', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::checkbox('tranceportation_round_trip_flg[]', 0, false, ['class' => 'form-control checkbox_size  tranceportation_round_trip_flg', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::number('tranceportation_fare[]', null, ['class' => 'form-control form_price  tranceportation_fare', $disable, 'readonly']) }}
                                            </td>
                                            <td>
                                                {{ Form::textarea('tranceportation_remarks[]', null, ['class' => 'form-control  form_text tranceportation_remarks', 'placeholder' => '備考', 'rows' => '1', $disable]) }}
                                            </td>
                                        </tr>
                                    @else
                                        {{ Form::hidden('tranceportation_cnt', $transportation_expenses->count()) }}
                                        @foreach ($transportation_expenses as $index => $value)
                                            @if ($index == 0)
                                                <tr class='tranceportation_row'>
                                                @else
                                                <tr class='tranceportation_add_row'>
                                            @endif
                                            <td>
                                                {{ Form::checkbox('tranceportation_select_flg[]', '1', false, ['class' => 'form-control checkbox_size custom-control-input tranceportation_select_flg', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('tranceportation_school_building[]', $school_buildings, $value->school_building, ['placeholder' => '選択してください', 'class' => 'form-control  form_text  tranceportation_school_building', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::text('tranceportation_route[]', $value->route, ['class' => 'form-control form_text tranceportation_route', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::text('tranceportation_boarding_station[]', $value->boarding_station, ['class' => 'form-control  form_text tranceportation_boarding_station', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::text('tranceportation_get_off_station[]', $value->get_off_station, ['class' => 'form-control  form_text tranceportation_get_off_station', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::number('tranceportation_unit_price[]', $value->unit_price, ['class' => 'form-control form_price  tranceportation_unit_price', 'max' => '100000', 'min' => '0', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::checkbox('tranceportation_round_trip_flg[]', $index, $value->round_trip_flg, ['class' => 'form-control checkbox_size  tranceportation_round_trip_flg', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::number('tranceportation_fare[]', $value->fare, ['class' => 'form-control form_price  tranceportation_fare', $disable, 'readonly']) }}
                                            </td>
                                            <td>
                                                {{ Form::textarea('tranceportation_remarks[]', $value->remarks ?? '', ['class' => 'form-control  form_text tranceportation_remarks', 'placeholder' => '備考', 'rows' => '1', $disable]) }}
                                            </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @if ($display_none == false)
                                <div class="panel-footer">
                                    {{ Form::button('追加', ['class' => 'btn btn-success add-tranceportation-row']) }}
                                    {{ Form::button('削除', ['class' => 'btn btn-danger delete-tranceportation-row']) }}
                                </div>
                            @endif

                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">その他実績</div>
                            <table class="table">
                                <thead class='other_performance_tr'>
                                    <tr>
                                        <th scope="col">選択</th>
                                        <th scope="col">実施校舎名</th>
                                        <th scope="col">種別</th>
                                        <th scope="col">備考</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($daily_other_salaries->count() == 0)
                                        {{ Form::hidden('other_performance_cnt', 1) }}
                                        <tr class='other_performance_row'>
                                            <td>
                                                {{ Form::checkbox('other_select_flg[]', '1', false, ['class' => 'form-control checkbox_size  custom-control-input', 'id' => 'select_flg', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('other_school_building[]', $school_buildings, false, ['placeholder' => '選択してください', 'class' => 'form-control  school_building', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('other_job_description[]', $other_job_descriptions, false, ['placeholder' => '選択してください', 'class' => 'form-control  job_description', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::textarea('other_remarks[]', null, ['class' => 'form-control ', 'id' => 'remarks', 'placeholder' => '備考', 'rows' => '1', $disable]) }}
                                            </td>
                                        </tr>
                                    @else
                                        {{ Form::hidden('other_performance_cnt', $daily_other_salaries->count()) }}
                                        @foreach ($daily_other_salaries as $index => $value)
                                            @if ($index == 0)
                                                <tr class='other_performance_row'>
                                                @else
                                                <tr class='other_performance_add_row'>
                                            @endif
                                            <td>
                                                {{ Form::checkbox('other_select_flg[]', '1', false, ['class' => 'form-control checkbox_size  custom-control-input', 'id' => 'select_flg', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('other_school_building[]', $school_buildings, $value->school_building, ['placeholder' => '選択してください', 'class' => 'form-control  school_building', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::select('other_job_description[]', $other_job_descriptions, $value->job_description, ['placeholder' => '選択してください', 'class' => 'form-control  job_description', $disable]) }}
                                            </td>
                                            <td>
                                                {{ Form::textarea('other_remarks[]', $value->remarks ?? '', ['class' => 'form-control ', 'id' => 'remarks', 'placeholder' => '備考', 'rows' => '1', $disable]) }}
                                            </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @if ($display_none == false)
                                <div class="panel-footer">
                                    {{ Form::button('追加', ['class' => 'btn btn-success add-other-performance-row']) }}
                                    {{ Form::button('削除', ['class' => 'btn btn-danger  delete-other-performance-row']) }}
                                </div>
                            @endif

                        </div>
                        @if ($display_none == false)
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        登録
                                    </button>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>
@endsection
