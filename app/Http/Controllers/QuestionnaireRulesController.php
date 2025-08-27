<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\QuestionnaireRule;

    //=======================================================================
    class QuestionnaireRulesController extends Controller
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
                $questionnaire_rule = QuestionnaireRule::where("id","LIKE","%$keyword%")->orWhere("rankstart", "LIKE", "%$keyword%")->orWhere("rankend", "LIKE", "%$keyword%")->orWhere("rankscore", "LIKE", "%$keyword%")->paginate($perPage);
            } else {
                    $questionnaire_rule = QuestionnaireRule::paginate($perPage);
            }
            return view("questionnaire_rule.index", compact("questionnaire_rule"));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\View\View
         */
        public function create()
        {
            return view("questionnaire_rule.create");
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
				"rankstart" => "nullable|digits:3", //integer('rankstart',3)->nullable()
				"rankend" => "nullable|digits:3", //integer('rankend',3)->nullable()
				"rankscore" => "nullable|digits:3", //integer('rankscore',3)->nullable()

            ]);
            $requestData = $request->all();

            QuestionnaireRule::create($requestData);

            return redirect("/shinzemi/questionnaire_rule")->with("flash_message", "データが登録されました。");
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
            $questionnaire_rule = QuestionnaireRule::findOrFail($id);
            return view("questionnaire_rule.show", compact("questionnaire_rule"));
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
            $questionnaire_rule = QuestionnaireRule::findOrFail($id);

            return view("questionnaire_rule.edit", compact("questionnaire_rule"));
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
				"rankstart" => "nullable|digits:3", //integer('rankstart',3)->nullable()
				"rankend" => "nullable|digits:3", //integer('rankend',3)->nullable()
				"rankscore" => "nullable|digits:3", //integer('rankscore',3)->nullable()

            ]);
            $requestData = $request->all();

            $questionnaire_rule = QuestionnaireRule::findOrFail($id);
            $questionnaire_rule->update($requestData);

            return redirect("/shinzemi/questionnaire_rule")->with("flash_message", "データが更新されました。");
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
            QuestionnaireRule::destroy($id);

            return redirect("/shinzemi/questionnaire_rule")->with("flash_message", "データが削除されました。");
        }
    }
    //=======================================================================

