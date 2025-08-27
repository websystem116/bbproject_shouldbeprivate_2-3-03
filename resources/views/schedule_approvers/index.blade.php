@extends('layouts.app')

@section('title', '承認者管理')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fas fa-users"></i> スケジュール承認者管理</span>
                        <div>
                            <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-calendar"></i> カレンダーに戻る
                            </a>
                            <a href="{{ route('schedule_approvers.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> 新規登録
                            </a>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    @if($approvers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>名前</th>
                                        <th>メールアドレス</th>
                                        <th>役割</th>
                                        <th>対象校舎</th>
                                        <th>状態</th>
                                        <th>登録日</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvers as $approver)
                                        <tr class="{{ !$approver->is_active ? 'warning' : '' }}">
                                            <td>{{ $approver->name }}</td>
                                            <td>{{ $approver->email }}</td>
                                            <td>
                                                @if($approver->role === 'admin')
                                                    <span class="label label-danger">管理者</span>
                                                @elseif($approver->role === 'office')
                                                    <span class="label label-warning">事務</span>
                                                @else
                                                    <span class="label label-info">{{ $approver->role }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($approver->school_building_id)
                                                    {{ $approver->schoolBuilding->name ?? '--' }}
                                                @else
                                                    <span class="text-muted">全校舎</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($approver->is_active)
                                                    <span class="label label-success">有効</span>
                                                @else
                                                    <span class="label label-default">無効</span>
                                                @endif
                                            </td>
                                            <td>{{ $approver->created_at->format('Y/m/d') }}</td>
                                            <td>
                                                <a href="{{ route('schedule_approvers.edit', $approver) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> 編集
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $approver->id }}">
                                                    <i class="fas fa-trash"></i> 削除
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade" id="deleteModal{{ $approver->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">承認者削除確認</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>以下の承認者を削除してもよろしいですか？</p>
                                                        <div class="alert alert-warning">
                                                            <strong>{{ $approver->name }}</strong><br>
                                                            {{ $approver->email }}<br>
                                                            役割: {{ $approver->role }}
                                                        </div>
                                                        <p class="text-danger"><strong>※ この操作は取り消すことができません。</strong></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                                                        <form method="POST" action="{{ route('schedule_approvers.destroy', $approver) }}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">削除</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            承認者が登録されていません。
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.label-danger {
    background-color: #d9534f;
}

.label-warning {
    background-color: #f0ad4e;
}

.label-info {
    background-color: #5bc0de;
}

.label-success {
    background-color: #5cb85c;
}

.label-default {
    background-color: #777;
}

.thead-light {
    background-color: #f8f9fa;
}

.table-responsive {
    overflow-x: auto;
}

.warning {
    background-color: #fcf8e3 !important;
}
</style>
@endsection