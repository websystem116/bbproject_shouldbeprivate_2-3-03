<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\Course;
use App\CourseType;
use App\CourseCurriculum;
use App\Curriculum;

//=======================================================================
class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get("search");
        $brand = $request->get("brand");

        $perPage = 25;

        $query = Course::query();

        // コース名
        $query->when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        });

        // ブランド
        $query->when($brand, function ($query) use ($brand) {
            $query->where('brand', $brand);
        });

        // 対象学年
        $query->when($request->grade, function ($query) use ($request) {
            $query->where(function ($query2) use ($request) {
                $query2->where('from_grade', $request->grade)
                    ->orWhere('to_grade', $request->grade);
            });
        });

        $course = $query->paginate($perPage);

        return view("course.index", compact("course"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("course.create");
    }

    public function getCourseCurriculum(Request $request)
    {
        $from_grade = $request->get("from_grade");
        $to_grade = $request->get("to_grade");

        $sHTML = '';
        if($from_grade && $to_grade){
            $curriculums = Curriculum::select('id', 'name')
                        ->where('from_grade', $from_grade)
                        ->where('to_grade', $to_grade)
                        ->get();
            foreach($curriculums as $curriculum){
                $sHTML .= '<li>
                                <label>
                                    <input type="checkbox" name="course_curriculum[]" class="custom-control-input" value="'.$curriculum->id.'">
                                    '.$curriculum->name.'
                                </label>
                            </li>';
            }
        }
        return response($sHTML);
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
                "brand" => "required",
                "name" => "required|max:40",
                "from_grade" => "required",
                "to_grade" => "required",
            ],
            [
                "brand.required" => "ブランドを選択してください。",
                "name.required" => "コース名を入力してください。",
                "name.max" => "コース名は40文字以内で入力してください。",
                "from_grade.required" => "開始学年を選択してください。",
                "to_grade.required" => "終了学年を選択してください。",
            ]
        );

        $brand = $request->input('brand');
        $name = $request->input('name');
        $from_grade = $request->input('from_grade');
        $to_grade = $request->input('to_grade');

        // コースマスター登録
        $course = Course::create([
            'brand' => $brand,
            'name' => $name,
            'from_grade' => $from_grade,
            'to_grade' => $to_grade,
        ]);
        if($course){
            // コース種別登録
            $add_course_type_count = $request->input('add_course_type_count');
            $add_course_type_count = intval($add_course_type_count);
            for($no=1; $no<=$add_course_type_count; $no++){
                $course_type_name = $request->input('course_type_name_'.$no);
                $course_type_show_pulldown = $request->input('course_type_show_pulldown_'.$no);
                if($course_type_name){
                    $course_type = CourseType::create([
                        'course_id' => $course->id,
                        'type_name' => $course_type_name,
                        'show_pulldown' => $course_type_show_pulldown == '1'?1:0,
                    ]);
                }
            }

            // 提供教科登録
            $course_curriculums = $request->input('course_curriculum');
            foreach($course_curriculums as $course_curriculum){
                $course_curriculum = CourseCurriculum::create([
                    'course_id' => $course->id,
                    'curriculum_id' => $course_curriculum,
                ]);
            }
        }

        return redirect("/shinzemi/course")->with("flash_message", "データが登録されました。");
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
        $course = Course::findOrFail($id);
        return view("course.show", compact("course"));
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

        $course = Course::findOrFail($id);
        $curriculums = [];
        if($course){
            $curriculums = Curriculum::select('id', 'name')
                        ->where('from_grade', $course->from_grade)
                        ->where('to_grade', $course->from_grade)
                        ->get();
        }

        return view("course.edit", compact("course", "curriculums", "url_for_back"));
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
        $this->validate(
            $request,
            [
                "brand" => "required",
                "name" => "required|max:40",
                "from_grade" => "required",
                "to_grade" => "required",
            ],
            [
                "brand.required" => "ブランドを選択してください。",
                "name.required" => "コース名を入力してください。",
                "name.max" => "コース名は40文字以内で入力してください。",
                "from_grade.required" => "開始学年を選択してください。",
                "to_grade.required" => "終了学年を選択してください。",
            ]
        );

        $brand = $request->input('brand');
        $name = $request->input('name');
        $from_grade = $request->input('from_grade');
        $to_grade = $request->input('to_grade');

        $course = Course::findOrFail($id);
        if($course){
            $course->brand = $brand;
            $course->name = $name;
            $course->from_grade = $from_grade;
            $course->to_grade = $to_grade;
            $course->save();

            // コース種別登録
            foreach($course->course_type as $old_course_type){
                $old_id = $old_course_type->id;
                $new_course_type_name = $request->input('old_course_type_name_'.$old_id);
                $new_course_type_show_pulldown = $request->input('course_type_show_pulldown_'.$old_id);
                if($new_course_type_name){ // 変更した場合、更新
                    CourseType::where('id', $old_id)
                        ->update([
                            'type_name' => $new_course_type_name,
                            'show_pulldown' => $new_course_type_show_pulldown == '1'?1:0,
                        ]);
                }else{ // 削除
                    CourseType::where('id', $old_id)
                        ->delete();
                }
            }

            $add_course_type_count = $request->input('add_course_type_count');
            $add_course_type_count = intval($add_course_type_count);
            for($no=1; $no<=$add_course_type_count; $no++){
                $course_type_name = $request->input('course_type_name_'.$no);
                $course_type_show_pulldown = $request->input('course_type_show_pulldown_'.$no);
                if($course_type_name){
                    $course_type = CourseType::create([
                        'course_id' => $course->id,
                        'type_name' => $course_type_name,
                        'show_pulldown' => $course_type_show_pulldown == '1'?1:0,
                    ]);
                }
            }

            // 提供教科登録
            // delete old data
            CourseCurriculum::where('course_id', $course->id)->delete();
            $course_curriculums = $request->input('course_curriculum');
            foreach($course_curriculums as $course_curriculum){
                $course_curriculum = CourseCurriculum::create([
                    'course_id' => $course->id,
                    'curriculum_id' => $course_curriculum,
                ]);
            }
        }

        // 同じページに戻る
        // $before_url = url()->previous();
        // return redirect($before_url)->with("flash_message", "データが更新されました。");

        $url = session("url");
        session()->forget("url");

        if (strpos($url, "course") !== false) {
            return redirect($url)->with("flash_message", "データが更新されました。");
        }

        if (strpos($url, "course") == false) {
            return redirect("/shinzemi/course")->with("flash_message", "データが更新されました。");
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
        Course::destroy($id);
        CourseType::where('course_id', $id)->delete();
        CourseCurriculum::where('course_id', $id)->delete();
        return redirect("/shinzemi/course")->with("flash_message", "データが削除されました。");
    }
}
    //=======================================================================