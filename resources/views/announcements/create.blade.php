@extends('layouts.app')

@push('css')
    <link href="{{ asset('css/announcements.css') }}" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Responsive Styles */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }
            
            .panel-title {
                font-size: 18px;
            }
            
            .form-group label {
                font-size: 14px;
            }
            
            .form-control {
                font-size: 16px; /* Prevents zoom on iOS */
            }
            
            .distribution-targets .col-md-3 {
                width: 50%;
                float: left;
                padding: 2px 5px; /* Reduced padding */
            }
            
            .alert {
                padding: 10px;
                font-size: 11px;
            }
            
            .btn {
                padding: 8px 12px;
                font-size: 14px;
                margin: 5px 2px;
            }
            
            .panel-body {
                padding: 10px;
            }
            
            .row {
                margin: 0 -5px;
            }
            
            .checkbox label {
                font-size: 13px;
                margin-bottom: 3px; /* Reduced margin */
                line-height: 1.2; /* Tighter line height */
            }
            
            .label {
                font-size: 10px;
            }
        }
        
        @media (max-width: 480px) {
            .distribution-targets .col-md-3 {
                width: 100%;
            }
            
            .btn {
                width: 100%;
                margin: 5px 0;
            }
            
            .alert {
                font-size: 10px;
                padding: 8px;
            }
        }
        
        /* Mobile Preview Modal */
        .mobile-preview-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
        }
        
        .mobile-preview-content {
            position: relative;
            margin: 2% auto;
            width: 375px;
            max-width: 90%;
            height: 667px;
            max-height: 90%;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #333;
        }
        
        .mobile-preview-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        .mobile-preview-body {
            padding: 15px;
            height: calc(100% - 60px);
            overflow-y: auto;
            font-size: 14px;
        }
        
        .mobile-preview-body img {
            max-width: 100%;
            height: auto;
        }
        
        .mobile-preview-close {
            position: absolute;
            top: 3px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: white;
            background: none;
            border: none;
        }
        
        .mobile-preview-button {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            cursor: pointer;
            margin-left: 0;
            flex-shrink: 0;
            white-space: nowrap;
        }
        
        /* Hide mobile preview button on actual mobile devices */
        @media (max-width: 768px) {
            .mobile-preview-button {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">お知らせ新規作成</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('announcements.store') }}">
                        @csrf                       
                       
                        <!-- 配信先 -->
                        <div class="form-group">
                            <label class="control-label">配信先 <span class="text-danger">*</span></label>
                            <div class="well well-sm distribution-targets">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" 
                                               name="distribution_targets[]" 
                                               value="all" 
                                               {{ in_array('all', old('distribution_targets', [])) ? 'checked' : '' }}
                                               id="select_all">
                                        <strong>全校舎に配信</strong>
                                    </label>
                                </div>
                                <hr>
                                <div class="row">
                                    @foreach($schoolBuildings as $schoolBuilding)
                                        <div class="col-md-3">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" 
                                                           name="distribution_targets[]" 
                                                           value="{{ $schoolBuilding->id }}" 
                                                           class="school-checkbox"
                                                           {{ in_array($schoolBuilding->id, old('distribution_targets', [])) ? 'checked' : '' }}>
                                                    {{ $schoolBuilding->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('distribution_targets')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 投稿内容 -->
                        <div class="form-group">
                            <label for="content" class="control-label">投稿内容 <span class="text-danger">*</span></label>
                            <div class="alert alert-info" style="font-size: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <strong>使用できる機能:</strong> 太字、斜体、文字色変更、ハイパーリンク、画像挿入、リスト作成など<br>
                                    <strong>配信先:</strong> 全校舎または特定の校舎を選択して配信できます<br>
                                    <strong>画像アップロード:</strong> 画像ボタンをクリック → "アップロード"タブ → "ファイルを選択"で画像を選択
                                </div>
                                <button type="button" class="mobile-preview-button" onclick="showMobilePreview()">📱 スマホプレビュー</button>
                            </div>
                             <!-- タイトル -->
                            <div class="form-group">
                                <label for="title" class="control-label">タイトル <span class="text-danger">*</span></label>
                                <input type="text" 
                                    class="form-control @error('title') has-error @enderror" 
                                    id="title" 
                                    name="title" 
                                    value="{{ old('title') }}" 
                                    placeholder="お知らせのタイトルを入力してください"
                                    required>
                                @error('title')
                                    <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <textarea class="form-control @error('content') has-error @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="15" 
                                      placeholder="お知らせの内容を入力してください"
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" name="action" value="save_draft" class="btn btn-default" onclick="tinyMCE.triggerSave();">
                                <span class="glyphicon glyphicon-floppy-disk"></span> 下書き保存
                            </button>
                            <button type="submit" name="action" value="request_approval" class="btn btn-primary" onclick="tinyMCE.triggerSave();">
                                <span class="glyphicon glyphicon-upload"></span> 承認申請
                            </button>
                            <a href="{{ route('announcements.index') }}" class="btn btn-default">
                                <span class="glyphicon glyphicon-arrow-left"></span> 戻る
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Preview Modal -->
<div id="mobilePreviewModal" class="mobile-preview-modal">
    <div class="mobile-preview-content">
        <div class="mobile-preview-header">
            スマホプレビュー
            <button class="mobile-preview-close" onclick="hideMobilePreview()">&times;</button>
        </div>
        <div class="mobile-preview-body" id="mobilePreviewBody">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.2/tinymce.min.js"></script>
<script>
// Global variables for translation
var translationInterval;
var observerInstance;
var translations = {
    // 画像関連の翻訳
    'Insert/Edit Image': '画像の挿入/編集',
    'Insert/edit image': '画像の挿入/編集',
    'Image description': '画像の説明',
    'Source': 'ソース',
    'Upload': 'アップロード',
    'General': '一般',
    'Advanced': '詳細',
    'Style': 'スタイル',
    'Dimensions': 'サイズ',
    'Insert image': '画像を挿入',
    'Browse for an image': '画像を参照',
    'Drop an image here': 'ここに画像をドロップ',
    'Alternative source': '代替ソース',
    'Alternative source URL': '代替ソースURL',
    'Alternative description': '代替説明',
    'Alt text': '代替テキスト',
    'Width': '幅',
    'Height': '高さ',
    'Constrain proportions': '比率を保持',
    'Cancel': 'キャンセル',
    'Save': '保存',
    'OK': 'OK',
    'OR': 'または',
    'Choose Files': 'ファイルを選択',
    'Browse': '参照',
    'Error': 'エラー',
    'Loading...': '読み込み中...',
    'Close': '閉じる',
    'Image title': '画像タイトル',
    'Border width': '境界線の幅',
    'Border style': '境界線のスタイル',
    'Vertical space': '垂直スペース',
    'Horizontal space': '水平スペース',
    'Border': '境界線',
    'Class': 'クラス',
    'Id': 'ID',
    'Name': '名前',
    'Target': 'ターゲット',
    'Rel': 'Rel',
    'Link list': 'リンクリスト',
    'Text to display': '表示テキスト',
    'Url': 'URL',
    'URL': 'URL',
    'Insert link': 'リンクを挿入',
    'New window': '新しいウィンドウ',
    'None': 'なし',
    'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?': '入力されたURLはメールアドレスのようです。必要なmailto:プレフィックスを追加しますか？',
    'The URL you entered seems to be an external link. Do you want to add the required http:// prefix?': '入力されたURLは外部リンクのようです。必要なhttp://プレフィックスを追加しますか？',
    // アップロード画面の翻訳
    'Drop an image here': 'ここに画像をドロップ',
    'Browse for an image': '画像を参照',
    'Image options': '画像オプション',
    'Image is decorative': '装飾用の画像',
    'Source URL': 'ソースURL',
    'Image dimensions': '画像の寸法',
    'Lock aspect ratio': 'アスペクト比をロック',
    'Uploading image': '画像をアップロード中',
    'Uploading...': 'アップロード中...',
    'Processing...': '処理中...',
    'Image uploaded successfully': '画像のアップロードに成功しました',
    // 基本的なエディタ用語
    'File': 'ファイル',
    'Edit': '編集',
    'Insert': '挿入',
    'View': '表示',
    'Format': 'フォーマット',
    'Table': '表',
    'Tools': 'ツール',
    'Help': 'ヘルプ',
    'About': 'について',
    'Bold': '太字',
    'Italic': '斜体',
    'Underline': '下線',
    'Align left': '左寄せ',
    'Align center': '中央寄せ',
    'Align right': '右寄せ',
    'Justify': '両端揃え',
    'Cut': '切り取り',
    'Copy': 'コピー',
    'Paste': '貼り付け',
    'Select all': 'すべて選択',
    'Find': '検索',
    'Replace': '置換',
    'Print': '印刷',
    'Preview': 'プレビュー',
    'Fullscreen': 'フルスクリーン',
    'Code view': 'コード表示',
    'Word count': '文字数',
    'Characters': '文字',
    'Characters (no spaces)': '文字（スペースなし）',
    'Words': '単語',
    'Paragraphs': '段落'
};

$(document).ready(function() {
    // CSRFトークンをAjaxに設定
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 全校舎選択のチェックボックス制御
    $('#select_all').change(function() {
        if ($(this).is(':checked')) {
            $('.school-checkbox').prop('checked', false);
        }
    });
    
    $('.school-checkbox').change(function() {
        if ($(this).is(':checked')) {
            $('#select_all').prop('checked', false);
        }
    });

    // TinyMCE初期化
    tinymce.init({
        selector: '#content',
        height: 450,
        language: 'ja', // 日本語設定
        language_url: '/shinzemi/js/tinymce/langs/ja.js', // 日本語ファイルのパス
        
        // 基本プラグインのみ
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
            'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'table', 'help', 'wordcount'
        ],
        
        // ツールバー設定
        toolbar: 'undo redo | formatselect | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | code | help',
        
        // 基本設定
        menubar: false,
        statusbar: true,
        resize: true,
        branding: false,
        
        // URL変換を無効化（絶対パスを維持）
        convert_urls: false,
        relative_urls: false,
        remove_script_host: false,
        
        // 画像アップロード設定
        images_upload_url: '/shinzemi/announcements/upload-image',
        images_upload_credentials: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        
        // 画像アップロードハンドラー
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                var formData = new FormData();
                
                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) {
                        var percent = Math.round((e.loaded / e.total) * 100);
                        progress(percent);
                    }
                };
                
                xhr.onload = function() {
                    if (xhr.status === 403) {
                        reject('権限エラー: ページをリロードして再試行してください。');
                        return;
                    }
                    
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('アップロードエラー: HTTP ' + xhr.status);
                        return;
                    }
                    
                    var json;
                    try {
                        json = JSON.parse(xhr.responseText);
                    } catch (e) {
                        reject('サーバーレスポンスの解析に失敗しました。');
                        return;
                    }
                    
                    if (json.error) {
                        reject(json.error);
                        return;
                    }
                    
                    if (!json.location) {
                        reject('画像URLが取得できませんでした。');
                        return;
                    }
                    
                    resolve(json.location);
                };
                
                xhr.onerror = function () {
                    reject('ネットワークエラーが発生しました。');
                };
                
                xhr.ontimeout = function () {
                    reject('アップロードがタイムアウトしました。');
                };
                
                xhr.open('POST', '/shinzemi/announcements/upload-image');
                xhr.timeout = 30000;
                
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                xhr.send(formData);
            });
        },
        
        // コンテンツスタイル
        content_style: `
            body { 
                font-family: "Helvetica Neue", Arial, "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, sans-serif; 
                font-size: 14px; 
                line-height: 1.6;
                padding: 10px;
            }
            p { margin: 0 0 10px 0; }
            img { max-width: 100%; height: auto; }
        `,
        
        // セットアップ
        setup: function(editor) {
            editor.on('init', function() {
                // 初期化後に継続的な翻訳を開始
                startContinuousTranslation();
            });
            
            editor.on('OpenWindow', function(e) {
                // ダイアログが開いたときに翻訳を強化
                startContinuousTranslation();
                // 複数回翻訳を実行して確実に翻訳
                var translateCount = 0;
                var translateInterval = setInterval(function() {
                    translateTinyMCEToJapanese();
                    translateCount++;
                    if (translateCount >= 10) {
                        clearInterval(translateInterval);
                    }
                }, 100);
            });
            
            editor.on('CloseWindow', function() {
                // ダイアログが閉じたときも翻訳は継続
                // stopContinuousTranslation();
            });
            
            // タブが切り替わったときの処理
            editor.on('TabStateChange', function() {
                setTimeout(function() {
                    translateTinyMCEToJapanese();
                }, 10);
            });
            
            // コンテンツが変更されたときの処理
            editor.on('NodeChange', function() {
                translateTinyMCEToJapanese();
            });
        }
    });
});

// 継続的な翻訶の開始（改良版）
function startContinuousTranslation() {
    // 既存のインターバルがあれば停止
    if (translationInterval) {
        clearInterval(translationInterval);
    }
    
    // 30ms間隔で翻訳を実行（さらに頻繁に）
    translationInterval = setInterval(function() {
        translateTinyMCEToJapanese();
    }, 30);
    
    // MutationObserverを使用してDOM変更を監視
    if (!observerInstance) {
        observerInstance = new MutationObserver(function(mutations) {
            translateTinyMCEToJapanese();
        });
        
        // body全体を監視
        observerInstance.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
        });
    }
}

// 継続的な翻訶の停止
function stopContinuousTranslation() {
    if (translationInterval) {
        clearInterval(translationInterval);
        translationInterval = null;
    }
    
    if (observerInstance) {
        observerInstance.disconnect();
        observerInstance = null;
    }
}

// TinyMCEの日本語化関数（完全版）
function translateTinyMCEToJapanese() {
    // すべてのTinyMCE関連要素を取得
    var tinyMCEElements = document.querySelectorAll('.tox-dialog, .tox-dialog-wrap, .tox-tinymce-aux');
    
    tinyMCEElements.forEach(function(container) {
        if (!container) return;
        
        // ボタンのテキストを置換
        container.querySelectorAll('.tox-button__text, .tox-button span, button').forEach(function(element) {
            var text = element.textContent.trim();
            if (translations[text] && element.textContent !== translations[text]) {
                element.textContent = translations[text];
            }
        });
        
        // ラベルのテキストを置換（すべてのラベルを含む）
        container.querySelectorAll('.tox-label, .tox-form__group label, label, .tox-form__label').forEach(function(element) {
            var text = element.textContent.trim();
            if (translations[text] && element.textContent !== translations[text]) {
                element.textContent = translations[text];
            }
        });
        
        // タブのテキストを置換
        container.querySelectorAll('.tox-tab__text, .tox-tab').forEach(function(element) {
            var text = element.textContent.trim();
            if (translations[text] && element.textContent !== translations[text]) {
                element.textContent = translations[text];
            }
        });
        
        // ダイアログタイトルを置換
        container.querySelectorAll('.tox-dialog__title, .tox-dialog-title').forEach(function(element) {
            var text = element.textContent.trim();
            if (translations[text] && element.textContent !== translations[text]) {
                element.textContent = translations[text];
            }
        });
        
        // プレースホルダーテキストを置換
        container.querySelectorAll('input[placeholder], textarea[placeholder]').forEach(function(element) {
            var placeholder = element.getAttribute('placeholder');
            if (translations[placeholder] && placeholder !== translations[placeholder]) {
                element.setAttribute('placeholder', translations[placeholder]);
            }
        });
        
        // ドロップゾーンのテキスト
        container.querySelectorAll('.tox-dropzone__text, .tox-dropzone p, .tox-dropzone span').forEach(function(element) {
            var text = element.textContent.trim();
            if (translations[text] && element.textContent !== translations[text]) {
                element.textContent = translations[text];
            }
        });
        
        // その他のテキスト要素（より幅広くキャッチ）
        container.querySelectorAll('.tox-textfield-label, .tox-checkbox__label, .tox-selectfield__label, .tox-collection__item-label').forEach(function(element) {
            var text = element.textContent.trim();
            if (translations[text] && element.textContent !== translations[text]) {
                element.textContent = translations[text];
            }
        });
        
        // テキストノードを直接チェック（ラベル要素内のテキスト）
        var walker = document.createTreeWalker(
            container,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function(node) {
                    // スクリプトやスタイルタグ内のテキストを除外
                    if (node.parentElement && 
                        (node.parentElement.tagName === 'SCRIPT' || 
                         node.parentElement.tagName === 'STYLE')) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    return NodeFilter.FILTER_ACCEPT;
                }
            },
            false
        );
        
        var textNodes = [];
        var node;
        while (node = walker.nextNode()) {
            var trimmedText = node.nodeValue.trim();
            if (trimmedText && translations[trimmedText]) {
                textNodes.push({
                    node: node,
                    originalText: trimmedText,
                    translatedText: translations[trimmedText]
                });
            }
        }
        
        textNodes.forEach(function(item) {
            if (item.node.nodeValue.trim() !== item.translatedText) {
                item.node.nodeValue = item.node.nodeValue.replace(item.originalText, item.translatedText);
            }
        });
    });
    
    // iframe内のTinyMCE要素も翻訳
    document.querySelectorAll('iframe').forEach(function(iframe) {
        try {
            var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            if (iframeDoc) {
                var iframeElements = iframeDoc.querySelectorAll('.tox-dialog, .tox-dialog-wrap');
                iframeElements.forEach(function(element) {
                    // iframe内でも同様の翻訳処理を実行
                    translateElementRecursively(element);
                });
            }
        } catch (e) {
            // クロスオリジンエラーを無視
        }
    });
}

// 要素を再帰的に翻訳する補助関数
function translateElementRecursively(element) {
    if (!element) return;
    
    // 要素内のすべてのテキストを含む要素を翻訳
    element.querySelectorAll('*').forEach(function(el) {
        if (el.childNodes.length === 1 && el.childNodes[0].nodeType === 3) {
            var text = el.childNodes[0].nodeValue.trim();
            if (translations[text]) {
                el.childNodes[0].nodeValue = translations[text];
            }
        }
    });
}

// Mobile Preview Functions
function showMobilePreview() {
    tinyMCE.triggerSave();
    var title = document.getElementById('title').value || 'タイトル未入力';
    var content = document.getElementById('content').value || 'コンテンツ未入力';
    
    // Get selected distribution targets
    var distributionText = '未設定';
    var allChecked = document.getElementById('select_all').checked;
    if (allChecked) {
        distributionText = '全校舎';
    } else {
        var selectedBuildings = [];
        document.querySelectorAll('.school-checkbox:checked').forEach(function(checkbox) {
            var label = checkbox.closest('label');
            if (label) {
                var text = label.textContent.trim();
                selectedBuildings.push(text);
            }
        });
        if (selectedBuildings.length > 0) {
            distributionText = selectedBuildings.join(', ');
        }
    }
    
    // Create preview HTML that matches the actual announcement detail page
    var previewHtml = `
        <div style="display:flex; justify-content: space-between; background-color: #f8f9fa; border-bottom: 2px solid #e9ecef; padding: 15px 0; ">
            <h1 style="color: black; font-size: 20px; text-decoration: underline; margin: 0; border-bottom: 4px double black; padding-bottom: 4px;">${title}</h1>
            <div style="background-color: #6c757d; color: white; padding: 4px 16px; text-align: center; text-decoration: none; display: block;">
                戻る
            </div>
        </div>
        
        <div style="border: 2px solid #dc3545; padding: 12px; margin-bottom: 20px; background-color: #fff;">
            <div style="margin-bottom: 6px; font-size: 13px;">
                <span style="font-weight: bold; color: #333;">投稿日：</span>${new Date().getFullYear()}年${String(new Date().getMonth() + 1).padStart(2, '0')}月${String(new Date().getDate()).padStart(2, '0')}日
            </div>
            <div style="margin-bottom: 0; font-size: 13px;">
                <span style="font-weight: bold; color: #333;">配信先：</span>${distributionText}
            </div>
        </div>
        
        <div style="line-height: 1.8; font-size: 13px; color: #333;">
            ${content}
        </div>
    `;
    
    document.getElementById('mobilePreviewBody').innerHTML = previewHtml;
    document.getElementById('mobilePreviewModal').style.display = 'block';
}

function hideMobilePreview() {
    document.getElementById('mobilePreviewModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    var modal = document.getElementById('mobilePreviewModal');
    if (event.target == modal) {
        hideMobilePreview();
    }
}

// ページを離れるときに翻訳インターバルを停止
window.addEventListener('beforeunload', function() {
    stopContinuousTranslation();
});
</script>
@endpush
@endsection