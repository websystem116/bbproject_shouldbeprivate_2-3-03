@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">アルバイト勤怠登録</div>
                    <div class="panel-body">
                        <a href="{{ url('/shinzemi/salary') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <form method="POST" action="{{ route('salary.store') }}" class="form-horizontal">
                            {{ csrf_field() }}
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">選択</th>
                                        <th scope="col">上長承認</th>
                                        <th scope="col">No</th>
                                        <th scope="col">実施校舎</th>
                                        <th scope="col">業務内容</th>
                                        <th scope="col">対象学年</th>
                                        <th scope="col">時間(分)</th>
                                        <th scope="col">備考</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {{ Form::checkbox('select_flg[]', '1', false, ['class' => 'custom-control-input', 'id' => 'select_flg']) }}
                                        </td>
                                        <td>
                                            {{ Form::checkbox('superior_approval[]', '1', false, ['class' => 'custom-control-input', 'id' => 'superior_approval']) }}
                                        </td>
                                        <td></td>
                                        <td>
                                            {{ Form::select('school_building[]', $products_select_list, false, ['placeholder' => '選択してください', 'class' => 'form-control school_building']) }}
                                        </td>
                                        <td>
                                            {{ Form::select('job_description[]', $products_select_list, false, ['placeholder' => '選択してください', 'class' => 'form-control job_description']) }}
                                        </td>
                                        <td>
                                            {{ Form::select('school_year[]', $products_select_list, false, ['placeholder' => '選択してください', 'class' => 'form-control school_year']) }}
                                        </td>
                                        <td>
                                            {{ Form::select('working_time[]', $products_select_list, false, ['placeholder' => '選択してください', 'class' => 'form-control working_time']) }}
                                        </td>
                                        <td>
                                            {{ Form::text('surname', null, ['class' => 'form-control', 'id' => 'surname', 'placeholder' => '姓']) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
