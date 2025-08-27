@extends('layouts.app')
@section('content')
@push('css')
<link href="{{ asset('css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.ja.min.js') }}"></script>
<script>
    $(function() {
        // monthPick
        // var currentTime = new Date();
        // var year = currentTime.getFullYear();
        // var year2 = parseInt(year) + 10;

        // $(".monthPick").datepicker({
        //     autoclose: true,
        //     language: 'ja',
        //     clearBtn: true,
        //     format: "yyyy-mm-dd",
        //     minViewMode: 1,
        //     maxViewMode: 2
        // });
    });
</script>
@endpush
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">南都WEB出力</div>
                <div class="panel-body">
                    <div class="form-group row">
                        {{ Form::open(['route' => ['charge_output.export_nanto'], 'method' => 'GET', 'class' => 'form-horizontal','style' => 'display:flex;']) }}

                        <div class="col-md-1 col-form-label" style="display:flex;align-items: center;">
                            {{ Form::label('date', '引落日',['style' => 'margin:0px;']) }}
                        </div>

                        <div class="col-md-2 m-0">
                            {{ Form::date('date', null, ['class' => 'form-control monthPick', '', 'id' => 'year', 'placeholder' => '', 'style' => 'background-color:white']) }}
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 ">
                            {{ Form::submit('出力', ['class' => 'btn btn-primary', 'onfocus' => 'this.blur();', 'onclick="return confirm(\'南都WEBを出力しますか？\')"']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection