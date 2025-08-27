
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Edit manage_target #{{ $manage_target->id }}</div>
                            <div class="panel-body">
                                <a href="{{ url("manage_target") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <br />
                                <br />

                            @if ($errors->any())
                                <ul class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
    
                            <form method="POST" action="/manage_target/{{ $manage_target->id }}" class="form-horizontal">
                                        {{ csrf_field() }}
                                        {{ method_field("PUT") }}
            
										<div class="form-group">
                                        <label for="id" class="col-md-4 control-label">id: </label>
                                        <div class="col-md-6">{{$manage_target->id}}</div>
                                    </div>
										<div class="form-group">
                                            <label for="year" class="col-md-4 control-label">year: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="year" type="text" id="year" value="{{$manage_target->year}}">
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="taget_classification" class="col-md-4 control-label">taget_classification: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="taget_classification" type="text" id="taget_classification" value="{{$manage_target->taget_classification}}">
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="target_value" class="col-md-4 control-label">target_value: </label>
                                            <div class="col-md-6">
                                                <input class="form-control" name="target_value" type="text" id="target_value" value="{{$manage_target->target_value}}">
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
    