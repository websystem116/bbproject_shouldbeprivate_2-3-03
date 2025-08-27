<?php

namespace App\Http\Controllers;

// use App\YearEnd;
use Illuminate\Http\Request;

use App\Student;
use App\BeforeStudent;
use DB;

class YearEndController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view("year_end.index");
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
	 * @param  \App\YearEnd  $yearEnd
	 * @return \Illuminate\Http\Response
	 */
	public function show(YearEnd $yearEnd)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\YearEnd  $yearEnd
	 * @return \Illuminate\Http\Response
	 */
	public function edit(YearEnd $yearEnd)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\YearEnd  $yearEnd
	 * @return \Illuminate\Http\Response
	 */
	public function update()
	{
		// dd("年度末処理");
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\YearEnd  $yearEnd
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(YearEnd $yearEnd)
	{
		//
	}

	/**
	 * 年度末処理
	 *
	 */
	public function fiscal_year_end_process()
	{

		DB::update('UPDATE students SET
			grade = case WHEN NOT grade=16 THEN grade + 1 ELSE grade END,
			brothers_grade1 = case WHEN NOT brothers_grade1=16 AND NOT brothers_grade1=99 THEN brothers_grade1 + 1 ELSE brothers_grade1 END,
			brothers_grade2 = case WHEN NOT brothers_grade2=16 AND NOT brothers_grade2=99 THEN brothers_grade2 + 1 ELSE brothers_grade2 END,
			brothers_grade3 = case WHEN NOT brothers_grade3=16 AND NOT brothers_grade3=99 THEN brothers_grade3 + 1 ELSE brothers_grade3 END;
		');


		DB::update('UPDATE before_students SET
			grade = case WHEN NOT grade=16 THEN grade + 1 ELSE grade END,
			brothers_grade1 = case WHEN NOT brothers_grade1=16 AND NOT brothers_grade1=99 THEN brothers_grade1 + 1 ELSE brothers_grade1 END,
			brothers_grade2 = case WHEN NOT brothers_grade2=16 AND NOT brothers_grade2=99 THEN brothers_grade2 + 1 ELSE brothers_grade2 END,
			brothers_grade3 = case WHEN NOT brothers_grade3=16 AND NOT brothers_grade3=99 THEN brothers_grade3 + 1 ELSE brothers_grade3 END;
		');

		DB::update('UPDATE years SET year=year+1 WHERE id = 1');

		return redirect("/shinzemi/year_end")->with("flash_message", "年度末処理が完了しました。");
	}
}
