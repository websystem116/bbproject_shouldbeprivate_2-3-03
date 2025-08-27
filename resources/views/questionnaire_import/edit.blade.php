@extends("layouts.app")
@section("content")

@push('css')
<style>
/* input numberのスピンボタンを非表示にする */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* tableの偶数行だけグレー色にする。ただしtdの中の要素はinput numberである*/
table tr:nth-child(even) td input[type="number"] {
    background-color: #eee;
}

/* class=bg-redは上のcssより優先順位を上にしてstyle適用させる */
.bg-red {
    background-color: #ff0000 !important;
}

/* th内の要素は真ん中寄せかつ縦位置は下にする */
th {
    text-align: center;
    vertical-align: bottom;
}

/* input numberの位置を右寄せにする */
input[type="number"] {
    text-align: right;
}

/* 文字サイズを18pxへ */
#wrap {
    font-size: 18px;
}

#wrap {
    width: 3200px;
}

/* thをスクロールしても見えるようにしたい */
table th {
    position: sticky;
    top: 0;
    background-color: #fff;

    /* left: 0; */

}

/* 1列目のtdがスクロールしても見えるようにしたい */
table th:nth-child(1) {
    position: sticky;
    left: 0;
    background-color: #fff;

    top: 0;
}

table td:nth-child(1) {
    position: sticky;
    left: 0;
    background-color: #fff;

    top: 0;
}
</style>
@endpush

@push('scripts')

<script>
$(function() {
    // id=submitがクリックされた時
    $('#submit').click(function() {
        // input numberの値を全て取得
        var numbers = $('input[type="number"]').map(function() {
            return $(this).val();
        }).get();

        // value = 99のinput numberがあるかチェック
        var isExist = numbers.some(function(value) {
            return value == 99;
        });

        // もし存在していたら、確認ダイアログを表示
        if (isExist) {
            if (!confirm('99が入力されています。よろしいですか？')) {
                return false;
            }
        }
    });
});
</script>
@endpush


<div id="wrap">

    <div class="panel panel-default">
        <div class="panel-heading">アンケートインポート確認画面</div>
        <div class="panel-body">
            <a href="{{ route('questionnaire_import.create') }}" title="Back">
                <button class="btn btn-warning">戻る</button>
            </a>

            <br />
            <br />

            @if ($errors->any())
            <ul class="alert alert-danger">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            @endif

            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach

            <div style="display:flex;">
                @foreach ($json as $datas)

                @foreach ($datas as $data)
                @foreach ($data as $key => $value)

                @if($value==99)
                <div style="margin-right: 16px;">
                    <div style="text-align: center;">
                        No{{ $datas[0]["id"] }}-{{ config('const.QUESTIONNAIRE_RABEL_TO_JAPANESE.'.$key) }}
                    </div>
                    <a href="{{ asset('storage/kobetsu/'.$datas[0]['id'].'_'.$key.'.jpg') }}" target="_blank">
                        <img src="{{ asset('storage/kobetsu/'.$datas[0]['id'].'_'.$key.'.jpg') }}" alt="" width="320px">
                    </a>
                </div>

                @endif


                @endforeach
                @endforeach

                @endforeach
            </div>



            <form method="POST" action="{{route('questionnaire_import.stores')}}" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="school_building_id" value="{{ $school_building_id }}">
                <input type="hidden" name="questionnaire_content_id" value="{{ $questionnaire_content_id }}">

                @csrf

                <table>

                    <tr>
                        <th>No</th>
                        <th>学年</th>
                        <th>英語<br>クラス</th>
                        <th>英語<br>質問1</th>
                        <th>英語<br>質問2</th>
                        <th>英語<br>質問3</th>
                        <th>英語<br>質問4</th>
                        <th>英語<br>質問5</th>
                        <th>英語<br>質問6</th>
                        <th>英語<br>質問7</th>
                        <th>理科<br>クラス</th>
                        <th>理科<br>質問1</th>
                        <th>理科<br>質問2</th>
                        <th>理科<br>質問3</th>
                        <th>理科<br>質問4</th>
                        <th>理科<br>質問5</th>
                        <th>理科<br>質問6</th>
                        <th>理科<br>質問7</th>
                        <th>数学<br>クラス</th>
                        <th>数学<br>質問1</th>
                        <th>数学<br>質問2</th>
                        <th>数学<br>質問3</th>
                        <th>数学<br>質問4</th>
                        <th>数学<br>質問5</th>
                        <th>数学<br>質問6</th>
                        <th>数学<br>質問7</th>
                        <th>国語<br>クラス</th>
                        <th>国語<br>質問1</th>
                        <th>国語<br>質問2</th>
                        <th>国語<br>質問3</th>
                        <th>国語<br>質問4</th>
                        <th>国語<br>質問5</th>
                        <th>国語<br>質問6</th>
                        <th>国語<br>質問7</th>
                        <th>社会<br>クラス</th>
                        <th>社会<br>質問1</th>
                        <th>社会<br>質問2</th>
                        <th>社会<br>質問3</th>
                        <th>社会<br>質問4</th>
                        <th>社会<br>質問5</th>
                        <th>社会<br>質問6</th>
                        <th>社会<br>質問7</th>
                        <th>その他<br>クラス</th>
                        <th>その他<br>質問1</th>
                        <th>その他<br>質問2</th>
                        <th>その他<br>質問3</th>
                        <th>その他<br>質問4</th>
                        <th>その他<br>質問5</th>
                        <th>その他<br>質問6</th>
                        <th>その他<br>質問7</th>
                    </tr>

                    @foreach ($json as $datas)

                    @php
                    $i = $loop->index;
                    @endphp

                    <tr>
                        @foreach ($datas as $data)

                        @foreach ($data as $key => $value)
                        <td>
                            <input type="number" name="array[{{ $i }}][{{$key}}]" id="" value="{{$value}}" style="width: 60px" @if($value==99) class="bg-red" @endif>
                        </td>
                        @endforeach

                        @endforeach
                    </tr>

                    @endforeach
                </table>

                <div class="" style="margin-top: 24px;">
                    <input id="submit" class="btn btn-primary" type="submit" value="登録">
                </div>

            </form>




        </div>
    </div>
</div>

@endsection