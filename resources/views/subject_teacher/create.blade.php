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
                <div class="panel-heading">新規登録</div>
                <div class="panel-body">
                    <a href="{{ url("/subject_teacher") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif


                    <form method="POST" action="{{route('subject_teacher.store')}}" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="school_building_id" class="col-md-4 control-label">
                                校舎:
                                <span class="text-danger">※</span>
                            </label>

                            <div class="col-md-6">
                                <select class="form-control" name="school_building_id" id="school_building_id">
                                    <option value="">-</option>
                                    @foreach ($school_buildings as $school_building)
                                    <option value="{{$school_building->id}}">{{ $school_building->name }}</option>
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
                                <select class="form-control" name="school_year" id="school_year">
                                    <option value="">-</option>

                                    @foreach (config('const.school_year') as $key => $value)
                                    <option value="{{$key}}">{{ $value }}</option>
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
                                <select class="form-control" name="classification_code_class" id="classification_code_class">
                                    <option value="">-</option>

                                    @foreach(config('const.subjects') as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
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
                                <select class="form-control" name="item_no_class" id="item_no_class">
                                    <option value="">-</option>

                                    @foreach(config('const.alphabets') as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
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
                                    <option value="">-</option>

                                    @foreach ($users as $user)
                                    <option value="{{$user->id}}">{{ $user->last_name }}{{ $user->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

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