@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">教科マスター</div>
                <div class="panel-body">

                    <div>
                        <a href="{{ url('/shinzemi/curriculum/create') }}" class="btn btn-success btn-sm" title="Add New Curriculum">
                            新規追加
                        </a>
                    </div>

                    <form method="GET" action="{{ url('/shinzemi/curriculum') }}" accept-charset="UTF-8" class="" role="search" style="display:flex;justify-content: flex-end;">

                        <div class="input-group" style="display:flex;align-items:end;">

                            <div>
                                <div class="input-group-text">
                                    教科名
                                </div>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="教科名">
                            </div>

                            <div>
                                <div class="input-group-text">
                                    対象学年
                                </div>
                                {{ Form::select('grade', config('const.grade_type'),request()->has('grade') ? request('grade'):null,['placeholder' => '選択してください', 'class' => 'form-control']) }}
                            </div>
                            <div class="input-group-btn">
                                <button class="btn btn-info" type="submit">
                                    <span>検索</span>
                                </button>
                            </div>

                        </div>

                    </form>

                    <div style="padding-top:24px;">
                        {{ $curriculum->total() }} 件中 {{ $curriculum->firstItem() }} - {{ $curriculum->lastItem() }} 件を表示
                    </div>

                    <div class="">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>教科名</th>
                                    <th>対象学年</th>
                                    <th width="50"></th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($curriculum as $item)

                                <tr>

                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->name}} </td>

                                    <td>{{ (config('const.grade_type')[$item->from_grade] ?? '') . ' 〜 ' . (config('const.grade_type')[$item->to_grade] ?? '')}} </td>

                                    <td>
                                        <a href="{{ url('/shinzemi/curriculum/' . $item->id . '/edit') }}" title="Edit Curriculum">
                                            <button class="btn btn-primary btn-xs">編集</button>
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{route('curriculum.destroy',$item->id)}}" class="form-horizontal" style="display:inline;">
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
                        <div class="pagination-wrapper"> {!! $curriculum->appends(request()->all())->links() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection