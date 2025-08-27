
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Create New company</div>
                            <div class="panel-body">
                                <a href="{{ url("/company") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <br />
                                <br />

                                @if ($errors->any())
                                    <ul class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                
                                
                                <form method="POST" action="/company/store" class="form-horizontal">
                                    {{ csrf_field() }}

    										<div class="form-group">
                                        <label for="name" class="col-md-4 control-label">name: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="name" type="text" id="name" value="{{old('name')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="name_short" class="col-md-4 control-label">name_short: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="name_short" type="text" id="name_short" value="{{old('name_short')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="zipcode" class="col-md-4 control-label">zipcode: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="zipcode" type="text" id="zipcode" value="{{old('zipcode')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="address1" class="col-md-4 control-label">address1: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="address1" type="text" id="address1" value="{{old('address1')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="address2" class="col-md-4 control-label">address2: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="address2" type="text" id="address2" value="{{old('address2')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="address3" class="col-md-4 control-label">address3: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="address3" type="text" id="address3" value="{{old('address3')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="tel" class="col-md-4 control-label">tel: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="tel" type="text" id="tel" value="{{old('tel')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="fax" class="col-md-4 control-label">fax: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="fax" type="text" id="fax" value="{{old('fax')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="email" class="col-md-4 control-label">email: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="email" type="text" id="email" value="{{old('email')}}">
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
    