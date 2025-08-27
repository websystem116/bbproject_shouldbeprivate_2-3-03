@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">編集 #{{ $branch_bank->id }}</div>
                <div class="panel-body">
                    <!-- <a href="{{ url()->previous() }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a> -->
                    <a href="{{ $url_for_back }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>

                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <form method="POST" action="{{route('branch_bank.update',$branch_bank->id)}}" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field("PUT") }}

                        <div class="form-group">
                            <label for="id" class="col-md-4 control-label">id: </label>
                            <div class="col-md-6">{{$branch_bank->id}}</div>
                        </div>
                        <div class="form-group">
                            <label for="code" class="col-md-4 control-label">
                                銀行名:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">

                                <select class="form-control" name="bank_id" id="bank_id">
                                    @foreach ($banks as $key => $bank)
                                    <option value="{{$bank->id}}" @if($branch_bank->bank_id == $bank->id) selected @endif>{{$bank->code}}　 {{ $bank->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="code" class="col-md-4 control-label">
                                銀行支店コード:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="code" type="text" id="code" value="{{$branch_bank->code}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                支店名:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name" type="text" id="name" value="{{$branch_bank->name}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name_kana" class="col-md-4 control-label">支店名（カナ）: </label>
                            <div class="col-md-6">
                                <input class="form-control hira_change hankaku_kana_change" name="name_kana" type="text" id="name_kana" value="{{$branch_bank->name_kana}}">
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                <input class="btn btn-primary" type="submit" value="更新">
                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection