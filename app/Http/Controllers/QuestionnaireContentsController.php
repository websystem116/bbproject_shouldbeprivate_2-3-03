<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Validate;
use DB;
use App\QuestionnaireContent;
use App\SchoolBuilding;

use App\QuestionnaireDecision;


// pdf使用
// use setasign\Fpdi\TcpdfFpdi;

// Excel出力
// use phpspreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpParser\Node\Stmt\Foreach_;

//=======================================================================
class QuestionnaireContentsController extends Controller
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
            $questionnaire_content = QuestionnaireContent::where("id", "LIKE", "%$keyword%")->orWhere("title", "LIKE", "%$keyword%")
                ->orWhere("summary", "LIKE", "%$keyword%")

                // orderByでソート 最新のものが上に来るようにする
                ->orderBy("id", "desc")
                ->paginate($perPage);
        } else {
            $questionnaire_content = QuestionnaireContent::orderBy("id", "desc")->paginate($perPage);
        }
        return view("questionnaire_content.index", compact("questionnaire_content"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("questionnaire_content.create");
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
            "title" => "required|max:30", //string('title',30)->nullable()
            "summary" => "nullable|max:200", //string('summary',200)->nullable()
        ], [
            "title.required" => "タイトルは必須です。",
            "title.max" => "タイトルは30文字以内で入力してください。",
            "summary.max" => "概要は200文字以内で入力してください。",
        ]);

        $requestData = $request->all();

        QuestionnaireContent::create($requestData);

        return redirect("/shinzemi/questionnaire_content")->with("flash_message", "登録しました");
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
        $questionnaire_content = QuestionnaireContent::findOrFail($id);
        return view("questionnaire_content.show", compact("questionnaire_content"));
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
        $questionnaire_content = QuestionnaireContent::findOrFail($id);

        return view("questionnaire_content.edit", compact("questionnaire_content"));
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
            "title" => "required|max:30", //string('title',30)
            "summary" => "nullable|max:200", //string('summary',200)->nullable()
            // "question1" => "nullable|max:40", //string('question1',40)->nullable()
            // "question1_compensation" => "nullable|digits:3", //integer('question1_compensation',3)->nullable()
            // "question1_choice1" => "nullable|max:40", //string('question1_choice1',40)->nullable()
            // "question1_choice2" => "nullable|max:40", //string('question1_choice2',40)->nullable()
            // "question1_choice3" => "nullable|max:40", //string('question1_choice3',40)->nullable()
            // "question1_choice4" => "nullable|max:40", //string('question1_choice4',40)->nullable()
            // "question2" => "nullable|max:40", //string('question2',40)->nullable()
            // "question2_compensation" => "nullable|digits:3", //integer('question2_compensation',3)->nullable()
            // "question2_choice1" => "nullable|max:40", //string('question2_choice1',40)->nullable()
            // "question2_choice2" => "nullable|max:40", //string('question2_choice2',40)->nullable()
            // "question2_choice3" => "nullable|max:40", //string('question2_choice3',40)->nullable()
            // "question2_choice4" => "nullable|max:40", //string('question2_choice4',40)->nullable()
            // "question3" => "nullable|max:40", //string('question3',40)->nullable()
            // "question3_compensation" => "nullable|digits:3", //integer('question3_compensation',3)->nullable()
            // "question3_choice1" => "nullable|max:40", //string('question3_choice1',40)->nullable()
            // "question3_choice2" => "nullable|max:40", //string('question3_choice2',40)->nullable()
            // "question3_choice3" => "nullable|max:40", //string('question3_choice3',40)->nullable()
            // "question3_choice4" => "nullable|max:40", //string('question3_choice4',40)->nullable()
            // "question4_compensation" => "nullable|digits:3", //integer('question4_compensation',3)->nullable()
            // "question4_choice1" => "nullable|max:40", //string('question4_choice1',40)->nullable()
            // "question4_choice2" => "nullable|max:40", //string('question4_choice2',40)->nullable()
            // "question4_choice3" => "nullable|max:40", //string('question4_choice3',40)->nullable()
            // "question4_choice4" => "nullable|max:40", //string('question4_choice4',40)->nullable()
            // "question5" => "nullable|max:40", //string('question5',40)->nullable()
            // "question5_compensation" => "nullable|digits:3", //integer('question5_compensation',3)->nullable()
            // "question5_choice1" => "nullable|max:40", //string('question5_choice1',40)->nullable()
            // "question5_choice2" => "nullable|max:40", //string('question5_choice2',40)->nullable()
            // "question5_choice3" => "nullable|max:40", //string('question5_choice3',40)->nullable()
            // "question5_choice4" => "nullable|max:40", //string('question5_choice4',40)->nullable()
            // "question6" => "nullable|max:40", //string('question6',40)->nullable()
            // "question6_compensation" => "nullable|digits:3", //integer('question6_compensation',3)->nullable()
            // "question6_choice1" => "nullable|max:40", //string('question6_choice1',40)->nullable()
            // "question6_choice2" => "nullable|max:40", //string('question6_choice2',40)->nullable()
            // "question6_choice3" => "nullable|max:40", //string('question6_choice3',40)->nullable()
            // "question6_choice4" => "nullable|max:40", //string('question6_choice4',40)->nullable()
            // "question7" => "nullable|max:40", //string('question7',40)->nullable()
            // "question7_compensation" => "nullable|digits:3", //integer('question7_compensation',3)->nullable()
            // "question7_choice1" => "nullable|max:40", //string('question7_choice1',40)->nullable()
            // "question7_choice2" => "nullable|max:40", //string('question7_choice2',40)->nullable()
            // "question7_choice3" => "nullable|max:40", //string('question7_choice3',40)->nullable()
            // "question7_choice4" => "nullable|max:40", //string('question7_choice4',40)->nullable()

        ], [
            "title.required" => "タイトルを入力してください",
            "title.max" => "タイトルは30文字以内で入力してください",
            "summary.max" => "概要は200文字以内で入力してください",
        ]);

        $requestData = $request->all();

        $questionnaire_content = QuestionnaireContent::findOrFail($id);
        $questionnaire_content->update($requestData);

        return redirect("/shinzemi/questionnaire_content")->with("flash_message", "更新しました");
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

        QuestionnaireDecision::where('questionnaire_contents_id', $id)->delete();

        QuestionnaireContent::destroy($id);

        return redirect("/shinzemi/questionnaire_content")->with("flash_message", "削除しました。");
    }

    // アンケ―ト用紙Excel出力
    public function export_questionnaire_papers(Request $request)
    {
        $requestData = $request->all();

        $school_building_id = $request->school_building_id;
        $questionnaire_content_id = $request->questionnaire_content_id;
        $questionnaire_content = QuestionnaireContent::findOrFail($questionnaire_content_id);
        $year_month = $questionnaire_content->month;

        if ($year_month) {
            $date = explode('-', $year_month);
            $year = $date[0];
            $month = $date[1];
            if ($month < 4) {
                $year--;
            }
        }

        //印刷枚数
        $number_of_sheets = $request->number_of_sheets;


        require_once('/var/www/html/shinzemi/lib/tcpdf/tcpdf.php');
        require_once('/var/www/html/shinzemi/lib/tcpdf/fpdi/autoload.php');

        // pdfのオブジェクト作成
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();

        $pdf->setPrintHeader(false);
        $pdf->SetCellPadding(0); // セルパディングの設定
        $pdf->SetAutoPageBreak(false); // 自動改ページを無効
        $pdf->setPrintHeader(false); // ページヘッダ無効
        $pdf->setPrintFooter(false); // ページフッタ無効


        // 0詰め
        $school_building_id = str_pad($school_building_id, 2, '0', STR_PAD_LEFT);

        // // シートのコピーをとり、シート名を設定
        for ($i = 0; $i < $number_of_sheets; $i++) {

            $pdf->setSourceFile("receipt.pdf");
            $pdf->AddPage();
            $tpl = $pdf->importPage(1);
            $pdf->useTemplate($tpl);

            $pdf->SetMargins(0, 0, 0); // 上左右マージンの設定

            $j = $i + 1;
            $j = str_pad($j, 3, '0', STR_PAD_LEFT);

            $pdf->SetFont('kozgopromedium', '', 20);

            // 年月挿入
            $pdf->Text(10, 22, $year);
            $pdf->Text(81, 22, $month);

            // $today = date("Y年m月d日");
            $pdf->Text(157, 36, $school_building_id . $j);

            // 右下にページ数を表示
            $pdf->SetFont('kozgopromedium', '', 15);
            $pdf->text(157, -18, "Page" . $j);

            // 左下にページ数を表示
            $pdf->SetFont('kozgopromedium', '', 15);
            $pdf->text(10, -18, "Ver001");
        }

        ob_end_clean();
        $pdf->Output("questionnaire_format.pdf", "I");
    }

    public function form_questionnaire_papers()
    {

        $questionnaire_contents = array();

        $questionnaire_contents_collection = QuestionnaireContent::all();
        foreach ($questionnaire_contents_collection as $questionnaire_content) {
            if ($questionnaire_content->questionnaire_decisions->isEmpty()) {
                $questionnaire_contents[] = $questionnaire_content;
            }
        }


        $school_buildings = SchoolBuilding::all();


        return view("questionnaire_content.form_questionnaire_papers", compact("questionnaire_contents", 'school_buildings'));
    }
}
