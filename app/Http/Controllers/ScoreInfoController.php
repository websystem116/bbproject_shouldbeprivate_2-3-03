<?php

namespace App\Http\Controllers;

use App\ScoreInfo;
use App\Student;
use App\SchoolBuilding;
use Illuminate\Http\Request;

class ScoreInfoController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$student = Student::get();
		return view("score_info.index", compact("student"));
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
	 * @param  \App\ScoreInfo  $scoreInfo
	 * @return \Illuminate\Http\Response
	 */
	public function show(ScoreInfo $scoreInfo)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\ScoreInfo  $scoreInfo
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$student = Student::findOrFail($id);
		return view("score_info.edit", compact("student"));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\ScoreInfo  $scoreInfo
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, ScoreInfo $scoreInfo)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\ScoreInfo  $scoreInfo
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(ScoreInfo $scoreInfo)
	{
		//
	}
}
