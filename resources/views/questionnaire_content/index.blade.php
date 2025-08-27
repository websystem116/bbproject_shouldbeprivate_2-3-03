@extends("layouts.app")
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">アンケート内容登録</div>
                <div class="panel-body">


                    <a href="{{ /shinzemi/questionnaire_content/create') }}" class="btn btn-success btn-sm" title="Add New questionnaire_content">
                        新規追加
                    </a>

                    <a href="{{ url('/shinzemi/questionnaire_content/form_questionnaire_papers') }}" class="btn btn-success btn-sm" title="Add New questionnaire_content">
                        アンケート用紙印刷
                    </a>

                    {{-- <form method="GET" action="{{ url("questionnaire_content") }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search...">
                        <span class="input-group-btn">
                            <button class="btn btn-info" type="submit">
                                <span>Search</span>
                            </button>
                        </span>
                    </div>
                    </form> --}}


                    <br />
                    <br />

                    <div>{{ $questionnaire_content->total() }} 件中 {{ $questionnaire_content->firstItem() }} - {{ $questionnaire_content->lastItem() }} 件を表示</div>


                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>タイトル</th>
                                    <th>概要</th>
                                    <th>集計・確定</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questionnaire_content as $item)
                                <tr>

                                    <td>{{ $item->id }} </td>

                                    <td>{{ $item->title }} </td>

                                    <td>{{ $item->summary }} </td>

                                    <td> @if($item->questionnaire_decisions->isNotEmpty()) 済 @endif </td>


                                    <td><a href="{{ url('/shinzemi/questionnaire_content/' . $item->id . '/edit') }}" title="Edit questionnaire_content"><button class="btn btn-primary btn-xs">編集</button></a></td>
                                    <td>
                                        <form method="POST" action="{{ route('questionnaire_content.destroy', $item->id) }}" class="form-horizontal" style="display:inline;">
                                            {{ csrf_field() }}

                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除してよろしいですか？')">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $questionnaire_content->appends(['search' => Request::get('search')])->render() !!} </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection