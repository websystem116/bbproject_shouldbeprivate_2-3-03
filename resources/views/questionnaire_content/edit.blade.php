@extends("layouts.app")
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">編集 #{{ $questionnaire_content->id }}</div>
                <div class="panel-body">
                    <a href="{{ url('/shinzemi/questionnaire_content') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <form method="POST" action="{{ route('questionnaire_content.update', $questionnaire_content->id) }}" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}

                        <div class="form-group">
                            <label for="id" class="col-md-4 control-label">id: </label>
                            <div class="col-md-6">{{ $questionnaire_content->id }}</div>
                        </div>
                        <div class="form-group">
                            <label for="title" class="col-md-4 control-label">
                                タイトル:
                                <span class="text-danger">
                                    ※
                                </span>
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" required="required" name="title" type="text" id="title" value="{{ $questionnaire_content->title }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="summary" class="col-md-4 control-label">概要: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="summary" type="text" id="summary" value="{{ $questionnaire_content->summary }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title" class="col-md-4 control-label">年月: </label>
                            <div class="col-md-6">
                                <input class="form-control" type="month" id="datepicker" name="month" value="{{ $questionnaire_content->month }}">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 40px;">


                            <div class="form-group">
                                <label for="question1_compensation" class="col-md-4 control-label">補正値1: </label>
                                <div class="col-md-6">
                                    <input class="form-control" name="question1_compensation" type="number" id="question1_compensation" value="{{ $questionnaire_content->question1_compensation }}">
                                </div>
                            </div>


                        </div>

                        <div class="row" style="margin-top: 40px;">

                            <div class="form-group">
                                <label for="question2_compensation" class="col-md-4 control-label">補正値2: </label>
                                <div class="col-md-6">
                                    <input class="form-control" name="question2_compensation" type="number" id="question2_compensation" value="{{ $questionnaire_content->question2_compensation }}">
                                </div>
                            </div>

                        </div>


                        <div class="row" style="margin-top: 40px;">

                            <div class="form-group">
                                <label for="question3_compensation" class="col-md-4 control-label">補正値3: </label>
                                <div class="col-md-6">
                                    <input class="form-control" name="question3_compensation" type="number" id="question3_compensation" value="{{ $questionnaire_content->question3_compensation }}">
                                </div>
                            </div>

                        </div>

                        <div class="row" style="margin-top: 40px;">


                            <div class="form-group">
                                <label for="question4_compensation" class="col-md-4 control-label">補正値4: </label>
                                <div class="col-md-6">
                                    <input class="form-control" name="question4_compensation" type="number" id="question4_compensation" value="{{ $questionnaire_content->question4_compensation }}">
                                </div>
                            </div>

                        </div>

                        <div class="row" style="margin-top: 40px;">

                            <div class="form-group">
                                <label for="question5_compensation" class="col-md-4 control-label">補正値5: </label>
                                <div class="col-md-6">
                                    <input class="form-control" name="question5_compensation" type="number" id="question5_compensation" value="{{ $questionnaire_content->question5_compensation }}">
                                </div>
                            </div>

                        </div>


                        <div class="row" style="margin-top: 40px;">

                            <div class="form-group">
                                <label for="question6_compensation" class="col-md-4 control-label">補正値6: </label>
                                <div class="col-md-6">
                                    <input class="form-control" name="question6_compensation" type="number" id="question6_compensation" value="{{ $questionnaire_content->question6_compensation }}">
                                </div>
                            </div>

                        </div>


                        <div class="row" style="margin-top: 40px;">

                            <div class="form-group">
                                <label for="question7_compensation" class="col-md-4 control-label">補正値7: </label>
                                <div class="col-md-6">
                                    <input class="form-control" name="question7_compensation" type="number" id="question7_compensation" value="{{ $questionnaire_content->question7_compensation }}">
                                </div>
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