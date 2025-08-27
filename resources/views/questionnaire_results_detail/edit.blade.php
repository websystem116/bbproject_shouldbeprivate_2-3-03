@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script>
$(function() {
    $('.select_search').select2({
        language: "ja",
        width: '550px'
    });

});
</script>
@endpush

@push('css')
<style>
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;

}
</style>
@endpush

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">編集</div>
                <div class="panel-body">
                    <!-- 戻るボタン previous -->
                    <a href="{{ url()->previous() }}" class="btn btn-warning btn-sm" title="Back">
                        戻る
                    </a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class=" alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <form method="POST" action="{{route('questionnaire_results_detail.update',$questionnaire_results_detail->id)}}" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field("PUT") }}

                        <div class="form-group">
                            <label for="id" class="col-md-4 control-label">id: </label>
                            <div class="col-md-6">{{$questionnaire_results_detail->id}}</div>
                        </div>
                        <div class="form-group">
                            <label for="management_code" class="col-md-4 control-label">アンケートNo: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="management_code" type="text" id="management_code" value="{{$questionnaire_results_detail->management_code}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="questionnaire_content_id" class="col-md-4 control-label">タイトル: </label>
                            <div class="col-md-6">
                                <select class="form-control select_search" name="questionnaire_content_id" id="questionnaire_content_id">
                                    @foreach($questionnaire_contents as $questionnaire_content)
                                    <option value="{{$questionnaire_content->id}}" @if($questionnaire_results_detail->questionnaire_content_id == $questionnaire_content->id) selected @endif>{{$questionnaire_content->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="school_building_id" class="col-md-4 control-label">校舎: </label>
                            <div class="col-md-6">
                                <select class="form-control select_search" name="school_building_id" id="school_building_id">
                                    <option value="">選択してください</option>
                                    @foreach($school_buildings as $school_building)
                                    <option value="{{$school_building->id}}" @if($questionnaire_results_detail->school_building_id == $school_building->id) selected @endif>{{$school_building->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="school_year_id" class="col-md-4 control-label">学年: </label>
                            <div class="col-md-6">
                                <select class="form-control" name="school_year_id" id="school_year_id">
                                    <option value="">選択してください</option>

                                    @foreach(config('const.school_year') as $key => $value)
                                    <option value="{{$key}}" @if($questionnaire_results_detail->school_year_id == $key) selected @endif>{{$value}}</option>
                                    @endforeach

                                    <!-- @foreach(config('const.school_year_for_ancake') as $key => $value)
                                    <option value="{{$key}}" @if($questionnaire_results_detail->school_year_id == $key) selected @endif>{{$value}}</option>
                                    @endforeach -->

                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject_id_1" class="col-md-4 control-label">英語: </label>
                            <!-- <div class="col-md-6">
                                <input class="form-control" name="subject_id_1" type="text" id="subject_id_1" value="{{$questionnaire_results_detail->subject_id_1}}">
                            </div> -->
                        </div>

                        <div class="form-group">
                            <label for="alphabet_id_1" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <select class="form-control" name="alphabet_id_1" id="alphabet_id_1">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}" @if($questionnaire_every_subjects_e->alphabet_id == $key) selected @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_id_1" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="user_id_1" type="text" id="user_id_1" value="{{$questionnaire_results_detail->user_id_1}}"> -->
                                <!-- select users -->
                                <select class="form-control select_search" name="user_id_1" id="user_id_1">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}" @if($questionnaire_every_subjects_e->user_id == $user->id) selected @endif>{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_1_1" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_1" type="number" id="question_1_1" value="{{$questionnaire_every_subjects_e->question1}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_1" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_1" type="number" id="question_2_1" value="{{$questionnaire_every_subjects_e->question2}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_1" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_1" type="number" id="question_3_1" value="{{$questionnaire_every_subjects_e->question3}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_1" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_1" type="number" id="question_4_1" value="{{$questionnaire_every_subjects_e->question4}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_1" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_1" type="number" id="question_5_1" value="{{$questionnaire_every_subjects_e->question5}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_1" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_1" type="number" id="question_6_1" value="{{$questionnaire_every_subjects_e->question6}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_1" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_1" type="number" id="question_7_1" value="{{$questionnaire_every_subjects_e->question7}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_id_2" class="col-md-4 control-label">理科: </label>
                            <!-- <div class="col-md-6">
                                <input class="form-control" name="subject_id_2" type="text" id="subject_id_2" value="{{$questionnaire_results_detail->subject_id_2}}">
                            </div> -->
                        </div>
                        <div class="form-group">
                            <label for="alphabet_id_2" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="alphabet_id_2" type="text" id="alphabet_id_2" value="{{$questionnaire_results_detail->alphabet_id_2}}"> -->
                                <!-- select alphabet -->
                                <select class="form-control" name="alphabet_id_2" id="alphabet_id_2">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}" @if($questionnaire_every_subjects_s->alphabet_id == $key) selected @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id_2" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="user_id_2" type="text" id="user_id_2" value="{{$questionnaire_results_detail->user_id_2}}"> -->
                                <!-- select users -->
                                <select class="form-control select_search" name="user_id_2" id="user_id_2">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}" @if($questionnaire_every_subjects_s->user_id == $user->id) selected @endif>{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_1_2" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_2" type="number" id="question_1_2" value="{{$questionnaire_every_subjects_s->question1}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_2" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_2" type="number" id="question_2_2" value="{{$questionnaire_every_subjects_s->question2}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_2" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_2" type="number" id="question_3_2" value="{{$questionnaire_every_subjects_s->question3}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_2" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_2" type="number" id="question_4_2" value="{{$questionnaire_every_subjects_s->question4}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_2" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_2" type="number" id="question_5_2" value="{{$questionnaire_every_subjects_s->question5}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_2" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_2" type="number" id="question_6_2" value="{{$questionnaire_every_subjects_s->question6}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_2" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_2" type="number" id="question_7_2" value="{{$questionnaire_every_subjects_s->question7}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_id_3" class="col-md-4 control-label">数学: </label>
                            <!-- <div class="col-md-6">
                                <input class="form-control" name="subject_id_3" type="text" id="subject_id_3" value="{{$questionnaire_results_detail->subject_id_3}}">
                            </div> -->
                        </div>
                        <div class="form-group">
                            <label for="alphabet_id_3" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="alphabet_id_3" type="text" id="alphabet_id_3" value="{{$questionnaire_results_detail->alphabet_id_3}}"> -->
                                <select class="form-control" name="alphabet_id_3" id="alphabet_id_3">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}" @if($questionnaire_every_subjects_m->alphabet_id == $key) selected @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id_3" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <select class="form-control select_search" name="user_id_3" id="user_id_3">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}" @if($questionnaire_every_subjects_m->user_id == $user->id) selected @endif>{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_1_3" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_3" type="number" id="question_1_3" value="{{$questionnaire_every_subjects_m->question1}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_3" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_3" type="number" id="question_2_3" value="{{$questionnaire_every_subjects_m->question2}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_3" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_3" type="number" id="question_3_3" value="{{$questionnaire_every_subjects_m->question3}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_3" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_3" type="number" id="question_4_3" value="{{$questionnaire_every_subjects_m->question4}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_3" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_3" type="number" id="question_5_3" value="{{$questionnaire_every_subjects_m->question5}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_3" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_3" type="number" id="question_6_3" value="{{$questionnaire_every_subjects_m->question6}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_3" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_3" type="number" id="question_7_3" value="{{$questionnaire_every_subjects_m->question7}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_id_4" class="col-md-4 control-label">国語: </label>
                        </div>
                        <div class="form-group">
                            <label for="alphabet_id_4" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <select class="form-control" name="alphabet_id_4" id="alphabet_id_4">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}" @if($questionnaire_every_subjects_j->alphabet_id == $key) selected @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_id_4" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <select class="form-control select_search" name="user_id_4" id="user_id_4">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}" @if($questionnaire_every_subjects_j->user_id == $user->id) selected @endif>{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="question_1_4" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_4" type="number" id="question_1_4" value="{{$questionnaire_every_subjects_j->question1}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_4" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_4" type="number" id="question_2_4" value="{{$questionnaire_every_subjects_j->question2}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_4" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_4" type="number" id="question_3_4" value="{{$questionnaire_every_subjects_j->question3}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_4" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_4" type="number" id="question_4_4" value="{{$questionnaire_every_subjects_j->question4}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_4" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_4" type="number" id="question_5_4" value="{{$questionnaire_every_subjects_j->question5}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_4" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_4" type="number" id="question_6_4" value="{{$questionnaire_every_subjects_j->question6}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_4" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_4" type="number" id="question_7_4" value="{{$questionnaire_every_subjects_j->question7}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_id_5" class="col-md-4 control-label">社会: </label>
                            <!-- <div class="col-md-6">
                                <input class="form-control" name="subject_id_5" type="text" id="subject_id_5" value="{{$questionnaire_results_detail->subject_id_5}}">
                            </div> -->
                        </div>

                        <div class="form-group">
                            <label for="alphabet_id_5" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="alphabet_id_5" type="text" id="alphabet_id_5" value="{{$questionnaire_results_detail->alphabet_id_5}}"> -->
                                <!-- select alphabet -->
                                <select class="form-control" name="alphabet_id_5" id="alphabet_id_5">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}" @if($questionnaire_every_subjects_so->alphabet_id == $key) selected @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id_5" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="user_id_5" type="text" id="user_id_5" value="{{$questionnaire_results_detail->user_id_5}}"> -->
                                <!-- select users -->
                                <select class="form-control select_search" name="user_id_5" id="user_id_5">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}" @if($questionnaire_every_subjects_so->user_id == $user->id) selected @endif>{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_1_5" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_5" type="number" id="question_1_5" value="{{$questionnaire_every_subjects_so->question1}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_5" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_5" type="number" id="question_2_5" value="{{$questionnaire_every_subjects_so->question2}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_5" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_5" type="number" id="question_3_5" value="{{$questionnaire_every_subjects_so->question3}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_5" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_5" type="number" id="question_4_5" value="{{$questionnaire_every_subjects_so->question4}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_5" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_5" type="number" id="question_5_5" value="{{$questionnaire_every_subjects_so->question5}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_5" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_5" type="number" id="question_6_5" value="{{$questionnaire_every_subjects_so->question6}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_5" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_5" type="number" id="question_7_5" value="{{$questionnaire_every_subjects_so->question7}}" min=0 max=4>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject_id_6" class="col-md-4 control-label">その他: </label>
                            <!-- <div class="col-md-6">
                                <input class="form-control" name="subject_id_6" type="text" id="subject_id_6" value="{{old('subject_id_6')}}">
                            </div> -->
                        </div>

                        <div class="form-group">
                            <label for="alphabet_id_5" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="alphabet_id_6" type="text" id="alphabet_id_6" value="{{$questionnaire_results_detail->alphabet_id_6}}"> -->
                                <select class="form-control" name="alphabet_id_6" id="alphabet_id_6">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}" @if($questionnaire_every_subjects_o->alphabet_id == $key) selected @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_id_6" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <select class="form-control select_search" name="user_id_6" id="user_id_6">
                                    <!-- <input class="form-control" name="user_id_6" type="text" id="user_id_6" value="{{$questionnaire_results_detail->user_id_6}}"> -->
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}" @if($questionnaire_every_subjects_o->user_id == $user->id) selected @endif>{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="question_1_6" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_6" type="number" id="question_1_6" value="{{$questionnaire_every_subjects_o->question1}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_6" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_6" type="number" id="question_2_6" value="{{$questionnaire_every_subjects_o->question2}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_6" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_6" type="number" id="question_3_6" value="{{$questionnaire_every_subjects_o->question3}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_6" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_6" type="number" id="question_4_6" value="{{$questionnaire_every_subjects_o->question4}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_6" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_6" type="number" id="question_5_6" value="{{$questionnaire_every_subjects_o->question5}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_6" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_6" type="number" id="question_6_6" value="{{$questionnaire_every_subjects_o->question6}}" min=0 max=4>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_6" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_6" type="number" id="question_7_6" value="{{$questionnaire_every_subjects_o->question7}}" min=0 max=4>
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