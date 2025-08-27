<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\OtherJobDescription;

//=======================================================================
class OtherJobDescriptionsController extends Controller
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
			$other_job_description = OtherJobDescription::where("id", "LIKE", "%$keyword%")->orWhere("code", "LIKE", "%$keyword%")->orWhere("name", "LIKE", "%$keyword%")->orWhere("name_kana", "LIKE", "%$keyword%")->paginate($perPage);
		} else {
			$other_job_description = OtherJobDescription::paginate($perPage);
		}
		return view("other_job_description.index", compact("other_job_description"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return view("other_job_description.create");
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
			"name" => "required|max:40", //string('name',15)->nullable()
		]);
		$requestData = $request->all();

		OtherJobDescription::create($requestData);

		return redirect("/shinzemi/other_job_description")->with("flash_message", "データが登録されました。");
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
		$other_job_description = OtherJobDescription::findOrFail($id);
		return view("other_job_description.show", compact("other_job_description"));
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
		$other_job_description = OtherJobDescription::findOrFail($id);

		return view("other_job_description.edit", compact("other_job_description"));
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
			"name" => "required|max:40", //string('name',15)->nullable()

		]);
		$requestData = $request->all();

		$other_job_description = OtherJobDescription::findOrFail($id);
		$other_job_description->update($requestData);

		return redirect("/shinzemi/other_job_description")->with("flash_message", "データが更新されました。");
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
		OtherJobDescription::destroy($id);

		return redirect("/shinzemi/other_job_description")->with("flash_message", "データが削除されました。");
	}
}
    //=======================================================================
