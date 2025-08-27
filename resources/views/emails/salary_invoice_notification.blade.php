@php
    $fullName = $salaryInvoice->user->first_name . ' ' . $salaryInvoice->user->last_name;
    $downloadUrl = url('shinzemi/salary/invoice/'.$salaryInvoice->id.'/download');
@endphp

{!! nl2br(e("
{$fullName} 様

給与明細が確定しました、ご確認の程お願い申し上げます。
------------------------------------------------------------------------------------------------
")) !!}

<br>

{{-- Optional: Add a real clickable link below --}}
<p>
    <strong>▶︎ ダウンロードはこちら：</strong><br>
    <a href="{{ $downloadUrl }}">{{ $downloadUrl }}</a>
</p>

{!! nl2br(e("

株式会社進学ゼミナール　（進学ゼミナール・進ゼミ個別・大学受験館・進ゼミキッズ）
〒631-0036　奈良県奈良市学園北1-11-10 森田ビル2Ｆ
TEL(本部):0742-51-3422
進学ゼミナール公式HP: https://www.shinzemi.co.jp/
")) !!}
