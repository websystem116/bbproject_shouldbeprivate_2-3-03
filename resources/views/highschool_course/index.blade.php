@extends("layouts.app")
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">高校コースマスタ</div>
                <div class="panel-body">


                    <a href="{{ url('/shinzemi/highschool_course/create') }}" class="btn btn-success btn-sm" title="Add New highschool_course">
                        新規追加
                    </a>


                    <div class="row">
                        <form method="GET" action="{{ url('/shinzemi/highschool_course') }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">

                            <div class="input-group">
                                <input type="text" class="form-control" name="name" placeholder="学校名" value="{{ request('name') }}">
                            </div>

                            <div class="input-group">
                                <span class="input-group-text">
                                </span>

                                <button class="btn btn-info" type="submit">
                                    <span>検索</span>
                                </button>

                            </div>

                        </form>
                    </div>

                    <br />
                    <br />

                    <div>{{ $highschool_course->total() }} 件中 {{ $highschool_course->firstItem() }} - {{ $highschool_course->lastItem() }} 件を表示</div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>コースNo</th>
                                    <th>学校No</th>
                                    <th>名称</th>
                                    <th>略称</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($highschool_course as $item)
                                <tr>

                                    <td>{{ $item->id }} </td>

                                    {{-- <td>{{ $item->school_id}} </td> --}}
                                    <td>{{ $item->school->name }} </td>

                                    <td>{{ $item->name }} </td>

                                    <td>{{ $item->name_short }} </td>

                                    <!-- <td><a href="{{ url('/shinzemi/highschool_course/' . $item->id) }}" title="View highschool_course"><button class="btn btn-info btn-xs">View</button></a></td> -->
                                    <td><a href="{{ url('/shinzemi/highschool_course/' . $item->id . '/edit') }}" title="Edit highschool_course"><button class="btn btn-primary btn-xs">編集</button></a></td>
                                    <td>
                                        <form method="POST" action="{{ route('highschool_course.destroy', $item->id) }}" class="form-horizontal" style="display:inline;">
                                            {{ csrf_field() }}

                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除しますか')">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- <div class="pagination-wrapper"> {!! $highschool_course->appends(['search' => Request::get('search')])->render() !!} </div> -->
                        <div class="pagination-wrapper">
                            {!! $highschool_course->appends(request()->all())->links() !!}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection