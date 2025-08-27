@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">校舎マスタ</div>
                <div class="panel-body">


                    <a href="{{ url("school_building/create") }}" class="btn btn-success btn-sm" title="Add New school_building">
                        新規追加
                    </a>

                    <form method="GET" action="{{ url("school_building") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                        <div class="input-group">

                            <input type="text" class="form-control" name="search" placeholder="校舎名" value="{{ request("search") }}">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="submit">
                                    <span>検索</span>
                                </button>
                            </span>
                        </div>
                    </form>


                    <br />
                    <br />

                    <div>{{ $school_building->total() }} 件中 {{ $school_building->firstItem() }} - {{ $school_building->lastItem() }} 件を表示</div>


                    <div class="table">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>校舎No</th>
                                    <th>校舎名</th>
                                    <th>校舎名（略称）</th>
                                    <th>郵便番号</th>
                                    <th>住所１</th>
                                    <th>住所２</th>
                                    <th>住所３</th>
                                    <th>電話番号</th>
                                    <th>FAX番号</th>
                                    <th>E-mailアドレス</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($school_building as $item)
                                <tr>

                                    <td>{{ $item->number}} </td>

                                    <td>{{ $item->name}} </td>

                                    <td>{{ $item->name_short}} </td>

                                    <td>{{ $item->zipcode}}</td>

                                    <td>{{ $item->address1}}</td>

                                    <td>{{ $item->address2}}</td>

                                    <td>{{ $item->address3}}</td>

                                    <td>{{ $item->tel}}</td>

                                    <td>{{ $item->fax}}</td>

                                    <td>{{ $item->email}}</td>

                                    <td><a href="{{ url("/school_building/" . $item->id . "/edit") }}" title="Edit school_building"><button class="btn btn-primary btn-xs">編集</button></a></td>
                                    <td>
                                        <form method="POST" action="{{ route('school_building.destroy', $item->id) }}" class="form-horizontal" style="display:inline;">
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
                        <div class="pagination-wrapper"> {!! $school_building->appends(["search" => Request::get("search")])->render() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection