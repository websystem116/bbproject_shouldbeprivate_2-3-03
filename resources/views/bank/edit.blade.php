@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">銀行マスタ #銀行コード{{ $bank->id }}</div>
                <div class="panel-body">
                    <!-- <a href="{{ url()->previous() }}" title="Back"> -->
                    <a href="{{ $url_for_back }}" title="Back">
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

                    {{ Form::model($bank, array('route' => array('bank.update', $bank->id), 'method' => 'PUT', 'class' => 'form-horizontal')) }}

                    <div class="form-group">
                        <label for="id" class="col-md-4 control-label">id: </label>
                        <div class="col-md-6">{{$bank->id}}</div>
                    </div>

                    <div class="form-group">
                        <label for="code" class="col-md-4 control-label">銀行コード: </label>
                        <div class="col-md-6">
                            {{ Form::text('code', null, array('class' => 'form-control')) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-md-4 control-label">
                            銀行名:
                            <span class="text-danger">※</span>
                        </label>
                        <div class="col-md-6">
                            {{ Form::text('name', null, array('class' => 'form-control')) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name_kana" class="col-md-4 control-label">銀行名（カナ）: </label>
                        <div class="col-md-6">
                            {{ Form::text('name_kana', null, array('class' => 'form-control hira_change hankaku_kana_change')) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            {{ Form::submit('更新', array('class' => 'btn btn-primary')) }}
                        </div>
                    </div>

                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection