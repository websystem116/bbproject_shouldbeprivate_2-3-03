<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\ManageTarget;

    //=======================================================================
    class ManageTargetsController extends Controller
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
                $manage_target = ManageTarget::where("id","LIKE","%$keyword%")->orWhere("year", "LIKE", "%$keyword%")->orWhere("taget_classification", "LIKE", "%$keyword%")->orWhere("target_value", "LIKE", "%$keyword%")->paginate($perPage);
            } else {
                    $manage_target = ManageTarget::paginate($perPage);
            }
            return view("manage_target.index", compact("manage_target"));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\View\View
         */
        public function create()
        {
            return view("manage_target.create");
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
				"year" => "nullable|max:4", //string('year',4)->nullable()
				"taget_classification" => "nullable|integer", //integer('taget_classification')->nullable()
				"target_value" => "nullable|digits:3", //integer('target_value',3)->nullable()

            ]);
            $requestData = $request->all();

            ManageTarget::create($requestData);

            return redirect("/shinzemi/manage_target")->with("flash_message", "データが登録されました。");
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
            $manage_target = ManageTarget::findOrFail($id);
            return view("manage_target.show", compact("manage_target"));
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
            $manage_target = ManageTarget::findOrFail($id);

            return view("manage_target.edit", compact("manage_target"));
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
				"year" => "nullable|max:4", //string('year',4)->nullable()
				"taget_classification" => "nullable|integer", //integer('taget_classification')->nullable()
				"target_value" => "nullable|digits:3", //integer('target_value',3)->nullable()

            ]);
            $requestData = $request->all();

            $manage_target = ManageTarget::findOrFail($id);
            $manage_target->update($requestData);

            return redirect("/shinzemi/manage_target")->with("flash_message", "データが更新されました。");
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
            ManageTarget::destroy($id);

            return redirect("/shinzemi/manage_target")->with("flash_message", "データが削除されました。");
        }
    }
    //=======================================================================

