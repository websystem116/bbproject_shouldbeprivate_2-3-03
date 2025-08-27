
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">新規作成</div>
                            <div class="panel-body">
                                <a href="{{ url("/questionnaire_rule") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                                <br />
                                <br />

                                @if ($errors->any())
                                    <ul class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                
                                
                                <form method="POST" action="{{route('questionnaire_rule.store')}}" class="form-horizontal">
                                    {{ csrf_field() }}

    										<div class="form-group">
                                        <label for="rankstart" class="col-md-4 control-label">始点（以上）: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="rankstart" type="text" id="rankstart" value="{{old('rankstart')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="rankend" class="col-md-4 control-label">終点（未満）: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="rankend" type="text" id="rankend" value="{{old('rankend')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="rankscore" class="col-md-4 control-label">点数: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="rankscore" type="text" id="rankscore" value="{{old('rankscore')}}">
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
    