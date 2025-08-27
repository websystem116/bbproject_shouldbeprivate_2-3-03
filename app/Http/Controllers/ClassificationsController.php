<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Classification;

    //=======================================================================
    class ClassificationsController extends Controller
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
                $classification = Classification::where("id","LIKE","%$keyword%")->orWhere("no", "LIKE", "%$keyword%")->orWhere("name", "LIKE", "%$keyword%")->paginate($perPage);
            } else {
                    $classification = Classification::paginate($perPage);
            }
            return view("classification.index", compact("classification"));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\View\View
         */
        public function create()
        {
            return view("classification.create");
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
				"no" => "nullable|max:2", //string('no',2)->nullable()
				"name" => "nullable|max:20", //string('name',20)->nullable()

            ]);
            $requestData = $request->all();

            Classification::create($requestData);

            return redirect("/shinzemi/classification")->with("flash_message", "データが登録されました。");

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
            $classification = Classification::findOrFail($id);
            return view("classification.show", compact("classification"));
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
            $classification = Classification::findOrFail($id);

            return view("classification.edit", compact("classification"));
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
				"no" => "nullable|max:2", //string('no',2)->nullable()
				"name" => "nullable|max:20", //string('name',20)->nullable()

            ]);
            $requestData = $request->all();

            $classification = Classification::findOrFail($id);
            $classification->update($requestData);

            return redirect("/shinzemi/classification")->with("flash_message", "データが更新されました。");
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
            Classification::destroy($id);

            return redirect("/shinzemi/classification")->with("flash_message", "データが削除されました。");
        }
    }
    //=======================================================================

