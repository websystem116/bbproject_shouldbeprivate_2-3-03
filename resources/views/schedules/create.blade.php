@extends('layouts.app')

@section('title', '予定登録')

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
                    <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fas fa-calendar-plus"></i> 予定登録</span>
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> 戻る
                        </a>
                    </div>
                </div>

                <div class="panel-body">
                    <form method="POST" action="{{ route('schedules.store') }}" id="scheduleCreateForm">
                        @csrf

                        <div class="form-group row">
                            <label for="title" class="col-md-2 col-form-label text-md-right">
                                <span class="text-danger">*</span> 予定タイトル
                            </label>
                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title') }}" required autocomplete="title" autofocus maxlength="255">
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="color" class="col-md-2 col-form-label text-md-right">
                                <span class="text-danger">*</span> 色
                            </label>
                            <div class="col-md-8">
                                <div class="color-picker-container">
                                    @if(isset($availableColors))
                                        @foreach($availableColors as $colorKey => $colorInfo)
                                            <label class="color-option" for="color_{{ $colorKey }}">
                                                <input type="radio" id="color_{{ $colorKey }}" name="color" value="{{ $colorKey }}" 
                                                       class="color-radio" {{ ($colorKey === 'yellow' || old('color') === $colorKey) ? 'checked' : '' }} required>
                                                <span class="color-sample" style="background-color: {{ $colorInfo['bg'] }}; border-color: {{ $colorInfo['border'] }}; color: {{ $colorInfo['text'] }}">
                                                    {{ $colorInfo['name'] }}
                                                </span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                                @error('color')
                                    <span class="invalid-feedback" role="alert" style="display: block;">
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
                                <select id="school_building_id" class="form-control select2-school-building @error('school_building_id') is-invalid @enderror" 
                                        name="school_building_id" required>
                                    <option value="">校舎を選択してください</option>
                                    @foreach($schoolBuildings as $building)
                                        <option value="{{ $building->id }}" 
                                                {{ (old('school_building_id', $selectedSchoolBuildingId) == $building->id) ? 'selected' : '' }}>
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
                            <label for="schedule_dates" class="col-md-2 col-form-label text-md-right">
                                <span class="text-danger">*</span> 予定日
                            </label>
                            <div class="col-md-6">
                                <div class="multiple-dates-container">
                                    <div class="single-date-row">
                                        <input id="schedule_date_1" type="date" class="form-control date-input @error('schedule_dates') is-invalid @enderror" 
                                               name="schedule_dates[]" value="{{ old('schedule_dates.0', $selectedDate) }}" required>
                                        <button type="button" class="btn btn-success btn-sm add-date-btn" title="日付を追加">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">複数の日付に同じ予定を登録する場合は「+」ボタンで日付を追加してください</small>
                                @error('schedule_dates')
                                    <span class="invalid-feedback" role="alert" style="display: block;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @error('schedule_dates.*')
                                    <span class="invalid-feedback" role="alert" style="display: block;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="start_time" class="col-md-2 col-form-label text-md-right">開始時間</label>
                            <div class="col-md-2">
                                <input id="start_time" type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       name="start_time" value="{{ old('start_time') }}">
                                @error('start_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <label for="end_time" class="col-md-1 col-form-label text-center">〜</label>
                            <div class="col-md-2">
                                <input id="end_time" type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       name="end_time" value="{{ old('end_time') }}">
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
                                          name="content" rows="5" placeholder="予定の詳細内容を入力してください">{{ old('content') }}</textarea>
                                @error('content')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-2">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    登録された予定は承認者による承認が必要です。承認後にカレンダーに表示されます。
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-2 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> 登録
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

<style>
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

/* Multiple Dates Styling */
.multiple-dates-container {
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

.add-date-btn {
    margin-left: 8px;
    padding: 6px 8px;
    border-radius: 4px;
    border: none;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.add-date-btn:hover {
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

/* Select2 styling */
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .color-picker-container {
        justify-content: center;
    }
    
    .color-sample {
        min-width: 50px;
        padding: 6px 10px;
        font-size: 11px;
    }
    
    .single-date-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .date-input {
        margin-right: 0;
        margin-bottom: 8px;
    }
    
    .add-date-btn, .remove-date-btn {
        margin-left: 0;
        align-self: center;
        width: 100px;
    }
}
</style>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2-school-building').select2({
        theme: 'bootstrap',
        language: 'ja',
        placeholder: '校舎を選択してください',
        allowClear: false,
        width: '100%'
    });
    
    // Set minimum date to today
    var today = new Date().toISOString().split('T')[0];
    $('.date-input').attr('min', today);
    
    // Multiple dates functionality
    $(document).on('click', '.add-date-btn', function() {
        addDateRow();
    });
    
    $(document).on('click', '.remove-date-btn', function() {
        $(this).closest('.single-date-row').remove();
        updateDatesSummary();
    });
    
    $(document).on('change', '.date-input', function() {
        updateDatesSummary();
    });
    
    // Validate time range
    $('#start_time, #end_time').change(function() {
        var startTime = $('#start_time').val();
        var endTime = $('#end_time').val();
        
        if (startTime && endTime && startTime >= endTime) {
            alert('終了時間は開始時間より後に設定してください。');
            $('#end_time').val('');
        }
    });
    
    // Form validation
    $('#scheduleCreateForm').on('submit', function(e) {
        var title = $('#title').val().trim();
        var color = $('input[name="color"]:checked').val();
        var schoolBuilding = $('#school_building_id').val();
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
        var startTime = $('#start_time').val();
        var endTime = $('#end_time').val();
        
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
});

// Add new date row
function addDateRow() {
    var today = new Date().toISOString().split('T')[0];
    var dateRowHtml = '<div class="single-date-row">' +
        '<input type="date" class="form-control date-input" name="schedule_dates[]" required min="' + today + '">' +
        '<button type="button" class="btn btn-danger btn-sm remove-date-btn" title="日付を削除">' +
        '<i class="fas fa-minus"></i>' +
        '</button>' +
        '</div>';
    
    $('.multiple-dates-container').append(dateRowHtml);
    updateDatesSummary();
}

// Update dates summary
function updateDatesSummary() {
    var container = $('.multiple-dates-container');
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
</script>
@endsection