@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />

<style type="text/css">
.select2-selection__rendered {
    line-height: 31px !important;
}

.select2-container .select2-selection--single {
    height: 36px !important;
}

.select2-selection__arrow {
    height: 34px !important;
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
                <div class="panel-heading">講師別アンケート数値マスタ</div>
                <div class="panel-body">


                    <!-- <a href="{{ url('/shinzemi/questionnaire_score/create') }}" class="btn btn-success btn-sm" title="Add New questionnaire_score">
                        新規作成
                    </a> -->

                    <form method="GET" action="{{ url('/shinzemi/questionnaire_score') }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                        <div class="input-group">
                            <!-- <input type="text" class="form-control" name="search" placeholder=""> -->

                            <select name="search" id="" class="form-control select_search">
                                <option value="">-</option>
                                @foreach($users_for_select as $user)
                                <option value="{{ $user->id }}" @if(request('search')==$user->id) selected @endif>
                                    {{ $user->last_name }} {{ $user->first_name }}
                                </option>
                                @endforeach
                            </select>


                            <span class="input-group-btn">
                                <button class="btn btn-info" type="submit">
                                    <span>検索</span>
                                </button>
                            </span>
                        </div>
                    </form>

                    <br />
                    <br />

                    <div>{{ $users->total() }} 件中 {{ $users->firstItem() }} - {{ $users->lastItem() }} 件を表示</div>


                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>講師</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $item)

                                <tr>
                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->last_name}}{{ $item->first_name}}</td>


                                    <td><a href="{{ url("/questionnaire_score/" . $item->id . "/edit") }}" title="Edit questionnaire_score"><button class="btn btn-primary btn-xs">編集</button></a></td>
                                    <td>
                                        <form method="POST" action="{{route('questionnaire_score.destroy',$item->id)}}" class="form-horizontal" style="display:inline;">
                                            {{ csrf_field() }}

                                            {{ method_field("DELETE") }}
                                        </form>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $users->appends(request()->all())->links() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection