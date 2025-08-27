@extends("layouts.app")
@section("content")
<script src="{{ asset('/js/before_juku_detail.js') }}"></script>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">入塾前売上明細出力</div>
                <div class="panel-body">
                    <div class="">
                        <div class="col-form-label m-0" style="align-self: center;">
                            {{Form::label('year','売上年月')}}
                        </div>

                        {{ Form::open(['route' => ['before_juku_detail.sales_item_output'], 'method' => 'get', 'class' => 'form-horizontal','target' => '_blank','style' => 'display:flex']) }}
                        <div class="col-md-1 m-0" style="padding-left: 0px;">
                            {{Form::text('year',null, ['class' => 'form-control year','id' => 'year','placeholder' => '例:2020','required'])}}
                        </div>

                        <div class="col-form-label" style="align-self: center;">
                            {{Form::label('','年')}}
                        </div>

                        <div class="col-md-1">
                            {{ Form::select('month',config('const.month'),null,['placeholder' => '-','class' => 'form-control month','required']) }}
                        </div>

                        <div class="col-form-label" style="align-self: center;">
                            {{Form::label('month','月')}}
                        </div>
                    </div>
                    <div style="margin-top:16px">
                        {{Form::submit('入塾前売上明細出力', ['id' => 'sales_item_output','class' => 'btn btn-primary', 'onfocus' => 'this.blur();'])}}
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection