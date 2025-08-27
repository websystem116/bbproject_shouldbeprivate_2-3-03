<html lang="ja">

<head>
    <title>{{$year}}-{{$month}}_入塾前売上明細</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
    @font-face {
        font-family: migmix;
        font-style: normal;
        font-weight: normal;
        src: url("{{ storage_path('fonts/migmix-2p-regular.ttf')}}") format('truetype');
    }

    @font-face {
        font-family: migmix;
        font-style: bold;
        font-weight: bold;
        src: url("{{ storage_path('fonts/migmix-2p-bold.ttf')}}") format('truetype');
    }

    body {
        font-family: migmix;
        line-height: 80%;
    }

    .main_image {
        width: 100%;
        text-align: center;
        margin: 10px 0;
    }

    .main_image img {
        width: 90%;
    }

    .Table {
        border: 1px solid #000;
        border-collapse: collapse;
        width: 100%;
    }

    .Table tr th {
        padding: 5px;
        border: 2px solid #000;
    }

    .Table tr td {
        padding: 5px;
        border: 1px solid #000;
    }
    </style>
</head>

<body>

    @php
    //改ページタイミングカウント用
    $counter=1;
    $total=0;
    @endphp

    @foreach ($sales_dates as $sales_date_key => $sales_date)
    <h2>{{$year}}年　{{$month}}月度　入塾前売上明細</h2>
    <div style="text-align: right">作成日：{{date('Y/m/d')}}</div>
    @foreach ($sales_date as $value_key => $value)
    @if ($value_key === 1)
    {{-- <div style="border-bottom: solid 1px">{{$value->school_building->name_short ?? ""}}</div> --}}
    <u>{{$value->school_building->name_short ?? ""}}</u>
    @endif
    @endforeach
    <table class="Table">
        <tr>
            <th>生徒No</th>
            <th>学年</th>
            <th>生徒氏名</th>
            <th>商品名</th>
            <th>金額</th>
            <th>入金日</th>
        </tr>
        @foreach ($sales_date as $value_key => $value)
        <tr>
            <td style="text-align: center">
                {{$value['before_student_no']}}
            </td>
            <td style="text-align: center">
                {{config('const.school_year')[$value->before_student->grade ?? 16]}}
            </td>
            <td style="text-align: center">
                {{$value->before_student->surname ?? ""}}　{{$value->before_student->name ?? ""}}
            </td>
            <td style="text-align: center">
                {{$value->product->name ?? ""}}
            </td>
            <td style="text-align: right">
                @if(!empty($value->product))
                {{number_format($value->subtotal) ?? ""}}
                @php
                $total+=$value->subtotal;
                @endphp
                @endif
            </td>
            <td style="text-align: center">
                {{$value->payment_date->format('Y/m/d') ?? ""}}
            </td>
        </tr>
        @endforeach
        <tr>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
                （合計）
            </td>
            <td style="text-align: right">
                {{number_format($total)}}
            </td>
            <td>
            </td>
        </tr>
    </table>
    @if($page!=$counter)
    {{-- 改ページ --}}
    <div style="page-break-after: always"></div>
    @endif
    @php
    $counter++;
    $total=0;
    @endphp
    @endforeach

</body>

</html>