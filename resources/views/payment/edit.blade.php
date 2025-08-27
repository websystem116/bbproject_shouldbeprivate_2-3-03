@extends('layouts.app')
@section('content')
@push('css')
<link href="{{ asset('css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.ja.min.js') }}"></script>

<script src="{{ asset('/js/payment_edit.js') }}"></script>
<script>
$(function() {
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
                <div class="panel-heading">コンビニ振込等登録</div>
                <div class="panel-body">
                    <a href="{{ url('/shinzemi/payment') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />
                    <div class="form-group">
                        <label for="code" class="col-md-1 control-label">生徒No: </label>
                        <div class="col-md-1 disp-value">
                            {{ $students->student_no }}
                        </div>

                        <label for="code" class="col-md-1 control-label">生徒氏名: </label>
                        <div class="col-md-2 disp-value">
                            {{ $students->full_name }}
                        </div>
                        <label for="code" class="col-md-1 control-label">校舎名: </label>
                        <div class="col-md-2 disp-value">
                            {{ $students->schoolbuilding->name }}
                        </div>
                    </div>
                    <br />

                    <div class="form-group">
                        <label for="code" class="col-md-1 control-label">未収金額: </label>
                        <div class="col-md-2 disp-value">
                            {{  $accrued_amount ?? 0 }}
                        </div>

                    </div>

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                    {{ Form::model($payments, ['route' => ['payment.update', $students->id], 'method' => 'PUT', 'class' => 'form-horizontal']) }}
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">入金日</th>
                                <th scope="col">計上年月</th>
                                <th scope="col">請求先名</th>
                                <th scope="col">入金額</th>
                                <th scope="col">区分</th>
                                <th scope="col">摘要</th>
                            </tr>
                        </thead>
                        <tbody class='payment_tr'>
                            @if ($payments->count() == 0)
                            <tr class='payment_row'>
                                <td>
                                    {{ Form::date('payment_date[]', null, ['class' => 'form-control payment_date']) }}
                                </td>
                                <td>
                                    {{ Form::text('sale_month[]', null, ['class' => 'form-control sale_month monthPick']) }}
                                </td>
                                <td>
                                    {{ Form::text('parent_name[]', $students->parent_surname . $students->parent_name, ['class' => 'form-control parent_name', 'readonly']) }}
                                </td>
                                <td>
                                    {{ Form::number('price[]', null, ['class' => 'form-control price job_description']) }}
                                </td>
                                <td>
                                    {{ Form::select('division[]', config('const.pay_method'), null, ['placeholder' => '未選択', 'class' => 'form-control  division']) }}
                                </td>
                                <td>
                                    {{ Form::textarea('remarks[]', null, ['class' => 'form-control remarks', 'placeholder' => '備考', 'rows' => '1']) }}
                                </td>
                            </tr>
                            @else
                            @foreach ($payments as $payment)
                            @if ($payments_last->id == $payment->id)
                            <tr class='payment_row'>
                                @else
                            <tr class='payment_add_row'>
                                @endif
                                {{ Form::hidden('id[]', $payment->id, ['class' => 'payment_id']) }}
                                <td>
                                    {{ Form::date('payment_date[]', $payment->payment_date, ['class' => 'form-control payment_date']) }}
                                </td>
                                <td>
                                    {{ Form::text('sale_month[]', $payment->sale_month, ['class' => 'form-control sale_month monthPick']) }}
                                </td>
                                <td>
                                    {{ Form::text('parent_name[]', $students->parent_surname . $students->parent_name, ['class' => 'form-control parent_name', 'readonly']) }}
                                </td>
                                <td>
                                    {{ Form::number('price[]', $payment->payment_amount, ['class' => 'form-control price job_description']) }}
                                </td>
                                <td>
                                    {{ Form::select('division[]', config('const.pay_method'), $payment->pay_method, ['placeholder' => '未選択', 'class' => 'form-control  division']) }}
                                </td>
                                <td>
                                    {{ Form::textarea('remarks[]', $payment->summary, ['class' => 'form-control remarks', 'placeholder' => '備考', 'rows' => '1']) }}
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                    <div class="form-group">
                        <div class="col-md-2">
                            {{ Form::button('追加', ['class' => 'btn btn-success add-payment-row']) }}
                        </div>
                        <div class="col-md-2">
                            {{ Form::button('削除', ['class' => 'btn btn-danger delete-payment-row']) }}
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                登録
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