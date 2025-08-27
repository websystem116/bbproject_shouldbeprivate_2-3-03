@extends('layouts.app')
@section('content')
@push('css')
<link href="{{ asset('css/bootstrap-datepicker3.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="{{ asset('/js/sales.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.ja.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script>
$(function() {
    $(".monthPick").datepicker({
        autoclose: true,
        language: 'ja',
        clearBtn: true,
        format: "yyyy-mm",
        minViewMode: 1,
        maxViewMode: 2
    });
});

$(function() {
    $('.select_search').select2({
        language: "ja",
        width: '300px'
    });
    $('.select_search_grade').select2({
        language: "ja",
        width: '100px'
    });
    $('.select_search_school').select2({
        language: "ja",
        width: '200px'
    });
});
</script>
@endpush

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">売上登録</div>
                <div class="panel-body">
                    <!-- <a href="{{ url('/shinzemi/sales') }}" title="Back">
                        <button class="btn btn-warning btn-xs">戻る</button>
                    </a> -->

                    <a href="{{ url()->previous() }}" title="Back">
                        <button class="btn btn-warning btn-xs">戻る</button>
                    </a>

                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <form method="POST" action="{{ route('sales.bulk_store') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <table class=" table">
                            <thead>
                                <tr>
                                    <th scope="col">売上月</th>
                                    <th scope="col">商品名</th>
                                    <th scope="col">割引後金額（円）</th>
                                    <th scope="col">備考</th>
                                </tr>
                            </thead>
                            <tbody id="sales_table_tbody">
                                <tr>
                                    <td>
                                        {{ Form::text('sale_month[]', '', ['class' => 'form-control monthPick', 'id' => 'sale_month', 'readonly', 'style' => 'background-color:white']) }}
                                    </td>
                                    <td>
                                        {{ Form::select('product_id[]', $products_select_list, '', ['placeholder' => '選択してください', 'class' => 'form-control product_id select_search']) }}
                                    </td>
                                    <td>
                                        {{ Form::number('price[]', '', ['class' => 'form-control job_description']) }}
                                    </td>
                                    <td>
                                        {{ Form::textarea('remarks[]', '', ['class' => 'form-control note', 'id' => 'remarks', 'placeholder' => '備考', 'rows' => '3']) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <div class="col-md-2">
                                {{Form::button('追加', ['name' => 'add','class'=>'btn btn-success add-input-sale'])}}
                            </div>
                            <div class="col-md-2">
                                {{Form::button('削除', ['class'=>'btn btn-danger sale-delete'])}}
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    登録
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection