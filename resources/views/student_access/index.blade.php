@extends('layouts.app_head_none')
@section('content')
    @push('css')
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
        <style>
            #wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 640px;
                margin: 0 auto;
            }

            #camera-container {
                position: relative;
                width: 640px;
                height: 480px;
            }

            #video, #camera-canvas, #rect-canvas, #qr-msg {
                position: absolute;
                top: 0;
                left: 0;
            }

            #video {
                visibility: hidden;
            }

            #camera-canvas {
                z-index: 50;
            }

            #rect-canvas {
                z-index: 100;
            }

            #message {
                margin-top: 20px;
                font-size: 24px;
                color: #333;
            }
        </style>
    @endpush
    @push('scripts')
        <script src="{{ asset('js/jsQR.js') }}"></script>
        <script>
            $(function () {

                // Webカメラの起動
                const video = document.getElementById('video');
                let contentWidth;
                let contentHeight;
                let lastSentTime = 0;

                const media = navigator.mediaDevices.getUserMedia({ audio: false, video: {width:640, height:480} })
                .then((stream) => {
                    video.srcObject = stream;
                    video.onloadeddata = () => {
                        video.play();
                        contentWidth = video.clientWidth;
                        contentHeight = video.clientHeight;
                        canvasUpdate();
                        checkImage();
                    }
                }).catch((e) => {
                    console.log(e);
                });

                // カメラ映像のキャンバス表示
                const cvs = document.getElementById('camera-canvas');
                const ctx = cvs.getContext('2d');
                const canvasUpdate = () => {
                    cvs.width = contentWidth;
                    cvs.height = contentHeight;
                    ctx.drawImage(video, 0, 0, contentWidth, contentHeight);
                    requestAnimationFrame(canvasUpdate);
                }

                // QRコードの検出
                const rectCvs = document.getElementById('rect-canvas');
                const rectCtx =  rectCvs.getContext('2d');
                const checkImage = () => {

                    const imageData = ctx.getImageData(0, 0, contentWidth, contentHeight);
                    const code = jsQR(imageData.data, contentWidth, contentHeight);

                    // 現在の時間を取得
                    const currentTime = new Date().getTime();

                    if (code) {
                        drawRect(code.location);

                        // 2秒以内のQRコードは送信しない
                        if (currentTime - lastSentTime >= 2000) {
                            lastSentTime = currentTime;
                            sendQRCodeData(code.data);
                        }
                    } else {
                        rectCtx.clearRect(0, 0, contentWidth, contentHeight);
                    }
                    setTimeout(()=>{ checkImage() }, 500);
                }

                // 四辺形の描画
                const drawRect = (location) => {
                    rectCvs.width = contentWidth;
                    rectCvs.height = contentHeight;
                    drawLine(location.topLeftCorner, location.topRightCorner);
                    drawLine(location.topRightCorner, location.bottomRightCorner);
                    drawLine(location.bottomRightCorner, location.bottomLeftCorner);
                    drawLine(location.bottomLeftCorner, location.topLeftCorner)
                }

                // 線の描画
                const drawLine = (begin, end) => {
                    rectCtx.lineWidth = 4;
                    rectCtx.strokeStyle = "#F00";
                    rectCtx.beginPath();
                    rectCtx.moveTo(begin.x, begin.y);
                    rectCtx.lineTo(end.x, end.y);
                    rectCtx.stroke();
                }

                function sendQRCodeData(data) {

                    if (data == null || data === '') {
                        // $('#ngSound')[0].play();
                        $('#message').text('QRコードが読み取れませんでした');
                        $('#message').css('color', 'red');
                        setDefaultMessage();

                        return;
                    }
                    const url = '/shinzemi/student_access/store';

                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            qr_code_data: data
                        },
                        success: function (response) {
                            if (response.status === 'exit') {
                                $('#exitSound')[0].play(); // 退出音声を再生
                            } else if (response.status === 'entry') {
                                $('#okSound')[0].play(); // 入室音声を再生
                            } else {
                                $('#ngSound')[0].play(); // エラー音声を再生
                            }
                            $('#message').text(response.message);
                            $('#message').css('color', response.color);

                            setDefaultMessage();
                        },
                        error: function (xhr, status, error) {
                            $('#ngSound')[0].play();
                            $('#message').text('打刻に失敗しました。');
                            $('#message').css('color', 'red');

                            setDefaultMessage();
                        }
                    });
                }
            });

            function setDefaultMessage() {
                setTimeout(() => {
                    $('#message').text('QRコードをカメラに向かってかざしてください');
                    $('#message').css('color', '#333');
                }, 5000);
            }

        </script>
    @endpush



    <div class="card-group card_fild">
        <div id="wrapper">
            <div id="camera-container">
                <video id="video" autoplay muted playsinline></video>
                <canvas id="camera-canvas"></canvas>
                <canvas id="rect-canvas"></canvas>
                <span id="qr-msg">カメラが見つかりません</span>
            </div>
            <!-- カメラの下に表示するテキスト -->
            <p id="message">QRコードをカメラに向かってかざしてください</p>
        </div>
    </div>
    <audio id="okSound" src="{{ asset('sounds/ok.mp3') }}" preload="auto"></audio>
    <audio id="ngSound" src="{{ asset('sounds/ng.mp3') }}" preload="auto"></audio>
    <audio id="exitSound" src="{{ asset('sounds/exit.mp3') }}" preload="auto"></audio>


@endsection