@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">新規作成</div>
                <div class="panel-body">
                    <a href="{{ url("/product") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif


                    <form method="POST" action="{{route('product.store')}}" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                商品No:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="number" type="number" id="name" value="{{old('number')}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                商品名:
                                <span class="text-danger">※</span>

                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name" type="text" id="name" value="{{old('name')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name_short" class="col-md-4 control-label">商品名（略称）: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name_short" type="text" id="name_short" value="{{old('name_short')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description" class="col-md-4 control-label">内容: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="description" type="text" id="description" value="{{old('description')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="price" class="col-md-4 control-label">価格: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="price" type="text" id="price" value="{{old('price')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tax_category" class="col-md-4 control-label">価格表示: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="tax_category" type="text" id="tax_category" value="{{old('tax_category')}}"> -->
                                <select class="form-control" name="tax_category" id="tax_category">
                                    @foreach(config('const.tax_kind') as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="division_code" class="col-md-4 control-label">売上区分: </label>
                            <div class="col-md-6">
                                {{-- <input class="form-control" name="division_code" type="text" id="division_code" value="{{old('division_code')}}"> --}}
                                <select class="form-control" name="division_code" id="division_code">
                                    @foreach($division_codes as $key => $value)
                                    <option value="{{$value->id}}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                                        <label for="item_no" class="col-md-4 control-label">売上区分（項目No）: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="item_no" type="text" id="item_no" value="{{old('item_no')}}">
                </div>
            </div> --}}
            <div class="form-group">
                <label for="tabulation" class="col-md-4 control-label">集計区分: </label>
                <div class="col-md-6">
                    <select class="form-control" name="tabulation" id="tabulation">
                        @foreach (config('const.class_categories') as $key => $value)
                        <option value="{{$key}}">{{ $value }}</option>
                        @endforeach
                    </select>

                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-4 col-md-4">
                    <input class="btn btn-primary" type="submit" value="登録">
                </div>
            </div>
            </form>


        </div>
    </div>
</div>
</div>
</div>
@endsection