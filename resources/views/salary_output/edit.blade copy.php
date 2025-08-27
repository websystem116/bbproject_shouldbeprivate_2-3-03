@extends('layouts.app')
@section('content')
    @push('css')
        <link href="{{ asset('/css/salary_edit.css') }}" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="{{ asset('/js/salary_edit.js') }}"></script>
    @endpush
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">アルバイト勤怠登録</div>
                    <div class="panel-body">
                        <a href="{{ url('/shinzemi/salary') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                        <br />
                        <br />
                        <div class="form-group">
                            <label for="code" class="col-md-1 control-label">ユーザーID: </label>
                            <div class="col-md-1 disp-value">
                                {{ $users->id }}
                            </div>
                            <label for="code" class="col-md-1 control-label">非常勤氏名: </label>
                            <div class="col-md-2 disp-value">
                                {{ $users->full_name }}
                            </div>
                            <label for="code" class="col-md-1 control-label">校舎名: </label>
                            <div class="col-md-2 disp-value">
                                {{ $users->school_buildings->name }}
                            </div>
                        </div>
                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        {{ Form::model($users, ['route' => ['salary.update', $users->id], 'method' => 'PUT', 'class' => 'form-horizontal']) }}
                        {{ csrf_field() }}
                        <div class="panel panel-default">
                            <div class="panel-heading">業務実績
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">選択</th>
                                        <th scope="col">上長承認</th>
                                        <th scope="col">実施校舎</th>
                                        <th scope="col">業務内容</th>
                                        <th scope="col">時間(分)</th>
                                        <th scope="col">備考</th>
                                    </tr>
                                </thead>
                                <tbody class='performance_tr'>
                                    <tr class='performance_row'>
                                        <td>
                                            {{ Form::checkbox('select_flg[]', '1', false, ['class' => 'form-control checkbox_size custom-control-input select_flg']) }}
                                        </td>
                                        <td>
                                            {{ Form::checkbox('superior_approval[]', '1', false, ['class' => ' form-control checkbox_size custom-control-input superior_approval']) }}
                                        </td>
                                        <td>
                                            {{ Form::select('school_building[]', $school_buildings, false, ['placeholder' => '選択してください', 'class' => 'form-control  school_building']) }}
                                        </td>
                                        <td>
                                            {{ Form::select('job_description[]', $job_descriptions, false, ['placeholder' => '選択してください', 'class' => 'form-control  job_description']) }}
                                        </td>
                                        <td>
                                            {{ Form::number('working_time[]', null, ['class' => 'form-control  working_time']) }}

                                        </td>
                                        <td>
                                            {{ Form::textarea('remarks', null, ['class' => 'form-control remarks', 'placeholder' => '備考', 'rows' => '1']) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="panel-footer">
                                {{ Form::button('追加', ['class' => 'btn btn-success add-performance-row']) }}
                                {{ Form::button('削除', ['class' => 'btn btn-danger delete-performance-row']) }}
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">交通費</div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">選択</th>
                                        <th scope="col">上長<br>承認</th>
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
                                <tbody>
                                    <tr id='tranceportation_row'>
                                        <td>
                                            {{ Form::checkbox('tranceportation_select_flg[]', '1', false, ['class' => 'form-control checkbox_size custom-control-input', 'id' => 'select_flg']) }}
                                        </td>
                                        <td>
                                            {{ Form::checkbox('tranceportation_superior_approval[]', '1', false, ['class' => 'form-control checkbox_size custom-control-input', 'id' => 'superior_approval']) }}
                                        </td>
                                        <td>
                                            {{ Form::select('tranceportation_school_building[]', $school_buildings, false, ['placeholder' => '選択してください', 'class' => 'form-control  form_text  school_building']) }}
                                        </td>
                                        <td>
                                            {{ Form::text('tranceportation_route[]', null, ['class' => 'form-control form_text job_description']) }}
                                        </td>
                                        <td>
                                            {{ Form::text('tranceportation_boarding_station[]', null, ['class' => 'form-control  form_text boarding_station']) }}
                                        </td>
                                        <td>
                                            {{ Form::text('tranceportation_get_off_station[]', null, ['class' => 'form-control  form_text get_off_station']) }}

                                        </td>
                                        <td>
                                            {{ Form::number('tranceportation_unit_price[]', null, ['class' => 'form-control form_price  unit_price', 'max' => '100000', 'min' => '0']) }}

                                        </td>
                                        <td>
                                            {{ Form::checkbox('tranceportation_round_trip_flg[]', 1, false, ['class' => 'form-control checkbox_size  round_trip_flg']) }}

                                        </td>
                                        <td>
                                            {{ Form::number('tranceportation_fare[]', null, ['class' => 'form-control form_price  fare']) }}
                                        </td>
                                        <td>
                                            {{ Form::textarea('tranceportation_remarks[]', null, ['class' => 'form-control  form_text', 'id' => 'remarks', 'placeholder' => '備考', 'rows' => '1']) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="panel-footer">
                                {{ Form::button('追加', ['class' => 'btn btn-success add-tranceportation-row']) }}
                                {{ Form::button('削除', ['class' => 'btn btn-danger delete-tranceportation-row']) }}
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">その他実績</div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">選択</th>
                                        <th scope="col">上長承認</th>
                                        <th scope="col">実施校舎名</th>
                                        <th scope="col">種別</th>
                                        <th scope="col">備考</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id='performance_row'>
                                        <td>
                                            {{ Form::checkbox('other_select_flg[]', '1', false, ['class' => 'form-control checkbox_size  custom-control-input', 'id' => 'select_flg']) }}
                                        </td>
                                        <td>
                                            {{ Form::checkbox('other_superior_approval[]', '1', false, ['class' => 'form-control checkbox_size  custom-control-input', 'id' => 'superior_approval']) }}
                                        </td>
                                        <td>
                                            {{ Form::select('other_school_building[]', $school_buildings, false, ['placeholder' => '選択してください', 'class' => 'form-control  school_building']) }}
                                        </td>
                                        <td>
                                            {{ Form::select('other_job_description[]', $other_job_descriptions, false, ['placeholder' => '選択してください', 'class' => 'form-control  job_description']) }}
                                        </td>
                                        <td>
                                            {{ Form::textarea('other_remarks[]', null, ['class' => 'form-control ', 'id' => 'remarks', 'placeholder' => '備考', 'rows' => '1']) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="panel-footer">
                                {{ Form::button('追加', ['class' => 'btn btn-success add-other-performance-row']) }}
                                {{ Form::button('削除', ['class' => 'btn btn-danger  delete-other-performance-row']) }}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    登録
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>
@endsection
