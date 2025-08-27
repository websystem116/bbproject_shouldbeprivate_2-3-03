
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Edit questionnaire_decision #{{ $questionnaire_decision->id }}</div>
                            <div class="panel-body">
                                <a href="{{ url("questionnaire_decision") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <br />
                                <br />

                            @if ($errors->any())
                                <ul class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
    
                            <form method="POST" action="/questionnaire_decision/{{ $questionnaire_decision->id }}" class="form-horizontal">
                                        {{ csrf_field() }}
                                        {{ method_field("PUT") }}
            
										<div class="form-group">
                                        <label for="id" class="col-md-4 control-label">id: </label>
                                        <div class="col-md-6">{{$questionnaire_decision->id}}</div>
                                    </div>
										<div class="form-group">
                                            <label for="questionnaire_contents_id" class="col-md-4 control-label">questionnaire_contents_id: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="questionnaire_contents_id" type="text" id="questionnaire_contents_id" value="{{$questionnaire_decision->questionnaire_contents_id}}">
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="user_id" class="col-md-4 control-label">user_id: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="user_id" type="text" id="user_id" value="{{$questionnaire_decision->user_id}}">
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="classroom_score" class="col-md-4 control-label">classroom_score: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="classroom_score" type="text" id="classroom_score" value="{{$questionnaire_decision->classroom_score}}">
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="subject_score" class="col-md-4 control-label">subject_score: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="subject_score" type="text" id="subject_score" value="{{$questionnaire_decision->subject_score}}">
                                            </div>
                                        </div>
               
                                    <div class="form-group">
                                        <div class="col-md-offset-4 col-md-4">
                                            <input class="btn btn-primary" type="submit" value="Update">
                                        </div>
                                    </div>   
                                </form>
                                

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    