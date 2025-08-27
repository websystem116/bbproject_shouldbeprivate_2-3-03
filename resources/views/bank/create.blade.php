@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">新規作成</div>
                <div class="panel-body">

                    <a href="{{ url("/bank") }}" title="Back">
                        <button class="btn btn-warning btn-xs">戻る</button>
                    </a>

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif



                    <form method="POST" action="{{route('bank.store')}}" class="form-horizontal">

                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="code" class="col-md-4 control-label">
                                銀行コード:
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="code" type="text" id="code" value="{{old('code')}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                銀行名:
                                <span class="text-danger">※</span>

                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name" type="text" id="name" value="{{old('name')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name_kana" class="col-md-4 control-label">銀行名（カナ）: </label>
                            <div class="col-md-6">
                                <input class="form-control hira_change hankaku_kana_change" name="name_kana" type="text" id="name_kana" value="{{old('name_kana')}}">
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