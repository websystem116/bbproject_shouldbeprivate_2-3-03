@extends("layouts.app")
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">新規作成</div>
                <div class="panel-body">
                    <a href="{{ url('/shinzemi/school') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    {{ Form::open(['route' => ['school.store'], 'method' => 'post', 'class' => 'form-horizontal']) }}
                    {{-- <form method="POST" action="{{route('school.store')}}" class="form-horizontal"> --}}
                    {{-- {{ csrf_field() }} --}}

                    <div class="form-group">
                        <label for="name" class="col-md-4 control-label">
                            学校名:
                            <span class="text-danger">※</span>
                        </label>

                        <div class="col-md-6">
                            {{-- <input class="form-control" name="name" type="text" id="name" value="{{old('name')}}"> --}}
                            {{ Form::text('name', old('name'), ['class' => 'form-control', 'id' => 'name']) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name_short" class="col-md-4 control-label">略称: </label>
                        <div class="col-md-6">
                            {{-- <input class="form-control" name="name_short" type="text" id="name_short" value="{{old('name_short')}}"> --}}
                            {{ Form::text('name_short', old('name_short'), ['class' => 'form-control', 'id' => 'name_short']) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="school_classification" class="col-md-4 control-label">学校区分: </label>
                        <div class="col-md-6">
                            {{-- <select class="form-control" name="school_classification" id="school_classification">
                                                @foreach (config('const.school_classification') as $key => $value)
                                                    <option value="{{$key}}">{{ $value }}</option>
                            @endforeach
                            </select> --}}
                            {{ Form::select('school_classification', config('const.school_classification'), '', ['class' => 'form-control','id' => 'school_classification']) }}
                        </div>



                    </div>
                    <div class="form-group">
                        <label for="university_classification" class="col-md-4 control-label">国立・私立・公立区分: </label>
                        <div class="col-md-6">
                            {{-- <select class="form-control" name="university_classification" id="university_classification">
                                                @foreach (config('const.university_classification') as $key => $value)
                                                    <option value="{{$key}}">{{ $value }}</option>
                            @endforeach
                            </select> --}}
                            {{ Form::select('university_classification', config('const.university_classification'), '', ['class' => 'form-control','id' => 'university_classification']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            {{-- <input class="btn btn-primary" type="submit" value="登録"> --}}
                            {{ Form::submit('登録', ['class' => 'btn btn-primary']) }}
                        </div>
                    </div>

                    {{-- </form> --}}
                    {{ Form::close() }}


                </div>
            </div>
        </div>
    </div>
</div>
@endsection