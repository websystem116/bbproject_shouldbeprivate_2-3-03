<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Curriculum;
//=======================================================================
class CurriculumsController extends Controller
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

        $query = Curriculum::query();

        // 教科名
        $query->when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        });

        // 対象学年
        $query->when($request->grade, function ($query) use ($request) {
            $query->where(function ($query2) use ($request) {
                $query2->where('from_grade', $request->grade)
                    ->orWhere('to_grade', $request->grade);
            });
        });

        $curriculum = $query->paginate($perPage);

        return view("curriculum.index", compact("curriculum"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("curriculum.create");
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
        $this->validate(
            $request,
            [
                "from_grade" => "required",
                "to_grade" => "required",
            ],
            [
                "from_grade.required" => "開始学年を入力してください。",
                "to_grade.required" => "終了学年を入力してください。",
            ]
        );
        $from_grade = $request->input('from_grade');
        $to_grade = $request->input('to_grade');
        $curriculum_names = $request->input('curriculum_name');

        foreach($curriculum_names as $curriculum_name){
            $curriculum = Curriculum::create([
                'name' => $curriculum_name,
                'from_grade' => $from_grade,
                'to_grade' => $to_grade,
            ]);
        }

        return redirect("/shinzemi/curriculum")->with("flash_message", "データが登録されました。");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    // public function show($id)
    // {
    //     $product = Product::findOrFail($id);
    //     return view("curriculum.show", compact("product"));
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $before_url = url()->previous();
        $current_url = url()->current();
        if ($before_url == $current_url) {
            // validationなどで戻ってきた場合（編集から編集へ）
            $url_for_back = session()->get("url");
        } else {
            // 通常の遷移の場合(一覧から編集へ)
            $url_for_back = $before_url;
            session(["url" => $before_url]);
        }

        $curriculum = Curriculum::findOrFail($id);
        $curriculums = [];
        if($curriculum){
            $curriculum_names = Curriculum::select('id', 'name')
                    ->where('from_grade', $curriculum->from_grade)
                    ->where('to_grade', $curriculum->to_grade)
                    ->get();
        }


        return view("curriculum.edit", compact("curriculum", "curriculum_names", "url_for_back"));
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
            "from_grade" => "required",
            "to_grade" => "required",
        ], [
            "from_grade.required" => "開始学年を入力してください。",
            "to_grade.required" => "終了学年を入力してください。",
        ]);

        $from_grade = $request->input('from_grade');
        $to_grade = $request->input('to_grade');

        $curriculum = Curriculum::findOrFail($id);
        if($curriculum){
           $old_curriculums = Curriculum::select('id', 'name')
                    ->where('from_grade', $curriculum->from_grade)
                    ->where('to_grade', $curriculum->to_grade)
                    ->get();
            
            foreach($old_curriculums as $old_curriculum){
                $old_id = $old_curriculum->id;
                $new_curriculum_name = $request->input('curriculum_name_'.$old_id);
                if($new_curriculum_name){ // 変更した場合、更新
                    Curriculum::where('id', $old_id)
                        ->update(['name' => $new_curriculum_name]);
                }else{ // 削除
                    Curriculum::where('id', $old_id)
                        ->delete();
                }
            }
        }

        // 新しいデータを追加
        $curriculum_names = $request->input('curriculum_name');
        if(!empty($curriculum_names)){
            foreach($curriculum_names as $curriculum_name){
                $curriculum = Curriculum::create([
                    'name' => $curriculum_name,
                    'from_grade' => $from_grade,
                    'to_grade' => $to_grade,
                ]);
            }
        }

        // 同じページに戻る
        // $before_url = url()->previous();
        // return redirect($before_url)->with("flash_message", "データが更新されました。");

        $url = session("url");
        session()->forget("url");

        if (strpos($url, "curriculum") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        }

        if (strpos($url, "curriculum") == false) {
            return redirect("/shinzemi/curriculum")->with("flash_message", "データが更新されました。");
        }
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
        Curriculum::destroy($id);

        return redirect("/shinzemi/curriculum")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================