<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\InvoiceComment;

//=======================================================================
class InvoiceCommentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request)
	{
		$keyword = $request->get("search");
		$perPage = 25;

		if (!empty($keyword)) {
			$invoice_comment = InvoiceComment::where("id", "LIKE", "%$keyword%")->orWhere("code", "LIKE", "%$keyword%")->orWhere("name", "LIKE", "%$keyword%")->orWhere("name_kana", "LIKE", "%$keyword%")->paginate($perPage);
		} else {
			$invoice_comment = InvoiceComment::paginate($perPage);
		}
		return view("invoice_comment.index", compact("invoice_comment"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return view("invoice_comment.create");
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			"comment" => "required"
		], [
			"comment.required" => "説明文を入力してください。",
		]);
		$requestData = $request->all();

		InvoiceComment::create($requestData);

		return redirect("/shinzemi/invoice_comment")->with("flash_message", "データが登録されました。");
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 *
	 * @return \Illuminate\View\View
	 */
	public function show($id)
	{
		$invoice_comment = InvoiceComment::findOrFail($id);
		return view("invoice_comment.show", compact("invoice_comment"));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 *
	 * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$invoice_comment = InvoiceComment::findOrFail($id);

		return view("invoice_comment.edit", compact("invoice_comment"));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function update(Request $request, $id)
	{
		$this->validate($request, [
			"comment" => "required", //string('name',15)->nullable()

		], [
			"comment.required" => "説明文を入力してください。",
		]);
		$requestData = $request->all();

		$invoice_comment = InvoiceComment::findOrFail($id);

		$invoice_comment->update($requestData);

		return redirect("/shinzemi/invoice_comment")->with("flash_message", "データが更新されました。");
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function destroy($id)
	{
		InvoiceComment::destroy($id);

		return redirect("/shinzemi/invoice_comment")->with("flash_message", "データが削除されました。");
	}
}
    //=======================================================================
