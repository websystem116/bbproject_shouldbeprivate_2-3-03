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
    // var currentTime = new Date();
    // var year = currentTime.getFullYear();
    // var year2 = parseInt(year) + 10;

    // $(".monthPick").datepicker({
    //     autoclose: true,
    //     language: 'ja',
    //     clearBtn: true,
    //     format: "yyyy-mm",
    //     minViewMode: 1,
    //     maxViewMode: 2,

    // });
});
</script>
@endpush
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">りそなNET出力</div>
                {{ Form::open(['route' => ['charge_output.export_risona'], 'method' => 'GET', 'class' => 'form-horizontal']) }}

                <div class="panel-body">
                    <div class="form-group row">
                        <div class="col-md-1 col-form-label m-0">
                            {{ Form::label('date', '引落日') }}
                        </div>
                        <div class="col-md-2 m-0">
                            {{ Form::date('date', null, ['class' => 'form-control monthPick', '', 'id' => 'year', 'placeholder' => '年月', 'style' => 'background-color:white']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 ">
                            {{ Form::submit('出力', ['class' => 'btn btn-primary', 'onfocus' => 'this.blur();', 'onclick="return confirm(\'りそなNETを出力しますか？\')"']) }}
                        </div>
                    </div>
                    <br />
                    <br />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection