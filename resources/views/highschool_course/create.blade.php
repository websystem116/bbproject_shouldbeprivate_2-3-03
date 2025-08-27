@extends("layouts.app")
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">新規作成</div>
                <div class="panel-body">
                    <a href="{{ url('/shinzemi/highschool_course') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif


                    <form method="POST" action="{{ route('highschool_course.store') }}" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="school_id" class="col-md-4 control-label">
                                学校No:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                {{-- <input class="form-control" name="school_id" type="text" id="school_id" value="{{old('school_id')}}"> --}}
                                <select class="form-control" name="school_id" id="school_id">
                                    @foreach ($schools as $school)
                                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                名称:
                                <span class="text-danger">※</span>

                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name" type="text" id="name" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name_short" class="col-md-4 control-label">略称: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name_short" type="text" id="name_short" value="{{ old('name_short') }}">
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