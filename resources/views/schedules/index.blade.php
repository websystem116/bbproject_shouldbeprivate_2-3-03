@extends('layouts.app')

@section('title', 'スケジュール管理')

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
                    <span><i class="fas fa-calendar"></i> スケジュール管理</span>
                </div>

                <div class="panel-body">
                    <!-- Bottom Action Panel -->
                    <div class="panel panel-default bottom-actions-panel" style="margin-top: 20px;">
                        <div class="panel-body text-center">
                            <div class="action-buttons">
                                @if(Auth::user()->roles == 1 || Auth::user()->roles == 2)
                                <button type="button" class="btn btn-warning" id="approvalToggleBtn">
                                    <i class="fas fa-eye-slash"></i> 承認待ち一覧
                                    <span class="badge">{{ $pendingSchedules->count() ?? 0 }}</span>
                                </button>
                                @endif
                                
                                @if(Auth::user()->roles == 1)
                                <a href="{{ route('schedule_approvers.index') }}" class="btn btn-info">
                                    <i class="fas fa-users"></i> 承認者管理
                                </a>
                                @endif
                                
                                <a href="{{ route('schedules.create', ['date' => now()->format('Y-m-d'), 'school_building_id' => $schoolBuildingId]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> 新規登録
                                </a>
                                
                                <a href="{{ route('schedules.history') }}" class="btn btn-secondary">
                                    <i class="fas fa-history"></i> 履歴
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Approval Section (Hidden by default) -->
                    <div id="approvalSection" style="display: none;">
                        <div class="panel panel-warning" style="margin-bottom: 20px;">
                            <div class="panel-heading">
                                <h4><i class="fas fa-check-circle"></i> スケジュール承認</h4>
                            </div>
                            <div class="panel-body">
                                @if(isset($pendingSchedules) && $pendingSchedules->count() > 0)
                                    <div style="display: flex; margin-bottom: 15px;">
                                        <button type="button" class="btn btn-primary btn-sm check_all_approval">
                                            一括選択
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" id="bulkApproveBtn" style="margin-left: 10px;">
                                            <i class="fas fa-check"></i> 一括承認
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" id="bulkRejectBtn" style="margin-left: 10px;">
                                            <i class="fas fa-times"></i> 一括却下
                                        </button>
                                    </div>

                                    <form id="bulkApprovalForm" method="POST" action="{{ url('shinzemi/schedules-bulk-approval') }}">
                                        @csrf
                                        <input type="hidden" name="action" id="bulkAction" value="">
                                        <input type="hidden" name="approval_note" id="bulkApprovalNote" value="">
                                        
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="50">選択</th>
                                                        <th>予定日</th>
                                                        <th>時間</th>
                                                        <th>タイトル</th>
                                                        <th>校舎</th>
                                                        <th>作成者</th>
                                                        <th>登録日時</th>
                                                        <th>操作</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pendingSchedules as $schedule)
                                                        <tr id="schedule-row-{{ $schedule->id }}">
                                                            <td>
                                                                <input type="checkbox" name="schedule_ids[]" value="{{ $schedule->id }}" class="approval-checkbox">
                                                            </td>
                                                            <td>{{ $schedule->schedule_date->format('Y/m/d') }}</td>
                                                            <td>{{ $schedule->time_display ?? '--' }}</td>
                                                            <td>
                                                                <strong>{{ $schedule->title }}</strong>
                                                                @if($schedule->content)
                                                                    <br><small class="text-muted">{{ Str::limit($schedule->content, 50) }}</small>
                                                                @endif
                                                            </td>
                                                            <td>{{ $schedule->schoolBuilding->name ?? '--' }}</td>
                                                            <td>{{ $schedule->creator->last_name ?? '' }}{{ $schedule->creator->first_name ?? '' }}</td>
                                                            <td>{{ $schedule->created_at->format('Y/m/d H:i') }}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-info" onclick="toggleDetails({{ $schedule->id }})">
                                                                    <i class="fas fa-eye"></i> 詳細
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-success" onclick="approveSchedule({{ $schedule->id }})">
                                                                    <i class="fas fa-check"></i> 承認
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-danger" onclick="rejectScheduleWithPrompt({{ $schedule->id }})">
                                                                    <i class="fas fa-times"></i> 却下
                                                                </button>
                                                            </td>
                                                        </tr>

                                                        <!-- Dropdown Details Row -->
                                                        <tr id="details-row-{{ $schedule->id }}" class="schedule-details-row" style="display: none;">
                                                            <td colspan="8">
                                                                <div class="panel panel-info details-panel">
                                                                    <div class="panel-heading">
                                                                        <strong>{{ $schedule->title }}</strong> の詳細情報
                                                                    </div>
                                                                    <div class="panel-body">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <table class="table table-borderless">
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
                                                                                        <td>{{ $schedule->schedule_date->format('Y年m月d日') }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th>時間:</th>
                                                                                        <td>{{ $schedule->time_display ?? '--' }}</td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <table class="table table-borderless">
                                                                                    <tr>
                                                                                        <th width="30%">作成者:</th>
                                                                                        <td>{{ $schedule->creator->name ?? '--' }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th>登録日時:</th>
                                                                                        <td>{{ $schedule->created_at->format('Y/m/d H:i') }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th>状態:</th>
                                                                                        <td><span class="label label-warning">{{ $schedule->status_display }}</span></td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        @if($schedule->content)
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <h5>内容:</h5>
                                                                                    <div class="well well-sm">
                                                                                        {!! nl2br(e($schedule->content)) !!}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="row">
                                                                            <div class="col-md-12 text-center">
                                                                                <button type="button" class="btn btn-success" onclick="approveSchedule({{ $schedule->id }})">
                                                                                    <i class="fas fa-check"></i> 承認
                                                                                </button>
                                                                                <button type="button" class="btn btn-danger" onclick="rejectScheduleWithPrompt({{ $schedule->id }})" style="margin-left: 10px;">
                                                                                    <i class="fas fa-times"></i> 却下
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle"></i>
                                        承認待ちのスケジュールはありません。
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Panel -->
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="fas fa-calendar-alt"></i> {{ $year }}年 {{ $month }}月 カレンダー</span>
                                <div class="month-navigation">
                                    <a href="{{ route('schedules.index', ['year' => $month == 1 ? $year - 1 : $year, 'month' => $month == 1 ? 12 : $month - 1, 'school_building_id' => $schoolBuildingId]) }}" class="btn btn-default btn-sm">
                                        <i class="fas fa-chevron-left"></i> 前月
                                    </a>
                                    <a href="{{ route('schedules.index', ['year' => now()->year, 'month' => now()->month, 'school_building_id' => $schoolBuildingId]) }}" class="btn btn-info btn-sm" style="margin: 0 5px;">
                                        当月
                                    </a>
                                    <a href="{{ route('schedules.index', ['year' => $month == 12 ? $year + 1 : $year, 'month' => $month == 12 ? 1 : $month + 1, 'school_building_id' => $schoolBuildingId]) }}" class="btn btn-default btn-sm">
                                        翌月 <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="panel-body">
                            <!-- Filters -->
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-4">
                                    <form method="GET" action="{{ route('schedules.index') }}" id="filterForm">
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <input type="hidden" name="month" value="{{ $month }}">
                                        
                                        <div class="form-group">
                                        <label for="school_building_id">校舎選択</label>
                                        <select name="school_building_id" id="school_building_id" class="form-control select2-school-building">
                                        <option value="">全校舎</option>
                                        @foreach($schoolBuildings as $building)
                                        <option value="{{ $building->id }}" {{ $schoolBuildingId == $building->id ? 'selected' : '' }}>
                                        {{ $building->name }}
                                        </option>
                                        @endforeach
                                        </select>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Calendar -->
                            <div class="calendar-container">
                                <table class="table table-bordered calendar-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center calendar-header">月</th>
                                            <th class="text-center calendar-header">火</th>
                                            <th class="text-center calendar-header">水</th>
                                            <th class="text-center calendar-header">木</th>
                                            <th class="text-center calendar-header">金</th>
                                            <th class="text-center calendar-header saturday-header">土</th>
                                            <th class="text-center calendar-header sunday-header">日</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($week = 0; $week < 6; $week++)
                                            @php
                                                $weekStart = $week * 7;
                                                $weekEnd = $weekStart + 6;
                                            @endphp
                                            @if(isset($calendarData[$weekStart]))
                                                <tr>
                                                    @for($day = $weekStart; $day <= $weekEnd && isset($calendarData[$day]); $day++)
                                                        @php
                                                            $dayData = $calendarData[$day];
                                                            $date = $dayData['date'];
                                                            $isCurrentMonth = $dayData['is_current_month'];
                                                            $schedules = $dayData['schedules'];
                                                            $isToday = $date->isToday();
                                                            $isSunday = $dayData['is_sunday'] ?? $date->isSunday();
                                                            $isSaturday = $dayData['is_saturday'] ?? $date->isSaturday();
                                                            $isHoliday = $dayData['is_holiday'] ?? false;
                                                            $holidayName = $dayData['holiday_name'] ?? null;
                                                            
                                                            // Adjust day order for Monday start (0=Monday, 6=Sunday)
                                                            $dayOfWeek = ($date->dayOfWeek + 6) % 7;
                                                            
                                                            $cellClasses = [];
                                                            if (!$isCurrentMonth) $cellClasses[] = 'text-muted bg-light';
                                                            if ($isToday) $cellClasses[] = 'today';
                                                            if ($isSunday) $cellClasses[] = 'sunday-cell';
                                                            if ($isSaturday) $cellClasses[] = 'saturday-cell';
                                                            if ($isHoliday) $cellClasses[] = 'holiday-cell';
                                                        @endphp
                                                        <td class="calendar-cell {{ implode(' ', $cellClasses) }}" 
                                                        data-date="{{ $date->format('Y-m-d') }}" 
                                                        onclick="handleCalendarCellClick(event, '{{ $date->format('Y-m-d') }}')"
                                                        @if($holidayName) title="{{ $holidayName }}" @endif>
                                                            
                                                            <div class="calendar-day-header">
                                                                <div class="day-number-container">
                                                                    <span class="day-number {{ $isToday ? 'today-number' : '' }} {{ $isSunday ? 'sunday-text' : '' }}">
                                                                        {{ $date->day }}
                                                                    </span>
                                                                    @if($isHoliday && $holidayName)
                                                                        <div class="holiday-name">{{ Str::limit($holidayName, 8) }}</div>
                                                                    @endif
                                                                </div>
                                                                @if($isCurrentMonth)
                                                                    <span class="add-schedule-icon" title="予定追加">
                                                                        <i class="fas fa-plus-circle"></i>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="schedule-list">
                                                                @foreach($schedules->take(3) as $schedule)
                                                                <div class="schedule-item">
                                                                <div class="schedule-event" 
                                                                 style="background: linear-gradient(135deg, {{ $schedule->color_info['bg'] }} 0%, {{ $schedule->color_info['bg'] }}dd 100%); 
                                                                    border: 1px solid {{ $schedule->color_info['border'] }}; 
                                                                        color: {{ $schedule->color_info['text'] }};"
                                                                 onclick="event.stopPropagation(); showEventEditModal({{ $schedule->id }}, '{{ addslashes($schedule->title) }}', '{{ addslashes(str_replace(["\r\n", "\r", "\n"], '\\n', $schedule->content ?? '')) }}', '{{ $schedule->schedule_date->format('Y-m-d') }}', '{{ $schedule->start_time }}', '{{ $schedule->end_time }}', {{ $schedule->school_building_id }}, '{{ addslashes($schedule->schoolBuilding->name ?? '') }}', '{{ $schedule->color }}');"> 
                                                                <span class="event-indicator">
                                                                <i class="fas fa-circle event-dot" style="color: {{ $schedule->color_info['border'] }};"></i>
                                                                </span>
                                                                    <span class="event-title">{{ Str::limit($schedule->title, 15) }}</span>
                                                                        @if($schedule->start_time)
                                                                                <div class="event-time">{{ substr($schedule->start_time, 0, 5) }}{{ $schedule->end_time ? ' - ' . substr($schedule->end_time, 0, 5) : '' }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                                                
                                                                @if($schedules->count() > 3)
                                                                    <div class="more-events">
                                                                        <small class="text-muted">他{{ $schedules->count() - 3 }}件</small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    @endfor
                                                </tr>
                                            @endif
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Registration Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">予定登録</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="scheduleForm" method="POST" action="{{ route('schedules.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_title">
                            <span class="text-danger">*</span> 予定タイトル
                        </label>
                        <input id="modal_title" type="text" class="form-control" 
                               name="title" required autocomplete="title" maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="modal_color">
                            <span class="text-danger">*</span> 色
                        </label>
                        <div class="color-picker-container">
                            @foreach($availableColors as $colorKey => $colorInfo)
                                <label class="color-option" for="modal_color_{{ $colorKey }}">
                                    <input type="radio" id="modal_color_{{ $colorKey }}" name="color" value="{{ $colorKey }}" 
                                           class="color-radio" {{ $colorKey === 'yellow' ? 'checked' : '' }} required>
                                    <span class="color-sample" style="background-color: {{ $colorInfo['bg'] }}; border-color: {{ $colorInfo['border'] }}; color: {{ $colorInfo['text'] }}">
                                        {{ $colorInfo['name'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_school_building_id">
                            <span class="text-danger">*</span> 校舎
                        </label>
                        <select id="modal_school_building_id" class="form-control select2-modal-school-building" 
                                name="school_building_id" required>
                            <option value="">校舎を選択してください</option>
                            @foreach($schoolBuildings as $building)
                                <option value="{{ $building->id }}">
                                    {{ $building->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="modal_schedule_date">
                            <span class="text-danger">*</span> 予定日
                        </label>
                        <div class="multiple-dates-container">
                            <div class="single-date-row">
                                <input id="modal_schedule_date" type="date" class="form-control date-input" 
                                       name="schedule_dates[]" required readonly>
                                <button type="button" class="btn btn-success btn-sm add-date-btn" title="日付を追加">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">複数の日付に同じ予定を登録する場合は「+」ボタンで日付を追加してください</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_start_time">開始時間</label>
                                <input id="modal_start_time" type="time" class="form-control" 
                                       name="start_time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_end_time">終了時間</label>
                                <input id="modal_end_time" type="time" class="form-control" 
                                       name="end_time">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_content">内容</label>
                        <textarea id="modal_content" class="form-control" 
                                  name="content" rows="4" placeholder="予定の詳細内容を入力してください"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        登録された予定は承認者による承認が必要です。承認後にカレンダーに表示されます。
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 登録
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Approval Note Modal -->
<div class="modal fade" id="bulkNoteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkNoteModalTitle">一括処理</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bulkNoteText" id="bulkNoteLabel">メモ</label>
                    <textarea id="bulkNoteText" class="form-control" rows="3" placeholder="メモを入力してください（任意）"></textarea>
                </div>
                <div class="alert alert-warning" id="bulkConfirmText">
                    選択された予定を一括処理します。よろしいですか？
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" id="bulkConfirmBtn">実行</button>
            </div>
        </div>
    </div>
</div>

<!-- Event View Modal (for unauthorized users) -->
<div class="modal fade" id="eventViewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">予定詳細</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><strong>予定タイトル</strong></label>
                    <p id="view_title" class="form-control-static"></p>
                </div>

                <div class="form-group">
                    <label><strong>校舎</strong></label>
                    <p id="view_school_building" class="form-control-static"></p>
                </div>

                <div class="form-group">
                    <label><strong>予定日</strong></label>
                    <p id="view_schedule_date" class="form-control-static"></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>開始時間</strong></label>
                            <p id="view_start_time" class="form-control-static"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>終了時間</strong></label>
                            <p id="view_end_time" class="form-control-static"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label><strong>内容</strong></label>
                    <div id="view_content" class="well" style="background-color: #f9f9f9; padding: 10px; border: 1px solid #e3e3e3; border-radius: 4px; min-height: 60px;">
                        <!-- Content will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>

<!-- Event Edit/Delete Modal -->
<div class="modal fade" id="eventEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">予定編集</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="eventEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title">
                            <span class="text-danger">*</span> 予定タイトル
                        </label>
                        <input id="edit_title" type="text" class="form-control" 
                               name="title" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="edit_color">
                            <span class="text-danger">*</span> 色
                        </label>
                        <div class="color-picker-container">
                            @foreach($availableColors as $colorKey => $colorInfo)
                                <label class="color-option" for="edit_color_{{ $colorKey }}">
                                    <input type="radio" id="edit_color_{{ $colorKey }}" name="color" value="{{ $colorKey }}" 
                                           class="color-radio" required>
                                    <span class="color-sample" style="background-color: {{ $colorInfo['bg'] }}; border-color: {{ $colorInfo['border'] }}; color: {{ $colorInfo['text'] }}">
                                        {{ $colorInfo['name'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_school_building_id">
                            <span class="text-danger">*</span> 校舎
                        </label>
                        <select id="edit_school_building_id" class="form-control select2-edit-school-building" 
                                name="school_building_id" required>
                            @foreach($schoolBuildings as $building)
                                <option value="{{ $building->id }}">
                                    {{ $building->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_schedule_date">
                            <span class="text-danger">*</span> 予定日
                        </label>
                        <div class="multiple-dates-container-edit">
                            <div class="single-date-row">
                                <input id="edit_schedule_date" type="date" class="form-control date-input" 
                                       name="schedule_dates[]" required>
                                <button type="button" class="btn btn-success btn-sm add-date-btn-edit" title="日付を追加">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">複数の日付に同じ予定を登録する場合は「+」ボタンで日付を追加してください</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_start_time">開始時間</label>
                                <input id="edit_start_time" type="time" class="form-control" 
                                       name="start_time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_end_time">終了時間</label>
                                <input id="edit_end_time" type="time" class="form-control" 
                                       name="end_time">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_content">内容</label>
                        <textarea id="edit_content" class="form-control" 
                                  name="content" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                    <button type="button" class="btn btn-danger" id="deleteEventBtn">
                        <i class="fas fa-trash"></i> 削除
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 更新
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Event Tooltip -->
<div id="eventTooltip" class="event-tooltip" style="display: none;">
    <div class="tooltip-content">
        <div class="tooltip-title"></div>
        <div class="tooltip-time"></div>
        <div class="tooltip-building"></div>
    </div>
</div>

<style>
/* Calendar panel styling */
.panel-primary > .panel-heading {
    background-color: #337ab7;
    border-color: #337ab7;
    color: #fff;
}

.calendar-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 10px;
}

.calendar-table {
    margin-bottom: 0;
    table-layout: fixed;
    width: 100%;
}

.calendar-header {
    background: #f8f9fa;
    padding: 12px 8px;
    font-weight: bold;
    border-bottom: 2px solid #dee2e6;
}

.sunday-header {
    background: #ffebee;
    color: #c62828;
}

.saturday-header {
    background: #e3f2fd;
    color: #1565c0;
}

.calendar-cell {
    position: relative;
    height: 100px;
    vertical-align: top;
    padding: 6px;
    cursor: pointer;
    border: 1px solid #e9ecef !important;
    transition: background-color 0.2s ease;
}

.calendar-cell:hover {
    background-color: #f8f9fa !important;
}

/* Weekend and holiday styling - ensuring consistent borders */
.sunday-cell {
    background-color: #ffcccc !important;
    border: 1px solid #e9ecef !important; /* Force same border */
}

.sunday-cell:hover {
    background-color: #ffb3b3 !important;
    border: 1px solid #e9ecef !important; /* Force same border */
}

.saturday-cell {
    background-color: #cce5ff !important;
    border: 1px solid #e9ecef !important; /* Force same border */
}

.saturday-cell:hover {
    background-color: #b3d9ff !important;
    border: 1px solid #e9ecef !important; /* Force same border */
}

.holiday-cell {
    background-color: #ffebcc !important;
    border: 1px solid #e9ecef !important; /* Force same border */
}

.holiday-cell:hover {
    background-color: #ffe0b3 !important;
    border: 1px solid #e9ecef !important; /* Force same border */
}

.sunday-text {
    color: #d32f2f !important;
    font-weight: bold;
}

/* Today gets special border - this overrides the base border */
.calendar-cell.today {
    background-color: #e3f2fd !important;
    border: 2px solid #2196f3 !important; /* Only today gets special border */
}

.calendar-day-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 4px;
}

.day-number-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.day-number {
    font-weight: bold;
    font-size: 14px;
    color: #495057;
}

.day-number.today-number {
    color: #2196f3;
    background: #fff;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.holiday-name {
    font-size: 8px;
    color: #d84315;
    font-weight: bold;
    margin-top: 1px;
    line-height: 1;
}

.add-schedule-icon {
    color: #6c757d;
    font-size: 12px;
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

.calendar-cell:hover .add-schedule-icon {
    opacity: 1;
    color: #007bff;
}

.schedule-list {
    max-height: 70px;
    overflow: hidden;
}

.schedule-item {
    margin-bottom: 2px;
}

/* Improved Event Styling - Updated for dynamic colors */

.more-events {
    text-align: center;
    margin-top: 2px;
}

.more-events small {
    font-size: 8px;
    color: #6c757d;
    font-style: italic;
}

/* Event Tooltip */
.event-tooltip {
    position: absolute;
    background: #2c3e50;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    max-width: 200px;
}

.event-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 20px;
    border: 6px solid transparent;
    border-top-color: #2c3e50;
}

.tooltip-title {
    font-weight: bold;
    margin-bottom: 4px;
}

.tooltip-time {
    color: #f39c12;
    font-size: 11px;
}

.tooltip-building {
    color: #95a5a6;
    font-size: 11px;
    margin-top: 2px;
}

/* Approval section styles */
.approval-checkbox {
    width: 16px;
    height: 16px;
}

#approvalSection {
    border-top: 3px solid #f0ad4e;
    margin-bottom: 20px;
}

/* Month navigation button styling */
.month-navigation .btn {
    margin: 0 2px;
}

/* Details panel styling */
.schedule-details-row {
    background-color: #f9f9f9;
}

.details-panel {
    margin: 10px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.details-panel .panel-heading {
    background-color: #d9edf7;
    color: #31708f;
}

.well {
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    padding: 10px;
    margin: 0;
}

/* Label styling */
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

.label-warning {
    background-color: #f0ad4e;
}

/* Bottom Action Panel */
.bottom-actions-panel {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.action-buttons .btn {
    min-width: 140px;
    padding: 12px 20px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.action-buttons .btn-warning {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    border: none;
    color: #fff;
}

.action-buttons .btn-warning:hover {
    background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
}

.action-buttons .btn-info {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border: none;
    color: #fff;
}

.action-buttons .btn-info:hover {
    background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
}

.action-buttons .btn-primary {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    border: none;
    color: #fff;
}

.action-buttons .btn-primary:hover {
    background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
}

.action-buttons .btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    border: none;
    color: #fff;
}

.action-buttons .btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
}

.badge {
    background: rgba(255,255,255,0.3);
    color: #fff;
    border-radius: 10px;
    padding: 3px 8px;
    font-size: 11px;
    margin-left: 8px;
    font-weight: bold;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .calendar-table {
        font-size: 12px;
    }
    
    .calendar-cell {
        height: 80px;
        padding: 4px;
    }
    
    .day-number {
        font-size: 12px;
    }
    
    .event-title {
        font-size: 9px;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .action-buttons .btn {
        width: 100%;
        max-width: 250px;
        margin-bottom: 10px;
    }
}

/* Modal improvements */
.modal-lg {
    max-width: 700px;
}

.form-group.row {
    margin-bottom: 1rem;
}

.alert-info {
    border-left: 4px solid #17a2b8;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

/* Select2 Custom Styling */
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
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}

.select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
    color: #555;
    padding: 0;
}

.select2-container--bootstrap .select2-selection--single .select2-selection__arrow {
    height: 32px;
    right: 3px;
}

.select2-container--bootstrap .select2-dropdown {
    border-color: #66afe9;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px rgba(102, 175, 233, 0.6);
}

.select2-container--bootstrap .select2-results__option--highlighted[aria-selected] {
    background-color: #337ab7;
    color: #fff;
}

.select2-container--bootstrap .select2-search--dropdown .select2-search__field {
    border-color: #66afe9;
}

/* Select2 in Modal */
.modal .select2-container {
    z-index: 9999;
}

.select2-drop-mask {
    z-index: 9998;
}

.select2-dropdown {
    z-index: 9999;
}

/* Color Picker Styling */
.color-picker-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 8px;
}

.color-option {
    position: relative;
    cursor: pointer;
    margin: 0;
}

.color-radio {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.color-sample {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 6px;
    border: 2px solid transparent;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
    min-width: 60px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.color-option:hover .color-sample {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.color-radio:checked + .color-sample {
    border-color: #333 !important;
    box-shadow: 0 0 0 3px rgba(51, 51, 51, 0.2), 0 4px 8px rgba(0,0,0,0.15);
    transform: scale(1.05);
}

.color-radio:focus + .color-sample {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/* Event Styling Updates - Remove default yellow styling */
.schedule-event {
    display: flex;
    align-items: center;
    padding: 2px 4px;
    border-radius: 4px;
    margin-bottom: 2px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    /* Remove default background and border - will be set dynamically */
}

.schedule-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    filter: brightness(1.1);
}

.event-indicator {
    margin-right: 4px;
}

.event-dot {
    font-size: 6px;
    text-shadow: 0 1px 1px rgba(0,0,0,0.3);
}

.event-title {
    font-size: 10px;
    font-weight: 600;
    flex: 1;
    line-height: 1.2;
}

.event-time {
    font-size: 8px;
    font-weight: 500;
    margin-top: 1px;
    opacity: 0.9;
}

/* Event View Modal Styling */
.form-control-static {
    padding: 8px 0;
    margin: 0;
    color: #333;
    font-size: 14px;
    line-height: 1.5;
    background: transparent;
    border: none;
    min-height: 20px;
}

#eventViewModal .modal-body {
    background-color: #fafafa;
}

#eventViewModal .form-group {
    margin-bottom: 15px;
    padding: 10px;
    background: white;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

#eventViewModal .form-group label {
    color: #495057;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

#view_content {
    max-height: 120px;
    overflow-y: auto;
    color: #333;
    line-height: 1.6;
}

#view_content:empty:before {
    content: '内容がありません';
    color: #999;
    font-style: italic;
}

/* Multiple Dates Styling */
.multiple-dates-container,
.multiple-dates-container-edit {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    background-color: #fafafa;
}

.single-date-row {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.single-date-row:last-child {
    margin-bottom: 0;
}

.date-input {
    flex: 1;
    margin-right: 8px;
}

.add-date-btn,
.add-date-btn-edit {
    margin-left: 8px;
    padding: 6px 8px;
    border-radius: 4px;
    border: none;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.add-date-btn:hover,
.add-date-btn-edit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
}

.remove-date-btn {
    margin-left: 8px;
    padding: 6px 8px;
    border-radius: 4px;
    border: none;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.remove-date-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
}

.multiple-dates-summary {
    background: #e3f2fd;
    border: 1px solid #2196f3;
    border-radius: 4px;
    padding: 8px 12px;
    margin-top: 10px;
    font-size: 12px;
    color: #1565c0;
}

.multiple-dates-summary strong {
    color: #0d47a1;
}

.date-count-badge {
    background: #2196f3;
    color: white;
    border-radius: 12px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: bold;
    margin-left: 8px;
}
</style>

<script>
$(document).ready(function() {
    // Initialize Select2
    initializeSelect2();
    
    // Force first option display after Select2 initialization
    setTimeout(function() {
        var $select = $('#school_building_id');
        var currentVal = $select.val();
        
        // If no value is selected or empty value is selected, ensure it shows "全校舎"
        if (!currentVal || currentVal === '') {
            $select.trigger('change');
        }
    }, 100);
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Toggle approval section with updated styling
    $('#approvalToggleBtn').on('click', function() {
        $('#approvalSection').slideToggle();
        
        // Update button text and icon
        if ($('#approvalSection').is(':visible')) {
            $(this).html('<i class="fas fa-eye-slash"></i> 承認待ち一覧 <span class="badge">{{ $pendingSchedules->count() ?? 0 }}</span>');
        } else {
            $(this).html('<i class="fas fa-eye"></i> 承認待ち一覧を表示 <span class="badge">{{ $pendingSchedules->count() ?? 0 }}</span>');
        }
    });
    
    // Initialize approval section as hidden
    $('#approvalSection').hide();
    
    // Bulk checkbox selection
    $('.check_all_approval').on('click', function() {
        if ($('input[name="schedule_ids[]"]:checked').length == 0) {
            $('input[name="schedule_ids[]"]').prop('checked', true);
        } else {
            $('input[name="schedule_ids[]"]').prop('checked', false);
        }
    });
    
    // Bulk approve button
    $('#bulkApproveBtn').on('click', function() {
        var checked = $('input[name="schedule_ids[]"]:checked').length;
        if (checked == 0) {
            alert('承認する予定を選択してください。');
            return false;
        }
        
        $('#bulkNoteModalTitle').text('一括承認');
        $('#bulkNoteLabel').text('承認メモ（任意）');
        $('#bulkConfirmText').text(checked + '件の予定を一括承認します。よろしいですか？');
        $('#bulkConfirmBtn').removeClass('btn-danger').addClass('btn-success').text('承認');
        $('#bulkAction').val('approve');
        $('#bulkNoteModal').modal('show');
    });
    
    // Bulk reject button
    $('#bulkRejectBtn').on('click', function() {
        var checked = $('input[name="schedule_ids[]"]:checked').length;
        if (checked == 0) {
            alert('却下する予定を選択してください。');
            return false;
        }
        
        $('#bulkNoteModalTitle').text('一括却下');
        $('#bulkNoteLabel').text('却下理由（推奨）');
        $('#bulkConfirmText').text(checked + '件の予定を一括却下します。よろしいですか？');
        $('#bulkConfirmBtn').removeClass('btn-success').addClass('btn-danger').text('却下');
        $('#bulkAction').val('reject');
        $('#bulkNoteModal').modal('show');
    });
    
    // Bulk confirm button
    $('#bulkConfirmBtn').on('click', function() {
        $('#bulkApprovalNote').val($('#bulkNoteText').val());
        $('#bulkApprovalForm').submit();
    });
    
    // Clear bulk note modal
    $('#bulkNoteModal').on('hidden.bs.modal', function() {
        $('#bulkNoteText').val('');
    });
    
    // Form validation
    $('#scheduleForm').on('submit', function(e) {
        var title = $('#modal_title').val().trim();
        var schoolBuilding = $('#modal_school_building_id').val();
        var scheduleDate = $('#modal_schedule_date').val();
        var color = $('input[name="color"]:checked').val();
        
        if (!title) {
            alert('予定タイトルを入力してください。');
            e.preventDefault();
            return false;
        }
        
        if (!color) {
            alert('色を選択してください。');
            e.preventDefault();
            return false;
        }
        
        if (!schoolBuilding) {
            alert('校舎を選択してください。');
            e.preventDefault();
            return false;
        }
        
        if (!scheduleDate) {
            alert('予定日を選択してください。');
            e.preventDefault();
            return false;
        }
        
        // Validate time range
        var startTime = $('#modal_start_time').val();
        var endTime = $('#modal_end_time').val();
        
        if (startTime && endTime && startTime >= endTime) {
            alert('終了時間は開始時間より後に設定してください。');
            e.preventDefault();
            return false;
        }
        
        return true;
    });
    
    // Clear modal when hidden
    $('#scheduleModal').on('hidden.bs.modal', function() {
        $('#scheduleForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        // Reinitialize select2 for modal
        $('.select2-modal-school-building').select2('destroy').select2({
            theme: 'bootstrap',
            language: 'ja',
            placeholder: '校舎を選択してください',
            allowClear: false,
            width: '100%',
            dropdownParent: $('#scheduleModal')
        });
    });
    
    // Set school building from filter when modal opens
    $('#scheduleModal').on('show.bs.modal', function() {
        var filterSchoolBuilding = $('#school_building_id').val();
        if (filterSchoolBuilding) {
            $('#modal_school_building_id').val(filterSchoolBuilding);
        }
    });
    
    // Event edit form validation and submission
    $('#eventEditForm').on('submit', function(e) {
        var title = $('#edit_title').val().trim();
        var schoolBuilding = $('#edit_school_building_id').val();
        var scheduleDate = $('#edit_schedule_date').val();
        var color = $('#eventEditModal input[name="color"]:checked').val();
        
        if (!title) {
            alert('予定タイトルを入力してください。');
            e.preventDefault();
            return false;
        }
        
        if (!color) {
            alert('色を選択してください。');
            e.preventDefault();
            return false;
        }
        
        if (!schoolBuilding) {
            alert('校舎を選択してください。');
            e.preventDefault();
            return false;
        }
        
        if (!scheduleDate) {
            alert('予定日を選択してください。');
            e.preventDefault();
            return false;
        }
        
        // Validate time range
        var startTime = $('#edit_start_time').val();
        var endTime = $('#edit_end_time').val();
        
        if (startTime && endTime && startTime >= endTime) {
            alert('終了時間は開始時間より後に設定してください。');
            e.preventDefault();
            return false;
        }
        
        return true;
    });
    
    // Delete event button
    $('#deleteEventBtn').on('click', function() {
        var scheduleId = $(this).data('schedule-id');
        if (confirm('この予定を削除してもよろしいですか？\n\n削除された予定は復元できません。')) {
            var form = $('<form>', {
                'method': 'POST',
                'action': '/shinzemi/schedules/' + scheduleId
            });
            
            form.append($('<input>', {'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}'}));
            form.append($('<input>', {'type': 'hidden', 'name': '_method', 'value': 'DELETE'}));
            
            $('body').append(form);
            form.submit();
        }
    });
    
    // Clear edit modal when hidden
    $('#eventEditModal').on('hidden.bs.modal', function() {
        $('#eventEditForm')[0].reset();
        $('#deleteEventBtn').removeData('schedule-id');
        // Reinitialize select2 for edit modal
        $('.select2-edit-school-building').select2('destroy').select2({
            theme: 'bootstrap',
            language: 'ja',
            placeholder: '校舎を選択してください',
            allowClear: false,
            width: '100%',
            dropdownParent: $('#eventEditModal')
        });
    });
    
    // Clear view modal when hidden
    $('#eventViewModal').on('hidden.bs.modal', function() {
        $('#view_title').text('');
        $('#view_school_building').text('');
        $('#view_schedule_date').text('');
        $('#view_start_time').text('');
        $('#view_end_time').text('');
        $('#view_content').html('');
    });
    
    // Hide event tooltip when clicking elsewhere
    $(document).on('click', function() {
        $('#eventTooltip').hide();
    });
    
    // Multiple dates functionality for registration modal
    $(document).on('click', '.add-date-btn', function() {
        addDateRow('.multiple-dates-container');
    });
    
    // Multiple dates functionality for edit modal
    $(document).on('click', '.add-date-btn-edit', function() {
        addDateRow('.multiple-dates-container-edit');
    });
    
    // Remove date row functionality
    $(document).on('click', '.remove-date-btn', function() {
        $(this).closest('.single-date-row').remove();
        updateDatesSummary();
    });
    
    // Update summary when dates change
    $(document).on('change', '.date-input', function() {
        updateDatesSummary();
    });
    
    // Handle clicks on schedule events using data attributes
    $(document).on('click', '.clickable-event', function(e) {
        e.stopPropagation();
        
        var scheduleId = $(this).data('schedule-id');
        var title = $(this).data('schedule-title');
        var content = $(this).data('schedule-content');
        var date = $(this).data('schedule-date');
        var startTime = $(this).data('schedule-start-time');
        var endTime = $(this).data('schedule-end-time');
        var schoolBuildingId = $(this).data('schedule-building-id');
        var schoolBuildingName = $(this).data('schedule-building-name');
        var color = $(this).data('schedule-color');
        
        showEventEditModal(scheduleId, title, content, date, startTime, endTime, schoolBuildingId, schoolBuildingName, color);
    });
});

// Toggle details dropdown
function toggleDetails(scheduleId) {
    var detailsRow = $('#details-row-' + scheduleId);
    var allDetailsRows = $('.schedule-details-row');
    
    // Hide all other detail rows
    allDetailsRows.not(detailsRow).hide();
    
    // Toggle current detail row
    detailsRow.toggle();
}

// Show event edit modal or view modal based on permissions
function showEventEditModal(scheduleId, title, content, date, startTime, endTime, schoolBuildingId, schoolBuildingName, color) {
    // Check if user has permission to edit
    @if(Auth::user()->roles == 1 || Auth::user()->roles == 2)
        var canEdit = true;
    @else
        var canEdit = false;
    @endif
    
    if (canEdit) {
        // Show edit modal for authorized users
        showEditModal(scheduleId, title, content, date, startTime, endTime, schoolBuildingId, color);
    } else {
        // Show view-only modal for unauthorized users
        showViewModal(title, content, date, startTime, endTime, schoolBuildingName);
    }
}

// Show edit modal for authorized users
function showEditModal(scheduleId, title, content, date, startTime, endTime, schoolBuildingId, color) {
    // Set form action URL
    $('#eventEditForm').attr('action', '/shinzemi/schedules/' + scheduleId);
    
    // Populate form fields
    $('#edit_title').val(title);
    $('#edit_content').val(content || '');
    
    // Reset multiple dates container to single date first
    var container = $('.multiple-dates-container-edit');
    container.find('.single-date-row').not(':first').remove();
    container.find('.multiple-dates-summary').remove();
    
    // Set the date in the first date input
    $('#edit_schedule_date').val(date);
    
    $('#edit_start_time').val(startTime ? startTime.substring(0, 5) : '');
    $('#edit_end_time').val(endTime ? endTime.substring(0, 5) : '');
    $('#edit_school_building_id').val(schoolBuildingId);
    
    // Set color selection
    if (color) {
        $('input[name="color"][value="' + color + '"]').prop('checked', true);
    }
    
    // Store schedule ID for delete function
    $('#deleteEventBtn').data('schedule-id', scheduleId);
    
    // Show modal
    $('#eventEditModal').modal('show');
}

// Show view-only modal for unauthorized users
function showViewModal(title, content, date, startTime, endTime, schoolBuildingName) {
    // Populate view fields
    $('#view_title').text(title);
    $('#view_school_building').text(schoolBuildingName || '--');
    
    // Format date nicely
    var formattedDate = new Date(date).toLocaleDateString('ja-JP', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long'
    });
    $('#view_schedule_date').text(formattedDate);
    
    // Format times
    $('#view_start_time').text(startTime ? startTime.substring(0, 5) : '--');
    $('#view_end_time').text(endTime ? endTime.substring(0, 5) : '--');
    
    // Set content with line breaks
    if (content && content.trim()) {
        $('#view_content').html(content.replace(/\n/g, '<br>'));
    } else {
        $('#view_content').html('<span class="text-muted">内容がありません</span>');
    }
    
    // Show modal
    $('#eventViewModal').modal('show');
}

// Show event tooltip
function showEventTooltip(scheduleId, event) {
    // You would need to pass schedule data here or make an AJAX call
    // For now, this is a placeholder implementation
    var tooltip = $('#eventTooltip');
    tooltip.css({
        left: event.pageX + 10,
        top: event.pageY - 10
    }).show();
}

// Handle calendar cell clicks (empty area vs events)
function handleCalendarCellClick(event, date) {
    // Check if the click is on an empty area (not on an event)
    var target = event.target;
    
    // If clicked on an event or event content, don't open registration modal
    if (target.closest('.schedule-event') || 
        target.closest('.schedule-item') || 
        target.closest('.clickable-event') ||
        target.classList.contains('schedule-event') ||
        target.classList.contains('clickable-event') ||
        target.classList.contains('event-title') ||
        target.classList.contains('event-time') ||
        target.classList.contains('event-indicator') ||
        target.classList.contains('event-dot')) {
        // Event click is handled by the event's own click handler
        return;
    }
    
    // Clicked on empty area - open registration modal
    openScheduleModal(date);
}

// Open schedule registration modal
function openScheduleModal(date) {
    // Set the selected date
    $('#modal_schedule_date').val(date);
    
    // Make the first date input editable when opened from calendar
    if (date) {
        $('#modal_schedule_date').prop('readonly', true);
    } else {
        $('#modal_schedule_date').prop('readonly', false);
    }
    
    // Set the modal title with selected date
    if (date) {
        var formattedDate = new Date(date).toLocaleDateString('ja-JP', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        $('#scheduleModalLabel').text('予定登録 - ' + formattedDate);
    } else {
        $('#scheduleModalLabel').text('予定登録');
    }
    
    // Show the modal
    $('#scheduleModal').modal('show');
    
    // Focus on title field after modal is shown
    $('#scheduleModal').on('shown.bs.modal', function() {
        $('#modal_title').focus();
    });
}

// Individual approval functions
function approveSchedule(scheduleId) {
    if (confirm('この予定を承認しますか？')) {
        var form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("schedules.updateApproval", ":id") }}'.replace(':id', scheduleId)
        });
        
        form.append($('<input>', {'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}'}));
        form.append($('<input>', {'type': 'hidden', 'name': '_method', 'value': 'PUT'}));
        form.append($('<input>', {'type': 'hidden', 'name': 'status', 'value': 'approved'}));
        
        $('body').append(form);
        form.submit();
    }
}

function rejectScheduleWithPrompt(scheduleId) {
    var reason = prompt('却下理由を入力してください（任意）:');
    if (reason !== null) {  // null means user clicked cancel
        var form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("schedules.updateApproval", ":id") }}'.replace(':id', scheduleId)
        });
        
        form.append($('<input>', {'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}'}));
        form.append($('<input>', {'type': 'hidden', 'name': '_method', 'value': 'PUT'}));
        form.append($('<input>', {'type': 'hidden', 'name': 'status', 'value': 'rejected'}));
        form.append($('<input>', {'type': 'hidden', 'name': 'approval_note', 'value': reason}));
        
        $('body').append(form);
        form.submit();
    }
}

// Initialize Select2 function
function initializeSelect2() {
    // Main filter select - no placeholder to show the first option (全校舎)
    $('.select2-school-building').select2({
        theme: 'bootstrap',
        language: 'ja',
        allowClear: false,
        width: '100%',
        minimumResultsForSearch: 5  // Show search box only if more than 5 options
    });
    
    // Modal select boxes
    $('.select2-modal-school-building').select2({
        theme: 'bootstrap',
        language: 'ja',
        placeholder: '校舎を選択してください',
        allowClear: false,
        width: '100%',
        dropdownParent: $('#scheduleModal')
    });
    
    $('.select2-edit-school-building').select2({
        theme: 'bootstrap',
        language: 'ja',
        placeholder: '校舎を選択してください',
        allowClear: false,
        width: '100%',
        dropdownParent: $('#eventEditModal')
    });
    
    // Handle change event for filter (only select event)
    $('.select2-school-building').on('select2:select', function() {
        document.getElementById('filterForm').submit();
    });
}

// Add new date row
function addDateRow(containerSelector) {
    var container = $(containerSelector);
    var dateRowHtml = '<div class="single-date-row">' +
        '<input type="date" class="form-control date-input" name="schedule_dates[]" required>' +
        '<button type="button" class="btn btn-danger btn-sm remove-date-btn" title="日付を削除">' +
        '<i class="fas fa-minus"></i>' +
        '</button>' +
        '</div>';
    
    container.append(dateRowHtml);
    updateDatesSummary();
}

// Update dates summary
function updateDatesSummary() {
    // For registration modal
    updateSummaryForContainer('.multiple-dates-container', '#scheduleModal');
    
    // For edit modal
    updateSummaryForContainer('.multiple-dates-container-edit', '#eventEditModal');
}

// Update summary for specific container
function updateSummaryForContainer(containerSelector, modalSelector) {
    var container = $(containerSelector);
    if (container.length === 0) return;
    
    var dateInputs = container.find('.date-input');
    var validDates = [];
    
    dateInputs.each(function() {
        var dateValue = $(this).val();
        if (dateValue && validDates.indexOf(dateValue) === -1) {
            validDates.push(dateValue);
        }
    });
    
    // Remove existing summary
    container.find('.multiple-dates-summary').remove();
    
    // Add summary if more than 1 date
    if (validDates.length > 1) {
        var summaryHtml = '<div class="multiple-dates-summary">' +
            '<strong>登録予定日数:</strong> ' + validDates.length + '日' +
            '<span class="date-count-badge">' + validDates.length + '</span>' +
            '<br><small>同じ内容の予定が ' + validDates.length + ' 日分登録されます</small>' +
            '</div>';
        container.append(summaryHtml);
    }
}

// Clear multiple dates when modal is hidden
$('#scheduleModal').on('hidden.bs.modal', function() {
    // Reset to single date row
    var container = $('.multiple-dates-container');
    container.find('.single-date-row').not(':first').remove();
    container.find('.multiple-dates-summary').remove();
    
    // Reset first date input
    container.find('.date-input:first').removeAttr('readonly');
});

$('#eventEditModal').on('hidden.bs.modal', function() {
    // Reset to single date row
    var container = $('.multiple-dates-container-edit');
    container.find('.single-date-row').not(':first').remove();
    container.find('.multiple-dates-summary').remove();
});

// Update form validation for multiple dates
$('#scheduleForm').off('submit').on('submit', function(e) {
    var title = $('#modal_title').val().trim();
    var schoolBuilding = $('#modal_school_building_id').val();
    var color = $('input[name="color"]:checked').val();
    var dates = [];
    
    // Collect all dates
    $('.multiple-dates-container .date-input').each(function() {
        var dateValue = $(this).val();
        if (dateValue && dates.indexOf(dateValue) === -1) {
            dates.push(dateValue);
        }
    });
    
    if (!title) {
        alert('予定タイトルを入力してください。');
        e.preventDefault();
        return false;
    }
    
    if (!color) {
        alert('色を選択してください。');
        e.preventDefault();
        return false;
    }
    
    if (!schoolBuilding) {
        alert('校舎を選択してください。');
        e.preventDefault();
        return false;
    }
    
    if (dates.length === 0) {
        alert('予定日を選択してください。');
        e.preventDefault();
        return false;
    }
    
    // Validate time range
    var startTime = $('#modal_start_time').val();
    var endTime = $('#modal_end_time').val();
    
    if (startTime && endTime && startTime >= endTime) {
        alert('終了時間は開始時間より後に設定してください。');
        e.preventDefault();
        return false;
    }
    
    // Confirm multiple dates
    if (dates.length > 1) {
        var confirmMessage = dates.length + '日分の予定を一度に登録します。\n\n登録予定日: ' + dates.join(', ') + '\n\nよろしいですか？';
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }
    }
    
    return true;
});

// Update edit form validation for multiple dates
$('#eventEditForm').off('submit').on('submit', function(e) {
    var title = $('#edit_title').val().trim();
    var schoolBuilding = $('#edit_school_building_id').val();
    var color = $('#eventEditModal input[name="color"]:checked').val();
    var dates = [];
    
    // Collect all dates
    $('.multiple-dates-container-edit .date-input').each(function() {
        var dateValue = $(this).val();
        if (dateValue && dates.indexOf(dateValue) === -1) {
            dates.push(dateValue);
        }
    });
    
    if (!title) {
        alert('予定タイトルを入力してください。');
        e.preventDefault();
        return false;
    }
    
    if (!color) {
        alert('色を選択してください。');
        e.preventDefault();
        return false;
    }
    
    if (!schoolBuilding) {
        alert('校舎を選択してください。');
        e.preventDefault();
        return false;
    }
    
    if (dates.length === 0) {
        alert('予定日を選択してください。');
        e.preventDefault();
        return false;
    }
    
    // Validate time range
    var startTime = $('#edit_start_time').val();
    var endTime = $('#edit_end_time').val();
    
    if (startTime && endTime && startTime >= endTime) {
        alert('終了時間は開始時間より後に設定してください。');
        e.preventDefault();
        return false;
    }
    
    // Confirm multiple dates
    if (dates.length > 1) {
        var confirmMessage = dates.length + '日分の予定を一度に更新します。\n\n更新予定日: ' + dates.join(', ') + '\n\nよろしいですか？';
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }
    }
    
    return true;
});
</script>
@endsection