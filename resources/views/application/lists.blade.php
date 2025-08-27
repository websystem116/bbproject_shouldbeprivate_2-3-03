@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script src="{{ asset('/js/student.js') }}"></script>
<script>
$(function() {
    $('.select_search3').select2({
        language: "ja",
    });
});

$(function() {
    $(".reset").on('click', function() {
        console.log('リセット');
        window.location.href = "/shinzemi/application/accept_index"; //URLリセットする
    });
    $('.check_all').on("click", function() {
        if ($('input[name="application_check[]"]:checked').length == 0) {
            $('input[name="application_check[]"]').prop('checked', true);
        } else {
            $('input[name="application_check[]"]').prop('checked', false);
        }
    });
});

</script>
<div class="container">
        <div class="row">
                <div class="col-md-12">
                        <div class="panel panel-default">
                                <div class="panel-heading">申込書リスト</div>
                                <div class="panel-body">
                                        {{ Form::model($application_search, ['route' => 'application.accept_index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
                                        <div class="">
                                                <div class="panel-group" id="sampleAccordion">
                                                        <div class="panel panel-default">
                                                                <div class="panel-heading">
                                                                        <h3 class="panel-title">
                                                                                <a data-toggle="collapse" data-parent="#sampleAccordion" href="#sampleAccordionCollapse1">▽検索条件</a>
                                                                        </h3>
                                                                </div>
                                                                <div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
                                                                        <div class="panel-body">
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('srch_reqest_start_date', '申込日', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('srch_reqest_start_date',$application_search['reqest_start_date'] ?? null, ['class' => 'form-control','id' => 'srch_reqest_start_date'])}}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-center">
                                                                                                {{ Form::label('wave', '～', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('srch_reqest_end_date', $application_search['reqest_end_date'] ?? null, ['class' => 'form-control','id' => 'srch_reqest_end_date'])}}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('srch_created_by', '申込者', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-4 mb-3">
                                                                                                {{ Form::text('srch_created_by', $application_search['created_by'] ?? null, ['placeholder' => '申込者名', 'class' => 'form-control form-name']) }}
                                                                                        </div>

                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('srch_application_type', '申込内容', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-4 mb-5">
                                                                                                {{ Form::select('srch_application_type',$application_type_list,$application_search['application_type'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 text-right">
                                                                                                {{ Form::label('srch_status', 'ステータス', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-4 mb-5">
                                                                                                {{ Form::select('srch_status',$status_list,$application_search['status'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
                                                                                        </div>
                                                                                </div>

                                                                                <div class="form-group">
                                                                                        <div class="row">
                                                                                                <div class="text-center">
                                                                                                        {{ Form::submit('検索', ['name' => 'search', 'class' => 'btn btn-primary']) }}
                                                                                                        {{ Form::reset('リセット', ['class' => 'btn btn-primary reset']) }}
                                                                                                </div>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                {{ Form::close() }}
                                        </div>
                                </div>
                                {{ Form::model($application_search, ['route' => 'application.accept_index', 'method' => 'POST', 'class' => 'form-horizontal']) }}
                                <div class="panel-body">
                                        <div>{{$applications->count() }}件を表示</div>
                                        {{-- <div>{{ $applications->total() }} 件中 {{ $applications->firstItem() }} - {{ $applications->lastItem() }} 件を表示
                                        </div> --}}
                                        <br>
                                        <!-- <a class="btn btn-primary check_all">
                                                一括選択
                                        </a> -->
                                        <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                        <thead>
                                                                <tr>
                                                                        <th style="width: 5%">選択</th>
                                                                        <th style="width: 15%">申込日</th>
                                                                        <th style="width: 10%">申込書CD</th>
                                                                        <th style="width: 10%">申込者</th>
                                                                        <th style="width: 20%">申込内容</th>
                                                                        <th style="width: 10%">ステータス</th>
                                                                        <th style="width: 10%">担当者</th>
                                                                        <th style="width: 10%">承認者</th>
                                                                        <th style="width: 10%">詳細</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                @foreach ($applications as $key => $item)
                                    <tr>
                                        <label>
                                            <td text-center>
                                                {{ Form::checkbox('application_check[]', $item->id, false, ['class' => 'custom-control-input form-checkbox']) }}
                                            </td>
                                        </label>
                                        <td>{{ date("Y-m-d", strtotime($item->reqest_date)) }} </td>
                                        <td>{{ $item->application_no }} </td>
                                        <td>{{ $item->created_by }} </td>
                                        <td>
                                        @if ($item->application_type == '0')
                                        入会
                                        @elseif ($item->application_type == '1')
                                        体験
                                        @elseif ($item->application_type == '2')
                                        コース変更
                                        @elseif ($item->application_type == '3')
                                        転籍
                                        @elseif ($item->application_type == '4')
                                        休塾
                                        @elseif ($item->application_type == '5')
                                        退塾
                                        @elseif ($item->application_type == '6')
                                        講習会
                                        @endif
                                        </td>
                                        <td>
                                        @if ($item->status == '0')
                                        未承認
                                        @elseif ($item->status == '1')
                                        承認
                                        @elseif ($item->status == '2')
                                        キャンセル
                                        @endif
                                        </td>
                                        <td>{{ $item->charged_by }} </td>
                                        <td>{{ $item->allowed_by }} </td>
                                        <td>
                                                <a href="{{ route('application.accept_detail', ['id' => $item->id]) }}"
                                                        title="詳細"
                                                        class="btn btn-primary btn-xs"
                                                        style="display: inline-block; margin-right: 15px;">詳細</a>
                                        </td>
                                    </tr>
                                @endforeach
                                                        </tbody>
                                                </table>
                                                {{-- <div class="pagination-wrapper"> {{ $applications->appends(request()->input())->links() }}
                                                </div> --}}
                                        </div>
                                </div>
                                {{ Form::close() }}
                        </div>
                </div>
        </div>
</div>
@endsection
