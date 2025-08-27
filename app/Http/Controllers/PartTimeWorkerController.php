<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Bank;

    //=======================================================================
    class PartTimeWorkerController extends Controller
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
                $bank = Bank::where("id","LIKE","%$keyword%")->orWhere("code", "LIKE", "%$keyword%")->orWhere("name", "LIKE", "%$keyword%")->orWhere("name_kana", "LIKE", "%$keyword%")->paginate($perPage);
            } else {
                    $bank = Bank::paginate($perPage);
            }
            return view("bank.index", compact("bank"));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\View\View
         */
        public function create()
        {
            return view("bank.create");
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
				"code" => "nullable|max:4", //string('code',4)->nullable()
				"name" => "required|max:15", //string('name',15)->nullable()
				"name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()

            ]);
            $requestData = $request->all();

            Bank::create($requestData);

            return redirect("/shinzemi/bank")->with("flash_message", "データが登録されました。");
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
            $bank = Bank::findOrFail($id);
            return view("bank.show", compact("bank"));
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
            $bank = Bank::findOrFail($id);

            return view("bank.edit", compact("bank"));
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
				"code" => "nullable|max:4", //string('code',4)->nullable()
				"name" => "required|max:15", //string('name',15)->nullable()
				"name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()

            ]);
            $requestData = $request->all();

            $bank = Bank::findOrFail($id);
            $bank->update($requestData);

            return redirect("/shinzemi/bank")->with("flash_message", "データが更新されました。");
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
            Bank::destroy($id);

            return redirect("/shinzemi/bank")->with("flash_message", "データが削除されました。");
        }
    }
    //=======================================================================
