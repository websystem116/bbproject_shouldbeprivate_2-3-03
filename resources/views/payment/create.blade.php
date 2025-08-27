@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">コンビニ振込等登録</div>
                    <div class="panel-body">
                        <a href="{{ url('/shinzemi/payment') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        {{ Form::open(['route' => 'register', 'class' => 'form-horizontal']) }}
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">選択</th>
                                    <th scope="col">入金日</th>
                                    <th scope="col">計上年月</th>
                                    <th scope="col">請求先名</th>
                                    <th scope="col">入金額</th>
                                    <th scope="col">区分</th>
                                    <th scope="col">摘要</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{ Form::checkbox('select_flg[]', '1', false, ['class' => 'custom-control-input', 'id' => 'select_flg']) }}
                                    </td>
                                    <td>
                                        {{ Form::text('sale_month[]', $item->sale_month, ['class' => 'form-control', 'id' => 'sale_month']) }}
                                    </td>
                                    <td>
                                        {{ Form::select('product_id[]', $products_select_list, $item->product_id, ['placeholder' => '選択してください', 'class' => 'form-control school_building']) }}
                                    </td>
                                    <td>
                                        {{ Form::number('price[]', $item->price, ['class' => 'form-control job_description']) }}
                                    </td>
                                    <td>
                                        {{ Form::textarea('remarks', $item->remarks, ['class' => 'form-control', 'id' => 'remarks', 'placeholder' => '備考', 'rows' => '3']) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <div class="col-md-2">
                                {{ Form::button('追加', ['class' => 'btn btn-success add-input-sale']) }}
                            </div>
                            <div class="col-md-2">
                                {{ Form::button('削除', ['class' => 'btn btn-danger sale-delete']) }}
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
