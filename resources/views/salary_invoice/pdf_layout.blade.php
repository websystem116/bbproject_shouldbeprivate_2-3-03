<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>非常勤給与明細</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{{ asset('/css/pdf.css') }}">
</head>

<style>
    /* 印刷用のスタイル */
    @font-face {
        font-family: 'Noto Sans JP';
        src: url('{{ public_path('fonts/NotoSansJP-Regular.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    @font-face {
        font-family: 'Noto Sans JP';
        src: url('{{ public_path('fonts/NotoSansJP-Bold.ttf') }}') format('truetype');
        font-weight: bold;
        font-style: normal;
    }

    body {
        margin: 0;
        font-family: 'Noto Sans JP', sans-serif;
        width: auto;
    }

    .a4-page {
        margin: 0;
        box-shadow: none;
        border: none;
    }
</style>
<body>
    {{-- @include('salary_invoice.invoice_pdf', compact('salaryInvoiceData', 'totalPrice', 'totalTax', 'totalSubtotal')) --}}
    @include('salary_invoice.invoice_pdf', compact('salaryInvoiceData'))
</body>

</html>
