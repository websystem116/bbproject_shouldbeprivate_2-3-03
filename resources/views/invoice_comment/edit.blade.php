@extends('layouts.app')
@section('content')
    @push('css')
        <link href="../../css/invoice_comment_edit.css" rel="stylesheet">
    @endpush

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">

                    <div class="panel-heading">
                        請求書説明文
                    </div>

                    <div class="panel-body">
                        <a href="{{ url('/shinzemi/invoice_comment') }}" title="Back"><button
                                class="btn btn-warning btn-xs">戻る</button></a>


                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        {{ Form::model($invoice_comment, ['route' => ['invoice_comment.update', $invoice_comment->id], 'method' => 'PUT', 'class' => 'form-horizontal']) }}

                        <div class="form-group">
                            <label for="id" class="col-md-4 control-label">支払い方法: </label>
                            <div class="col-md-6 val-disp" style="padding-top: 7px">
                                {{ config('const.invoice_payment_method')[$invoice_comment->division] }}</div>
                        </div>

                        <div class="form-group">
                        </div>

                        <div class="form-group">
                            <label for="comment" class="col-md-4 control-label">
                                説明文:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                {{ Form::textarea('comment', null, ['class' => 'form-control']) }}
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                {{ Form::submit('更新', ['class' => 'btn btn-primary']) }}
                            </div>
                        </div>

                        {{ Form::close() }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
