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

    .user-info-table {
        width: 45%;
        margin-bottom: 20px;
        table-layout: fixed;
    }

    .user-info-table td:first-child {
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
@php
    $invoice = $salaryInvoiceData['salaryInvoice'];
    $salaryInvoiceDetails = $invoice->salaryInvoiceDetails;
    // dd($invoice->transportation_expenses);
    $deduction_sum = $invoice->health_insurance + $invoice->welfare_pension + $invoice->employment_insurance;
	$salary_sum = $invoice->salary - $deduction_sum;

    $payment_amount = 0;
    $other_payment_amount = 0;
    $other_deduction_amount = 0;
    $year_end_adjustment = 0;

    foreach ($invoice->salaryInvoiceDetails ?? [] as $detail) {
        $payment_amount +=$detail->payment_amount;
        $other_payment_amount +=$detail->other_payment_amount;
        $other_deduction_amount +=$detail->other_deduction_amount;
        $year_end_adjustment +=$detail->year_end_adjustment;
    }

    // $month = explode('-', $invoice->tightening_date)[1];
       
@endphp

<div>
    <table class="header-table">
        <tr>
            <td class="header-address">
                〒{{ $invoice->post_code ?? '' }}<br>
                {{ ($invoice->address1 ?? '') . ($invoice->address2 ?? '') . ($invoice->address3 ?? '') }}
            </td>
            <td class="header-title">
                <span class="title-main">給与明細</span><br>
                {{ date('Y年n月分', strtotime($invoice->tightening_date)) }}
            </td>
            <td class="header-date">
                発行日 {{ date('Y年m月d日', strtotime($invoice->updated_at)) }}
            </td>
        </tr>
    </table>

    <table class="client-info-table">
        <tr>
            <td class="client-name">
                {{ ($invoice->user->first_name ?? '') . ' ' . ($invoice->user->last_name ?? '') }} 様
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

    <table class="user-info-table">
        <tr>
            <td class="user-info-label">校舎名</td>
            <td class="user-info-value">{{ $invoice->school_building_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="user-info-label">ユーザー氏名</td>
            <td class="user-info-value">{{ $invoice->user_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="user-info-label">ユーザー番号</td>
            <td class="user-info-value">{{ $invoice->user_id ?? 0 }}</td>
        </tr>
        <tr>
            <td class="user-info-label" style="width: 40%;">ユーザーパスワード&nbsp;&nbsp;</td>
            <td class="user-info-value"><strong>{{ $invoice->user->password }}</strong></td>
        </tr>
    </table>

    {{-- <div class="billing-message">
        下記のとおりご給与明細書申し上げます。
        毎月25日に、ご指定の銀行口座より自動引き落としさせて頂きます。
        残高不足にはご注意ください。
        ※25日が休日の場合は前営業日に引き落としとなります。
    </div> --}}

    {{-- <table class="billing-summary-table">
        <thead>
            <tr>
                <th>前月繰越金</th>
                <th>当月明細合計</th>
                <th>当月消費税額</th>
                <th>事前入金</th>
                <th>合計給与明細書額</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>0</td>
                <td>{{ number_format($salary_sum + 0) }}</td>
                <td>{{ number_format($salaryInvoiceData['totalTax']) }}</td>
                @php
                    $total = ($salary_sum ?? 0) + ($salaryInvoiceData['totalTax']);
                @endphp
                <td>{{ number_format($total) }}</td>
                <td>0</td>
            </tr>
        </tbody>
    </table> --}}

    <table class="billing-details-table">
        <thead>
            <tr>
                <th>明細</th>
                <th>金額(円)</th>
                <th>その他支給額</th>
                <th>その他控除額</th>
                <th>年末調整</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->salaryInvoiceDetails ?? [] as $detail)
                <tr>
                    <td class="item-name">{{$detail->job_description_name}}</td>
                    <td>{{ number_format($detail->payment_amount ?? 0) }}</td>
                    <td>{{ number_format($detail->other_payment_amount ?? 0) }}</td>
                    <td>{{ number_format($detail->other_deduction_amount ?? 0) }}</td>
                    <td>{{ number_format($detail->year_end_adjustment ?? 0) }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="total-label" colspan="4">交通費</td>
                <td>{{ number_format($invoice->transportation_expenses ?? 0) }}</td>
            </tr>
            <tr>
                <td class="total-label" colspan="4">総支給額</td>
                <td>{{ number_format($payment_amount + $other_payment_amount + $other_deduction_amount + $year_end_adjustment) }}</td>
            </tr>
        </tbody>
    </table>
    <div style="text-align: left; margin-top: 20px; font-size: 12px;">
        <p>今回の給与の総額は、{{ number_format($payment_amount + $other_payment_amount + $other_deduction_amount + $year_end_adjustment) }}円 となります。</p>
        {{-- @if ($invoice->applied_discount_name)
            <p>割引適応: {{ $invoice->applied_discount_name }}</p>
        @endif --}}
    </div>
</div>

