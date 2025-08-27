@extends('layouts.app')

@section('content')
<!-- <script src="{{ asset('/js/questionnaire_output.js') }}"></script> -->

@push('css')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endpush

<script>
$(function() {

    // ランキング表
    $('#ranking').on('click', function() {

        // get user_category value
        const user_category = $('#user_category').val();

        var questionnaire_content_id = $("#questionnaire_content_id").val();

        var url = '{{ route("questionnaire.export",["id" => 9999999999 ]) }}'
        url = url.replace("9999999999", questionnaire_content_id);

        document.mainform.action = url;
        document.mainform.submit();
    })

    //講師別評価点一覧
    $('#evaluate_scores').on('click', function() {

        // get user_category value
        const user_category = $('#user_category').val();

        var questionnaire_content_id = $("#questionnaire_content_id").val();
        var url = '{{ route("questionnaire.export_teacher_evaluation",["id" => 9999999999 ]) }}';

        url = url.replace("9999999999", questionnaire_content_id);

        document.mainform.action = url;
        document.mainform.submit();
    })

    // 教室別
    $('#export_every_classroom').on('click', function() {

        // get user_category value
        const user_category = $('#user_category').val();

        var questionnaire_content_id = $("#questionnaire_content_id").val();
        var url = '{{ route("questionnaire.export_every_classroom",["id" => 9999999999 ]) }}';
        url = url.replace("9999999999", questionnaire_content_id);

        document.mainform.action = url;
        document.mainform.submit();
    })

    // 講師別一覧
    $('#export_every_teachers').on('click', function() {

        // get user_category value
        const user_category = $('#user_category').val();

        var questionnaire_content_id = $("#questionnaire_content_id").val();
        var url = '{{ route("questionnaire.export_every_teachers",["id" => 9999999999 ]) }}';
        url = url.replace("9999999999", questionnaire_content_id);

        document.mainform.action = url;
        document.mainform.submit();
    })
})
</script>

<div class="container">
    <div class="row ">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">アンケート結果出力</div>
                <div class="panel-body">

                    <!-- <div>集計・確定済のアンケート内容が表示されます</div> -->

                    <form action="" name="mainform">

                        <div class="form-group">
                            <select class="form-control" name="questionnaire_content_id" id="questionnaire_content_id">
                                @foreach ($questionnaire_contents as $questionnaire_content)
                                <option value="{{$questionnaire_content->id}}">{{ $questionnaire_content->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- radio button 全体、社員のみ、非常勤のみ -->
                        <div class="form-group">
                            <label class="radio-inline">
                                <input type="radio" name="employment_status" value="1" checked> 全体
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="employment_status" value="2"> 社員のみ
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="employment_status" value="3"> 非常勤のみ
                            </label>
                        </div>

                        <div class="" style="margin-top: 24px;">

                            <div style="padding-bottom:8px">
                                <button type="button" id="evaluate_scores" class="btn btn-lg  btn-primary">
                                    講師別評価点一覧
                                </button>
                            </div>

                            <div style="padding-bottom:8px">
                                <button type="button" id="export_every_teachers" class="btn btn-lg btn-primary">
                                    講師別一覧
                                </button>
                            </div>

                            <div style="padding-bottom:8px">
                                <button type="button" id="export_every_classroom" class="btn btn-lg  btn-primary">
                                    教室別
                                </button>
                            </div>

                            <div style="padding-bottom:8px">
                                <button type="button" id="ranking" class="btn btn-lg  btn-primary">
                                    ランキング表
                                </button>
                            </div>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection