@extends("layouts.app")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">アンケート結果点数化ルールマスタ</div>
                <div class="panel-body">


                    <a href="{{ url("questionnaire_rule/create") }}" class="btn btn-success btn-sm" title="Add New questionnaire_rule">
                        新規追加
                    </a>

                    {{-- <form method="GET" action="{{ url("questionnaire_rule") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search...">
                        <span class="input-group-btn">
                            <button class="btn btn-info" type="submit">
                                <span>検索</span>
                            </button>
                        </span>
                    </div>
                    </form> --}}


                    <br />
                    <br />


                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>始点（以上）</th>
                                    <th>終点（未満）</th>
                                    <th>点数</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionnaire_rule as $item)

                                <tr>

                                    <td>{{ $item->id}} </td>

                                    <td>{{ $item->rankstart}} </td>

                                    <td>{{ $item->rankend}} </td>

                                    <td>{{ $item->rankscore}} </td>

                                    <!-- <td><a href="{{ url("/questionnaire_rule/" . $item->id) }}" title="View questionnaire_rule"><button class="btn btn-info btn-xs">View</button></a></td> -->
                                    <td><a href="{{ url("/questionnaire_rule/" . $item->id . "/edit") }}" title="Edit questionnaire_rule"><button class="btn btn-primary btn-xs">編集</button></a></td>
                                    <td>
                                        <form method="POST" action="{{route('questionnaire_rule.destroy',$item->id)}}" class="form-horizontal" style="display:inline;">
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
                        <div class="pagination-wrapper"> {!! $questionnaire_rule->appends(["search" => Request::get("search")])->render() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection