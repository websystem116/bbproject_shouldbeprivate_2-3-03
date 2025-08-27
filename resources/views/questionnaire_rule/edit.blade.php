
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">編集 #{{ $questionnaire_rule->id }}</div>
                            <div class="panel-body">
                                <a href="{{ url("questionnaire_rule") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                                <br />
                                <br />

                            @if ($errors->any())
                                <ul class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
    
                            <form method="POST" action="{{route('questionnaire_rule.update',$questionnaire_rule->id)}}" class="form-horizontal">
                                        {{ csrf_field() }}
                                        {{ method_field("PUT") }}
            
										<div class="form-group">
                                        <label for="id" class="col-md-4 control-label">id: </label>
                                        <div class="col-md-6">{{$questionnaire_rule->id}}</div>
                                    </div>
										<div class="form-group">
                                            <label for="rankstart" class="col-md-4 control-label">始点（以上）: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="rankstart" type="text" id="rankstart" value="{{$questionnaire_rule->rankstart}}">
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="rankend" class="col-md-4 control-label">終点（未満）: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="rankend" type="text" id="rankend" value="{{$questionnaire_rule->rankend}}">
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="rankscore" class="col-md-4 control-label">点数: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="rankscore" type="text" id="rankscore" value="{{$questionnaire_rule->rankscore}}">
                                            </div>
                                        </div>
               
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
    