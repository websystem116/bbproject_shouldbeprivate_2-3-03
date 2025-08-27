@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">新規作成</div>
                <div class="panel-body">
                    <a href="{{ url("/discount") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif


                    <form method="POST" action="{{route('discount.store')}}" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                割引名:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name" type="text" id="name" value="{{old('name')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name_short" class="col-md-4 control-label">略名: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name_short" type="text" id="name_short" value="{{old('name_short')}}">
                            </div>
                        </div>

                        @foreach ($division_codes as $key => $division_code)

                        <div class="form-group">
                            <label for="" class="col-md-4 control-label"> {{ $division_code->name }}:</label>
                            <div class="col-md-6">
                                <input class="form-control" name="discount_rate[{{$division_code->id}}]" type="number" max="100" min="0" id="" value="">
                            </div>
                        </div>

                        @endforeach

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