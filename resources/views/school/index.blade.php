@extends("layouts.app")
@section("content")
<div class="container">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">学校マスタ</div>
                <div class="panel-body">


                    <a href="{{ url('/shinzemi/school/create') }}" class="btn btn-success btn-sm" title="Add New school">
                        新規追加
                    </a>

                    <div class="row">
                        <form method="GET" action="{{ url('/shinzemi/school') }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">

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

                    <div>{{ $school->total() }} 件中 {{ $school->firstItem() }} - {{ $school->lastItem() }} 件を表示</div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>学校No</th>
                                    <th>学校名</th>
                                    <th>略称</th>
                                    <th>学校区分</th>
                                    <th>国立・私立・公立区分</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($school as $item)

                                <tr>

                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->name}} </td>

                                    <td>{{ $item->name_short}} </td>

                                    <td>{{ config('const.school_classification')[$item->school_classification] }} </td>

                                    <td>{{ config('const.university_classification')[$item->university_classification] }} </td>

                                    <td>
                                        <a href="{{ url("/school/" . $item->id . "/edit") }}" title="Edit school">
                                            <button class="btn btn-primary btn-xs">編集</button>
                                        </a>
                                    </td>

                                    <td>
                                        {{ Form::open(['route' => ['school.destroy', $item->id], 'method' => 'delete', 'class'=>'form-horizontal']) }}
                                        {{ csrf_field() }}

                                        <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除してもよろしいでしょうか。')">
                                            削除
                                        </button>
                                        {{ Form::close() }}
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        <!-- <div class="pagination-wrapper"> {!! $school->appends(["search" => Request::get("search")])->render() !!} </div> -->
                        <div class="pagination-wrapper">
                            {!! $school->appends(request()->all())->links() !!}
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection