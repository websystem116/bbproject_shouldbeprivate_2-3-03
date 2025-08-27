<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\JobDescription;

//=======================================================================
class JobDescriptionsController extends Controller
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
			$job_description = JobDescription::where("id", "LIKE", "%$keyword%")->orWhere("code", "LIKE", "%$keyword%")->orWhere("name", "LIKE", "%$keyword%")->orWhere("name_kana", "LIKE", "%$keyword%")->paginate($perPage);
		} else {
			$job_description = JobDescription::paginate($perPage);
		}
		return view("job_description.index", compact("job_description"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return view("job_description.create");
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

		JobDescription::create($requestData);

		return redirect("/shinzemi/job_description")->with("flash_message", "データが登録されました。");
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
		$job_description = JobDescription::findOrFail($id);
		return view("job_description.show", compact("job_description"));
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
		$job_description = JobDescription::findOrFail($id);

		return view("job_description.edit", compact("job_description"));
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

		$job_description = JobDescription::findOrFail($id);
		$job_description->update($requestData);

		return redirect("/shinzemi/job_description")->with("flash_message", "データが更新されました。");
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
		JobDescription::destroy($id);

		return redirect("/shinzemi/job_description")->with("flash_message", "データが削除されました。");
	}
}
    //=======================================================================
