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

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">新規登録</div>
                <div class="panel-body">
                    <a href="{{ url("/questionnaire_results_detail") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif


                    <form method="POST" action="{{route('questionnaire_results_detail.store')}}" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="management_code" class="col-md-4 control-label">アンケートNo: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="management_code" type="text" id="management_code" value="{{old('management_code')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="questionnaire_content_id" class="col-md-4 control-label">タイトル: </label>
                            <div class="col-md-6">
                                <select class="form-control select_search" name="questionnaire_content_id" id="questionnaire_content_id">
                                    @foreach($questionnaire_contents as $questionnaire_content)
                                    <option value="{{$questionnaire_content->id}}">{{$questionnaire_content->title}}</option>
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
                                    <option value="{{$school_building->id}}">{{$school_building->name}}</option>
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
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_id_1" class="col-md-4 control-label">英語: </label>
                        </div>

                        <div class="form-group">
                            <label for="alphabet_id_1" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <select class="form-control" name="alphabet_id_1" id="alphabet_id_1">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user_id_1" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <select class="form-control select_search" name="user_id_1" id="user_id_1">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="question_1_1" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_1" type="number" id="question_1_1" value="{{old('question_1_1')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_1" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_1" type="number" id="question_2_1" value="{{old('question_2_1')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_1" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_1" type="number" id="question_3_1" value="{{old('question_3_1')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_1" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_1" type="number" id="question_4_1" value="{{old('question_4_1')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_1" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_1" type="number" id="question_5_1" value="{{old('question_5_1')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_1" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_1" type="number" id="question_6_1" value="{{old('question_6_1')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_1" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_1" type="number" id="question_7_1" value="{{old('question_7_1')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_id_2" class="col-md-4 control-label">理科: </label>

                        </div>
                        <div class="form-group">
                            <label for="alphabet_id_2" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="alphabet_id_2" type="text" id="alphabet_id_2" value="{{old('alphabet_id_2')}}"> -->
                                <select class="form-control" name="alphabet_id_2" id="alphabet_id_2">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id_2" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="user_id_2" type="text" id="user_id_2" value="{{old('user_id_2')}}"> -->
                                <select class="form-control select_search" name="user_id_2" id="user_id_2">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_1_2" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_2" type="number" id="question_1_2" value="{{old('question_1_2')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_2" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_2" type="number" id="question_2_2" value="{{old('question_2_2')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_2" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_2" type="number" id="question_3_2" value="{{old('question_3_2')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_2" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_2" type="number" id="question_4_2" value="{{old('question_4_2')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_2" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_2" type="number" id="question_5_2" value="{{old('question_5_2')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_2" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_2" type="number" id="question_6_2" value="{{old('question_6_2')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_2" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_2" type="number" id="question_7_2" value="{{old('question_7_2')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_id_3" class="col-md-4 control-label">数学: </label>
                            <!-- <div class="col-md-6">
                                <input class="form-control" name="subject_id_3" type="text" id="subject_id_3" value="{{old('subject_id_3')}}">
                            </div> -->
                        </div>
                        <div class="form-group">
                            <label for="alphabet_id_3" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="alphabet_id_3" type="text" id="alphabet_id_3" value="{{old('alphabet_id_3')}}"> -->
                                <select class="form-control" name="alphabet_id_3" id="alphabet_id_3">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id_3" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="user_id_3" type="text" id="user_id_3" value="{{old('user_id_3')}}"> -->
                                <select class="form-control select_search" name="user_id_3" id="user_id_3">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_1_3" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_3" type="number" id="question_1_3" value="{{old('question_1_3')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_3" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_3" type="number" id="question_2_3" value="{{old('question_2_3')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_3" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_3" type="number" id="question_3_3" value="{{old('question_3_3')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_3" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_3" type="number" id="question_4_3" value="{{old('question_4_3')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_3" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_3" type="number" id="question_5_3" value="{{old('question_5_3')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_3" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_3" type="number" id="question_6_3" value="{{old('question_6_3')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_3" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_3" type="number" id="question_7_3" value="{{old('question_7_3')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_id_4" class="col-md-4 control-label">国語: </label>
                            <!-- <div class="col-md-6">
                                <input class="form-control" name="subject_id_4" type="text" id="subject_id_4" value="{{old('subject_id_4')}}">
                            </div> -->
                        </div>
                        <div class="form-group">
                            <label for="alphabet_id_4" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="alphabet_id_4" type="text" id="alphabet_id_4" value="{{old('alphabet_id_4')}}"> -->
                                <select class="form-control" name="alphabet_id_4" id="alphabet_id_4">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id_4" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="user_id_4" type="text" id="user_id_4" value="{{old('user_id_4')}}"> -->
                                <select class="form-control select_search" name="user_id_4" id="user_id_4">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_1_4" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_4" type="number" id="question_1_4" value="{{old('question_1_4')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_4" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_4" type="number" id="question_2_4" value="{{old('question_2_4')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_4" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_4" type="number" id="question_3_4" value="{{old('question_3_4')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_4" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_4" type="number" id="question_4_4" value="{{old('question_4_4')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_4" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_4" type="number" id="question_5_4" value="{{old('question_5_4')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_4" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_4" type="number" id="question_6_4" value="{{old('question_6_4')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_4" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_4" type="number" id="question_7_4" value="{{old('question_7_4')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject_id_5" class="col-md-4 control-label">社会: </label>
                            <!-- <div class="col-md-6">
                                <input class="form-control" name="subject_id_5" type="text" id="subject_id_5" value="{{old('subject_id_5')}}">
                            </div> -->
                        </div>
                        <div class="form-group">
                            <label for="alphabet_id_5" class="col-md-4 control-label">クラス: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="alphabet_id_5" type="text" id="alphabet_id_5" value="{{old('alphabet_id_5')}}"> -->
                                <select class="form-control" name="alphabet_id_5" id="alphabet_id_5">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id_5" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="user_id_5" type="text" id="user_id_5" value="{{old('user_id_5')}}"> -->
                                <select class="form-control select_search" name="user_id_5" id="user_id_5">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_1_5" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_5" type="number" id="question_1_5" value="{{old('question_1_5')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_5" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_5" type="number" id="question_2_5" value="{{old('question_2_5')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_5" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_5" type="number" id="question_3_5" value="{{old('question_3_5')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_5" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_5" type="number" id="question_4_5" value="{{old('question_4_5')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_5" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_5" type="number" id="question_5_5" value="{{old('question_5_5')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_5" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_5" type="number" id="question_6_5" value="{{old('question_6_5')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_5" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_5" type="number" id="question_7_5" value="{{old('question_7_5')}}">
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
                                <!-- <input class="form-control" name="alphabet_id_6" type="text" id="alphabet_id_6" value="{{old('alphabet_id_6')}}"> -->
                                <select class="form-control" name="alphabet_id_6" id="alphabet_id_6">
                                    <option value="">選択してください</option>
                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id_6" class="col-md-4 control-label">講師: </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="user_id_6" type="text" id="user_id_6" value="{{old('user_id_6')}}"> -->
                                <select class="form-control select_search" name="user_id_6" id="user_id_6">
                                    <option value="">選択してください</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->last_name}}{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_1_6" class="col-md-4 control-label">質問1: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_1_6" type="number" id="question_1_6" value="{{old('question_1_6')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_2_6" class="col-md-4 control-label">質問2: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_2_6" type="number" id="question_2_6" value="{{old('question_2_6')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_3_6" class="col-md-4 control-label">質問3: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_3_6" type="number" id="question_3_6" value="{{old('question_3_6')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_4_6" class="col-md-4 control-label">質問4: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_4_6" type="number" id="question_4_6" value="{{old('question_4_6')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_5_6" class="col-md-4 control-label">質問5: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_5_6" type="number" id="question_5_6" value="{{old('question_5_6')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_6_6" class="col-md-4 control-label">質問6: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_6_6" type="number" id="question_6_6" value="{{old('question_6_6')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="question_7_6" class="col-md-4 control-label">質問7: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="question_7_6" type="number" id="question_7_6" value="{{old('question_7_6')}}">
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label for="Creator" class="col-md-4 control-label">Creator: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="Creator" type="text" id="Creator" value="{{old('Creator')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Updater" class="col-md-4 control-label">Updater: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="Updater" type="text" id="Updater" value="{{old('Updater')}}">
                            </div>
                        </div> -->

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