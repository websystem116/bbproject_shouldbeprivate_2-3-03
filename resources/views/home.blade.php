@extends('layouts.app')
@section('content')
    @push('css')
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    @endpush
    @push('scripts')
        <script>
            $(function() {

                $(document).on('click', '.make_sales', function() {
                    if (!confirm('売上データを作成します。よろしいですか。')) {
                        return false;
                    } else {
                        location.href = "{{ route('sales.data_migration') }}";
                    }
                });
                $(document).on('click', '.not_make_sales', function() {
                    alert('売上データはすでに作成済みです。');
                });

                //請求データ作成ボタン押下時に確認ダイアログを表示する
                $(document).on('click', '#make_invoice', function() {
                    if (!confirm('請求データを作成します。よろしいですか。')) {
                        return false;
                    } else {
                        location.href = "{{ route('charge.data_migration') }}";
                    }
                });
                //請求データ作成ボタン押下時に確認ダイアログを表示する
                $(document).on('click', '#not_make_invoice', function() {
                    alert('請求データが確定されています。解除後に作成が可能です');
                });

                // 請求確定解除ボタン押下時に確認ダイアログを表示する
                $(document).on('click', '#cancel_invoice', function() {
                    if (!confirm('請求確定を解除します。よろしいですか。')) {
                        return false;
                    } else {
                        location.href = "{{ route('charge.charge_confirm_lift') }}";
                    }
                });

                // 請求データ確定ボタン押下時に確認ダイアログを表示する
                $(document).on('click', '#define_invoice', function() {
                    if (!confirm('請求データを確定します。よろしいですか。')) {
                        return false;
                    } else {
                        location.href = "{{ route('charge.charge_confirm') }}";
                    }
                });

                // 月次締処理ボタン押下時に確認ダイアログを表示する
                $(document).on('click', '#monthly_closing_process', function() {
                    if (!confirm('月次締処理を行います。よろしいですか。')) {
                        return false;
                    } else {
                        location.href = "{{ route('charge.charge_closing') }}";
                    }
                });

                // 非常勤給与月次締ボタン押下時に確認ダイアログを表示する
                $(document).on('click', '#Parttime_payroll_monthly_closing', function() {
                    if (!confirm('非常勤給与月次締を行います。よろしいですか。')) {
                        return false;
                    } else {
                        location.href = "{{ route('salary.monthly_tightening') }}";
                    }
                });

                // 非常勤一覧出力ボタン押下時に確認ダイアログを表示する
                $(document).on('click', '#part_timer_list', function() {
                    if (!confirm('非常勤一覧を出力します。よろしいですか。')) {
                        return false;
                    } else {
                        location.href = "{{ route('salary_output.export_part_timer_list') }}";

                    }
                });

                $(document).on('click', '#transfer_charges', function() {
                    // 確認メッセージを取得 (Ajax)
                    $.ajax({
                        url: '{{ route('invoice.confirm_transfer') }}', // 確認メッセージ取得用のルート
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            // 確認ダイアログを表示
                            if (confirm(response.message)) {
                                // ローディングアイコン表示
                                $('#transfer_charges').prop('disabled', true);
                                $('#transfer_charges').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> 反映中...');

                                // データ移行処理を呼び出す (Ajax)
                                $.ajax({
                                    url: '{{ route('invoice.transfer') }}', // データ移行用のルート
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        alert(response.message); // 完了メッセージをアラート表示
                                        location.reload();
                                    },
                                    error: function(xhr, status, error) {
                                        let errorMessage = 'エラーが発生しました';
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMessage += ': ' + xhr.responseJSON.message;
                                        }
                                        alert(errorMessage);
                                    },
                                    complete: function() {
                                        $('#transfer_charges').prop('disabled', false);
                                        $('#transfer_charges').html('請求書自動発行システム反映');
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'エラーが発生しました';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += ': ' + xhr.responseJSON.message;
                            }
                            alert(errorMessage);
                        }
                    });
                });

                // 請求通知ボタン
                $(document).on('click', '#notify_invoice', function() {
                    // 確認メッセージを取得 (Ajax)
                    $.ajax({
                        url: '{{ route('invoice.confirm_notification') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            // 確認ダイアログを表示
                            if (confirm(response.message)) {
                                // ローディングアイコン表示
                                $('#notify_invoice').prop('disabled', true);
                                $('#notify_invoice').html(
                                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> 送信中...');

                                // メール送信処理 (Ajax)
                                $.ajax({
                                    url: '{{ route('invoice.send_notification') }}', // メール送信用ルート
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        alert(response.message); // 完了メッセージをアラート表示
                                        location.reload();
                                    },
                                    error: function(xhr, status, error) {
                                        let errorMessage = 'エラーが発生しました';
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMessage += ': ' + xhr.responseJSON.message;
                                        }
                                        alert(errorMessage);
                                    },
                                    complete: function() {
                                        $('#notify_invoice').prop('disabled', false);
                                        $('#notify_invoice').html('請求書の通知');
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'エラーが発生しました';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += ': ' + xhr.responseJSON.message;
                            }
                            alert(errorMessage);
                        }
                    });
                });


                $(document).on('click', '#Parttime_salary_confirmation', function() {
                    // 確認メッセージを取得 (Ajax)
                    $.ajax({
                        url: '{{ route('part_time.confirm_transfer') }}', // 確認メッセージ取得用のルート
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            // 確認ダイアログを表示
                            if (confirm(response.message)) {
                                console.log(response.message + "part_time")
                                // ローディングアイコン表示
                                $('#Parttime_salary_confirmation').prop('disabled', true);
                                $('#Parttime_salary_confirmation').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> 反映中...');

                                // データ移行処理を呼び出す (Ajax)
                                $.ajax({
                                    url: '{{ route('part_time.transfer') }}', // データ移行用のルート
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        alert(response.message); // 完了メッセージをアラート表示
                                        location.reload();
                                    },
                                    error: function(xhr, status, error) {
                                        let errorMessage = 'エラーが発生しました';
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMessage += ': ' + xhr.responseJSON.message;
                                        }
                                        alert(errorMessage);
                                    },
                                    complete: function() {
                                        $('#Parttime_salary_confirmation').prop('disabled', false);
                                        $('#Parttime_salary_confirmation').html('給与明細書自動発行システム反映');
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'エラーが発生しました';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += ': ' + xhr.responseJSON.message;
                            }
                            alert(errorMessage);
                        }
                    });
                });

                // 
                $(document).on('click', '#notify_salary_invoice', function() {
                    // 確認メッセージを取得 (Ajax)
                    $.ajax({
                        url: '{{ route('salary_invoice.confirm_notification') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            // 確認ダイアログを表示
                            if (confirm(response.message)) {
                                // ローディングアイコン表示
                                $('#notify_salary_invoice').prop('disabled', true);
                                $('#notify_salary_invoice').html(
                                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> 送信中...');

                                // メール送信処理 (Ajax)
                                $.ajax({
                                    url: '{{ route('invoice.send_notification') }}', // メール送信用ルート
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        alert(response.message); // 完了メッセージをアラート表示
                                        location.reload();
                                    },
                                    error: function(xhr, status, error) {
                                        let errorMessage = 'エラーが発生しました';
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMessage += ': ' + xhr.responseJSON.message;
                                        }
                                        alert(errorMessage);
                                    },
                                    complete: function() {
                                        $('#notify_salary_invoice').prop('disabled', false);0
                                        $('#notify_salary_invoice').html('給与明細書の通知');
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'エラーが発生しました';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage += ': ' + xhr.responseJSON.message;
                            }
                            alert(errorMessage);
                        }
                    });
                });
            });
        </script>
    @endpush








    <div class="card-group card_fild">
        <div class="card">
            <div class="card-row col-sm-12 text-center" style="display: flex; flex-wrap: nowrap; justify-content: space-evenly; gap: 10px;">
                <div class="card shadow-sm" style="min-width: 160px; flex: 1; max-width: 220px;">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">生徒情報管理</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('student.index') }}'">
                                    生徒情報登録
                                </button>
                            </li>
                            @if(auth()->user()->roles == 1)
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('juko_info.index') }}'">
                                    受講情報登録
                                </button>
                            </li>
                            @endif
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('average_point.index') }}'">
                                    成績情報登録/平均点登録
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('student_karte.index') }}'">
                                    過去成績情報登録/平均点確認
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('score.index') }}'">
                                    試験別成績一覧
                                </button>
                            </li>
                            @if (auth()->user()->roles != 3 && auth()->user()->roles != 2)
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('year_end.index') }}'">
                                        年度末処理
                                    </button>
                                </li>
                            @endif
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-success"
                                    onclick="location.href='{{ route('student_access.history_index') }}'">
                                    入退室情報
                                </button>
                            </li>
                            @if (auth()->user()->roles == 1)
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-danger" 
                                        onclick="location.href='{{ route('application.accept_index') }}'">
                                        入退塾等の手続き(承認一覧)
                                    </button>
                                </li>
                            @endif
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('application.index') }}'">
                                    入退塾等の手続き
                                </button>
                            </li>


                        </ul>
                    </div>
                </div>
                <div class="card shadow-sm" style="min-width: 160px; flex: 1; max-width: 220px;">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">請求管理</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            @if (auth()->user()->roles != 3 && auth()->user()->roles != 2)
                                @if ($charge_progress_exist == false)
                                    <li>
                                        <button type="button" class="btn btn-lg btn-block btn-danger make_sales">
                                            売上データ作成
                                        </button>
                                    </li>
                                @else
                                    <li>
                                        <button type="button" class="btn btn-lg btn-block btn-danger not_make_sales "
                                            disabled>
                                            売上データ作成
                                        </button>
                                    </li>
                                @endif
                            @endif
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('sales.index') }}'">
                                    売上登録
                                </button>
                            </li>
                            @if (auth()->user()->roles != 3 && auth()->user()->roles != 2)
                                @if ($charge_confirm_exist == false)
                                    <li>
                                        <button type="button" id="make_invoice" class="btn btn-lg btn-block btn-danger">
                                            請求データ作成
                                        </button>
                                    </li>
                                @else
                                    <li>
                                        <button type="button" id="not_make_invoice" class="btn btn-lg btn-block btn-danger"
                                            disabled>
                                            請求データ作成
                                        </button>
                                    </li>
                                @endif
                                <li>
                                    <button type="button" id="cancel_invoice" class="btn btn-lg btn-block btn-danger">
                                        請求確定解除
                                    </button>
                                </li>
                                <li>
                                    <button type="button" id="define_invoice" class="btn btn-lg btn-block btn-danger">
                                        請求データ確定
                                    </button>
                                </li>
                            @endif
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('charge_output.index') }}'">
                                    請求書出力指示
                                </button>
                            </li>
                            @if (auth()->user()->roles != 3 && auth()->user()->roles != 2)
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('charge_output.nanto_index') }}'">
                                        南都WEB出力
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('charge_output.risona_index') }}'">
                                        りそなNET出力
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('charge_output.nanto_import_index') }}'">
                                        南都WEB取り込み
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('charge_output.risona_import_index') }}'">
                                        りそなNET取り込み
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('payment.index') }}'">
                                        コンビニ振込等登録
                                    </button>
                                </li>
                                <li>
                                    <button type="button" id="monthly_closing_process"
                                        class="btn btn-lg btn-block btn-danger">
                                        月次締処理
                                    </button>
                                </li>
                            @endif
                            @if(auth()->user()->roles == 1 || auth()->user()->roles == 2)
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('charge_excel.index') }}'">
                                    帳票出力
                                </button>
                            </li>
                            @endif
                            @if (auth()->user()->roles == 1)
                            <li>
                                <button type="button" id="transfer_charges"
                                    class="btn btn-lg btn-block btn-danger">
                                    請求書自動発行システム反映
                                </button>
                            </li>
                            <li>
                                <button type="button" id="notify_invoice" class="btn btn-lg btn-block btn-danger">
                                    請求書の通知
                                </button>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="card shadow-sm" style="min-width: 160px; flex: 1; max-width: 220px;">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">非常勤管理</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('salary.index') }}'">
                                    非常勤業務明細一覧
                                </button>
                            </li>
                            <li>
                                <!-- <button type="button" class="btn btn-lg btn-block btn-primary" id="part_timer_list" onclick="location.href='{{ route('salary_output.export_part_timer_list') }}'"> -->
                                <button type="button" class="btn btn-lg btn-block btn-primary" id="part_timer_list">
                                    非常勤一覧出力
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('salary_output.index') }}'">
                                    非常勤給与一覧出力
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('salary_output.school_building_index') }}'">
                                    校舎別非常勤<br>
                                    給与一覧出力
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('salary_output.working_school_building_index') }}'">
                                    校舎別非常勤<br>
                                    給与一覧出力
                                    (勤務校舎)
                                </button>
                            </li>
                            @if (auth()->user()->roles == 1)
                                <li>
                                    <button type="button" id="Parttime_payroll_monthly_closing"
                                        class="btn btn-lg btn-block btn-danger">
                                        非常勤給与月次締
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('salary_output.export_salary_index') }}'">
                                        非常勤給与振込データ作成
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('salary_output.export_wage_ledger_index') }}'">
                                        賃金台帳出力
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('salary.monthly_salary_index') }}'">
                                        非常勤給与明細差分表示
                                    </button>
                                </li>
                                <li>
                                    <button type="button" id="Parttime_salary_confirmation"
                                        class="btn btn-lg btn-block btn-danger">
                                        非常勤給与確定
                                    </button>
                                </li>
                                <li>
                                    <button type="button" id="notify_salary_invoice"
                                        class="btn btn-lg btn-block btn-danger">
                                        非常勤給与明細送信
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="card shadow-sm" style="min-width: 160px; flex: 1; max-width: 220px;">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">アンケート管理</h4>
                    </div>
                    <div class="card-body">
                        <!-- <h1 class="card-title pricing-card-title">$0 <small class="text-muted">/ mo</small></h1> -->
                        <ul class="list-unstyled mt-3 mb-4">
                            @if (auth()->user()->roles == 1)
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('questionnaire_content.index') }}'">
                                        アンケート内容登録
                                    </button>
                                </li>
                            @endif
                            @if (auth()->user()->roles == 1||auth()->user()->roles == 2)
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('questionnaire_import.create') }}'">
                                        アンケート結果自動取込
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('questionnaire_results_detail.index') }}'">
                                        アンケート結果確認
                                    </button>
                                </li>
                            @endif
                            @if (auth()->user()->roles == 1)
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('questionnaire_decision.create') }}'">
                                        アンケート結果集計・確定
                                    </button>
                                </li>
                            @endif
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('questionnaire_output.index') }}'">
                                    アンケート結果出力
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                @if (auth()->user()->roles != 3)
                    <div class="card shadow-sm" style="min-width: 160px; flex: 1; max-width: 220px;">
                        <div class="card-header">
                            <h4 class="my-0 font-weight-normal">入塾前管理</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mt-3 mb-4">
                                @if (auth()->user()->roles != 2)
                                    <li>
                                        <button type="button" class="btn btn-lg btn-block btn-primary"
                                            onclick="location.href='{{ route('before_student.index') }}'">
                                            入塾前情報登録
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="btn btn-lg btn-block btn-primary"
                                            onclick="location.href='{{ route('before_juku_sales.index') }}'">
                                            入塾前売上登録
                                        </button>
                                    </li>
                                @endif
                                <li>
                                    <button type="button" class="btn btn-lg btn-block btn-primary"
                                        onclick="location.href='{{ route('before_juku_detail.index') }}'">
                                        入塾前売上明細出力
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="card shadow-sm" style="min-width: 160px; flex: 1; max-width: 220px;">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">マイページ管理</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('announcements.index') }}'">
                                    お知らせ一覧
                                    @php
                                        $unpublishedCount = \App\Announcement::getUnpublishedCount();
                                    @endphp
                                    @if($unpublishedCount > 0)
                                        <span class="badge" style="background-color: #dc3545; color: white; margin-left: 5px;">{{ $unpublishedCount }}</span>
                                    @endif
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary position-relative"
                                    onclick="location.href='{{ route('schedules.index') }}'">
                                    スケジュール管理
                                    @if($pending_schedules_count > 0)
                                        <span class="badge" style="background-color: #d9534f !important; color: #ffffff !important;">{{ $pending_schedules_count }}</span>
                                    @endif
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card shadow-sm" style="min-width: 160px; flex: 1; max-width: 220px;">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">マスタメンテ</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('register.index') }}'">
                                    ユーザーマスタ
                                </button>
                            </li>
                            @if (auth()->user()->roles == 1)

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('bank.index') }}'">
                                    銀行マスタ
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('branch_bank.index') }}'">
                                    銀行支店マスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('product.index') }}'">
                                    商品マスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('discount.index') }}'">
                                    割引マスタ
                                </button>
                            </li>
                            @endif
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('invoice_comment.index') }}'">
                                    請求書説明文マスタ
                                </button>
                            </li>
                            @if (auth()->user()->roles == 1)

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('subject_teacher.index') }}'">
                                    科目担当講師マスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('questionnaire_score.index') }}'">
                                    講師別アンケート数値マスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('job_description.index') }}'">
                                    業務内容マスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('other_job_description.index') }}'">
                                    その他実績種別マスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('school_building.index') }}' ">
                                    校舎マスタ
                                </button>
                            </li>
                            @endif
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('school.index') }}'">
                                    学校マスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('highschool_course.index') }}'">
                                    高校コースマスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('result_category.index') }}'">
                                    成績カテゴリーマスタ
                                </button>
                            </li>
                            @if (auth()->user()->roles == 1)

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('division_code.index') }}'">
                                    売上区分マスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary fontsize"
                                    onclick="location.href='{{ route('company.edit', 1) }}' ">
                                    会社マスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('course.index') }}' ">
                                    コースマスタ
                                </button>
                            </li>

                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('curriculum.index') }}' ">
                                    教科マスタ
                                </button>
                            </li>
                            
                            @endif
                            {{-- <li>
							<button type="button" class="btn btn-lg btn-block btn-primary" onclick="location.href='{{ route('subject.index') }}'">
                        成績教科マスタ
                        </button>
                        </li> --}}

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
