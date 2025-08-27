<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\SubjectTeachersHistorie;

    //=======================================================================
    class SubjectTeachersHistoriesController extends Controller
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

				// ----------------------------------------------------
				// -- QueryBuilder: SELECT [subject_teachers_histories]--
				// ----------------------------------------------------
				$subject_teachers_historie = DB::table("subject_teachers_histories")
				->orWhere("subject_teachers_histories.questionnaire_contents_id", "LIKE", "%$keyword%")->orWhere("subject_teachers_histories.school_year", "LIKE", "%$keyword%")->orWhere("subject_teachers_histories.classification_code_class", "LIKE", "%$keyword%")->orWhere("subject_teachers_histories.item_no_class", "LIKE", "%$keyword%")->orWhere("subject_teachers_histories.user_id", "LIKE", "%$keyword%")->select("*")->addSelect("subject_teachers_histories.id")->paginate($perPage);
            } else {
                    //$subject_teachers_historie = SubjectTeachersHistorie::paginate($perPage);
				// ----------------------------------------------------
				// -- QueryBuilder: SELECT [subject_teachers_histories]--
				// ----------------------------------------------------
				$subject_teachers_historie = DB::table("subject_teachers_histories")
				->select("*")->addSelect("subject_teachers_histories.id")->paginate($perPage);
            }
            return view("subject_teachers_historie.index", compact("subject_teachers_historie"));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\View\View
         */
        public function create()
        {
            return view("subject_teachers_historie.create");
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
				"questionnaire_contents_id" => "nullable|integer", //integer('questionnaire_contents_id')->nullable()
				"school_year" => "nullable|max:2", //string('school_year',2)->nullable()
				"classification_code_class" => "nullable|max:4", //string('classification_code_class',4)->nullable()
				"item_no_class" => "nullable|max:2", //string('item_no_class',2)->nullable()
				"user_id" => "nullable|digits:3", //integer('user_id',3)->nullable()

            ]);
            $requestData = $request->all();

            SubjectTeachersHistorie::create($requestData);

            return redirect("/shinzemi/subject_teachers_historie")->with("flash_message", "subject_teachers_historie added!");
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
            //$subject_teachers_historie = SubjectTeachersHistorie::findOrFail($id);

				// ----------------------------------------------------
				// -- QueryBuilder: SELECT [subject_teachers_histories]--
				// ----------------------------------------------------
				$subject_teachers_historie = DB::table("subject_teachers_histories")
				->select("*")->addSelect("subject_teachers_histories.id")->where("subject_teachers_histories.id",$id)->first();
            return view("subject_teachers_historie.show", compact("subject_teachers_historie"));
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
            $subject_teachers_historie = SubjectTeachersHistorie::findOrFail($id);

            return view("subject_teachers_historie.edit", compact("subject_teachers_historie"));
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
				"questionnaire_contents_id" => "nullable|integer", //integer('questionnaire_contents_id')->nullable()
				"school_year" => "nullable|max:2", //string('school_year',2)->nullable()
				"classification_code_class" => "nullable|max:4", //string('classification_code_class',4)->nullable()
				"item_no_class" => "nullable|max:2", //string('item_no_class',2)->nullable()
				"user_id" => "nullable|digits:3", //integer('user_id',3)->nullable()

            ]);
            $requestData = $request->all();

            $subject_teachers_historie = SubjectTeachersHistorie::findOrFail($id);
            $subject_teachers_historie->update($requestData);

            return redirect("/shinzemi/subject_teachers_historie")->with("flash_message", "subject_teachers_historie updated!");
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
            SubjectTeachersHistorie::destroy($id);

            return redirect("/shinzemi/subject_teachers_historie")->with("flash_message", "subject_teachers_historie deleted!");
        }
    }
    //=======================================================================

