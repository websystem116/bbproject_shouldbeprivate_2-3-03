@extends("layouts.app")
@section("content")
@push('css')
<link href="../../css/job_description_edit.css" rel="stylesheet">
@endpush

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">業務内容マスタ{{ $job_description->id }}</div>
                <div class="panel-body">
                    <a href="{{ url("job_description") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    {{ Form::model($job_description, array('route' => array('job_description.update', $job_description->id), 'method' => 'PUT', 'class' => 'form-horizontal')) }}

                    <div class="form-group">
                        <label for="id" class="col-md-4 control-label">id: </label>
                        <div class="col-md-6 val-disp">{{$job_description->id}}</div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-md-4 control-label">名称: </label>
                        <div class="col-md-6">
                            {{ Form::text('name', null, array('class' => 'form-control')) }}
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
