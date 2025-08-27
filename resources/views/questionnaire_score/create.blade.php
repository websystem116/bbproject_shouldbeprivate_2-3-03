@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">新規作成</div>
                <div class="panel-body">
                    <a href="{{ url("/questionnaire_score") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif


                    <form method="POST" action="{{route('questionnaire_score.store')}}" class="form-horizontal">

                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="user_id" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <select class="form-control" name="user_id" id="user_id">
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_id" class="col-md-4 control-label">校舎: </label>
                            <div class="col-md-6">
                                @foreach($school_buildings as $school_building)

                                <div>{{$school_building->name}}</div>

                                <div class="form-group">
                                    <label for="classroom_score" class="col-md-4 control-label">教室数補正値: </label>
                                    <div class="col-md-6">
                                        <input class="form-control" name="classroom_score[]" type="number" step="0.01" id="classroom_score" value="{{old('classroom_score')}}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="subject_score" class="col-md-4 control-label">教科数補正値: </label>
                                    <div class="col-md-6">
                                        <input class="form-control" name="subject_score[]" type="number" step="0.01" id="subject_score" value="{{old('subject_score')}}">
                                    </div>
                                </div>

                                <input type="hidden" name="school_building_id[]" value="{{ $school_building->id }}">

                                @endforeach
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