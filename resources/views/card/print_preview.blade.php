<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>カードプレビュー</title>
        <style>
        .card-container {
        width: 166mm; /* 横幅を8.3cmに設定 */
        height: 104mm; /* 縦幅を5.2cmに設定 */
        margin: 0;
        overflow: hidden;
        }

        .card-image {
        width: 166mm; /* 画像の幅をコンテナに合わせて100%にする */
        height: 104mm; /* 画像の高さを自動調整 */
        object-fit: contain; /* アスペクト比を維持したままコンテナに収める */
        object-position: left top;
        }

        .name {
        position: absolute; /* 絶対位置指定 */
        top: 51mm; /* 上からの位置 */
        left: 134mm; /* 左からの位置 */
        font-size: 16px;
        font-weight: bold;
        z-index: 10; /* z-indexを指定 */
        }

        .schoolname {
        position: absolute; /* 絶対位置指定 */
        top: 51mm; /* 上からの位置 */
        left: 88.5mm; /* 左からの位置 */
        font-weight: bold;
        z-index: 10; /* z-indexを指定 */
        }

        .qr-code {
        position: absolute; /* 絶対位置指定 */
        top: 26mm; /* 上からの位置 */
        left: 33mm; /* 左からの位置 */
        width: 70mm; /* QRコードの幅 */
        height: 70mm; /* QRコードの高さ */
        z-index: 10; /* z-indexを指定 */
        }
    </style>
</head>

<body>
    <div class="card-container">
        <img src="{{ asset('images/QR_Card.png') }}" alt="カードテンプレート" class="card-image">
        <div class="qr-code">{!! $qr_code !!}</div>
        <div class="schoolname" style="font-size:{{$school_font_size}}">{{$accessUser->schoolbuilding->name_short}}</div>
        <div class="name" style="font-size:{{$user_font_size}}">{{ $accessUser->surname }} {{ $accessUser->name }}</div>
    </div>
</body>
</html>