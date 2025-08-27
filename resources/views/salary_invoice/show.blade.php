@extends('layouts.app')

@push('css')
    <link href="{{ asset('css/app-mypage.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="a4-page bg-white shadow-lg mx-auto my-6 p-6" id="a4-page">
        {{-- PDF用のbladeを読み込み --}}
        @include('salary_invoice.invoice_pdf', compact('salaryInvoice', 'totalPrice', 'totalTax', 'totalSubtotal'))
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const a4Page = document.getElementById("a4-page");
            const screenWidth = window.innerWidth;
            const a4Width = 210; 

            const scale = screenWidth / (a4Width * 3.78); // mm -> px換算（1mm ≈ 3.78px）
            console.log(screenWidth);
            console.log(a4Width * 3.78);
            console.log(scale);
            if (scale < 1) {
                a4Page.style.transform = `scale(${scale})`;
                a4Page.style.transformOrigin = "top center";
            }
        });
    </script>
@endsection
