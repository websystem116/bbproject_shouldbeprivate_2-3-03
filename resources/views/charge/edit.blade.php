@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">コンビニ振込等登録</div>
                    <div class="panel-body">
                        <a href="{{ url('/shinzemi/charge') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        {{ Form::model($payments, ['route' => ['charge.update', $sale->id], 'method' => 'PUT', 'class' => 'form-horizontal']) }}
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">選択</th>
                                    <th scope="col">売上月</th>
                                    <th scope="col">商品名</th>
                                    <th scope="col">割引後金額（円）</th>
                                    <th scope="col">備考</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->sales_detail as $item)
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
                                @endforeach
                            </tbody>
                        </table>
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
