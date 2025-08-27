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
    $('.select_search1').select2({
        language: "ja",
        width: '100px'
    });
    $('.select_search2').select2({
        language: "ja",
        width: '150px'
    });
    $('.select_search3').select2({
        language: "ja",
        width: '400px'
    });
});

$(function() {
    $(".reset").on('click', function() {
        console.log('リセット');
        window.location.href = "/shinzemi/student"; //URLリセットする
    });
    $('.check_all').on("click", function() {
        if ($('input[name="student_check[]"]:checked').length == 0) {
            $('input[name="student_check[]"]').prop('checked', true);
        } else {
            $('input[name="student_check[]"]').prop('checked', false);
        }
    });
});
$(function() {
    $(document).on('click', '.output', function() {
        var student_id_cnt = $('input[name="student_check[]"]:checked').length;
        if (student_id_cnt == 0) {
            alert('出力する生徒をチェックしてください。');
            return false;
        }
        if (!confirm('CSVを出力します。よろしいですか。')) {
            return false;
        } else {
            console.log(student_id_cnt);

            var form = $(this).parents('form');
            var action_url = "{{ route('student.student_info_output') }}";
            form.attr('action', action_url);
            form.submit();
        }
    });
});
$(document).on('click', '.reset-password-btn', function(e) {
    e.preventDefault();

    if (!confirm($(this).data('message'))) {
        return;
    }

    var id = $(this).data('id');
    var url = "{{ route('student.reset_password', ':id') }}".replace(':id', id); // ルートとIDからURL生成

    // 動的にフォームを作成
    var form = $('<form>', {
        'action': url,
        'method': 'POST',
        'style': 'display:none;' // フォームを非表示に
    });

    var csrfToken = $('<input>', {
        'type': 'hidden',
        'name': '_token',
        'value': $('meta[name="csrf-token"]').attr('content') // metaタグから取得
    });

    form.append(csrfToken);

    $.ajax({
        url: url,
        type: 'POST',
        data: form.serialize(), // フォームデータを送信
        dataType: 'json',
        success: function(response) {
            alert(response.message); // 成功メッセージを表示
            location.reload(); // ページをリロード
        },
        error: function(xhr, status, error) {
            var errorMessage = 'エラーが発生しました。';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message; // エラーメッセージを取得
            }
            alert(errorMessage);
        }
    });

});
// マイページ案内メール再送信
$(document).on('click', '.resend-mypage-guide-btn', function(e) {
    e.preventDefault();

    if (!confirm($(this).data('message'))) {
        return;
    }

    var id = $(this).data('id');
    var url = "{{ route('student.resend_mypage_guide', ':id') }}".replace(':id', id);

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content') // CSRFトークン
        },
        dataType: 'json',
        success: function(response) {
            alert(response.message);
        },
        error: function(xhr, status, error) {
            var errorMessage = 'エラーが発生しました。';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
});
// マイページ案内メール(不具合説明付き)再送信
$(document).on('click', '.resend-mypage-guide-re-btn', function(e) {
    e.preventDefault();

    if (!confirm($(this).data('message'))) {
        return;
    }

    var id = $(this).data('id');
    var url = "{{ route('student.resend_mypage_guide_re', ':id') }}".replace(':id', id);

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content') // CSRFトークン
        },
        dataType: 'json',
        success: function(response) {
            alert(response.message);
        },
        error: function(xhr, status, error) {
            var errorMessage = 'エラーが発生しました。';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
});
</script>
<div class="container">
        <div class="row">
                <div class="col-md-12">
                        <div class="panel panel-default">
                                <div class="panel-heading">生徒リスト</div>
                                <div class="panel-body">
                                        {{ Form::model($student_search, ['route' => 'student.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
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
                                                                                                {{ Form::label('number', '管理No', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::number('id_start',$student_search['id_start'] ?? null, ['placeholder' => '管理No', 'class' => 'form-control form-name']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-center">
                                                                                                {{ Form::label('wave', '～', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::number('id_end', $student_search['id_end'] ?? null, ['placeholder' => '管理No', 'class' => 'form-control form-name']) }}
                                                                                        </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('number', '生徒No', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::number('no_start',$student_search['no_start'] ?? null, ['placeholder' => '生徒No', 'class' => 'form-control form-name']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-center">
                                                                                                {{ Form::label('wave', '～', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::number('no_end', $student_search['no_end'] ?? null, ['placeholder' => '生徒No', 'class' => 'form-control form-name']) }}
                                                                                        </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('name', '生徒氏名', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::text('surname',$student_search['surname'] ?? null, ['placeholder' => '姓', 'class' => 'form-control form-name']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::text('name', $student_search['name'] ?? null, ['placeholder' => '名', 'class' => 'form-control form-name']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::text('surname_kana', $student_search['surname_kana'] ?? null, ['placeholder' => '姓カナ', 'class' => 'form-control form-name hira_change']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::text('name_kana', $student_search['name_kana'] ?? null, ['placeholder' => '名カナ', 'class' => 'form-control form-name hira_change']) }}
                                                                                        </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('phone', '電話番号', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::text('phone', $student_search['phone'] ?? null, ['placeholder' => '電話番号', 'class' => 'form-control form-name']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('school_name', '学校名', ['class' => 'control-label ']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::select('school_id',$schools_select_list,$student_search['school_id'] ?? null, ['placeholder' => '選択してください', 'class' => 'form-control form-name select_search2']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-1">
                                                                                                <label>
                                                                                                        {{Form::checkbox('brothers_flg', '1',$student_search['brothers_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'brothers_flg'])}}兄弟姉妹
                                                                                                </label>
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-1">
                                                                                                <label>
                                                                                                        {{Form::checkbox('fatherless_flg', '1',$student_search['fatherless_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'fatherless_flg'])}}ひとり親家庭
                                                                                                </label>
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-1">
                                                                                                <label>
                                                                                                        {{Form::checkbox('temporary_flg', '1', $student_search['temporary_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'temporary_flg'])}}仮入塾
                                                                                                </label>
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-1">
                                                                                                <label>
                                                                                                        {{Form::checkbox('rest_flg', '1', $student_search['rest_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'rest_flg'])}}休塾者
                                                                                                </label>
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-1">
                                                                                                <label>
                                                                                                        {{Form::checkbox('graduation_flg', '1', $student_search['graduation_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'graduation_flg'])}}卒塾者
                                                                                                </label>
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-1">
                                                                                                <label>
                                                                                                        {{Form::checkbox('withdrawal_flg', '1', $student_search['withdrawal_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'withdrawal_flg'])}}退塾者
                                                                                                </label>
                                                                                        </div>

                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('greade', '学年', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::select('grade_start', config('const.school_year'),$student_search['grade_start'] ?? null,['placeholder' => '選択してください', 'class' => 'form-control select_search2']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-center">
                                                                                                {{ Form::label('wave', '～', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::select('grade_end', config('const.school_year'), $student_search['grade_end'] ?? null,['placeholder' => '選択してください', 'class' => 'form-control select_search2']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('school_building', '校舎名', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{ Form::select('school_building_id',$schooolbuildings_select_list,$student_search['school_building_id'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
                                                                                        </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('product_select', '商品名', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-5 mb-5">
                                                                                                {{ Form::select('product_select',$products_select_list,$student_search['product_select'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('discount_select', '割引', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-5 mb-5">
                                                                                                {{ Form::select('discount_select',$discounts_select_list,$student_search['discount_select'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
                                                                                        </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('suggested_school', '進学先', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-4 mb-4">
                                                                                                {{ Form::text('suggested_school', $student_search['suggested_school'] ?? null, ['placeholder' => '進学先名', 'class' => 'form-control form-name']) }}
                                                                                        </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('juku_start_date', '入塾日', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_start_date',null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-center">
                                                                                                {{ Form::label('wave', '～', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_end_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('juku_start_date', '卒塾日', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_graduation_start_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-center">
                                                                                                {{ Form::label('wave', '～', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_graduation_end_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('juku_start_date', '復塾日', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_return_start_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-center">
                                                                                                {{ Form::label('wave', '～', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_return_end_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('juku_withdrawal_date', '退塾日', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_withdrawal_start_date',null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-center">
                                                                                                {{ Form::label('wave', '～', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_withdrawal_end_date',null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-left">
                                                                                                {{ Form::label('juku_rest_date', '休塾日', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_rest_start_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                        <div class="col-xs-1 text-center">
                                                                                                {{ Form::label('wave', '～', ['class' => 'control-label']) }}
                                                                                        </div>
                                                                                        <div class="col-xs-2 mb-3">
                                                                                                {{Form::date('juku_rest_end_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
                                                                                        </div>
                                                                                </div>

                                                                                <div class="form-group">
                                                                                        <div class="row">
                                                                                                <div class="text-center">
                                                                                                        {{ Form::submit('検索', ['name' => 'search', 'class' => 'btn btn-primary']) }}
                                                                                                        {{ Form::reset('リセット', ['class' => 'btn btn-primary reset']) }}
                                                                                                        {{-- {{ Form::submit('CSV出力', ['name' => 'output','class' => 'btn btn-primary']) }} --}}
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
                                {{ Form::model($student_search, ['route' => 'student.index', 'method' => 'POST', 'class' => 'form-horizontal']) }}
                                <div class="panel-body">
                                        <div>{{$student->count() }}件を表示</div>
                                        {{-- <div>{{ $student->total() }} 件中 {{ $student->firstItem() }} - {{ $student->lastItem() }} 件を表示
                                        </div> --}}
                                        <a href="{{ url('/student/create') }}" class="btn btn-success btn-sm" title="Add New student">
                                                新規追加
                                        </a>
                                        <br>
                                        <br>
                                        <a class="btn btn-primary check_all">
                                                一括選択
                                        </a>
                                        <span>
                                                {{Form::button('CSV出力', ['class' => 'btn btn-primary output', 'onfocus' => 'this.blur();'])}}
                                        </span>
                                        <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                        <thead>
                                                                <tr>
                                                                        <th style="width: 5%">選択</th>
                                                                        <th style="width: 10%">管理No</th>
                                                                        <th style="width: 10%">生徒No</th>
                                                                        <th style="width: 10%">パスワード</th>
                                                                        <th style="width: 10%">生徒名</th>
                                                                        <th style="width: 5%">学年</th>
                                                                        <th style="width: 10%">校舎名</th>
                                                                        <th style="width: 10%">学校名</th>
                                                                        <th style="width: 20%">商品名</th>
                                                                        <th style="width: 20%">割引</th>
                                                                        <th style="width: 6%">状態</th>
                                                                        <th style="width: 5%; padding-right: 10px;">編集</th>
                                                                        <th style="width: 5%">Re</th>
                                                                        @if (auth()->user()->roles != 3 && auth()->user()->roles != 2)
                                                                        <th style="width: 5%">再案内</th>
                                                                        <th style="width: 5%">ログイン</th>
                                                                        @endif
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                @foreach ($student as $key => $item)
                                    <tr>
                                        <label>
                                            <td text-center>
                                                {{ Form::checkbox('student_check[]', $item->student_no, false, ['class' => 'custom-control-input form-checkbox']) }}
                                            </td>
                                        </label>
                                        <td>{{ $item->id }} </td>
                                        <td>{{ $item->student_no }} </td>
                                        <td>{{ $item->initial_password }} </td>
                                        <td>{{ $item->surname }} {{ $item->name }}</td>
                                        <td>{{ config('const.school_year')[$item->grade] ?? '' }}</td>
                                        <td>{{ $item->schoolbuilding->name ?? '' }}</td>
                                        <td>{{ $item->school->name ?? '' }}</td>
                                        <td>{{ $item->juko_info->product->name ?? '' }}</td>
                                        <td>{{ $item->discount->name ?? '' }}</td>
                                        {{-- NOTE: 不要だがいつか戻すかもしれないためコメントアウトへ --}}
                                        {{-- <td>{{ config('const.temporary_flg')[$item->temporary_flg] ?? '' }}</td> --}}
                                        <td>
                                        @if ($item->parentLoggedin())
                                                <span style="color: green;">✓</span>
                                        @else
                                                <span style="color: red;">✗</span>
                                        @endif
                                        </td>
                                        <td>
                                                <a href="{{ url('/student/' . $item->id . '/edit') }}"
                                                        title="Edit bank"
                                                        class="btn btn-primary btn-xs"
                                                        style="display: inline-block; margin-right: 15px;">編集</a> {{-- ★ インラインスタイル追加 ★ --}}
                                        </td>
                                        <td>
                                                <button type="button"
                                                                class="btn btn-info btn-xs resend-mypage-guide-re-btn"
                                                                data-id="{{ $item->id }}"
                                                                data-message="生徒ID: {{ $item->id }} ({{ $item->surname }} {{ $item->name }}さん) にマイページ案内(不具合報告付き)メールを再送信します。よろしいですか？">
                                                        Re
                                                </button>
                                        </td>
                                        @if (auth()->user()->roles != 3 && auth()->user()->roles != 2)
                                        <td>
                                            <button type="button"
                                                    class="btn btn-info btn-xs resend-mypage-guide-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-message="生徒ID: {{ $item->id }} ({{ $item->surname }} {{ $item->name }}さん) にマイページ案内メールを再送信します。よろしいですか？">
                                                再案内
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-danger btn-xs reset-password-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-message="生徒ID: {{ $item->id }} ({{ $item->surname }} {{ $item->name }}さん) のパスワードをリセットします。よろしいですか？">
                                                リセット
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                                                        </tbody>
                                                </table>
                                                {{-- <div class="pagination-wrapper"> {{ $student->appends(request()->input())->links() }}
                                                </div> --}}
                                        </div>
                                </div>
                                {{ Form::close() }}
                        </div>
                </div>
        </div>
</div>
@endsection
