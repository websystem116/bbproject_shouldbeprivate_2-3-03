@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">非常勤（支給/控除）登録</div>
                    <div class="panel-body">
                        <!-- <a href="{{ url('/shinzemi/salary') }}" title="Back">
                                                                                                <button class="btn btn-warning btn-xs">戻る</button>
                                                                                            </a> -->

                        <a href="{{ url()->previous() }}" title="Back">
                            <button class="btn btn-warning btn-xs">戻る</button>
                        </a>

                        <br />
                        <br />
                        {{ Form::model($users, ['route' => ['salary.deduction', $users->id, $month], 'method' => 'GET', 'class' => 'form-horizontal']) }}
                        <div class="form-group">
                            <label for="code" class="col-md-1 control-label">ユーザーID: </label>
                            <div class="col-md-1 disp-value">
                                {{ $users->user_id }}
                            </div>

                            <label for="code" class="col-md-1 control-label">非常勤氏名: </label>
                            <div class="col-md-1 disp-value">
                                {{ $users->full_name }}
                            </div>
                        </div>
                        {{ Form::close() }}

                        {{-- @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                    @endif --}}
                        {{ Form::model($salaries, ['route' => ['salary.deduction_update', $salaries->id], 'method' => 'PUT', 'class' => 'form-horizontal']) }}
                        {{ csrf_field() }}
                        {{ Form::hidden('month', $month) }}
                        <div class="panel panel-default">
                            <div class="panel-heading">業務実績
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">金額(円)</th>
                                        <th scope="col">事由</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>臨時の給与</td>
                                        <td>
                                            {{ Form::number('payment', $salaries->other_payment_amount, ['class' => 'form-control  payment', $disable]) }}

                                        </td>
                                        <td>
                                            {{ Form::textarea('payment_reason', $salaries->other_payment_reason, ['class' => 'form-control reason', 'placeholder' => '事由', 'rows' => '1', $disable]) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>健康保険料</td>
                                        <td>
                                            {{ Form::number('health_insurance', $salaries->health_insurance, ['class' => 'form-control  health_insurance', $disable]) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>厚生年金保険料</td>
                                        <td>
                                            {{ Form::number('welfare_pension', $salaries->welfare_pension, ['class' => 'form-control  welfare_pension', $disable]) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>雇用保険料</td>
                                        <td>
                                            {{ Form::number('employment_insurance', $salaries->employment_insurance, ['class' => 'form-control  employment_insurance', $disable]) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>市町村民税</td>
                                        <td>
                                            {{ Form::number('municipal_tax', $salaries->municipal_tax, ['class' => 'form-control  municipal_tax', $disable]) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>年末調整</td>
                                        <td>

                                            {{ Form::number('year_end_adjustment', null, ['class' => 'form-control  payment', $disable]) }}

                                        </td>
                                        <td>
                                            ※年末調整で追納の必要がある場合はマイナス
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

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
