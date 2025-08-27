<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\ResultCategory;
use App\Subject;
use App\Implementation;

class SubjectController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$perPage = 25;
		$result_category = ResultCategory::paginate($perPage);
		return view("subject.index", compact("result_category"));
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
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//登録してる成績カテゴリー情報取得
		$result_category = ResultCategory::findOrFail($id);

		$subjects = Subject::where('result_category_id', $result_category->id)->get();

		return view("subject.edit", compact("result_category", "subjects"));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$requestDatas = $request->all();
		//対象の成績カテゴリーの情報取得
		// $result_category = ResultCategory::findOrFail($id);
		/*
		*教科アップデート処理
		*/
		$count = 1;
		for ($i = 0; $i < count($requestDatas['subject_name']); $i++) {
			if (isset($requestDatas['hidden_subject_id'][$i])) {
				$subject = Subject::findOrFail($requestDatas['hidden_subject_id'][$i]);
				$requestData_subject = [
					'result_category_id' => $id,
					'subject_no' => $count,
					'subject_name' => $requestDatas['subject_name'][$i] //教科名
				];
				$subject->update($requestData_subject);
			} else {
				$requestData_subject = [
					'result_category_id' => $id,
					'subject_no' => $count,
					'subject_name' => $requestDatas['subject_name'][$i] //教科名
				];
				Subject::create($requestData_subject);
			}
			$count++;
		}
		return redirect("/shinzemi/subject/$id/edit/")->with("flash_message", "データが登録されました。");

		/*
		*教科アップデート処理end
		*/
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id)
	{
		// dd($request->id);
		if ($request->identification_flg == 1) {
			$subject = Subject::findOrFail($request->id);
			$subject->delete();
		} else {
			$implementation = Implementation::findOrFail($request->id);
			$implementation->delete();
		}
	}
}
