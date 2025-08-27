@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">商品</div>
                <div class="panel-body">

                    <div>
                        <a href="{{ url('/shinzemi/product/create') }}" class="btn btn-success btn-sm" title="Add New product">
                            新規追加
                        </a>
                    </div>

                    <form method="GET" action="{{ url('/shinzemi/product') }}" accept-charset="UTF-8" class="" role="search" style="display:flex;justify-content: flex-end;">

                        <div class="input-group" style="display:flex;align-items:end;">

                            <div>
                                <div class="input-group-text">
                                    商品名
                                </div>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="商品名">
                            </div>

                            <div>
                                <div class="input-group-text">
                                    商品No
                                </div>
                                <input type="text" class="form-control" name="number" value="{{ request('number') }}" placeholder="商品No">
                            </div>

                            <div>
                                <div class="input-group-text">
                                    売上区分
                                </div>

                                <select name="division_code" id="" class="form-control">
                                    <option value="">-</option>

                                    @foreach($division_codes as $key => $division_code)
                                    <option value="{{ $division_code->id }}" @if(request('division_code')==$division_code->id) selected @endif>
                                        {{ $division_code->name }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>

                            <div>
                                <div class="input-group-text">
                                    集計区分
                                </div>

                                <select name="class_categories" id="" class="form-control">
                                    <option value="">-</option>
                                    @foreach(config('const.class_categories') as $class_categories_id => $class_categories)
                                    <option value="{{ $class_categories_id }}" @if(request('class_categories')==$class_categories_id) selected @endif>
                                        {{ $class_categories }}
                                    </option>
                                    @endforeach
                                </select>


                            </div>

                            <div class="input-group-btn">
                                <button class="btn btn-info" type="submit">
                                    <span>検索</span>
                                </button>
                            </div>

                        </div>

                    </form>

                    <div style="padding-top:24px;">
                        {{ $product->total() }} 件中 {{ $product->firstItem() }} - {{ $product->lastItem() }} 件を表示
                        {{ $product->total() }} 件中 {{ $product->firstItem() }} - {{ $product->lastItem() }} 件を表示
                    </div>

                    <div class="">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>商品No</th>
                                    <th>商品名</th>
                                    <th>商品名（略称）</th>
                                    <th>内容</th>
                                    <th class="text-center">価格</th>
                                    <th>価格表示</th>
                                    <th>売上区分</th>
                                    <th>集計区分</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product as $item)

                                <tr>

                                    <td>{{ $item->number}} </td>

                                    <td>{{ $item->name}} </td>

                                    <td>{{ $item->name_short}} </td>

                                    <td>{{ $item->description}} </td>

                                    <td class="text-right">
                                        {{ $item->comma_price}}
                                    </td>

                                    <td>{{ config('const.tax_kind')[$item->tax_category] }}</td>

                                    <td>{{ $division_codes_array[$item->division_code] }} </td>

                                    <td>{{ config('const.class_categories')[$item->tabulation] }} </td>

                                    <td>
                                        <a href="{{ url('/shinzemi/product/' . $item->id . '/edit') }}" title="Edit product">
                                            <button class="btn btn-primary btn-xs">編集</button>
                                        </a>
                                    </td>

                                    <td>
                                        <form method="POST" action="{{route('product.destroy',$item->id)}}" class="form-horizontal" style="display:inline;">
                                            {{ csrf_field() }}

                                            {{ method_field("DELETE") }}
                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除しますか')">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $product->appends(request()->all())->links() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection