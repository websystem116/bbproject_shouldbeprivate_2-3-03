<style>
    body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        font-size: 12px;
        line-height: 1.25;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td {
        vertical-align: top;
    }

    .a4-page {
        width: 210mm;
        height: 297mm;
        padding: 20mm;
        box-sizing: border-box;
    }

    .header-table td:first-child {
        width: 30%;
    }

    .header-table td:nth-child(2) {
        width: 40%;
        text-align: center;
    }

    .header-table td:last-child {
        width: 30%;
        text-align: right;
    }

    .header-table .header-address {
        margin-bottom: 30px;
    }

    .header-table .header-title {
        font-size: 12px;
        font-weight: normal;
        margin-bottom: 15px;
    }

    .header-table .header-title .title-main {
        line-height: 1.25;
        font-weight: bold;
        font-size: 20px;
        margin-bottom: 15px;
    }

    .header-table .header-date {
        padding-top: 40px;
    }

    .client-info-table td:first-child {
        width: 50%;
    }

    .client-info-table td:last-child {
        width: 50%;
        text-align: left;
        padding-left: 30mm;
        padding-top: 5mm;
    }

    .client-info-table .client-name {
        margin-bottom: 80px;
        padding-right: 100px;
        text-align: right;
    }

    .client-info-table .company-name {
        font-size: 16px;
        margin-bottom: 5px;
    }

    .student-info-table {
        width: 45%;
        margin-bottom: 20px;
        table-layout: fixed;
    }

    .student-info-table td:first-child {
        width: 25%;
    }

    .billing-message {
        margin-top: 10px;
        margin-bottom: 60px;
        padding-left: 5px;
        width: 45%;
    }

    .billing-summary-table {
        width: 80%;
        margin-left: auto;
        margin-bottom: 10px;
        text-align: center;
    }

    .billing-summary-table th,
    .billing-summary-table td {
        border: 1px solid black;
        padding: 2px;
        font-size: 12px;
        height: 20px;
    }

    .billing-summary-table th {
        font-weight: normal;
    }

    .billing-details-table {
        text-align: center;
    }

    .billing-details-table th,
    .billing-details-table td {
        font-weight: normal;
        border: 1px solid black;
        padding: 2px;
    }

    .billing-details-table .item-label {
        text-align: left;
        width: 20%;
    }

    .billing-details-table .item-name {
        text-align: left;
        width: 44%;
    }

    .billing-details-table .item-price,
    .billing-details-table .item-tax,
    .billing-details-table .item-subtotal {
        text-align: right;
        width: 12%;
    }

    .billing-details-table .total-label {
        text-align: left;
    }

    .billing-details-table .total-price,
    .billing-details-table .total-tax,
    .billing-details-table .total-subtotal {
        text-align: right;
    }
</style>
<div>
    <table class="header-table">
        <tr>
            <td class="header-address">
                〒{{ $invoice->recipient_zip_code }}<br>
                {{ $invoice->recipient_address1 . $invoice->recipient_address2 . $invoice->recipient_address3 }}
            </td>
            <td class="header-title">
                <span class="title-main">御請求書</span><br>
                {{ date('Y年n月分', strtotime($invoice->charge_month)) }}
            </td>
            <td class="header-date">
                発行日 {{ date('Y年m月d日', strtotime($invoice->updated_at)) }}
            </td>
        </tr>
    </table>

    <table class="client-info-table">
        <tr>
            <td class="client-name">
                {{ $invoice->recipient_surname . ' ' . $invoice->recipient_name }} 様
            </td>
            <td class="company-info">
                <div class="company-name">株式会社進学ゼミナール</div>
                <div>〒631-0036</div>
                <div>奈良県奈良市学園北1丁目11-10</div>
                <div>森田ビル2階</div>
                <div>TEL 0742-51-3422</div>
                <div>登録番号T7150001008095</div>
            </td>
        </tr>
    </table>

    <table class="student-info-table">
        <tr>
            <td class="student-info-label">校舎名</td>
            <td class="student-info-value">{{ $invoice->school_building_name }}</td>
        </tr>
        <tr>
            <td class="student-info-label">学年</td>
            <td class="student-info-value">{{ config('const.school_year')[$invoice->grade] }}</td>
        </tr>
        <tr>
            <td class="student-info-label">受講授業</td>
            <td class="student-info-value">{{ $invoice->getClassNameByInvoiceId() }}</td>
        </tr>
        <tr>
            <td class="student-info-label">生徒番号</td>
            <td class="student-info-value">{{ $invoice->student_no }}</td>
        </tr>
        <tr>
            <td class="student-info-label">生徒氏名</td>
            <td class="student-info-value">{{ $invoice->student_name }}</td>
        </tr>
        <tr>
            <td class="student-info-label">生徒パスワード</td>
            <td class="student-info-value"><strong>{{ $invoice->student->initial_password }}</strong></td>
        </tr>
    </table>

    <div class="billing-message">
        {{ $invoice->display_message }}
    </div>

    <table class="billing-summary-table">
        <thead>
            <tr>
                <th>前月繰越金</th>
                <th>当月明細合計</th>
                <th>当月消費税額</th>
                <th>事前入金</th>
                <th>合計請求額</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($invoice->carryover) }}</td>
                <td>{{ number_format($invoice->month_sum) }}</td>
                <td>{{ number_format($invoice->month_tax_sum) }}</td>
                <td>{{ number_format($invoice->prepaid) }}</td>
                <td>{{ number_format($invoice->sum) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="billing-details-table">
        <thead>
            <tr>
                <th colspan="2">明細</th>
                <th>金額</th>
                <th>消費税</th>
                <th>小計</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->invoiceDetails as $invoiceDetails)
                <tr>
                    <td class="item-label">{{ $invoiceDetails->division_name }}</td>
                    <td class="item-name">{{ $invoiceDetails->product_name }}</td>
                    <td class="item-price">{{ number_format($invoiceDetails->price) }}</td>
                    <td class="item-tax">{{ number_format($invoiceDetails->tax) }}</td>
                    <td class="item-subtotal">{{ number_format($invoiceDetails->subtotal) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="total-label">合計</td>
                <td class="total-price">{{ number_format($totalPrice) }}</td>
                <td class="total-tax">{{ number_format($totalTax) }}</td>
                <td class="total-subtotal">{{ number_format($totalSubtotal) }}</td>
            </tr>
        </tbody>
    </table>
    <div style="text-align: left; margin-top: 20px; font-size: 12px;">
        <p>今回の合計請求額は、{{ number_format($invoice->sum) }}円です。</p>
        @if ($invoice->applied_discount_name)
            <p>割引適応: {{ $invoice->applied_discount_name }}</p>
        @endif
    </div>
</div>
