@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<style type="text/css">
.select2-selection__rendered {
    line-height: 31px !important;
}

.select2-container .select2-selection--single {
    height: 34px !important;
}

.select2-selection__arrow {
    height: 31px !important;
}
</style>
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script>
$(function() {
    $('.select_search').select2({
        language: "ja",
    });
});
</script>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">編集 #{{ $subject_teacher->id }}</div>
                <div class="panel-body">
                    <!-- <a href="{{ url("subject_teacher") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a> -->
                    <a href="{{ url()->previous() }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>

                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <form method="POST" action="{{ route('subject_teacher.update',$subject_teacher->id) }}" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field("PUT") }}

                        <div class="form-group">
                            <label for="id" class="col-md-4 control-label">科目担当講師マスタ: </label>
                            <div class="col-md-6">{{$subject_teacher->id}}</div>
                        </div>

                        <!-- form select school_building_id -->
                        <div class="form-group">
                            <label for="school_building_id" class="col-md-4 control-label">
                                校舎:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                <select class="form-control" name="school_building_id" id="school_building_id">
                                    @foreach ($school_buildings as $school_building)
                                    <option value="{{$school_building->id}}" @if($subject_teacher->school_building_id == $school_building->id )selected @endif>{{ $school_building->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="school_year" class="col-md-4 control-label">
                                学年:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                {{-- <input class="form-control" name="school_year" type="text" id="school_year" value="{{$subject_teacher->school_year}}"> --}}
                                <select class="form-control" name="school_year" id="school_year">
                                    @foreach (config('const.school_year') as $key => $value)
                                    <option value="{{$key}}" @if($subject_teacher->school_year==$key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="classification_code_class" class="col-md-4 control-label">
                                科目:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="classification_code_class" type="text" id="classification_code_class" value="{{$subject_teacher->classification_code_class}}"> -->
                                <select class="form-control" name="classification_code_class" id="classification_code_class">
                                    @foreach (config('const.subjects') as $key => $value)
                                    <option value="{{$key}}" @if($subject_teacher->classification_code_class==$key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="item_no_class" class="col-md-4 control-label">
                                クラス:
                                <span class="text-danger">※</span>

                            </label>
                            <div class="col-md-6">
                                <!-- <input class="form-control" name="item_no_class" type="text" id="item_no_class" value="{{$subject_teacher->item_no_class}}"> -->
                                <select class="form-control" name="item_no_class" id="item_no_class">
                                    @foreach (config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}" @if($subject_teacher->item_no_class==$key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user_id" class="col-md-4 control-label">
                                講師:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                <select class="form-control select_search" name="user_id" id="user_id">
                                    @foreach ($users as $user)
                                    <option value="{{$user->id}}" @if($subject_teacher->user_id==$user->id) selected @endif>{{ $user->last_name }}{{ $user->first_name }}</option>
                                    @endforeach
                                </select>

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