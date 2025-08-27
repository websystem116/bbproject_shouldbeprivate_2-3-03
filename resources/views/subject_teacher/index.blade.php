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
        width: '200px'
    });
});
</script>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">科目担当講師マスタ</div>
                <div class="panel-body">

                    <div>
                        <a href="{{ url('/shinzemi/subject_teacher/create') }}" class="btn btn-success btn-sm" title="Add New subject_teacher">
                            新規追加
                        </a>
                    </div>

                    <form method="GET" action="{{ url('/shinzemi/subject_teacher') }}" accept-charset="UTF-8" class="" role="search" style="display:flex;justify-content: flex-end;">

                        <div style="display:flex;align-items:end;">

                            <div class="input-group" style="align-self:start;">
                                <div class="input-group-text">
                                    講師
                                </div>
                                <select name="user_id" id="" class="form-control select_search">
                                    <option value="">-</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" @if(request('user_id')==$user->id) selected @endif>
                                        {{ $user->last_name }}{{ $user->first_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group" style="align-self:start;">
                                <div class="input-group-text">
                                    校舎
                                </div>
                                <select name="school_building_id" id="" class="form-control select_search">
                                    <option value="">-</option>
                                    @foreach($school_buildings as $school_building)
                                    <option value="{{ $school_building->id }}" @if(request('school_building_id')==$school_building->id) selected @endif>
                                        {{ $school_building->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <div class="input-group-text">
                                    学年
                                </div>
                                <select name="school_year_search" id="" class="form-control">
                                    <option value="">-</option>
                                    @foreach(config('const.school_year') as $school_year_id => $school_year)
                                    <option value="{{ $school_year_id }}" @if(request('school_year_search')==$school_year_id) selected @endif>
                                        {{ $school_year }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <div class="input-group-text">
                                    科目
                                </div>
                                <select name="subject_search" id="" class="form-control">
                                    <option value="">-</option>
                                    @foreach(config('const.subjects') as $subject_id => $subject)
                                    <option value="{{ $subject_id }}" @if(request('subject_search')==$subject_id) selected @endif>
                                        {{ $subject }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <div class="input-group-text">
                                    クラス
                                </div>
                                <select name="alphabet_search" id="" class="form-control">
                                    <option value="">-</option>
                                    @foreach(config('const.alphabets') as $alphabet_id => $alphabet)
                                    <option value="{{ $alphabet_id }}" @if(request('alphabet_search')==$alphabet_id) selected @endif>
                                        {{ $alphabet }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn btn-info" type="submit">
                                        <span>検索</span>
                                    </button>
                                </span>
                            </div>

                        </div>

                    </form>


                    <div style="padding-top:24px;">{{ $subject_teacher->total() }} 件中 {{ $subject_teacher->firstItem() }} - {{ $subject_teacher->lastItem() }} 件を表示</div>


                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>校舎</th>
                                    <th>学年</th>
                                    <th>科目</th>
                                    <th>クラス</th>
                                    <th>講師</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subject_teacher as $item)

                                <tr>

                                    <td>{{ $item->id}}</td>

                                    <td>{{ $item->school_building->name}} </td>

                                    <td>{{ config('const.school_year')[$item->school_year]}} </td>

                                    <td>{{ config('const.subjects')[$item->classification_code_class]}} </td>

                                    <td>{{ config('const.alphabets')[$item->item_no_class]}} </td>

                                    <td>{{ $item->user->last_name}}{{ $item->user->first_name}}</td>

                                    <td><a href="{{ url("/subject_teacher/" . $item->id . "/edit") }}" title="Edit subject_teacher"><button class="btn btn-primary btn-xs">編集</button></a></td>
                                    <td>
                                        <form method="POST" action="{{route('subject_teacher.destroy',$item->id)}}" class="form-horizontal" style="display:inline;">
                                            {{ csrf_field() }}

                                            {{ method_field("DELETE") }}
                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除しますか')">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper">
                            {!! $subject_teacher->appends(request()->all())->links() !!}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection