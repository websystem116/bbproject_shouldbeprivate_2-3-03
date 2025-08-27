@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
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

    $(function () {
        $(".reset").on('click', function () {
            console.log('リセット');
            window.location.href = "/shinzemi/student_access/history_index";//URLリセットする
        });
    });

</script>
@endpush
@push('css')
<style>

    .container{
        width: 100%;
    }
    .flex-box {
        display: flex;
    }
    .flex-item-left {
        flex: 3;
    }
    .flex-item-right {
        flex: 2;
        margin-left: 50px;
    }
    .history-table {
        width: 80%;
    }
</style>
@endpush
<div class="container">
    <div class="row">
        <div class="col-md-12">
            {{ Form::model($student_search, ['route' => 'student_access.history_index', 'method' => 'GET', 'class' => 'form-horizontal']) }}

            <div class="panel panel-default">
                <div class="panel-heading">入退室履歴</div>
                <div class="panel-body">
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
                                                {{ Form::label('school_building', '校舎名', ['class' => 'control-label']) }}
                                            </div>
                                            <div class="col-xs-2 mb-3">
                                                {{ Form::select('school_building_id',$schooolbuildings_select_list,$student_search['school_building_id'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="text-center">
                                                    @foreach(request()->except('page')  as $key => $value)
                                                    @if(is_array($value))
                                                        @foreach($value as $subValue)
                                                            <input type="hidden" name="{{ $key }}[]" value="{{ $subValue }}">
                                                        @endforeach
                                                    @endif
                                                    @endforeach
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
                    </div>
                </div>
                {{ Form::close() }}

                <div class="panel-body">
                <a href="{{route('student_access.index')}}" class="btn btn btn-success" target="_blank">QRスキャナー</a>
                <a href="{{route('access_user.create')}}" class="btn btn btn-success">入退室生徒新規登録</a>
                <a href="#" class="btn btn btn-success" id="bulk-qr-card">QRカード一括発行</a>

                </div>
                <div class="panel-body flex-box">
                    <div class="flex-item-left">

                        <div>{{$accessUsers->count() }}件を表示</div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" id="select-all">一括選択</button>
                            <button type="button" class="btn btn-primary" id="select-off">一括解除</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">選択</th>
                                        <th style="width: 5%">管理No</th>
                                        <th style="width: 10%">生徒名</th>
                                        <th style="width: 10%">校舎名</th>
                                        <th style="width: 15%">メールアドレス1</th>
                                        <th style="width: 5%">確認</th>
                                        <th style="width: 5%">編集</th>
                                        <th style="width: 5%">削除</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accessUsers as $key => $user)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_users[]" value="{{ $user->id }}"
                                                {{ in_array($user->id, $selectedUsers) ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $user->id}} </td>
                                            <td>{{ $user->surname}} {{ $user->name}}</td>
                                            <td>{{ $user->schoolbuilding->name ?? ""}}</td>
                                            <td>{{ $user->email_access}}</td>
                                            <td>
                                                <a href="#"  onclick="return false;" data-user-id="{{ $user->id }}" class="btn btn-primary btn-xs view-details">確認</a>
                                            </td>
                                            <td>
                                                <a href="{{ url('/shinzemi/access_user/' . $user->id . '/edit') }}" title="Edit bank" class="btn btn-primary btn-xs">編集</a>
                                            </td>
                                            <td>

                                                <form action="{{ url('/shinzemi/access_user/' . $user->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    @foreach(request()->except('page')  as $key => $value)
                                                        @if(is_array($value))
                                                            @foreach($value as $subValue)
                                                                <input type="hidden" name="{{ $key }}[]" value="{{ $subValue }}">
                                                            @endforeach
                                                        @elseif(!in_array($key, ['_token', '_method']))
                                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                        @endif
                                                    @endforeach
                                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('本当に削除してもよろしいですか？');">
                                                        削除
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper">
								<form id="pagination-form" method="GET" action="{{ route('student_access.history_index') }}">
									@csrf
									<!-- 現在の検索条件をhidden inputとして送信 -->
									@foreach(request()->except('page') as $key => $value)
										@if(is_array($value))
											@foreach($value as $subValue)
												<input type="hidden" name="{{ $key }}[]" value="{{ $subValue }}">
											@endforeach
										@else
											<input type="hidden" name="{{ $key }}" value="{{ $value }}">
										@endif
									@endforeach

									{{ $accessUsers->appends(request()->except('page'))->links() }}
								</form>
							</div>
                        </div>
                    </div>
                    <div class="flex-item-right">
                        <div id="user-details"></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover history-table">
                                <thead>
                                    <tr><th>入退室時間</th><th>入室/退室</th></tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@push('scripts')
<script>
    $(function() {
        // ページネーションリンクをクリックしたときに選択状態を収集してフォームに追加
        $('.pagination a').on('click', function (e) {
            e.preventDefault();

            // リンクのURLからページ番号を取得
            const url = $(this).attr('href');
            const page = new URL(url).searchParams.get('page');

            // ページ番号をフォームに追加（既存のものは削除してから）
            $('#pagination-form').find('input[name="page"]').remove();
            $('#pagination-form').append(`<input type="hidden" name="page" value="${page}">`);


            let selectedUsers = [];
            $('input[name="selected_users[]"]:checked').each(function() {
                selectedUsers.push($(this).val());
            });
            const deleteSelectedUsers = oldSelectedUsers.filter(user => !selectedUsers.includes(user));

            // 既存の delete_selected_users[] を削除
            $('#pagination-form input[name="delete_selected_users[]"]').remove();
            deleteSelectedUsers.forEach(function(userId) {
                $('#pagination-form').append('<input type="hidden" name="delete_selected_users[]" value="' + userId + '">');
            });

            // フォームを送信
            $('#pagination-form').submit();
        });

        // 一括選択ボタン
        $('#select-all').on('click', function() {
            $('input[name="selected_users[]"]').prop('checked', true);
              updateHiddenInputs();
        });

        // 一括解除ボタン
        $('#select-off').on('click', function() {
            $('input[name="selected_users[]"]').prop('checked', false);
            updateHiddenInputs();
        });

        // チェックボックスの状態が変更されたら、hidden input も更新
        $(document).on('change', 'input[name="selected_users[]"]', function() {
             updateHiddenInputs();
        });

        // 初回のみ
        const oldSelectedUsers = [];
        $('input[name="selected_users[]"]:checked').each(function() {
            oldSelectedUsers.push($(this).val());
        });
        console.log("oldSelectedUsers:", oldSelectedUsers);

        // hidden input を更新する関数
        function updateHiddenInputs() {
            let selectedUsers = [];
            $('input[name="selected_users[]"]:checked').each(function() {
                selectedUsers.push($(this).val());
            });

            // 既存の hidden input を削除
            $('#pagination-form input[name="selected_users[]"]').remove();

            // 新しい hidden input を追加
            selectedUsers.forEach(function(userId) {
                $('#pagination-form').append('<input type="hidden" name="selected_users[]" value="' + userId + '">');
            });
             console.log("Selected Users:", selectedUsers);
        }
          // ページ読み込み時に選択状態を復元し、hidden input を初期化
        updateHiddenInputs();

        $('.view-details').on('click', function () {
            var userId = $(this).data('user-id');
            // 非同期通信でデータを取得
            $.ajax({
                url: '/shinzemi/student_access/getHistoryData',
                type: 'GET',
                data: {
                    user_id: userId
                },
                dataType: 'json',
                success: function (data) {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        // データを表示
                        console.log(data);
                        displayStudentDetails(data);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    alert('データの取得中にエラーが発生しました');
                }
            });

        });

        function displayStudentDetails(data) {
            let detailsContainer = $('#user-details');
            const baseUrl = '{{ url('/shinzemi/') }}';
            const printPreviewUrl = baseUrl + '/card/print_preview?access-user-id=' + data.accessUser.id;

            detailsContainer.html(`
                <h4>入退室履歴</h4>
                <h4>${data.accessUser.surname} ${data.accessUser.name}　　${data.accessUser.schoolbuilding?.name}</h4>
                <h5><a href="${printPreviewUrl}">QRを印刷する</a></h5>
            `);

            let tbody = $('.history-table tbody');
            tbody.empty(); // 既存の行を削除
            data.studentAccesses.forEach(access => {
                tbody.append(`
                    <tr>
                        <td>${access.access_time}</td>
                        <td>${access.access_type === 1 ? '入室' : '退室'}</td>
                    </tr>
                `);
            });
        }
            // 「QRカード一括発行」ボタンがクリックされた時の処理
        $('#bulk-qr-card').on('click', function () {
            const baseUrl = '{{ url('/shinzemi/') }}';

            const form = $('<form>', {
                method: 'POST',
                action: baseUrl + '/card/print_preview_all'  // QRカード一括発行の処理を行うルート
            });

            // 選択したユーザーIDをフォームに追加
            let selectedUsers = [];
            $('input[name="selected_users[]"]:checked').each(function() {
                selectedUsers.push($(this).val());
            });

            // ユーザーが選択されていない場合
            if (selectedUsers.length === 0) {
                alert('ユーザーを選択してください');
                return;
            }

            // 選択したユーザーIDをフォームに追加
            selectedUsers.forEach(function(userId) {
                form.append('<input type="hidden" name="selected_users[]" value="' + userId + '">');
            });

            // 選択解除されたユーザーIDをフォームに追加
            const deleteSelectedUsers = oldSelectedUsers.filter(user => !selectedUsers.includes(user));
            deleteSelectedUsers.forEach(function(userId) {
                form.append('<input type="hidden" name="delete_selected_users[]" value="' + userId + '">');
            });

            // CSRFトークンをフォームに追加
            form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');

            // フォームを送信
            $('body').append(form);
            form.submit();
        });

    });
</script>
@endpush
@endsection