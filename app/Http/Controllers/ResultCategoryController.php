<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\ResultCategory;
use App\Subject;
use App\Implementation;


class ResultCategoryController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$perPage = 25;
		$result_category = ResultCategory::paginate($perPage);

		return view("result_category.index", compact("result_category"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return view("result_category.create");
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		// dd($request);

		$request->validate([
			'result_category_name' => 'required', //必須入力
		]);
		$requestDatas = $request->all();

		//成績カテゴリーの追加
		$requestData = [
			'result_category_name' => $requestDatas['result_category_name'],
			'average_point_flg' => $requestDatas['average_point_flg'], //平均点表示フラグ
			'elementary_school_student_display_flg' => $requestDatas['elementary_school_student_display_flg'], //小学生表示フラグ
			'junior_high_school_student_display_flg' => $requestDatas['junior_high_school_student_display_flg'], //中学生表示フラグ
		];

		$data = ResultCategory::create($requestData);

		//実施回登録
		$count = 1;
		for ($i = 0; $i < count($requestDatas['implementation_name']); $i++) {
			$requestData_implementation = [
				'result_category_id' => $data->id,
				'implementation_no' => $count,
				'implementation_name' => $requestDatas['implementation_name'][$i], //実施回名
			];
			Implementation::create($requestData_implementation);
			$count++;
		}
		return redirect("/shinzemi/result_category")->with("flash_message", "データが登録されました。");
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id == $result_category_id (成績カテゴリー主キー)
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//登録してる成績カテゴリー情報取得
		$result_category = ResultCategory::findOrFail($id);

		$implementations = Implementation::where('result_category_id', $result_category->id)->get();

		return view("result_category.edit", compact("result_category", "implementations"));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id $id == $result_category_id (成績カテゴリー主キー)
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$requestDatas = $request->all();
		// dd($requestDatas);
		// /*
		// *成績カテゴリーアップデート処理
		// */
		//対象の成績カテゴリーの情報取得
		$result_category = ResultCategory::findOrFail($id);
		//配列から成績カテゴリーの追加データ抽出
		$result_requestData = [
			'result_category_name' => $requestDatas['result_category_name'],
			'average_point_flg' => $requestDatas['average_point_flg'], //平均点表示フラグ
			'elementary_school_student_display_flg' => $requestDatas['elementary_school_student_display_flg'], //小学生表示フラグ
			'junior_high_school_student_display_flg' => $requestDatas['junior_high_school_student_display_flg'], //中学生表示フラグ
		];
		$result_category->update($result_requestData);
		/*
		*成績カテゴリーアップデート処理end
		*/
		/*
		*実施回アップデート処理
		*/
		$count = 1;
		for ($i = 0; $i < count($requestDatas['implementation_name']); $i++) {
			if (isset($requestDatas['hidden_implementation_id'][$i])) {
				$implementation = Implementation::findOrFail($requestDatas['hidden_implementation_id'][$i]);
				$requestData_implementation = [
					'result_category_id' => $id,
					'implementation_no' => $count,
					'implementation_name' => $requestDatas['implementation_name'][$i] //教科名
				];
				$implementation->update($requestData_implementation);
			} else {
				$requestData_implementation = [
					'result_category_id' => $id,
					'implementation_no' => $count,
					'implementation_name' => $requestDatas['implementation_name'][$i] //教科名
				];
				Implementation::create($requestData_implementation);
			}
			$count++;
		}
		/*
		*試験アップデート処理end
		*/
		// return back()->with("flash_message", "データが登録されました。");
		return redirect("/shinzemi/result_category/$id/edit/")->with("flash_message", "データが登録されました。");
	}

	/**
	 * 並び替え順保存処理
	 *
	 * @param Request $request
	 * result_category_id_array　id
	 * sort_order_array 並び順
	 * @return void
	 */
	// public function order_save(Request $request)
	// {
	// 	$data = $request->all();
	// 	$loop = count($data['sort_order_array']);
	// 	// dd($data['sort_order_array'][0]);

	// 	for ($i = 0; $i < $loop; $i++) { //並び順の数だけ回す
	// 		$result_category = ResultCategory::findOrFail($data['result_category_id_array'][$i]);
	// 		$result_category->sort_order = $data['sort_order_array'][$i];

	// 		$result_category->save();
	// 	}
	// }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @param int identification_flg 1:教科削除　2：試験削除　それ以外は入らない
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id)
	{
		ResultCategory::destroy($id); //成績カテゴリー削除
		Implementation::where('result_category_id', $id)->delete(); //成績カテゴリーに紐づく実施回の削除
		Subject::where('result_category_id', $id)->delete(); //成績カテゴリーに紐づく教科の削除

		return redirect("/shinzemi/result_category")->with("flash_message", "データが削除されました。");
	}
}
