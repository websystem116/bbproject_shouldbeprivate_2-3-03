@extends("layouts.app")
@section("content")
<script src="{{ asset('/js/result_category_sortable.js') }}"></script>
<!-- Sortable読み込み -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<!-- Sortableの実装 -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">成績カテゴリーマスタ</div>
                <div class="panel-body">
                    <div class="form-group">
                        <a href="{{ url('/shinzemi/result_category/create') }}" class="btn btn-success btn-sm" title="Add New student">
                            新規成績カテゴリー追加
                        </a>
                    </div>

                    <div>{{ $result_category->total() }} 件中 {{ $result_category->firstItem() }} - {{ $result_category->lastItem() }} 件を表示</div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>成績カテゴリーNo</th>
                                    <th>成績カテゴリー名</th>
                                    <th>実施回の登録</th>
                                    <th>教科登録</th>
                                    <th>削除</th>
                                </tr>
                            </thead>
                            <tbody class="contents">
                                @foreach($result_category as $key => $result_category_info)
                                <div class="js_result_category_row">
                                    <tr>
                                        <td>{{ $result_category_info->id}} </td>
                                        <td>{{ $result_category_info->result_category_name}} </td>
                                        <td>
                                            <a href="{{ url('/shinzemi/result_category/' . $result_category_info->id . '/edit') }}" title="Edit bank"><button class="btn btn-primary btn-xs">実施回の登録</button></a>
                                        </td>
                                        <td>
                                            <a href="{{ url('/shinzemi/subject/' . $result_category_info->id . '/edit') }}" title="Edit bank"><button class="btn btn-primary btn-xs">教科の登録</button></a>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{route('result_category.destroy',$result_category_info->id)}}" class="form-horizontal" style="display:inline;">
                                                {{ csrf_field() }}

                                                {{ method_field("DELETE") }}
                                                <button type="submit" class="btn btn-danger btn-xs" title="Delete student" onclick="return confirm('本当にを削除しますか？元に戻すことはできません。')">
                                                    削除
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection