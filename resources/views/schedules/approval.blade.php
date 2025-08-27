@extends('layouts.app')

@section('title', 'スケジュール承認')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fas fa-check-circle"></i> スケジュール承認</span>
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-calendar"></i> カレンダーに戻る
                        </a>
                    </div>
                </div>

                <div class="panel-body">
                    <!-- Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('schedules.approval') }}" id="filterForm">
                                <div class="form-group">
                                    <label for="school_building_id">校舎選択</label>
                                    <select name="school_building_id" id="school_building_id" class="form-control" onchange="document.getElementById('filterForm').submit();">
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

                    @if($pendingSchedules->count() > 0)
                        <!-- Bulk Actions -->
                        <div class="row mb-3">
                            <div class="col-md-12">
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
                                            <tr>
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
                                                <td>{{ $schedule->creator->name ?? '--' }}</td>
                                                <td>{{ $schedule->created_at->format('Y/m/d H:i') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $schedule->id }}">
                                                        <i class="fas fa-eye"></i> 詳細
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $schedule->id }}">
                                                        <i class="fas fa-check"></i> 承認
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $schedule->id }}">
                                                        <i class="fas fa-times"></i> 却下
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Detail Modal -->
                                            <div class="modal fade" id="detailModal{{ $schedule->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">予定詳細</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
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
                                                                <tr>
                                                                    <th>内容:</th>
                                                                    <td>{!! nl2br(e($schedule->content)) !!}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>作成者:</th>
                                                                    <td>{{ $schedule->creator->name ?? '--' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>登録日時:</th>
                                                                    <td>{{ $schedule->created_at->format('Y/m/d H:i') }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Approve Modal -->
                                            <div class="modal fade" id="approveModal{{ $schedule->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">予定承認</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="POST" action="{{ route('schedules.updateApproval', $schedule) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="approved">
                                                            
                                                            <div class="modal-body">
                                                                <p>以下の予定を承認してもよろしいですか？</p>
                                                                <div class="alert alert-info">
                                                                    <strong>{{ $schedule->title }}</strong><br>
                                                                    {{ $schedule->schedule_date->format('Y年m月d日') }} {{ $schedule->time_display ?? '' }}<br>
                                                                    {{ $schedule->schoolBuilding->name ?? '--' }}
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label for="approval_note_approve{{ $schedule->id }}">承認メモ（任意）</label>
                                                                    <textarea name="approval_note" id="approval_note_approve{{ $schedule->id }}" 
                                                                              class="form-control" rows="3" 
                                                                              placeholder="承認に関するメモがあれば入力してください"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                                                                <button type="submit" class="btn btn-success">承認</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal{{ $schedule->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">予定却下</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="POST" action="{{ route('schedules.updateApproval', $schedule) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="rejected">
                                                            
                                                            <div class="modal-body">
                                                                <p>以下の予定を却下してもよろしいですか？</p>
                                                                <div class="alert alert-warning">
                                                                    <strong>{{ $schedule->title }}</strong><br>
                                                                    {{ $schedule->schedule_date->format('Y年m月d日') }} {{ $schedule->time_display ?? '' }}<br>
                                                                    {{ $schedule->schoolBuilding->name ?? '--' }}
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label for="approval_note_reject{{ $schedule->id }}">却下理由（推奨）</label>
                                                                    <textarea name="approval_note" id="approval_note_reject{{ $schedule->id }}" 
                                                                              class="form-control" rows="3" 
                                                                              placeholder="却下する理由を入力してください"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                                                                <button type="submit" class="btn btn-danger">却下</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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

<style>
/* Approval section styles */
.approval-checkbox {
    width: 16px;
    height: 16px;
}

.mb-3 {
    margin-bottom: 1rem;
}

.thead-light {
    background-color: #f8f9fa;
}

.table-responsive {
    overflow-x: auto;
}

.btn-sm {
    margin-right: 5px;
}

.modal-dialog {
    max-width: 600px;
}

.alert {
    margin-bottom: 1rem;
}

.table-borderless td,
.table-borderless th {
    border: none;
}
</style>

<script>
$(document).ready(function() {
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
});
</script>
@endsection