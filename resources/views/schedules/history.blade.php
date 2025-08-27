@extends('layouts.app')

@section('title', 'スケジュール履歴')

@push('css')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ja.js"></script>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span><i class="fas fa-history"></i> スケジュール履歴</span>
                    <div class="pull-right">
                        <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-arrow-left"></i> カレンダーに戻る
                        </a>
                    </div>
                </div>

                <div class="panel-body">
                    <!-- Page Description -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        @if($viewType === 'approver')
                            <strong>承認者履歴:</strong> あなたが承認または却下したスケジュールの履歴を表示しています。
                        @else
                            <strong>作成者履歴:</strong> あなたが作成したスケジュールの承認状況を表示しています。
                        @endif
                    </div>

                    <!-- Filters -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4><i class="fas fa-filter"></i> 検索条件</h4>
                        </div>
                        <div class="panel-body">
                            <form method="GET" action="{{ route('schedules.history') }}" id="filterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="school_building_id">校舎</label>
                                            <select name="school_building_id" id="school_building_id" class="form-control select2-school-building">
                                                <option value="">全校舎</option>
                                                @foreach($schoolBuildings as $building)
                                                <option value="{{ $building->id }}" {{ $schoolBuildingId == $building->id ? 'selected' : '' }}>
                                                    {{ $building->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="status">ステータス</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">全て</option>
                                                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>承認済み</option>
                                                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>却下</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_from">予定日（開始）</label>
                                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_to">予定日（終了）</label>
                                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="per_page">表示件数</label>
                                            <select name="per_page" id="per_page" class="form-control">
                                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10件</option>
                                                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20件</option>
                                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50件</option>
                                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100件</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-search"></i> 検索
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-default" onclick="clearFilters()">
                                            <i class="fas fa-times"></i> 条件をクリア
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results -->
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4>
                                <i class="fas fa-list"></i> 履歴一覧
                                @if($schedules->total() > 0)
                                <span class="badge" style="background-color: #fff; color: #337ab7; margin-left: 10px;">
                                    {{ $schedules->total() }}件
                                </span>
                                @endif
                            </h4>
                        </div>
                        <div class="panel-body">
                            @if($schedules->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="120">予定日</th>
                                                <th width="100">時間</th>
                                                <th>タイトル</th>
                                                <th width="120">校舎</th>
                                                @if($viewType === 'approver')
                                                    <th width="100">作成者</th>
                                                @else
                                                    <th width="100">承認者</th>
                                                @endif
                                                <th width="80">ステータス</th>
                                                <th width="120">処理日時</th>
                                                <th width="80">操作</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedules as $schedule)
                                                <tr>
                                                    <td>{{ $schedule->schedule_date->format('Y/m/d') }}</td>
                                                    <td>{{ $schedule->time_display ?? '--' }}</td>
                                                    <td>
                                                        <div class="schedule-title">
                                                            <strong>{{ $schedule->title }}</strong>
                                                            @if($schedule->content)
                                                                <br><small class="text-muted">{{ Str::limit($schedule->content, 80) }}</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $schedule->schoolBuilding->name ?? '--' }}</td>
                                                    @if($viewType === 'approver')
                                                        <td>{{ $schedule->creator->last_name ?? '' }}{{ $schedule->creator->first_name ?? '' }}</td>
                                                    @else
                                                        <td>{{ $schedule->approver->last_name ?? '' }}{{ $schedule->approver->first_name ?? '' }}</td>
                                                    @endif
                                                    <td>
                                                        @if($schedule->status === 'approved')
                                                            <span class="label label-success">承認済み</span>
                                                        @elseif($schedule->status === 'rejected')
                                                            <span class="label label-danger">却下</span>
                                                        @else
                                                            <span class="label label-warning">{{ $schedule->status_display }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($schedule->approved_at)
                                                            {{ $schedule->approved_at->format('Y/m/d H:i') }}
                                                        @else
                                                            --
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info" onclick="showHistoryDetail({{ $schedule->id }})">
                                                            <i class="fas fa-eye"></i> 詳細
                                                        </button>
                                                    </td>
                                                </tr>

                                                <!-- Hidden detail row -->
                                                <tr id="detail-row-{{ $schedule->id }}" class="detail-row" style="display: none;">
                                                    <td colspan="8">
                                                        <div class="detail-panel">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h5><i class="fas fa-calendar"></i> 予定詳細</h5>
                                                                    <table class="table table-borderless table-sm">
                                                                        <tr>
                                                                            <th width="30%">タイトル:</th>
                                                                            <td>{{ $schedule->title }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>校舎:</th>
                                                                            <td>{{ $schedule->schoolBuilding->name ?? '--' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>予定日:</th>
                                                                            <td>{{ $schedule->schedule_date->format('Y年m月d日 (') }}{{ ['日', '月', '火', '水', '木', '金', '土'][$schedule->schedule_date->dayOfWeek] }}{{ ')' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>時間:</th>
                                                                            <td>{{ $schedule->time_display ?? '終日' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>色:</th>
                                                                            <td>
                                                                                <span class="color-indicator" style="background-color: {{ $schedule->color_info['bg'] }}; color: {{ $schedule->color_info['text'] }};">
                                                                                    {{ $schedule->color_info['name'] }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h5><i class="fas fa-clock"></i> 処理情報</h5>
                                                                    <table class="table table-borderless table-sm">
                                                                        @if($viewType === 'approver')
                                                                            <tr>
                                                                                <th width="30%">作成者:</th>
                                                                                <td>{{ $schedule->creator->last_name ?? '' }}{{ $schedule->creator->first_name ?? '' }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>作成日時:</th>
                                                                                <td>{{ $schedule->created_at->format('Y/m/d H:i') }}</td>
                                                                            </tr>
                                                                        @else
                                                                            <tr>
                                                                                <th width="30%">承認者:</th>
                                                                                <td>{{ $schedule->approver->last_name ?? '' }}{{ $schedule->approver->first_name ?? '' }}</td>
                                                                            </tr>
                                                                        @endif
                                                                        <tr>
                                                                            <th>処理日時:</th>
                                                                            <td>{{ $schedule->approved_at ? $schedule->approved_at->format('Y/m/d H:i') : '--' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>ステータス:</th>
                                                                            <td>
                                                                                @if($schedule->status === 'approved')
                                                                                    <span class="label label-success">承認済み</span>
                                                                                @elseif($schedule->status === 'rejected')
                                                                                    <span class="label label-danger">却下</span>
                                                                                @else
                                                                                    <span class="label label-warning">{{ $schedule->status_display }}</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            @if($schedule->content)
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <h5><i class="fas fa-file-text"></i> 内容</h5>
                                                                        <div class="well well-sm">
                                                                            {!! nl2br(e($schedule->content)) !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if($schedule->approval_note)
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <h5>
                                                                            <i class="fas fa-comment"></i> 
                                                                            @if($schedule->status === 'approved')
                                                                                承認メモ
                                                                            @else
                                                                                却下理由
                                                                            @endif
                                                                        </h5>
                                                                        <div class="well well-sm {{ $schedule->status === 'rejected' ? 'alert-danger' : 'alert-info' }}">
                                                                            {!! nl2br(e($schedule->approval_note)) !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pagination-info">
                                            {{ $schedules->firstItem() ?? 0 }}〜{{ $schedules->lastItem() ?? 0 }}件 / 全{{ $schedules->total() }}件
                                        </div>
                                        {{ $schedules->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i>
                                    @if($viewType === 'approver')
                                        承認履歴がありません。
                                    @else
                                        処理済みのスケジュールがありません。
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.panel-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.panel-heading .pull-right {
    margin-left: auto;
}

.panel-default .panel-heading {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.table-hover tbody tr:hover {
    background-color: #f5f5f5;
}

.schedule-title {
    max-width: 300px;
}

.detail-row {
    background-color: #f9f9f9;
}

.detail-panel {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin: 10px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.detail-panel h5 {
    color: #337ab7;
    border-bottom: 1px solid #ddd;
    padding-bottom: 8px;
    margin-bottom: 15px;
}

.detail-panel .table-borderless th {
    border: none;
    color: #666;
    font-weight: 600;
    font-size: 12px;
    padding: 4px 8px 4px 0;
}

.detail-panel .table-borderless td {
    border: none;
    padding: 4px 8px 4px 0;
}

.color-indicator {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    border: 1px solid rgba(0,0,0,0.1);
}

.label {
    display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}

.label-success {
    background-color: #5cb85c;
}

.label-danger {
    background-color: #d9534f;
}

.label-warning {
    background-color: #f0ad4e;
}

.well {
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
}

.well-sm {
    padding: 9px;
    border-radius: 3px;
}

.pagination-info {
    margin-bottom: 10px;
    color: #666;
    font-size: 14px;
}

.alert-danger.well {
    background-color: #f2dede;
    border-color: #ebccd1;
    color: #a94442;
}

.alert-info.well {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #31708f;
}

.select2-container {
    width: 100% !important;
}

.select2-container--bootstrap .select2-selection--single {
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.badge {
    display: inline-block;
    min-width: 10px;
    padding: 3px 7px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    border-radius: 10px;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }
    
    .detail-panel .row {
        margin: 0;
    }
    
    .detail-panel .col-md-6 {
        padding: 0;
        margin-bottom: 15px;
    }
    
    .schedule-title {
        max-width: 200px;
    }
}
</style>

<script>
$(document).ready(function() {
    $('.select2-school-building').select2({
        theme: 'bootstrap',
        language: 'ja',
        allowClear: false,
        width: '100%',
        minimumResultsForSearch: 5
    });
    
    $('#school_building_id, #status, #per_page').on('change', function() {
        $('#filterForm').submit();
    });
    
    $('[data-toggle="tooltip"]').tooltip();
});

function showHistoryDetail(scheduleId) {
    var detailRow = $('#detail-row-' + scheduleId);
    var allDetailRows = $('.detail-row');
    
    allDetailRows.not(detailRow).hide();
    detailRow.toggle();
    
    var button = $('button[onclick="showHistoryDetail(' + scheduleId + ')"]');
    if (detailRow.is(':visible')) {
        button.html('<i class="fas fa-eye-slash"></i> 閉じる');
    } else {
        button.html('<i class="fas fa-eye"></i> 詳細');
    }
}

function clearFilters() {
    $('#school_building_id').val('').trigger('change');
    $('#status').val('');
    $('#date_from').val('');
    $('#date_to').val('');
    $('#per_page').val('20');
    $('#filterForm').submit();
}
</script>
@endsection