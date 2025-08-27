@extends('layouts.app')

@section('title', '予定編集')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fas fa-calendar-edit"></i> 予定編集</span>
                        <div>
                            <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> 戻る
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
                                <i class="fas fa-trash"></i> 削除
                            </button>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    @if($schedule->status !== 'pending')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            この予定は{{ $schedule->status_display }}です。編集すると再度承認が必要になります。
                        </div>
                    @endif

                    <form method="POST" action="{{ route('schedules.update', $schedule) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label for="title" class="col-md-2 col-form-label text-md-right">
                                <span class="text-danger">*</span> 予定タイトル
                            </label>
                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title', $schedule->title) }}" required autocomplete="title" autofocus maxlength="255">
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="school_building_id" class="col-md-2 col-form-label text-md-right">
                                <span class="text-danger">*</span> 校舎
                            </label>
                            <div class="col-md-4">
                                <select id="school_building_id" class="form-control @error('school_building_id') is-invalid @enderror" 
                                        name="school_building_id" required>
                                    <option value="">校舎を選択してください</option>
                                    @foreach($schoolBuildings as $building)
                                        <option value="{{ $building->id }}" 
                                                {{ (old('school_building_id', $schedule->school_building_id) == $building->id) ? 'selected' : '' }}>
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_building_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="schedule_date" class="col-md-2 col-form-label text-md-right">
                                <span class="text-danger">*</span> 予定日
                            </label>
                            <div class="col-md-3">
                                <input id="schedule_date" type="date" class="form-control @error('schedule_date') is-invalid @enderror" 
                                       name="schedule_date" value="{{ old('schedule_date', $schedule->schedule_date->format('Y-m-d')) }}" required>
                                @error('schedule_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="start_time" class="col-md-2 col-form-label text-md-right">開始時間</label>
                            <div class="col-md-2">
                                <input id="start_time" type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       name="start_time" value="{{ old('start_time', $schedule->start_time ? substr($schedule->start_time, 0, 5) : '') }}">
                                @error('start_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="end_time" class="col-md-1 col-form-label text-center">〜</label>
                            <div class="col-md-2">
                                <input id="end_time" type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       name="end_time" value="{{ old('end_time', $schedule->end_time ? substr($schedule->end_time, 0, 5) : '') }}">
                                @error('end_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="content" class="col-md-2 col-form-label text-md-right">内容</label>
                            <div class="col-md-6">
                                <textarea id="content" class="form-control @error('content') is-invalid @enderror" 
                                          name="content" rows="5" placeholder="予定の詳細内容を入力してください">{{ old('content', $schedule->content) }}</textarea>
                                @error('content')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        @if($schedule->status === 'approved' || $schedule->status === 'rejected')
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">承認情報</label>
                                <div class="col-md-8">
                                    <div class="panel panel-info" style="margin-bottom: 0;">
                                        <div class="panel-body">
                                            <strong>承認状況:</strong> {{ $schedule->status_display }}<br>
                                            @if($schedule->approver)
                                                <strong>承認者:</strong> {{ $schedule->approver->name }}<br>
                                            @endif
                                            @if($schedule->approved_at)
                                                <strong>承認日時:</strong> {{ $schedule->approved_at->format('Y/m/d H:i') }}<br>
                                            @endif
                                            @if($schedule->approval_note)
                                                <strong>承認メモ:</strong> {{ $schedule->approval_note }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-2">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    編集された予定は再度承認が必要です。承認後にカレンダーに表示されます。
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-2 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> 更新
                                </button>
                                <a href="{{ route('schedules.index') }}" class="btn btn-secondary" style="margin-left: 10px;">
                                    キャンセル
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">予定削除確認</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>この予定を削除してもよろしいですか？</p>
                <div class="alert alert-info">
                    <strong>{{ $schedule->title }}</strong><br>
                    {{ $schedule->schedule_date->format('Y年m月d日') }}{{ $schedule->time_display ? ' ' . $schedule->time_display : '' }}<br>
                    {{ $schedule->schoolBuilding->name ?? '--' }}
                </div>
                <p class="text-danger"><strong>※ この操作は取り消すことができません。</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <form method="POST" action="{{ route('schedules.destroy', $schedule) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">削除</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validate time range
    $('#start_time, #end_time').change(function() {
        var startTime = $('#start_time').val();
        var endTime = $('#end_time').val();
        
        if (startTime && endTime && startTime >= endTime) {
            alert('終了時間は開始時間より後に設定してください。');
            $('#end_time').val('');
        }
    });
});
</script>
@endsection