@extends("layouts.app")
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">編集 #No{{ $school->id }}</div>
                <div class="panel-body">
                    <!-- <a href="{{ url('school') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a> -->

                    <a href="{{ url()->previous() }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>

                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    {{ Form::model($school, ['route' => ['school.update', $school->id],'method' => 'put','class' => 'form-horizontal']) }}

                    <div class="form-group">
                        <label for="id" class="col-md-4 control-label">id: </label>
                        <div class="col-md-6">{{ $school->id }}</div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-md-4 control-label">
                            学校名:
                            <span class="text-danger">※</span>
                        </label>
                        <div class="col-md-6">
                            {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name_short" class="col-md-4 control-label">略称: </label>
                        <div class="col-md-6">
                            {{ Form::text('name_short', null, ['class' => 'form-control', 'id' => 'name_short']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="school_classification" class="col-md-4 control-label">学校区分: </label>
                        <div class="col-md-6">
                            {{ Form::select('school_classification', config('const.school_classification'), null, ['class' => 'form-control','id' => 'school_classification']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="university_classification" class="col-md-4 control-label">国立・私立・公立区分: </label>
                        <div class="col-md-6">
                            {{ Form::select('university_classification', config('const.university_classification'), null, ['class' => 'form-control','id' => 'university_classification']) }}
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