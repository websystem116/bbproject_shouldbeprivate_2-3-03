@extends('layouts.app')

@push('css')
    <link href="{{ asset('css/announcements.css') }}" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .announcement-detail-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .announcement-detail-title {
            max-width: 90%;
            color: black;
            font-size: 24px;
            font-weight: bold underline;
            margin: 0;
            border-bottom: 4px double black; /* double line */
            padding-bottom: 4px;
        }
        
        .edit-button {
            /* Remove custom styling - will use Bootstrap classes instead */
        }
        
        .delete-button {
            /* Remove custom styling - will use Bootstrap classes instead */
        }
        
        .announcement-meta-box {
            border: 2px solid #dc3545;
            padding: 15px;
            margin-bottom: 30px;
            background-color: #fff;
        }
        
        .meta-row {
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .meta-row:last-child {
            margin-bottom: 0;
        }
        
        .meta-label {
            font-weight: bold;
            color: #333;
        }
        
        .announcement-content-area {
            line-height: 1.8;
            font-size: 14px;
            color: #333;
            margin-bottom: 30px;
        }
        
        .announcement-content-area img {
            max-width: 100%;
            height: auto;
        }
        
        .back-button {
            background-color: #6c757d;
            color: white;
            padding: 4px 10px;
            border: none;
            text-decoration: none;
            display: inline-block;
        }
        
        .back-button:hover {
            background-color: #545b62;
            color: white;
            text-decoration: none;
        }
        
        .delete-button {
            /* Remove custom styling - will use Bootstrap classes instead */
        }
        
        .action-buttons {
            margin-bottom: 20px;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .container-fluid, .container {
                padding: 15px;
            }
            
            .announcement-detail-header {
                padding: 15px 0;
                margin-bottom: 20px;
            }
            
            .announcement-detail-title {
                font-size: 20px;
                margin-bottom: 10px;
            }
            
            .action-buttons {
                margin-bottom: 0;
                width: 100%;
                display: flex;
                gap: 5px;
            }
            
            .btn-sm {
                margin: 2px 5px 2px 0;
                padding: 6px 12px;
                font-size: 12px;
            }
            
            .announcement-meta-box {
                padding: 12px;
                margin-bottom: 20px;
            }
            
            .meta-row {
                font-size: 13px;
                margin-bottom: 6px;
            }
            
            .announcement-content-area {
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            
            .back-button {
                width: 100%;
                text-align: center;
                /* padding: 12px; */
            }
        }
        
        @media (max-width: 480px) {
            .announcement-detail-title {
                font-size: 18px;
            }
            
            .edit-button,
            .delete-button {
                /* width: calc(50% - 5px); */
                text-align: center;
                margin: 2px 0;
            }
            
            .action-buttons {
                display: flex;
                gap: 10px;
            }
            
            .announcement-meta-box {
                padding: 10px;
            }
            
            .meta-row {
                font-size: 12px;
            }
            
            .announcement-content-area {
                font-size: 13px;
            }
        }
        
        /* Remove any navigation arrows or controls */
        .swiper-button-next,
        .swiper-button-prev,
        .carousel-control,
        .carousel-control-next,
        .carousel-control-prev,
        .nav-arrows,
        .navigation-arrows,
        .slick-arrow,
        .prev-next-nav {
            display: none !important;
        }
        
        /* Ensure no JavaScript navigation elements appear */
        [class*="arrow"],
        [class*="nav-"],
        [class*="swiper"],
        [class*="carousel"] {
            display: none !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="announcement-detail-header">
        <div class="container">
            <div class="header-content">
                <h1 class="announcement-detail-title">{{ $announcement->title }}</h1>
                <!-- Navigation -->
                <div>
                    <a href="{{ route('announcements.index') }}" class="back-button">
                        戻る
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content Section -->
    <div class="container">
        <!-- Meta Information Box -->
        <div class="announcement-meta-box">
            <div class="meta-row">
                <span class="meta-label">作成日：</span>{{ $announcement->created_at->format('Y年m月d日') }}
            </div>
            <div class="meta-row">
                <span class="meta-label">作成者：</span>{{ $announcement->creator->last_name . $announcement->creator->first_name ?? '不明' }}
            </div>
            @if($announcement->approved_at)
                <div class="meta-row">
                    <span class="meta-label">承認日：</span>{{ $announcement->approved_at->format('Y年m月d日') }}
                </div>
                <div class="meta-row">
                    <span class="meta-label">承認者：</span>{{ $announcement->creator->last_name . $announcement->creator->first_name ?? '不明' }}
                </div>
                @if($announcement->approval_comment)
                    <div class="meta-row">
                        <span class="meta-label">承認コメント：</span>{{ $announcement->approval_comment }}
                    </div>
                @endif
            @endif
            @if($announcement->published_at)
                <div class="meta-row">
                    <span class="meta-label">公開日：</span>{{ $announcement->published_at->format('Y年m月d日') }}
                </div>
            @endif
            <div class="meta-row">
                <span class="meta-label">配信先：</span>{{ $announcement->formatted_distribution }}
            </div>
            <div class="meta-row">
                <span class="meta-label">ステータス：</span>
                <span class="label {{ $announcement->status_badge_class }}">
                    {{ $announcement->status_label }}
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="announcement-content-area">
{!! $announcement->content !!}
        </div>
    </div>
</div>
@endsection