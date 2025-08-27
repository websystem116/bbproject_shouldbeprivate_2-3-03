@extends('layouts.app')

@push('css')
    <link href="{{ asset('css/app-mypage.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="mb-8 text-2xl font-semibold tracking-tight text-gray-900">
            給与明細書を確認してください
        </h2>


        <form action="{{ route('salary.invoice.index') }}" name="mainform" method="GET">
            <div class="flex rounded items-center p-2">
                <input type="hidden" name="user_id" value="{{ request()->query('user_id') }}">
                <select name="search_salary_month" class="w-3/4 rounded focus:outline-none text-gray-700">
                    <option value="">全期間</option>
                    @foreach ($salaryInvoices['uniqueSalaryMonths'] as $month)
                        <option value="{{ $month }}" @if ($search_salary_month == $month) selected @endif>
                            {{ date('Y年m月', strtotime($month . '-01')) }}
                        </option>
                    @endforeach
                </select>
                <button class="p-2 border w-1/4 rounded-md bg-blue-800 text-white" type="submit">検索</button>
            </div>
        </form>

        <div class="shadow-md sm:rounded-lg mt-8">
            <table class="w-full text-sm text-left text-gray-500">
                <thead>
                    <tr class="bg-white border-b text-lg">
                        <th>
                            給与明細書リスト
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salaryInvoices['salaryInvoices'] as $salaryInvoice)
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4 relative">
                                <div class='text-lg font-bold'>
                                    {{ date('Y年m月', strtotime($salaryInvoice->salary_month . '-01')) }}給与明細書
                                </div>
                                <div class=''>
                                    ユーザー番号：{{ $salaryInvoice->user_id }}
                                </div>
                                <div class=''>
                                    更新日時：{{ $salaryInvoice->updated_at->format('Y年m月d日') }}
                                </div>
                                <div class=''>
                                    公開期限：{{ $salaryInvoice->created_at->addMonths(3)->format('Y年m月d日') }}
                                </div>
                                <!-- ボタンを追加 -->
                                <div class="mt-2 space-x-2 flex items-center">
                                    <a href="{{ route('salary.invoice.show', ['invoice' => $salaryInvoice->id]) }}"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-green-500 rounded hover:bg-green-600">
                                        詳細ページへ
                                    </a>

                                    <!-- PDFダウンロードボタン -->
                                    <a href="{{ route('salary.invoice.download', ['invoice' => $salaryInvoice->id]) }}"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded hover:bg-blue-600">
                                        PDFをダウンロード
                                    </a>
                                </div>
                                @if ($salaryInvoice->read_at !== null)
                                    <div
                                        class="inline-flex items-center justify-center border border-blue-200 text-blue-800 bg-gray-100 py-1 px-2 text-sm rounded-full shadow-md absolute bottom-2 right-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        既読
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $salaryInvoices['salaryInvoices']->appends($param)->links() }}
    </div>
@endsection
