<?php

namespace App\Http\Controllers;

// use App\BeforeJukuDetail;
use Illuminate\Http\Request;
use App\BeforeJukuSales;
use App\BeforeStudent;
use PDF;
use DB;

class BeforeJukuDetailController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view("before_juku_detail.index");
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\BeforeJukuDetail  $beforeJukuDetail
	 * @return \Illuminate\Http\Response
	 */
	public function show(BeforeJukuDetail $beforeJukuDetail)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\BeforeJukuDetail  $beforeJukuDetail
	 * @return \Illuminate\Http\Response
	 */
	public function edit(BeforeJukuDetail $beforeJukuDetail)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\BeforeJukuDetail  $beforeJukuDetail
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, BeforeJukuDetail $beforeJukuDetail)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\BeforeJukuDetail  $beforeJukuDetail
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(BeforeJukuDetail $beforeJukuDetail)
	{
		//
	}

	/**
	 * 入塾前売上明細出力
	 * $year_month = before_juku_sales=>sales_date
	 */
	public function sales_item_output(Request $request)
	{
		//年月加工処理
		$year = $request->get("year");
		$month = $request->get("month");
		$month = sprintf('%02d', $month);
		$year_month = $year . $month;



		$sales_dates_all = BeforeJukuSales::where('sales_date', $year_month)->get(); //売上年月の一致するデータ取得
		if ($sales_dates_all->isEmpty()) {
			return redirect()
				->route('before_juku_detail.index', ['year' => $year, 'month' => $month])
				->with("flash_message", "売上データがありません。")->withInput();
		}

		$sales_dates = $sales_dates_all->groupBy('school_building_id'); //学校ごとにグループ化
		$page = count($sales_dates); //改ページ用


		$pdf = \PDF::loadView('before_juku_detail/pdf_output', compact("year", "month", "sales_dates", "page"));
		$pdf->setPaper('A4');
		return $pdf->stream();
	}
}
