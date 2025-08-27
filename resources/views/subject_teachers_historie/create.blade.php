
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">Create New subject_teachers_historie</div>
                            <div class="panel-body">
                                <a href="{{ url("/subject_teachers_historie") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <br />
                                <br />

                                @if ($errors->any())
                                    <ul class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                
                                
                                <form method="POST" action="/subject_teachers_historie/store" class="form-horizontal">
                                    {{ csrf_field() }}

    										<div class="form-group">
                                        <label for="questionnaire_contents_id" class="col-md-4 control-label">questionnaire_contents_id: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="questionnaire_contents_id" type="text" id="questionnaire_contents_id" value="{{old('questionnaire_contents_id')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="school_year" class="col-md-4 control-label">school_year: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="school_year" type="text" id="school_year" value="{{old('school_year')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="classification_code_class" class="col-md-4 control-label">classification_code_class: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="classification_code_class" type="text" id="classification_code_class" value="{{old('classification_code_class')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="item_no_class" class="col-md-4 control-label">item_no_class: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="item_no_class" type="text" id="item_no_class" value="{{old('item_no_class')}}">
                                        </div>
                                    </div>
										<div class="form-group">
                                        <label for="user_id" class="col-md-4 control-label">user_id: </label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="user_id" type="text" id="user_id" value="{{old('user_id')}}">
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
    