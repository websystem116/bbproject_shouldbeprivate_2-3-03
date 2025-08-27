<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>カードプレビュー</title>
        <style>
        .card-container {
            width: 166mm;
            height: 68mm;
            position: relative; /* 子要素の基準位置 */
            margin: 0;
            overflow: hidden;
        }


        .card-image {
        width: 166mm; /* 画像の幅をコンテナに合わせて100%にする */
        height: 68mm; /* 画像の高さを自動調整 */
        object-fit: contain; /* アスペクト比を維持したままコンテナに収める */
        object-position: left top;
        }

        .qr-code {
            position: absolute;
            top: 24mm; /* 上からの位置 */
            left: 31mm; /* 左からの位置 */
            width: 30mm; /* QRコードの幅 */
            height: 30mm; /* QRコードの高さ */
            z-index: 10;
        }

        .schoolname {
            position: absolute;
            top: 49mm; /* 上からの位置 */
            left: 87mm; /* 左からの位置 */
            font-weight: bold;
            z-index: 10;
        }

        .name {
            position: absolute;
            top: 49mm; /* 上からの位置 */
            left: 131mm; /* 左からの位置 */
            font-size: 16px;
            font-weight: bold;
            z-index: 10;
        }

    </style>
</head>

<body>
    @foreach($accessUsers as $accessUser)
    <div class="card-container">
        <img src="{{ asset('images/QR_Card.png') }}" alt="カードテンプレート" class="card-image">
        <div class="qr-code">{!! $accessUser->qr_code !!}</div>
        <div class="schoolname" style="font-size:{{$accessUser->school_font_size}}">{{$accessUser->schoolbuilding_name}}</div>
        <div class="name" style="font-size:{{$accessUser->user_font_size}}">{{ $accessUser->surname }} {{ $accessUser->name }}</div>
    </div>
    @endforeach
</body>
</html>