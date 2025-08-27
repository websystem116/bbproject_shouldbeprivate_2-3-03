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
        width: '200px'
    });
    $('.select_search2').select2({
        language: "ja",
        width: '300px'
    });
});
</script>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">アンケート結果確認</div>
                <div class="panel-body">


                    <a href="{{ url("/shinzemi/questionnaire_results_detail/create") }}" class="btn btn-success btn-sm" title="Add New questionnaire_results_detail">
                        新規追加
                    </a>


                    <div class="row">
                        <form method="GET" action="{{ url('questionnaire_results_detail') }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">

                            <div class="input-group">

                                <span class="input-group-text">
                                    アンケートNo
                                </span>

                                <input type="text" name="management_code" class="form-control" value="{{request('management_code')}}" placeholder="アンケートNo">

                            </div>
                            <div class="input-group">

                                <div class="input-group-text">
                                    タイトル
                                </div>

                                <select name="questionnaire_content_id" id="" class="form-control select_search2">
                                    <option value="">-</option>
                                    @foreach($questionnaire_contents as $questionnaire_content)
                                    <option value="{{ $questionnaire_content->id }}" @if(request('questionnaire_content_id')==$questionnaire_content->id) selected @endif>
                                        {{ $questionnaire_content->title }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="input-group">

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

                                <span class="input-group-text">
                                    学年
                                </span>

                                <select name="school_year_search" id="" class="form-control">
                                    <option value="">-</option>
                                    @foreach(config('const.school_year') as $school_year_id => $school_year)
                                    <option value="{{ $school_year_id }}" @if(request('school_year_search')==$school_year_id) selected @endif>
                                        {{ $school_year }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="input-group" style="margin-top:18px;">

                                <span class="input-group-btn">
                                    <button class="btn btn-info" type="submit">
                                        <span>検索</span>
                                    </button>
                                </span>

                            </div>

                        </form>

                    </div>

                    <br />
                    <br />

                    <div>{{ $questionnaire_results_detail->total() }} 件中 {{ $questionnaire_results_detail->firstItem() }} - {{ $questionnaire_results_detail->lastItem() }} 件を表示</div>


                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>アンケートNo</th>
                                    <th>タイトル</th>
                                    <th>校舎</th>
                                    <th>学年</th>
                                    <th>作成日</th>
                                    <th>更新日</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionnaire_results_detail as $item)

                                <tr>

                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->management_code}} </td>

                                    <td>{{ $item->title ?? ''}} </td>

                                    <td>{{ $item->name ?? ''}} </td>

                                    <td>{{ config('const.school_year')[$item->school_year_id] ?? ''}} </td>

                                    <!-- <td>{{ config('const.school_year_for_ancake')[$item->school_year_id] ?? ''}} </td> -->



                                    <td>{{ $item->created_at}} </td>

                                    <td>{{ $item->updated_at}} </td>


                                    <td>
                                        <a href="{{ url("/questionnaire_results_detail/" . $item->id . "/edit") }}" title="Edit questionnaire_results_detail"><button class="btn btn-primary btn-xs">編集</button></a>
                                    </td>
                                    <td>
                                        <!-- <form method="POST" action="/questionnaire_results_detail/{{ $item->id }}" class="form-horizontal" style="display:inline;"> -->
                                        {{ Form::open(['url' => '/questionnaire_results_detail/'.$item->id, 'method' => 'delete', 'class' => 'form-horizontal', 'style' => 'display:inline;']) }}
                                        {{ csrf_field() }}

                                        {{ method_field("DELETE") }}
                                        <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除してもよろしいでしょうか。')">
                                            削除
                                        </button>
                                        {{ Form::close() }}
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper">
                            <!-- {!! $questionnaire_results_detail->appends(["search" => Request::get("search")])->render() !!} -->
                            {!! $questionnaire_results_detail->appends(request()->all())->links() !!}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection