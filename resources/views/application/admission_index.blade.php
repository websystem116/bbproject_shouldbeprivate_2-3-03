@extends('layouts.app')
@section('content')
    @push('css')
    @endpush
    @push('scripts')
    <script>
        function go_next(){
            var agree_check = document.getElementById('agree_check');
            if (agree_check.checked) {
                document.location.href = "{{ route('application.admission_student_create') }}";
            } else {
                alert("「上記の個人情報の取扱いについて、同意します」のチェックボックスを選択してください。");
            }
        }

    </script>
    @endpush
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">個人情報同意ページ</div>
                    <div class="panel-body">

                        <div class="container">
                            <div class="row">
                                <div class="col-xs-11">
                                    <h4>1. 個人情報の収集・利用目的について</h4>
                                    <p>
                                        当団体は、お客様からご提供いただく個人情報を、以下の目的のためにのみ利用いたします。
                                    </p>
                                    <ul>
                                        <li>お申し込みいただいたサービスの提供およびそれに付随する連絡のため</li>
                                        <li>当団体からの情報提供（イベント、新サービス等のお知らせ）のため</li>
                                        <li>お客様からのお問い合わせへの対応のため</li>
                                        <li>サービス改善のための分析（個人を特定できない統計情報として利用）のため</li>
                                    </ul>
                                    <br>
                                    <h4>2. 収集する個人情報の項目</h4>
                                    <p>
                                        当団体が収集する個人情報は、以下の通りです。
                                    </p>
                                    <ul>
                                        <li>氏名</li>
                                        <li>住所</li>
                                        <li>電話番号</li>
                                        <li>メールアドレス</li>
                                        <li>その他、お申し込みいただいたサービスに必要な情報</li>
                                    </ul>

                                    <br>
                                    <h4>3. 個人情報の第三者への提供について</h4>
                                    <p>
                                        当団体は、法令に基づく場合を除き、お客様の同意なく個人情報を第三者に開示・提供することはありません。
                                    </p>
                                    <br>
                                    <h4>4. 個人情報の管理について</h4>
                                    <p>
                                        当団体は、お客様の個人情報を厳重に管理し、不正アクセス、紛失、破壊、改ざんおよび漏洩を防止するための適切な安全管理措置を講じます。
                                    </p>
                                    <br>
                                    <h4>5. 個人情報の開示・訂正・削除について</h4>
                                    <p>
                                        お客様は、ご自身の個人情報について、開示、訂正、追加、または削除を求めることができます。ご希望される場合は、下記のお問い合わせ先までご連絡ください。ご本人確認の上、速やかに対応いたします。
                                    </p>
                                    <br>
                                    <h4>6. 本同意書の変更について</h4>
                                    <p>
                                        当団体は、法令の改正その他の必要に応じて、本同意書の内容を改定する場合があります。改定を行った場合は、当団体ウェブサイト等にて公表いたします。
                                    </p>
                                    <br>
                                    <h4>7. お問い合わせ先</h4>
                                    <p>
                                        〇〇（ご担当部署名）<br>
                                        電話番号：XXX-XXXX-XXXX<br>
                                        E-mail：XXXX@XXXX.com

                                    </p>
                                    <br>
                                    <div class="form-group">
                                        <input type="checkbox" class="custom-control-input" name="agree_check" id="agree_check">
                                        <label class="custom-control-label" for="agree_check">上記の個人情報の取扱いについて、同意します</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top:10px">
                                <div class="col-xs-11">
                                    <button class="btn btn-primary" onclick="go_next();">同意後、入塾者内容登録へ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
