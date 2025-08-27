@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">請求書説明文《新規登録》</div>
                <div class="panel-body">
                    <a href="{{ url('/shinzemi/invoice_comment') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif



                    <form method="POST" action="{{ route('invoice_comment.store') }}" class="form-horizontal">

                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="abbreviation" class="col-md-4 control-label">
                                略称:
                                <span class="text-danger">※</span>

                            </label>
                            <div class="col-md-6">
                                {{ Form::text('abbreviation', old('abbreviation'), ['class' => 'form-control']) }}
                                ※略称は請求書出力前の説明文選択時に表示されます。
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="comment" class="col-md-4 control-label">
                                説明文:
                                <span class="text-danger">※</span>

                            </label>
                            <div class="col-md-6">
                                {{ Form::textarea('comment', old('comment'), ['class' => 'form-control']) }}
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