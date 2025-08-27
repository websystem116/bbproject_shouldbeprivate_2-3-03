@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">アンケート集計・確定</div>
                <div class="panel-body">
                    <!-- <a href="{{ url("/questionnaire_decision") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a> -->

                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif


                    <form method="POST" action="{{ route('questionnaire_decision.store') }}" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="form-group" style="display: flex;justify-content: space-around;">
                            <label for="name" class="col-md-2 control-label">アンケート内容: </label>
                            <div class="col-md-8">

                                <select class="form-control" name="questionnaire_content_id" id="questionnaire_content_id">
                                    @foreach ($questionnaire_contents_name as $questionnaire_content)
                                    <option value="{{ $questionnaire_content->id }}">{{ $questionnaire_content->title }}</option>
                                    @endforeach
                                </select>

                                <div class="text-danger">
                                    集計・確定済のアンケート内容は表示されません。
                                </div>

                                <div class="text-danger">
                                    集計・確定処理を行ったタイミングで講師別アンケート数値マスタの補正値と該当アンケートが紐づきます。
                                </div>

                            </div>
                        </div>

                        <!-- <div class="form-group">
                            <label for="questionnaire_contents_id" class="col-md-4 control-label">questionnaire_contents_id: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="questionnaire_contents_id" type="text" id="questionnaire_contents_id" value="{{old('questionnaire_contents_id')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id" class="col-md-4 control-label">user_id: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="user_id" type="text" id="user_id" value="{{old('user_id')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="classroom_score" class="col-md-4 control-label">classroom_score: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="classroom_score" type="text" id="classroom_score" value="{{old('classroom_score')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_score" class="col-md-4 control-label">subject_score: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="subject_score" type="text" id="subject_score" value="{{old('subject_score')}}">
                            </div>
                        </div> -->

                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                <input class="btn btn-primary" type="submit" value="集計・確定">
                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection