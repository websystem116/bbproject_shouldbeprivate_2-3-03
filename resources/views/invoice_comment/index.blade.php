@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請求書説明文 </div>
                    <div class="panel-body">



                        <div style="padding-top:24px;">
                            {{ $invoice_comment->total() }} 件中 {{ $invoice_comment->firstItem() }} -
                            {{ $invoice_comment->lastItem() }} 件を表示
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th style="width:200px">支払方法</th>
                                        <th>説明文</th>
                                        <th>編集</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice_comment as $item)
                                        <tr>

                                            <td>{{ $item->id }} </td>

                                            <td>{{ config('const.invoice_payment_method')[$item->division] }} </td>
                                            <td>{{ $item->comment }} </td>

                                            <td>
                                                <a href="{{ url('/shinzemi/invoice_comment/' . $item->id . '/edit') }}"
                                                    title="Edit invoice_comment"><button
                                                        class="btn btn-primary btn-xs">編集</button></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $invoice_comment->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
