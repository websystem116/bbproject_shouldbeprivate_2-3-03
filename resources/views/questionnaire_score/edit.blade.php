@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">編集　#{{ $id }}</div>
                <div class="panel-body">
                    <!-- <a href="{{ url("questionnaire_score") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a> -->

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


                    <form method="POST" action="{{route('questionnaire_score.update',$id)}}" class="form-horizontal">

                        {{ csrf_field() }}
                        {{ method_field("PUT") }}

                        <div class="form-group">

                            <label for="user_id" class="col-md-4 control-label">講師: </label>

                            <div class="col-md-6">
                                {{$user->last_name}}{{ $user->first_name }}
                            </div>

                        </div>

                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        @foreach ($questionnaire_scores as $index=>$questionnaire_score)

                        <div class="form-group">

                            <label for="school_building_id" class="col-md-4 control-label"> </label>

                            <div class="col-md-6">
                                {{$questionnaire_score->school_building_name ?? ''}}
                            </div>

                        </div>

                        <input type="hidden" name="test[{{$index}}][school_building_id]" value="{{$questionnaire_score->school_building_key ?? ''}}">

                        <div class="form-group">

                            <label for="classroom_score" class="col-md-4 control-label">教室数補正値: </label>

                            <div class="col-md-6">
                                <input class="form-control" name="test[{{$index}}][classroom_score]" type="number" max="1" step="0.01" id="classroom_score" value="{{$questionnaire_score->classroom_score ?? ''}}">
                            </div>

                        </div>

                        <div class="form-group">

                            <label for="subject_score" class="col-md-4 control-label">教科数補正値: </label>

                            <div class="col-md-6">
                                <input class="form-control" name="test[{{$index}}][subject_score]" type="number" max="1" step="0.01" id="subject_score" value="{{$questionnaire_score->subject_score ?? ''}}">
                            </div>

                        </div>

                        @endforeach



                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                <input class="btn btn-primary" type="submit" value="更新">
                            </div>
                        </div>

                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection