<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Authoritie;

    //=======================================================================
    class AuthoritiesController extends Controller
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
                $authoritie = Authoritie::where("id","LIKE","%$keyword%")->orWhere("user_id", "LIKE", "%$keyword%")->orWhere("password", "LIKE", "%$keyword%")->orWhere("classification_code", "LIKE", "%$keyword%")->orWhere("item_no", "LIKE", "%$keyword%")->orWhere("Is_need_password", "LIKE", "%$keyword%")->orWhere("last_login_date", "LIKE", "%$keyword%")->orWhere("changed_password_date", "LIKE", "%$keyword%")->orWhere("fail_times_login", "LIKE", "%$keyword%")->paginate($perPage);
            } else {
                    $authoritie = Authoritie::paginate($perPage);
            }
            return view("authoritie.index", compact("authoritie"));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\View\View
         */
        public function create()
        {
            return view("authoritie.create");
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
				"user_id" => "nullable|digits:3", //integer('user_id',3)->nullable()
				"password" => "nullable|max:20", //string('password',20)->nullable()
				"classification_code" => "nullable|integer", //integer('classification_code')->nullable()
				"item_no" => "nullable|max:2", //string('item_no',2)->nullable()
				"Is_need_password" => "nullable|digits:1", //integer('Is_need_password',1)->nullable()
				"last_login_date" => "nullable|date_format:Y-m-d H:i:s", //datetime('last_login_date')->nullable()
				"changed_password_date" => "nullable|date_format:Y-m-d H:i:s", //datetime('changed_password_date')->nullable()
				"fail_times_login" => "nullable|integer", //integer('fail_times_login')->nullable()

            ]);
            $requestData = $request->all();

            Authoritie::create($requestData);

            return redirect("/shinzemi/authoritie")->with("flash_message", "authoritie added!");
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
            $authoritie = Authoritie::findOrFail($id);
            return view("authoritie.show", compact("authoritie"));
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
            $authoritie = Authoritie::findOrFail($id);

            return view("authoritie.edit", compact("authoritie"));
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
				"user_id" => "nullable|digits:3", //integer('user_id',3)->nullable()
				"password" => "nullable|max:20", //string('password',20)->nullable()
				"classification_code" => "nullable|integer", //integer('classification_code')->nullable()
				"item_no" => "nullable|max:2", //string('item_no',2)->nullable()
				"Is_need_password" => "nullable|digits:1", //integer('Is_need_password',1)->nullable()
				"last_login_date" => "nullable|date_format:Y-m-d H:i:s", //datetime('last_login_date')->nullable()
				"changed_password_date" => "nullable|date_format:Y-m-d H:i:s", //datetime('changed_password_date')->nullable()
				"fail_times_login" => "nullable|integer", //integer('fail_times_login')->nullable()

            ]);
            $requestData = $request->all();

            $authoritie = Authoritie::findOrFail($id);
            $authoritie->update($requestData);

            return redirect("/shinzemi/authoritie")->with("flash_message", "authoritie updated!");
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
            Authoritie::destroy($id);

            return redirect("/shinzemi/authoritie")->with("flash_message", "authoritie deleted!");
        }
    }
    //=======================================================================

