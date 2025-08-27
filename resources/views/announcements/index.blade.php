@extends('layouts.app')

@push('css')
    <link href="{{ asset('css/announcements.css') }}" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .announcement-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .announcement-title {
            color: #007bff;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }
        
        .new-button {
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            border: none;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .new-button:hover {
            background-color: #c82333;
            color: white;
            text-decoration: none;
        }
        
        .announcement-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .announcement-item {
            border-bottom: 1px solid #e9ecef;
            padding: 15px 0;
            display: flex;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .announcement-item:last-child {
            border-bottom: none;
        }
        
        .announcement-date {
            color: #6c757d;
            font-size: 14px;
            min-width: 100px;
            flex-shrink: 0;
        }
        
        .announcement-link {
            color: #007bff;
            text-decoration: none;
            flex-grow: 1;
            word-wrap: break-word;
            word-break: break-all;
            line-height: 1.4;
            min-width: 0;
        }
        
        .announcement-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        
        .announcement-status {
            flex-shrink: 0;
        }
        
        .announcement-actions {
            flex-shrink: 0;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
            white-space: nowrap;
        }
        
        /* Modal styles */
        .approval-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        
        .approval-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 500px;
            max-width: 90%;
            border-radius: 5px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .container-fluid, .container {
                padding: 15px;
            }
            
            .announcement-header {
                padding: 15px 0;
                margin-bottom: 20px;
            }
            
            .announcement-title {
                font-size: 24px;
            }
            
            .announcement-item {
                padding: 12px 0;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .announcement-date {
                min-width: auto;
                font-size: 12px;
                order: 3;
            }
            
            .announcement-link {
                order: 1;
                font-size: 16px;
                line-height: 1.4;
                word-break: break-word;
            }
            
            .announcement-status {
                order: 2;
            }
            
            .announcement-actions {
                order: 4;
                width: 100%;
                justify-content: flex-start;
            }
            
            .btn-sm {
                margin: 2px 5px 2px 0;
                padding: 6px 12px;
                font-size: 12px;
            }
            
            .new-button {
                width: 100%;
                text-align: center;
                padding: 12px;
                margin-bottom: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .container-fluid, .container {
                padding: 10px;
            }
            
            .announcement-title {
                font-size: 20px;
            }
            
            .announcement-item {
                padding: 10px 0;
            }
            
            .announcement-link {
                font-size: 15px;
            }
            
            .btn-sm {
                text-align: center;
            }
            
            .announcement-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
        }
        
        /* Empty state responsiveness */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .empty-state {
                padding: 30px 15px;
            }
            
            .empty-state h4 {
                font-size: 18px;
            }
        }
        
        /* Pagination responsive */
        @media (max-width: 768px) {
            .pagination {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .pagination > li > a,
            .pagination > li > span {
                padding: 6px 8px;
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="announcement-header">
        <div class="container">
            <h1 class="announcement-title">お知らせ一覧</h1>
        </div>
    </div>
    
    <!-- Content Section -->
    <div class="container">
        @if(in_array(auth()->user()->roles, [1, 2]))
            <a href="{{ route('announcements.create') }}" class="new-button">
                新規追加
            </a>
        @endif
        
        @if($announcements->count() > 0)
            <ul class="announcement-list">
                @foreach($announcements as $announcement)
                    <li class="announcement-item">
                        <div class="announcement-date">
                            {{ $announcement->published_at ? $announcement->published_at->format('Y年m月d日') : $announcement->created_at->format('Y年m月d日') }}
                        </div>
                        
                        <a href="{{ route('announcements.show', $announcement->id) }}" class="announcement-link">
                            {{ $announcement->title }}
                        </a>
                        
                        @if(in_array(auth()->user()->roles, [1, 2]))
                            <div class="announcement-actions">
                                <!-- ステータスバッジ -->
                                <div class="announcement-status">
                                    @if($announcement->isPublished())
                                        <span class="label label-primary">公開</span>
                                    @else
                                        <span class="label label-default">未公開</span>
                                    @endif
                                </div>
                                
                                <!-- ステータスに応じたアクションボタン -->
                                @if($announcement->isDraft())
                                    <form method="POST" action="{{ route('announcements.request_approval', $announcement->id) }}" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('承認申請してもよろしいですか？')">承認申請</button>
                                    </form>
                                    <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-warning btn-sm">編集</a>
                                @elseif($announcement->isPending())
                                    <button class="btn btn-success btn-sm" onclick="showApprovalModal({{ $announcement->id }}, 'approve')">承認</button>
                                    <button class="btn btn-danger btn-sm" onclick="showApprovalModal({{ $announcement->id }}, 'reject')">却下</button>
                                @elseif($announcement->isApproved())
                                    <form method="POST" action="{{ route('announcements.publish', $announcement->id) }}" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('公開してもよろしいですか？')">公開</button>
                                    </form>
                                    <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-warning btn-sm">編集</a>
                                @elseif($announcement->isPublished())
                                    <form method="POST" action="{{ route('announcements.unpublish', $announcement->id) }}" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('未公開に変更してもよろしいですか？')">未公開</button>
                                    </form>
                                    <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-warning btn-sm">編集</a>
                                @endif
                                
                                <form method="POST" action="{{ route('announcements.destroy', $announcement->id) }}" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('削除してもよろしいですか？')">削除</button>
                                </form>
                            </div>
                        @else
                            <!-- 一般ユーザーの場合はステータスバッジのみ表示 -->
                            <div class="announcement-status">
                                @if($announcement->isPublished())
                                    <span class="label label-primary">公開</span>
                                @else
                                    <span class="label label-default">未公開</span>
                                @endif
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            @if($announcements->hasPages())
                <div style="margin-top: 30px; text-align: center;">
                    {{ $announcements->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <h4>お知らせはまだ投稿されていません。</h4>
                @if(in_array(auth()->user()->roles, [1, 2]))
                    <p style="margin-top: 20px;">
                        <a href="{{ route('announcements.create') }}" class="new-button">
                            最初のお知らせを作成する
                        </a>
                    </p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- 承認/却下モーダル -->
<div id="approvalModal" class="approval-modal">
    <div class="approval-modal-content">
        <span class="close" onclick="closeApprovalModal()">&times;</span>
        <h3 id="modalTitle">承認</h3>
        <form id="approvalForm" method="POST">
            @csrf
            <div class="form-group">
                <label for="approval_comment">コメント（任意）:</label>
                <textarea class="form-control" name="approval_comment" id="approval_comment" rows="3" placeholder="承認・却下の理由やコメントを入力してください"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="modalSubmitBtn">承認</button>
                <button type="button" class="btn btn-default" onclick="closeApprovalModal()">キャンセル</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showApprovalModal(announcementId, action) {
    const modal = document.getElementById('approvalModal');
    const form = document.getElementById('approvalForm');
    const title = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('modalSubmitBtn');
    const commentField = document.getElementById('approval_comment');
    
    if (action === 'approve') {
        title.textContent = '承認';
        submitBtn.textContent = '承認';
        submitBtn.className = 'btn btn-success';
        form.action = '/shinzemi/announcements/' + announcementId + '/approve';
        commentField.placeholder = '承認理由やコメントを入力してください（任意）';
    } else if (action === 'reject') {
        title.textContent = '却下';
        submitBtn.textContent = '却下';
        submitBtn.className = 'btn btn-danger';
        form.action = '/shinzemi/announcements/' + announcementId + '/reject';
        commentField.placeholder = '却下理由を入力してください（任意）';
    }
    
    commentField.value = '';
    modal.style.display = 'block';
}

function closeApprovalModal() {
    document.getElementById('approvalModal').style.display = 'none';
}

// モーダル外をクリックして閉じる
window.onclick = function(event) {
    const modal = document.getElementById('approvalModal');
    if (event.target == modal) {
        closeApprovalModal();
    }
}
</script>
@endpush
@endsection