@extends('layouts.app')

@section('title', '承認者編集')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fas fa-user-edit"></i> 承認者編集</span>
                        <a href="{{ route('schedule_approvers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> 戻る
                        </a>
                    </div>
                </div>

                <div class="panel-body">
                    <form method="POST" action="{{ route('schedule_approvers.update', $scheduleApprover) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label for="name" class="col-md-2 col-form-label text-md-right">
                                <span class="text-danger">*</span> 名前
                            </label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $scheduleApprover->name) }}" required autocomplete="name" autofocus maxlength="100">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-2 col-form-label text-md-right">
                                <span class="text-danger">*</span> メールアドレス
                            </label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email', $scheduleApprover->email) }}" required autocomplete="email" maxlength="255">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-md-2 col-form-label text-md-right">
                                <span class="text-danger">*</span> 役割
                            </label>
                            <div class="col-md-4">
                                <select id="role" class="form-control @error('role') is-invalid @enderror" 
                                        name="role" required>
                                    <option value="">役割を選択してください</option>
                                    <option value="admin" {{ old('role', $scheduleApprover->role) === 'admin' ? 'selected' : '' }}>管理者</option>
                                    <option value="office" {{ old('role', $scheduleApprover->role) === 'office' ? 'selected' : '' }}>事務</option>
                                    <option value="manager" {{ old('role', $scheduleApprover->role) === 'manager' ? 'selected' : '' }}>マネージャー</option>
                                </select>
                                @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="school_building_id" class="col-md-2 col-form-label text-md-right">対象校舎</label>
                            <div class="col-md-4">
                                <select id="school_building_id" class="form-control @error('school_building_id') is-invalid @enderror" 
                                        name="school_building_id">
                                    <option value="">全校舎（制限なし）</option>
                                    @foreach($schoolBuildings as $building)
                                        <option value="{{ $building->id }}" {{ old('school_building_id', $scheduleApprover->school_building_id) == $building->id ? 'selected' : '' }}>
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_building_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    特定の校舎のみを担当する場合は選択してください。未選択の場合は全校舎が対象になります。
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="is_active" class="col-md-2 col-form-label text-md-right">状態</label>
                            <div class="col-md-6">
                                <div class="checkbox" style="margin-top: 10px;">
                                    <label>
                                        <input type="checkbox" id="is_active" name="is_active" 
                                               {{ old('is_active', $scheduleApprover->is_active) ? 'checked' : '' }}>
                                        有効
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    チェックを外すと承認権限が無効になります。
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="notes" class="col-md-2 col-form-label text-md-right">備考</label>
                            <div class="col-md-6">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                          name="notes" rows="3" placeholder="備考事項があれば入力してください" maxlength="1000">{{ old('notes', $scheduleApprover->notes) }}</textarea>
                                @error('notes')
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
                                    承認者情報を変更すると、スケジュール承認権限に影響する場合があります。
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-2 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> 更新
                                </button>
                                <a href="{{ route('schedule_approvers.index') }}" class="btn btn-secondary" style="margin-left: 10px;">
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
.checkbox {
    position: relative;
    display: block;
    margin-bottom: 10px;
}

.checkbox label {
    min-height: 20px;
    padding-left: 20px;
    margin-bottom: 0;
    font-weight: 400;
    cursor: pointer;
}

.checkbox input[type="checkbox"] {
    position: absolute;
    margin-top: 4px;
    margin-left: -20px;
}
</style>
@endsection