
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Create New authoritie</div>
                            <div class="panel-body">
                                <a href="{{ url("/authoritie") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <br />
                                <br />

                                @if ($errors->any())
                                    <ul class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                
                                
                                <form method="POST" action="/authoritie/store" class="form-horizontal">
                                    {{ csrf_field() }}

    										<div class="form-group">
                                        <label for="user_id" class="col-md-4 control-label">user_id: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="user_id" type="text" id="user_id" value="{{old('user_id')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="password" class="col-md-4 control-label">password: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="password" type="text" id="password" value="{{old('password')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="classification_code" class="col-md-4 control-label">classification_code: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="classification_code" type="text" id="classification_code" value="{{old('classification_code')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="item_no" class="col-md-4 control-label">item_no: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="item_no" type="text" id="item_no" value="{{old('item_no')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="Is_need_password" class="col-md-4 control-label">Is_need_password: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="Is_need_password" type="text" id="Is_need_password" value="{{old('Is_need_password')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="last_login_date" class="col-md-4 control-label">last_login_date: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="last_login_date" type="text" id="last_login_date" value="{{old('last_login_date')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="changed_password_date" class="col-md-4 control-label">changed_password_date: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="changed_password_date" type="text" id="changed_password_date" value="{{old('changed_password_date')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="fail_times_login" class="col-md-4 control-label">fail_times_login: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="fail_times_login" type="text" id="fail_times_login" value="{{old('fail_times_login')}}">
                                        </div>
                                    </div>
                    
                                    <div class="form-group">
                                        <div class="col-md-offset-4 col-md-4">
                                            <input class="btn btn-primary" type="submit" value="Create">
                                        </div>
                                    </div>     
                                </form>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    