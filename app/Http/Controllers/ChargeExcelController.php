<?php

namespace App\Http\Controllers;

// ini_set('display_errors', "On");
ini_set("memory_limit", "2048M");

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;
use Validate;
use DB;
use App\Sale;
use App\SalesDetail;
use App\BeforeJukuSales;
use App\SchoolBuilding;
use App\School;
use App\JobDescription;
use App\DailySalary;
use App\Student;
use App\Product;
use App\Discount;
use App\ChargeDetail;
use App\Charge;
use App\Payment;
use App\Company;
use App\ChargeProgress;
use App\InvoiceComment;
use App\DivisionCode;

// use phpspreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpParser\Node\Stmt\Foreach_;

// 罫線引きたい
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;


//python 実行用
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

//=======================================================================
class ChargeExcelController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request)
	{
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		return view("charge_excel.index", compact("school_buildings"));
	}


	// 	return view("salary.create");
	// }
	public function store(Request $request)
	{
		$this->validate($request, [
			"code" => "nullable|max:4", //string('code',4)->nullable()
			"name" => "required|max:15", //string('name',15)->nullable()
			"name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
		]);
		$requestData = $request->all();

		DailySalary::create($requestData);

		return redirect("/shinzemi/salary")->with("flash_message", "データが登録されました。");
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */

	public function export_charge(Request $request)
	{
		$chargeIds = $request->input('charge_ids');
		$convenience_store_flg = $request->input('convenience_store_flg');

		// hidden フィールドの値が 1 の場合、コンビニ振込用の処理を行う
		if ($convenience_store_flg == '1') {
			Charge::whereIn('id', $chargeIds)->update(['convenience_store_flg' => 1]);
		}

		//Excel出力
		$reader = new XlsxReader();
		// $spreadsheet = $reader->load(storage_path() . '/app/template/charge.xlsx'); //template.xlsx 読込
		// NOTE: 一時に的に生徒パスワードを付与したものに変更
		$spreadsheet = $reader->load(storage_path() . '/app/template/charge_250222.xlsx');
		$sheet = $spreadsheet->getActiveSheet();
		$requestData = $request->all();
		// 売上区分マスタ
		$division_codes = DivisionCode::all()->pluck('name', 'id');

		$charges = Charge::whereIn('id', $request->charge_ids)->with('student')->get()->sort(function ($first, $second) {
			if ($first->student->school_building_id == $second->student->school_building_id) {
				if ($first->student->grade == $second->student->grade) {
					return $first->student->juku_start_date < $second->student->juku_start_date ? -1 : 1;
				}
				return $first->student->grade < $second->student->grade ? -1 : 1;
			}
			return $first->student->school_building_id < $second->student->school_building_id ? -1 : 1;
		});
		$company = Company::find(1);
		$invoice_comment = InvoiceComment::all();
		$company_address = "〒" . $company->zipcode . "\n　";
		$company_address .= $company->address1;
		$company_address .= $company->address2 . "\n　";
		$company_address .= $company->address3 . "\n　";
		$company_address .= "TEL " . $company->tel . "\n　";
		$company_address .= "登録番号T7150001008095";
		$company_name = $company->name;
		$sheet->setCellValue('AI3', '発行日　' . date('Y年m月d日'));
		$sheet->setCellValue('X5', $company_name);
		$sheet->setCellValue('Y6', $company_address);

		foreach ($charges as $charge) {
			$charge_month = $charge->charge_month;
			$student = $charge->student;
			if ($convenience_store_flg == 1) {
				$comments = $invoice_comment->where('division', '3');
				$comment = $comments[2]->comment;
			} else {
				switch ($student->payment_methods) {
					case '1':
						$comments = $invoice_comment->where('division', '1');
						$comment = $comments[0]->comment;
						break;
					case '2':
						$comments = $invoice_comment->where('division', '2');
						$comment = $comments[1]->comment;
						break;
					case '3':
						$comments = $invoice_comment->where('division', '4');
						$comment = $comments[3]->comment;
						break;
					case '4':
						$comments = $invoice_comment->where('division', '4');
						$comment = $comments[3]->comment;
						break;
				}
			}
			$school_building = $student->schoolbuilding;
			$charge_details = $charge->charge_detail;
			$full_name = $student->surname . " " . $student->name;
			$sheetname = $charge->charge_month . $student->surname . $student->name;
			$clonedWorksheet = clone $spreadsheet->getSheetByName('Sheet1');
			$clonedWorksheet->setTitle($sheetname);
			$spreadsheet->addSheet($clonedWorksheet);
			$sheet = $spreadsheet->getSheetByName($sheetname); //weatherシート取得
			$student_address = "〒" . $student->zip_code . "\n";
			$student_address .= "　" . $student->address1 . "\n";
			$student_address .= "　" . $student->address2;
			$student_address .= $student->address3;
			$parent_fullname = $student->parent_surname . "　" . $student->parent_name . " 様";
			$school_years = config('const.school_year')[$student->grade];
			$student_fullname = $student->surname . "　" . $student->name;
			$discount = "";
			if (!empty($student->discount->name)) {
				$discount = $student->discount->name;
			}

			$sheet->setCellValue('B1', $student_address);
			$sheet->setCellValue('C15', $comment);

			$sheet->setCellValue('M4', $parent_fullname);
			$sheet->setCellValue('U2', date("Y年n月分", strtotime($charge_month . "-01")));

			$sheet->setCellValue('D9', $school_building->name);
			$sheet->setCellValue('D10', $school_years);
			$sheet->setCellValue('D12', $student->student_no);
			$sheet->setCellValue('D13', $student_fullname);
			$sheet->setCellValue('F14', $student->initial_password ?? '');
			$sheet->getStyle('F14')->getFont()->setBold(true);
			$sheet->getStyle('K20:AI20')->getNumberFormat()
				->setFormatCode('#,##0');

			$sheet->setCellValue('K20', $charge->carryover);
			$sheet->setCellValue('P20', $charge->month_sum);
			$sheet->setCellValue('U20', $charge->month_tax_sum);
			$sheet->setCellValue('Z20', $charge->prepaid);
			$charge_sum = $charge->sum;
			$deposit = 0;
			if ($charge->sum < 0) {
				$charge_sum = 0;
				$deposit = 0 - $charge->sum;
			}
			$sheet->setCellValue('AE20', $charge_sum);
			$row = 23;
			foreach ($charge_details as $charge_detail) {
				$product = $charge_detail->product;
				// $division_code = config('const.division_code')[$product->division_code];
				if (empty($product->division_code)) {
					// dump($charge_detail);
					// dump($student);
					// dd($product);
					return redirect()->back()->with('flash_message', '該当する商品情報がありません。');
				}
				$division_code = $division_codes[$product->division_code];
				if ($row == 23) {
					$sheet->setCellValue('D11', $charge_detail->product->name);
				}

				$sheet->getStyle('X' . $row . ':AI' . $row)->getNumberFormat()
					->setFormatCode('#,##0');
				$sheet->setCellValue('B' . $row, $division_code);
				$sheet->mergeCells('B' . $row . ":H" . $row);
				$sheet->setCellValue('I' . $row, $charge_detail->product_name);
				$sheet->mergeCells('I' . $row . ":W" . $row);
				$sheet->setCellValue('X' . $row, $charge_detail->price);
				$sheet->mergeCells('X' . $row . ":AA" . $row);
				$sheet->setCellValue('AB' . $row, $charge_detail->tax);
				$sheet->mergeCells('AB' . $row . ":AE" . $row);
				$sheet->setCellValue('AF' . $row, $charge_detail->price + $charge_detail->tax);
				$sheet->mergeCells('AF' . $row . ":AI" . $row);
				$row++;
			}
			$sheet->getStyle('B23:AI' . $row)
				->getBorders()
				->getAllBorders()
				->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			$sheet->setCellValue('B' . $row, "合計");
			$sheet->mergeCells('B' . $row . ":W" . $row);
			$sheet->getStyle('X' . $row . ':AI' . $row)->getNumberFormat()
				->setFormatCode('#,##0');

			$sheet->setCellValue('X' . $row, "=SUM(X23:X" . ($row - 1) . ")");
			$sheet->mergeCells('X' . $row . ":AA" . $row);

			$sheet->setCellValue('AB' . $row, "=SUM(AB23:AB" . ($row - 1) . ")");
			$sheet->mergeCells('AB' . $row . ":AE" . $row);

			$sheet->setCellValue('AF' . $row, "=SUM(AF23:AF" . ($row - 1) . ")");
			$sheet->mergeCells('AF' . $row . ":AI" . $row);

			// 高さ:row=23から今のrowまで 幅:BからAFまで　太い外枠の罫線を設定する
			$sheet->getStyle('B22:AI' . $row)
				->getBorders()
				->getOutline()
				->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

			$row++;
			$row++;
			$sheet->getStyle('J' . $row)->getNumberFormat()
				->setFormatCode('#,##0');

			$sheet->setCellValue('B' . $row, "今回の合計請求額は");
			$sheet->setCellValue('J' . $row, "=AE20");
			$sheet->mergeCells('J' . $row . ":M" . $row);
			$sheet->setCellValue('N' . $row, "円でございます。");
			if ($deposit > 0) {
				$row++;
				$sheet->setCellValue('B' . $row, "お預かり金は");
				$sheet->setCellValue('J' . $row, $deposit);
				$sheet->mergeCells('J' . $row . ":M" . $row);
				$sheet->setCellValue('N' . $row, "円でございます。");
			}
			$sheet->getStyle("A18:AI" . $row)->getFont()->setSize(9);
			if ($discount != "") {
				$row++;
				$sheet->setCellValue('B' . $row, "割引適用：" . $discount);
				$sheet->getStyle("B" . $row)->getFont()->setSize(9);
			}
			$spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.4);
			$spreadsheet->getActiveSheet()->getPageSetup()
				->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			$spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:AI' . $row);
			$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);
		}

		$sheetIndex = $spreadsheet->getIndex(
			$spreadsheet->getSheetByName('Sheet1')
		);
		$spreadsheet->removeSheetByIndex($sheetIndex);


		$writer = new Xlsx($spreadsheet);
		$writer->save(storage_path() . '/app/excel/charge' . auth()->user()->id . '.xlsx');

		$orderNote = '/var/www/html/shinzemi/storage/app/excel/charge' . auth()->user()->id . '.xlsx';
		$command = "export HOME=/tmp && libreoffice --headless --convert-to pdf --outdir /var/www/html/shinzemi/storage/app/excel/ /var/www/html/shinzemi/storage/app/excel/ " . $orderNote;

		exec($command);
		$DLFileName = '請求書' . '.pdf';

		$file_path_excel = '/var/www/html/shinzemi/storage/app/excel/charge' . auth()->user()->id . '.pdf';
		$file_path_pdf = '/var/www/html/shinzemi/storage/app/excel/charge' . auth()->user()->id . '.pdf';

		//タイプをダウンロードと指定
		header('Content-Type: application/pdf');

		//ファイルのサイズを取得してダウンロード時間を表示する
		header('Content-Length: ' . filesize($file_path_pdf));
		header('Content-Disposition: attachment; filename="' . $DLFileName . '"');
		//ファイルを読み込んでダウンロード
		readfile($file_path_pdf);

		//保存したエクセルとpdf削除
		// unlink($file_path_pdf);
		// unlink($file_path_excel);

		ob_end_clean(); //バッファ消去
		exit;
	}


	public function export_school_building_sales(Request $request)
	{
		// 校舎別売上明細

		$requestData = $request->all();

		if (empty($requestData['sale_month'])) {
			return redirect()->back()->withInput()->with('flash_message', '年月を入力してください。');
		}

		$sales = Sale::when(!empty($requestData['school_building_id']), function ($query) use ($requestData) {
			return $query->where('school_building_id', $requestData['school_building_id']);
		})->when(!empty($requestData['sale_month']), function ($query) use ($requestData) {
			return $query->where('sale_month', $requestData['sale_month']);
		})->orderBy('school_building_id', 'asc')->get();

		$before_juku_sales = BeforeJukuSales::leftJoin('before_students', function ($join) {
			$join->on('before_students.before_student_no', '=', 'before_juku_sales.before_student_no');
		})->when(!empty($requestData['school_building_id']), function ($query) use ($requestData) {
			return $query->where('before_juku_sales.school_building_id', $requestData['school_building_id']);
		})->when(!empty($requestData['sale_month']), function ($query) use ($requestData) {
			return $query->where('before_juku_sales.sales_date', str_replace("-", "", $requestData['sale_month']));
		})->orderBy('before_juku_sales.school_building_id', 'asc')->get();
		$year = date('Y', strtotime($requestData['sale_month']));
		$month = date('m', strtotime($requestData['sale_month']));

		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/school_building_sales.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', $year);
		$sheet->setCellValue('G1', $month);
		$sheet->setCellValue('BA1', '作成日:　　' . date('Y/m/d'));

		$consumption_tax = 1 + (config('const.consumption_tax') / 100);
		$used_id = array();
		foreach ($sales as $list) {
			$lists[$list->school_building_id][$list->student_no]['before_juku_sales'] = 0;

			$lists[$list->school_building_id]['school_building_name'] = $list->school_building->name;
			$lists[$list->school_building_id][$list->student_no]['school_year'] = config('const.school_year')[$list->school_year];
			$full_name = "削除された生徒";
			if (!empty($list->student)) {
				$full_name = $list->student->full_name;
				$before_juku_sale = $before_juku_sales->where('surname', $list->student->surname)->where('name', $list->student->name);
				foreach ($before_juku_sale as $before_juku_sale_single) {
					$lists[$list->school_building_id][$list->student_no]['before_juku_sales'] += $before_juku_sale_single->subtotal;
					$used_id[] = $before_juku_sale_single->id;
				}
			}
			$lists[$list->school_building_id][$list->student_no]['student_name'] = $full_name;
			$sales_details = $list->sales_detail;
			$lists[$list->school_building_id][$list->student_no]['tuition_fee'] = 0;
			$lists[$list->school_building_id][$list->student_no]['course_fee'] = 0;
			$lists[$list->school_building_id][$list->student_no]['entrance_fee'] = 0;
			$lists[$list->school_building_id][$list->student_no]['monthly_cost'] = 0;
			$lists[$list->school_building_id][$list->student_no]['teaching_materials_fee'] = 0;
			$lists[$list->school_building_id][$list->student_no]['test_fee'] = 0;
			$lists[$list->school_building_id][$list->student_no]['examination_fee'] = 0;
			$lists[$list->school_building_id][$list->student_no]['other_fee'] = 0;
			$lists[$list->school_building_id][$list->student_no]['subsidy'] = 0;

			foreach ($sales_details as $sale_detail) {

				switch ($sale_detail->sales_category) {
					case 1:
						$lists[$list->school_building_id][$list->student_no]['tuition_fee'] += $sale_detail->subtotal;
						break;
					case 2:
						$lists[$list->school_building_id][$list->student_no]['tuition_fee'] += $sale_detail->subtotal;
						break;
					case 3:
						$lists[$list->school_building_id][$list->student_no]['course_fee'] += $sale_detail->subtotal;
						break;
					case 4:
						$lists[$list->school_building_id][$list->student_no]['entrance_fee'] += $sale_detail->subtotal;
						break;
					case 5:
						$lists[$list->school_building_id][$list->student_no]['monthly_cost'] += $sale_detail->subtotal;
						break;
					case 6:
						$lists[$list->school_building_id][$list->student_no]['teaching_materials_fee'] += $sale_detail->subtotal;
						break;
					case 7:
						$lists[$list->school_building_id][$list->student_no]['test_fee'] += $sale_detail->subtotal;
						break;
					case 8:
						$lists[$list->school_building_id][$list->student_no]['examination_fee'] += $sale_detail->subtotal;
						break;
					case 9:
						$lists[$list->school_building_id][$list->student_no]['other_fee'] += $sale_detail->subtotal;
						break;
					case 10:
						$lists[$list->school_building_id][$list->student_no]['subsidy'] += $sale_detail->subtotal;
						break;
				}
			}
		}
		foreach ($before_juku_sales as $before_juku_sale) {
			if (in_array($before_juku_sale->id, $used_id) === false) {
				$full_name = "削除された生徒";
				$before_student_no = "";
				if (!empty($before_juku_sale->before_student)) {
					$full_name = $before_juku_sale->before_student->full_name;
					$grade = $before_juku_sale->before_student->grade;
					$before_student_no = "B" . $before_juku_sale->before_student_no;
				}
				$lists[$before_juku_sale->school_building_id][$before_student_no]['school_year'] = config('const.school_year')[$grade];

				$lists[$before_juku_sale->school_building_id][$before_student_no]['student_name'] = $full_name;
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['tuition_fee'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['tuition_fee'] = 0;
				}
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['course_fee'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['course_fee'] = 0;
				}
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['entrance_fee'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['entrance_fee'] = 0;
				}
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['monthly_cost'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['monthly_cost'] = 0;
				}
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['teaching_materials_fee'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['teaching_materials_fee'] = 0;
				}
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['test_fee'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['test_fee'] = 0;
				}
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['examination_fee'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['examination_fee'] = 0;
				}
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['other_fee'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['other_fee'] = 0;
				}
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['subsidy'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['subsidy'] = 0;
				}
				if (empty($lists[$before_juku_sale->school_building_id][$before_student_no]['before_juku_sales'])) {
					$lists[$before_juku_sale->school_building_id][$before_student_no]['before_juku_sales'] = 0;
				}
				$lists[$before_juku_sale->school_building_id][$before_student_no]['before_juku_sales'] += $before_juku_sale->subtotal;
			}
		}

		if (empty($lists)) {
			return redirect()->back()->with('flash_message', '該当するデータがありません。');
		}
		$before_student_flg = false;
		foreach ($lists as $school_building_id => $values) {
			$clonedWorksheet = clone $spreadsheet->getSheetByName('Sheet1');
			$sheetname = $values['school_building_name'];
			$clonedWorksheet->setTitle($sheetname);
			$spreadsheet->addSheet($clonedWorksheet);
			$sheet = $spreadsheet->getSheetByName($sheetname); //weatherシート取得
			$sheet->setCellValue('D3', $values['school_building_name']);
			$row = 6;
			foreach ($values as $student_no => $value) {
				if ($student_no != 'school_building_name') {
					if (strpos($student_no, "B") !== false && $before_student_flg == false) {
						$before_student_flg = true;
						$sheet->mergeCells('A' . $row . ":BA" . $row);
						$sheet->setCellValue('A' . $row, '入塾前生徒');
						$sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
						$row++;
					}
					$sheet->setCellValue('A' . $row, str_replace("B", "", $student_no));

					$sheet->setCellValue('D' . $row, $value['school_year']);
					$sheet->setCellValue('F' . $row, $value['student_name']);

					$sheet->setCellValue('J' . $row, $value['tuition_fee']);
					$sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('N' . $row, $value['course_fee']);
					$sheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('R' . $row, $value['entrance_fee']);
					$sheet->getStyle('R' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('V' . $row, $value['monthly_cost']);
					$sheet->getStyle('V' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('Z' . $row, $value['teaching_materials_fee']);
					$sheet->getStyle('Z' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('AD' . $row, $value['test_fee']);
					$sheet->getStyle('AD' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('AH' . $row, $value['examination_fee']);
					$sheet->getStyle('AH' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('AL' . $row, $value['other_fee']);
					$sheet->getStyle('AL' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('AP' . $row, $value['subsidy']);
					$sheet->getStyle('AP' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('AT' . $row, $value['before_juku_sales']);
					$sheet->getStyle('AT' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->setCellValue('AX' . $row, "=SUM(J" . $row . ":AW" . $row . ")");
					$sheet->getStyle('AX' . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する
					$sheet->mergeCells('A' . $row . ":C" . $row);
					$sheet->mergeCells('D' . $row . ":E" . $row);
					$sheet->mergeCells('F' . $row . ":I" . $row);
					$sheet->mergeCells('J' . $row . ":M" . $row);
					$sheet->mergeCells('N' . $row . ":Q" . $row);
					$sheet->mergeCells('R' . $row . ":U" . $row);
					$sheet->mergeCells('V' . $row . ":Y" . $row);
					$sheet->mergeCells('Z' . $row . ":AC" . $row);
					$sheet->mergeCells('AD' . $row . ":AG" . $row);
					$sheet->mergeCells('AH' . $row . ":AK" . $row);
					$sheet->mergeCells('AL' . $row . ":AO" . $row);
					$sheet->mergeCells('AP' . $row . ":AS" . $row);
					$sheet->mergeCells('AT' . $row . ":AW" . $row);
					$sheet->mergeCells('AX' . $row . ":BA" . $row);
					$sheet->getStyle('A6:BA'  . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

					// 行の高さを20に設定
					$sheet->getRowDimension($row)->setRowHeight(20);

					$row++;
				}
			}
			$sheet->setCellValue('A' . $row, "合計");
			$sheet->setCellValue('J' . $row, "=SUM(J6:J" . ($row - 1) . ")");
			$sheet->setCellValue('N' . $row, "=SUM(N6:N" . ($row - 1) . ")");
			$sheet->setCellValue('R' . $row, "=SUM(R6:R" . ($row - 1) . ")");
			$sheet->setCellValue('V' . $row, "=SUM(V6:V" . ($row - 1) . ")");
			$sheet->setCellValue('Z' . $row, "=SUM(Z6:Z" . ($row - 1) . ")");
			$sheet->setCellValue('AD' . $row, "=SUM(AD6:AD" . ($row - 1) . ")");
			$sheet->setCellValue('AH' . $row, "=SUM(AH6:AH" . ($row - 1) . ")");
			$sheet->setCellValue('AL' . $row, "=SUM(AL6:AL" . ($row - 1) . ")");
			$sheet->setCellValue('AP' . $row, "=SUM(AP6:AP" . ($row - 1) . ")");
			$sheet->setCellValue('AT' . $row, "=SUM(AT6:AT" . ($row - 1) . ")");
			$sheet->setCellValue('AX' . $row, "=SUM(AX6:AX" . ($row - 1) . ")");
			$sheet->getStyle('J' . $row . ":AX" . $row)->getNumberFormat()->setFormatCode('#,##0'); //カンマ付与に対応する

			$sheet->mergeCells('A' . $row . ":I" . $row);
			$sheet->mergeCells('J' . $row . ":M" . $row);
			$sheet->mergeCells('N' . $row . ":Q" . $row);
			$sheet->mergeCells('R' . $row . ":U" . $row);
			$sheet->mergeCells('V' . $row . ":Y" . $row);
			$sheet->mergeCells('Z' . $row . ":AC" . $row);
			$sheet->mergeCells('AD' . $row . ":AG" . $row);
			$sheet->mergeCells('AH' . $row . ":AK" . $row);
			$sheet->mergeCells('AL' . $row . ":AO" . $row);
			$sheet->mergeCells('AP' . $row . ":AS" . $row);
			$sheet->mergeCells('AT' . $row . ":AW" . $row);
			$sheet->mergeCells('AX' . $row . ":BA" . $row);
			$sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$sheet->getStyle("A6:AX" . $row)->getFont()->setSize(7);
		}

		$sheetIndex = $spreadsheet->getIndex(
			$spreadsheet->getSheetByName('Sheet1')
		);
		$spreadsheet->removeSheetByIndex($sheetIndex);

		// active sheetを一番左のシートにする
		$spreadsheet->setActiveSheetIndex(0);

		$filename = '校舎別売上明細.xlsx';
		// ダウンロード
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}
	public function export_school_building_charge(Request $request)
	{
		// 校舎別請求明細
		$requestData = $request->all();
		$import_flg['nanto'] = 0;
		$import_flg['risona'] = 0;
		$sql_data = $requestData;
		$sql_data['nanto'] = 0;
		$sql_data['risona'] = 0;
		// dd($sql_data);
		if (empty($requestData['charge_month'])) {
			return redirect()->back()->withInput()->with('flash_message', '年月を入力してください。');
		}
		if (!empty($requestData['payment_failed_flg'])) {
			$charge_progress = ChargeProgress::where('sales_month', $requestData['charge_month'])->orderBy('sales_month', 'desc')->first();
			if ($charge_progress->withdrawal_import_risona_date != NULL) {
				$import_flg['risona'] = 1;
			}
			if ($charge_progress->withdrawal_import_nanto_date != NULL) {
				$import_flg['nanto'] = 1;
			}
		}
		$charges = Charge::whereIn('student_no', function ($query) use ($sql_data) {
			$query->from('students')
				->select('students.student_no')
				->when(!empty($sql_data['school_building_id']), function ($query) use ($sql_data) {
					return $query->where('students.school_building_id', $sql_data['school_building_id']);
				})->when(!empty($sql_data['payment_failed_flg']), function ($query) use ($sql_data) {
					if ($sql_data['risona']) {
						if ($sql_data['nanto']) {
							return $query;
						} else {
							return $query->where(function ($query) {
								$query->orWhere('students.payment_methods', 2)->orWhere('students.payment_methods', 4);
							});
						}
					} else {
						if ($sql_data['nanto']) {
							return $query->where(function ($query) {
								$query->orWhere('students.payment_methods', 1)->orWhere('students.payment_methods', 3);
							});
						} else {
							return false;
						}
					}
				});
		})->when(!empty($sql_data['charge_month']), function ($query) use ($sql_data) {
			return $query->where('charge_month', $sql_data['charge_month']);
		})->whereIn('student_no', function ($query) use ($sql_data) {
			$query->from('sales_details')
				->distinct()
				->select('sales_details.student_no')
				->when(!empty($sql_data['charge_month']), function ($query) use ($sql_data) {
					return $query->where('sales_details.sale_month', $sql_data['charge_month']);
				})
				->when(!empty($sql_data['charge_month']), function ($query) use ($sql_data) {
					return $query->where('sales_details.sale_month', $sql_data['charge_month']);
				});
		})->when(!empty($sql_data['payment_failed_flg']), function ($query) use ($sql_data) {
			return $query->whereNull('withdrawal_confirmed')->where('sum', '<>', 0);
		})->get();
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/school_building_charge.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();
		$year = date('Y', strtotime($requestData['charge_month']));
		$month = date('m', strtotime($requestData['charge_month']));

		$sheet->setCellValue('A1', $year);
		$sheet->setCellValue('F1', $month);
		$sheet->setCellValue('AI1', '作成日:　　' . date('Y/m/d'));

		foreach ($charges as $list) {
			$school_building_id = $list->student->schoolbuilding->name;
			$lists[$school_building_id]['school_building_name'] = $list->student->schoolbuilding->name;
			if (!empty($list->sale->school_year)) {
				$lists[$school_building_id][$list->student_no]['school_year'] = config('const.school_year')[$list->sale->school_year];
			} else {
				$lists[$school_building_id][$list->student_no]['school_year'] = "";
			}

			$lists[$school_building_id][$list->student_no]['student_name'] = $list->student->full_name;
			$lists[$school_building_id][$list->student_no]['month_sum'] = $list->month_sum;
			$lists[$school_building_id][$list->student_no]['month_tax_sum'] = $list->month_tax_sum;
			$lists[$school_building_id][$list->student_no]['carryover'] = $list->carryover;
			$lists[$school_building_id][$list->student_no]['prepaid'] = $list->prepaid;

			$lists[$school_building_id][$list->student_no]['sum'] = $list->sum;
			$lists[$school_building_id][$list->student_no]['withdrawal_confirmed'] = $list->withdrawal_confirmed;
		}

		if (empty($lists)) {
			return redirect()->back()->withInput()->with('flash_message', '該当するデータがありません。');
		}

		foreach ($lists as $school_building_id => $values) {
			$clonedWorksheet = clone $spreadsheet->getSheetByName('Sheet1');
			$sheetname = $values['school_building_name'];
			$clonedWorksheet->setTitle($sheetname);
			$spreadsheet->addSheet($clonedWorksheet);
			$sheet = $spreadsheet->getSheetByName($sheetname);
			$row = 6;
			$sheet->setCellValue('A3', $values['school_building_name']);

			foreach ($values as $student_no => $value) {
				if ($student_no != 'school_building_name') {

					$sheet->setCellValue('A' . $row, $student_no);
					$sheet->setCellValue('E' . $row, $value['school_year']);
					$sheet->setCellValue('G' . $row, $value['student_name']);

					$sheet->setCellValue('L' . $row, $value['month_sum']);
					$sheet->setCellValue('P' . $row, $value['month_tax_sum']);
					$sheet->setCellValue('T' . $row, $value['carryover']);
					$sheet->setCellValue('X' . $row, $value['prepaid']);
					$sheet->setCellValue('AB' . $row, $value['sum']);
					if ($value['withdrawal_confirmed'] == 1) {
						$sheet->setCellValue('AF' . $row, "済");
					}
					$sheet->mergeCells('A' . $row . ":D" . $row);

					// 書式設定でセル内に収まるようにする
					$sheet->getStyle('A' . $row . ':D' . $row)
						->getAlignment()
						->setShrinkToFit(true);

					$sheet->mergeCells('E' . $row . ":F" . $row);
					$sheet->mergeCells('G' . $row . ":K" . $row);

					$sheet->mergeCells('L' . $row . ":O" . $row);
					// 右寄せLからO
					$sheet->getStyle('L' . $row . ':O' . $row)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

					// 表示形式を数値かつカンマ区切りにする
					$sheet->getStyle('L' . $row . ':O' . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');


					$sheet->mergeCells('P' . $row . ":S" . $row);
					// 右寄せPからS
					$sheet->getStyle('P' . $row . ':S' . $row)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

					// 表示形式を数値かつカンマ区切りにする
					$sheet->getStyle('P' . $row . ':S' . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$sheet->mergeCells('T' . $row . ":W" . $row);
					// 右寄せTからW
					$sheet->getStyle('T' . $row . ':W' . $row)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

					// 表示形式を数値かつカンマ区切りにする
					$sheet->getStyle('T' . $row . ':W' . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$sheet->mergeCells('X' . $row . ":AA" . $row);
					// 右寄せXからAA
					$sheet->getStyle('X' . $row . ':AA' . $row)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

					// 表示形式を数値かつカンマ区切りにする
					$sheet->getStyle('X' . $row . ':AA' . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$sheet->mergeCells('AB' . $row . ":AE" . $row);
					// 右寄せABからAE
					$sheet->getStyle('AB' . $row . ':AE' . $row)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

					// 表示形式を数値かつカンマ区切りにする
					$sheet->getStyle('AB' . $row . ':AE' . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$sheet->mergeCells('AF' . $row . ":AI" . $row);



					$row++;
				}
			}

			$sheet->setCellValue('A' . $row, '合計');
			$sheet->setCellValue('L' . $row, "=SUM(L6:L" . ($row - 1) . ")");
			$sheet->setCellValue('P' . $row, "=SUM(P6:P" . ($row - 1) . ")");
			$sheet->setCellValue('T' . $row, "=SUM(T6:T" . ($row - 1) . ")");
			$sheet->setCellValue('X' . $row, "=SUM(X6:X" . ($row - 1) . ")");
			$sheet->setCellValue('AB' . $row, "=SUM(AB6:AB" . ($row - 1) . ")");

			$sheet->mergeCells('A' . $row . ":K" . $row);

			$sheet->mergeCells('L' . $row . ":O" . $row);
			// セル形式を数値かつカンマ区切りにする
			$sheet->getStyle('L' . $row . ':O' . $row)
				->getNumberFormat()
				->setFormatCode('#,##0');

			$sheet->mergeCells('P' . $row . ":S" . $row);
			// セル形式を数値かつカンマ区切りにする
			$sheet->getStyle('P' . $row . ':S' . $row)
				->getNumberFormat()
				->setFormatCode('#,##0');

			$sheet->mergeCells('T' . $row . ":W" . $row);
			// セル形式を数値かつカンマ区切りにする
			$sheet->getStyle('T' . $row . ':W' . $row)
				->getNumberFormat()
				->setFormatCode('#,##0');

			$sheet->mergeCells('X' . $row . ":AA" . $row);
			// セル形式を数値かつカンマ区切りにする
			$sheet->getStyle('X' . $row . ':AA' . $row)
				->getNumberFormat()
				->setFormatCode('#,##0');

			$sheet->mergeCells('AB' . $row . ":AE" . $row);
			// セル形式を数値かつカンマ区切りにする
			$sheet->getStyle('AB' . $row . ':AE' . $row)
				->getNumberFormat()
				->setFormatCode('#,##0');

			$sheet->mergeCells('AF' . $row . ":AI" . $row);
			$sheet->getStyle('A6:AI'  . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		}

		$sheetIndex = $spreadsheet->getIndex($spreadsheet->getSheetByName('Sheet1'));
		$spreadsheet->removeSheetByIndex($sheetIndex);

		// active sheetを一番左のシートにする
		$spreadsheet->setActiveSheetIndex(0);

		$filename = '校舎別請求明細.xlsx';


		// ダウンロード
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function export_school_building_payment(Request $request)
	{
		// 校舎別入金明細

		$requestData = $request->all();

		if (empty($requestData['payment_month'])) {
			return redirect()->back()->withInput()->with('flash_message', '年月を入力してください。');
		}

		$payments = Payment::when(!empty($requestData['school_building_id']), function ($query) use ($requestData) {
			return $query->where('school_building_id', $requestData['school_building_id']);
		})->when(!empty($requestData['payment_month']), function ($query) use ($requestData) {
			return $query->where('sale_month',   $requestData['payment_month']);
		})->when(!empty($requestData['pay_method']), function ($query) use ($requestData) {
			return $query->where('pay_method',  $requestData['pay_method']);
		})->orderBy('school_building_id', 'asc')->get();

		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/school_building_payment.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();

		$year = date('Y', strtotime($requestData['payment_month']));
		$month = date('m', strtotime($requestData['payment_month']));


		$sheet->setCellValue('A1', $year);
		$sheet->setCellValue('G1', $month);
		$sheet->setCellValue('AW1', '作成日:　　' . date('Y/m/d'));
		if (!empty($requestData['pay_method'])) {
			$sheet->setCellValue('M1', '校舎別入金明細（' . config('const.pay_method')[$requestData['pay_method']] . ')');
		} else {
			$sheet->setCellValue('M1', '校舎別入金明細（指定なし）');
		}
		foreach ($payments as $list) {
			$lists[$list->school_building_id]['school_building_name'] = $list->school_building->name_short;
			$lists[$list->school_building_id][$list->student_id]['payment_date'][] = $list->payment_date;
			$lists[$list->school_building_id][$list->student_id]['student_name'][] = $list->student->full_name;
			$lists[$list->school_building_id][$list->student_id]['pay_method'][] = $list->pay_method;
			$lists[$list->school_building_id][$list->student_id]['summary'][] = $list->summary;
			$lists[$list->school_building_id][$list->student_id]['payment_amount'][] = $list->payment_amount;
		}

		//$listsが未定義の場合はエラーになるので、定義しておく
		if (!isset($lists)) {
			// 前の画面にリダイレクト
			return redirect()->back()->with('flash_message', '該当するデータがありません。');
		}

		foreach ($lists as $school_building_id => $values) {
			$clonedWorksheet = clone $spreadsheet->getSheetByName('Sheet1');
			$sheetname = $values['school_building_name'];
			$clonedWorksheet->setTitle($sheetname);
			$spreadsheet->addSheet($clonedWorksheet);
			$sheet = $spreadsheet->getSheetByName($sheetname); //weatherシート取得
			$sheet->setCellValue('E3', $values['school_building_name']);
			$row = 6;
			$totalAmount = 0;
			foreach ($values as $student_id => $value) {
				if ($student_id != 'school_building_name') {
					$payment_date_cnt = is_countable($value['payment_date']) ? count($value['payment_date']) : 0;
					for ($i = 0; $i < $payment_date_cnt; $i++) {
						$sheet->setCellValue('A' . $row, $value['payment_date'][$i]);
						$sheet->setCellValue('G' . $row, $student_id);
						$sheet->setCellValue('N' . $row, $value['student_name'][$i]);
						$sheet->setCellValue('Y' . $row, config('const.pay_method')[$value['pay_method'][$i]]);
						$sheet->setCellValue('AE' . $row, $value['summary'][$i]);
						$sheet->setCellValue('AR' . $row, $value['payment_amount'][$i]);

						// 校舎別の合計金額を計算
						$totalAmount += $value['payment_amount'][$i];

						Payment::mergeCells($sheet, $row);

						// AからFまでのセルの書式を設定して縮小して全体を表示
						$sheet->getStyle('A' . $row . ":F" . $row)
							->getAlignment()
							->setShrinkToFit(true);

						// ARからAWまでのセルを右寄せにする
						$sheet->getStyle('AR' . $row . ":AW" . $row)
							->getAlignment()
							->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

						// use setWidth() to set the width of a column Y column
						$sheet->getColumnDimension('Y')->setWidth(5);

						$row++;
					}
				}
			}

			Payment::mergeCells($sheet, $row);

			// AからFまでのセルの書式を設定して縮小して全体を表示
			$sheet->getStyle('A' . $row . ":F" . $row)
				->getAlignment()
				->setShrinkToFit(true);

			// 合計金額をセット
			Payment::setTotalAmount($sheet, $row, $totalAmount);

			// 囲い線を全体に設定
			$sheet->getStyle('A6:AW'  . $row)
				->getBorders()
				->getAllBorders()
				->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		}

		// Sheet1を削除
		$sheetIndex = $spreadsheet->getIndex(
			$spreadsheet->getSheetByName('Sheet1')
		);
		$spreadsheet->removeSheetByIndex($sheetIndex);

		// active sheetを一番左のシートにする
		$spreadsheet->setActiveSheetIndex(0);

		$filename = '校舎別入金明細.xlsx';
		// ダウンロード
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		// ob_end_clean(); //バッファ消去

	}

	public function export_year_sales(Request $request)
	{
		// 年間総売上

		$requestData = $request->all();

		if (empty($requestData['year'])) {
			return redirect()->back()->with('flash_message', '年度を入力してください。');
		}

		$sales = sale::when(!empty($requestData['year']), function ($query) use ($requestData) {
			return $query->where('sale_month', 'like', $requestData['year'] . '-%');
		})->orderBy('school_building_id', 'asc')->get();
		$division_codes = DivisionCode::all();
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/year_sale.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', $requestData['year']);
		$sheet->setCellValue('A3', '校舎名');
		$sheet->mergeCells("A3:I3");
		$consumption_tax = 1 + (config('const.consumption_tax') / 100);
		$school_buildings = SchoolBuilding::all()->pluck('name_short', 'id');;
		// $sheet->setCellValue('AI1', '作成日:　　' . date('Y/m/d'));
		foreach ($sales as $list) {
			if (empty($list->school_building->name_short)) {
				dump($school_buildings[$list->school_building_id]);
				dd($list->school_building);
			}
			$lists[$list->school_building_id]['school_building_name'] = $list->school_building->name_short;
			$student_no[$list->school_building_id][]=$list->student_no;

			$sales_details = $list->sales_detail;
			foreach ($sales_details as $sales_detail) {
				$sales_subtotal = $sales_detail->subtotal;
				if (!empty($lists[$list->school_building_id]['division_code'][$sales_detail->sales_category])) {
					$lists[$list->school_building_id]['division_code'][$sales_detail->sales_category] += $sales_subtotal;
				} else {
					$lists[$list->school_building_id]['division_code'][$sales_detail->sales_category] = $sales_subtotal;
				}
			}
		}
		$row = 4;

		if (!isset($lists)) {
			return redirect()->back()->with('flash_message', '該当するデータがありません。');
		}

		foreach ($lists as $school_building_id => $values) {
			$sheet->setCellValue('A' . $row, $values['school_building_name']);
			$sheet->mergeCells("A" . $row . ":I" . $row);
			$col_no = 10;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 7);

			if ($row == 4) {
				$sheet->setCellValue($col . '3', '請求人数');
				$sheet->mergeCells($col .  "3:" . $col_end . "3");
			}
			$charge_cnts = array();
			if (!empty($student_no[$school_building_id])) {
				$charge_cnts = array_unique($student_no[$school_building_id]);
				$sheet->setCellValue($col . $row, count($charge_cnts));
			} else {
				$sheet->setCellValue($col . $row, 0);
			}
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$col_no += 8;

			foreach ($division_codes as $division_code) {
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
				if($division_code->id==10){
					$before_joseikin_col = Coordinate::stringFromColumnIndex($col_no-1);
					$after_joseikin_col = Coordinate::stringFromColumnIndex($col_no+8);
				}
				if ($row == 4) {
					$sheet->setCellValue($col . '3', $division_code->name);
					$sheet->mergeCells($col .  "3:" . $col_end . "3");
				}
				if (!empty($values['division_code'][$division_code->id])) {
					$sheet->setCellValue($col . $row, $values['division_code'][$division_code->id]);
				} else {
					$sheet->setCellValue($col . $row, 0);
				}
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$col_no += 8;
			}
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
			if ($row == 4) {
				$sheet->setCellValue($col . '3', '小計');
				$sheet->mergeCells($col .  "3:" . $col_end . "3");
			}
			$before_col = Coordinate::stringFromColumnIndex($col_no - 1);
			if(!empty($before_joseikin_col)){
				$sheet->setCellValue($col . $row, '=SUM(R' . $row . ':' . $before_joseikin_col . $row . ')+SUM('. $after_joseikin_col . $row.':' .$before_col.$row.')');
			}else{
				$sheet->setCellValue($col . $row, '=SUM(R' . $row . ':' .$before_col.$row.')');
			}
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$last_col = $col;
			$sum_col = $col;
			// $spreadsheet->getActiveSheet()->getRowDimension('10')->setRowHeight(12);
			$col_no += 8;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
			if ($row == 4) {
				$sheet->setCellValue($col . '3', '受講単価');
				$sheet->mergeCells($col .  "3:" . $col_end . "3");
			}
			$sheet->setCellValue($col . $row, '=ROUND(' . $sum_col . $row . '/'. count($charge_cnts).',0)');
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);

			// 行の高さを20に設定
			$sheet->getRowDimension($row)->setRowHeight(20);

			$row++;
		}
		$sheet->setCellValue('A' . $row, '合計');
		$sheet->setCellValue('A' . ($row + 1), '構成比');
		$sheet->mergeCells("A" . $row . ":I" . $row);
		$sheet->mergeCells("A" . ($row + 1) . ":I" . ($row + 1));
		$col_no = 10;
		$col = Coordinate::stringFromColumnIndex($col_no);
		$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
		$sheet->setCellValue($col . $row, '=SUM(' . $col . '3:' . $col . ($row - 1) . ')');
		$sheet->mergeCells($col . $row . ":" . $col_end . $row);
		$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
		$col_no += 8;

		foreach ($division_codes as $division_code) {

			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
			$sheet->setCellValue($col . $row, '=SUM(' . $col . '3:' . $col . ($row - 1) . ')');
			$sheet->setCellValue($col . ($row + 1), '=ROUND(' . $col . $row . '/' . $sum_col . $row . '*100,1)');
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));

			$col_no += 8;
		}
		$col = Coordinate::stringFromColumnIndex($col_no);
		$col_end = Coordinate::stringFromColumnIndex($col_no + 7);

		$sheet->setCellValue($col . $row, '=SUM(' . $col . '3:' . $col . ($row - 1) . ')');
		$sheet->setCellValue($col . ($row + 1), '=' . $col . $row . '/' . $sum_col . $row . '*100');
		$sheet->mergeCells($col . $row . ":" . $col_end . $row);
		$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
		$col_no += 8;
		$col = Coordinate::stringFromColumnIndex($col_no);
		$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
		$sheet->setCellValue($col . $row, '=ROUND(' . $sum_col . $row . '/J'.$row.',0)');
		$sheet->mergeCells($col . $row . ":" . $col_end . $row);
		$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));

		$spreadsheet->getActiveSheet()->getStyle("J3:" . $col_end . $row)->getNumberFormat()
			->setFormatCode('#,##0');
		$sheet->getStyle("A3:" . $col_end . ($row + 1))->getFont()->setSize(9);
		$sheet->getStyle('A3:' . $col_end . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('A4:' . $col_end . ($row - 1))
			->getAlignment()->setWrapText(true);
		$spreadsheet->getActiveSheet()->getPageSetup()
			->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$spreadsheet->getActiveSheet()->getPageSetup()
			->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:' . $col_end . ($row - 1));
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

		// active sheetを一番左のシートにする
		$spreadsheet->setActiveSheetIndex(0);

		$filename = '年間総売上.xlsx';
		// ダウンロード
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		// ob_end_clean(); //バッファ消去

	}

	public function export_month_sales(Request $request)
	{
		// 月別売上明細


		$requestData = $request->all();

		if (empty($requestData['sale_month_start']) || empty($requestData['sale_month_end'])) {
			return redirect()->back()->with('flash_message', '期間を選択してください');
		}

		$sales = sale::whereBetween('sale_month', [$requestData['sale_month_start'], $requestData['sale_month_end']])->orderBy('sale_month', 'asc')->orderBy('school_building_id', 'asc')->get();
		$division_codes = DivisionCode::all();
		$school_buildings = SchoolBuilding::all();
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/month_sale.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();
		$month = explode('-', $requestData['sale_month_start']);

		$end_year = date('Y', strtotime($requestData['sale_month_end']));
		$end_month = date('n', strtotime($requestData['sale_month_end']));
		// L1に売上年度をセット
		$sheet->setCellValue('L1', $end_year);
		// 書式設定で縮小して全体を表示 setShrinkToFit(true)
		$sheet->getStyle('L1')->getAlignment()->setShrinkToFit(true);
		// P1に売上月終了をセット
		$sheet->setCellValue('Q1', $end_month);
		// 書式設定で縮小して全体を表示 setShrinkToFit(true)
		$sheet->getStyle('Q1')->getAlignment()->setShrinkToFit(true);


		$consumption_tax = 1 + (config('const.consumption_tax') / 100);
		$all_col_no = 18;
		// $sheet->setCellValue('A1', $month[0]);

		// F1に売上月開始をセット
		$sheet->setCellValue('F1', $month[1]);

		$sheet->setCellValue('A5', '校舎名');
		$sheet->mergeCells("A5:I5");

		// $sheet->setCellValue('AI1', '作成日:　　' . date('Y/m/d'));
		foreach ($sales as $list) {
			$lists[$list->sale_month][$list->school_building_id]['school_building_name'] = '未登録';
			if (!empty($list->school_building)) {
				$lists[$list->sale_month][$list->school_building_id]['school_building_name'] = $list->school_building->name_short;
			}
			if (empty($lists[$list->sale_month][$list->school_building_id]['charge_cnt'])){
				$lists[$list->sale_month][$list->school_building_id]['charge_cnt']=1;
			}else{
				$lists[$list->sale_month][$list->school_building_id]['charge_cnt']++;
			}
			$student_no[$list->school_building_id][]=$list->student_no;

			$sales_details = $list->sales_detail;
			foreach ($sales_details as $sales_detail) {
				$sales_subtotal = $sales_detail->subtotal;
				if (!empty($lists[$list->sale_month][$list->school_building_id]['division_code'][$sales_detail->sales_category])) {
					$lists[$list->sale_month][$list->school_building_id]['division_code'][$sales_detail->sales_category] += $sales_subtotal;
				} else {
					$lists[$list->sale_month][$list->school_building_id]['division_code'][$sales_detail->sales_category] = $sales_subtotal;
				}
			}
		}
		foreach ($lists as $sale_month => $values) {
			$row = 6;
			$clonedWorksheet = clone $spreadsheet->getSheetByName('Sheet1');
			$sheetname = date('Y年n月', strtotime($sale_month));

			ksort($values);

			$month = date('n', strtotime($sale_month)) . '月';


			$clonedWorksheet->setTitle($sheetname);
			$spreadsheet->addSheet($clonedWorksheet);
			$sheet = $spreadsheet->getSheetByName($sheetname);
			$year = date('Y', strtotime($sale_month));
			// A1に売上年度をセット
			$sheet->setCellValue('A1', $year);

			foreach ($values as $school_building_id =>$value) {
				$sheet->setCellValue('A' . $row, $value['school_building_name']);
				$sheet->mergeCells("A" . $row . ":I" . $row);
				$col_no = 10;
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 7);

				if ($row == 6) {
					$sheet->setCellValue($col . '5', '請求人数');
					$sheet->mergeCells($col .  "5:" . $col_end . "5");
				}
				if (empty($value['charge_cnt'])){
					$sheet->setCellValue($col . $row, 0);
				}else{
					$sheet->setCellValue($col . $row, $value['charge_cnt']);
				}
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$col_no += 8;

				foreach ($division_codes as $division_code) {
					$col = Coordinate::stringFromColumnIndex($col_no);
					$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
					if($division_code->id==10){
						$before_joseikin_col = Coordinate::stringFromColumnIndex($col_no-1);
						$after_joseikin_col = Coordinate::stringFromColumnIndex($col_no+8);
					}

					if ($row == 6) {
						$sheet->setCellValue($col . '5', $division_code->name);
						$sheet->mergeCells($col .  "5:" . $col_end . "5");
					}
					if (!empty($value['division_code'][$division_code->id])) {
						$sheet->setCellValue($col . $row, $value['division_code'][$division_code->id]);
					} else {
						$sheet->setCellValue($col . $row, 0);
					}

					$sheet->mergeCells($col . $row . ":" . $col_end . $row);
					$col_no += 8;
				}
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
				$sumcol = $col ;
				if ($row == 6) {
					$sheet->setCellValue($col . '5', '小計');
					$sheet->mergeCells($col .  "5:" . $col_end . "5");
				}
				$before_col = Coordinate::stringFromColumnIndex($col_no - 1);

				if(!empty($before_joseikin_col)){
					$sheet->setCellValue($col . $row, '=SUM(R' . $row . ':' . $before_joseikin_col . $row . ')+SUM('. $after_joseikin_col . $row.':' .$before_col.$row.')');
				}else{
					$sheet->setCellValue($col . $row, '=SUM(R' . $row . ':' .$before_col.$row.')');
				}
					$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				// 行の高さを20に設定
				$sheet->getRowDimension($row)->setRowHeight(25);
				$col_no += 8;
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
				if ($row == 6) {
					$sheet->setCellValue($col . '5', '受講単価');
					$sheet->mergeCells($col .  "5:" . $col_end . "5");
				}
				$sheet->setCellValue($col . $row, '=ROUND(' . $sumcol . $row . '/'.$value['charge_cnt'].',0)');
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				$row++;
			}
			$sheet->setCellValue('A' . $row, "合計");
			$sheet->mergeCells("A" . $row . ":I" . $row);
			$sheet->setCellValue('A' . ($row + 1), "構成比");
			$sheet->mergeCells("A" . ($row + 1) . ":I" . ($row + 1));

			// C3に付売上月を設定
			$sheet->setCellValue('C3', $month);

			$col_no = 10;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
			$sheet->setCellValue($col . $row, '=SUM(' . $col . '6:' . $col . ($row - 1) . ')');
			// $sheet->setCellValue($col . ($row + 1), '=TEXT(' . $col . $row . "/" . $last_col . $row  . '*100,"#0.0")');
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
			$col_no += 8;

			foreach ($division_codes as $division_code) {
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
				$sheet->setCellValue($col . $row, '=SUM(' . $col . '6:' . $col . ($row - 1) . ')');
				$sheet->setCellValue($col . ($row + 1), '=TEXT(' . $col . $row . "/" . $sumcol . $row  . '*100,"#0.0")');
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
				$col_no += 8;
			}
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 7);
			$sheet->setCellValue($col . $row, '=SUM(' . $col . '6:' . $col . ($row - 1) . ')');
			$sheet->setCellValue($col . ($row + 1), '=TEXT(' . $col . $row . "/" . $sumcol . $row  . '*100,"#0.0")');
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
			$col_no += 8;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 7);

			$sheet->setCellValue($col . $row, '=ROUND(' . $sumcol . $row . '/J'.$row.',0)');
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));

			$sheet->getStyle("J5:" . $col_end . $row)->getNumberFormat()->setFormatCode('#,##0');
			$sheet->getStyle("A5:" . $col_end . ($row + 1))->getFont()->setSize(9);
			$sheet->getStyle('A5:' . $col_end . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			$spreadsheet->getActiveSheet()->getStyle('A6:' . $col_end . ($row - 1))
				->getAlignment()->setWrapText(true);
			$spreadsheet->getActiveSheet()->getPageSetup()
				->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
			$spreadsheet->getActiveSheet()->getPageSetup()
				->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			$spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:' . $col_end . ($row - 1));
			$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

			$sheet = $spreadsheet->getSheetByName('全校舎');
			$all_row = 3;




			$all_col = Coordinate::stringFromColumnIndex($all_col_no);
			$all_col_end = Coordinate::stringFromColumnIndex($all_col_no + 7);
			$sheet->mergeCells($all_col . $all_row . ":" . $all_col_end . $all_row);
			//例：××××年×月を書き込む（全校舎シート）
			$sheet->setCellValue($all_col . $all_row, $sheetname);
			// $col = Coordinate::stringFromColumnIndex($col_no - 8);
			// $col_end = Coordinate::stringFromColumnIndex($col_no - 1);
			// 行の高さを25に設定
			$sheet->getRowDimension($all_row)->setRowHeight(25);

			$all_row++;
			foreach ($school_buildings as $school_building) {
				if ($school_building->name_short != '休職中') {
					//校舎名を書き込む（全校舎シート）
					$sheet->setCellValue('A' . $all_row, $school_building->name_short);
					$sheet->mergeCells('A' . $all_row . ":I"  . $all_row);
					// $fx =  '=' . date('Y年n月', strtotime($sale_month)) . '!' . $col . ($all_row + 2);
					$total = 0;
					if (!empty($lists[$sale_month][$school_building->id])) {
						if(!empty($lists[$sale_month][$school_building->id]['division_code'][10])){
							unset($lists[$sale_month][$school_building->id]['division_code'][10]);
						}
						$total = array_sum($lists[$sale_month][$school_building->id]['division_code']);
					}
					//合計値を書き込む（全校舎シート）
					$sheet->setCellValue($all_col . $all_row, $total);
					$sheet->mergeCells($all_col . $all_row . ":" . $all_col_end . $all_row);

					// 行の高さを20に設定
					$sheet->getRowDimension($all_row)->setRowHeight(25);
					$all_row++;
				}
			}
			$all_col_no += 8;
		}

		$sheet->getStyle("J4:" . $all_col_end . ($all_row - 1))->getNumberFormat()->setFormatCode('#,##0');
		$sheet->getStyle("A3:" . $all_col_end . ($all_row - 1))->getFont()->setSize(9);
		$sheet->getStyle('A3:' . $all_col_end . ($all_row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('A4:' . $all_col_end . ($row - 1))
			->getAlignment()->setWrapText(true);
		$spreadsheet->getActiveSheet()->getPageSetup()
			->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$spreadsheet->getActiveSheet()->getPageSetup()
			->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:' . $all_col_end . ($all_row - 1));
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

		$all_col_no = 10;
		$all_row = 3;
		$all_col = Coordinate::stringFromColumnIndex($all_col_no);
		$all_col_end = Coordinate::stringFromColumnIndex($all_col_no + 7);
		$sheet->setCellValue($all_col . $all_row, '請求人数');
		$sheet->mergeCells($all_col . $all_row . ":" . $all_col_end . $all_row);

		$all_row++;
		foreach ($school_buildings as $school_building) {
			if(!empty($student_no[$school_building->id])){
				$charge_cnt = array_unique($student_no[$school_building->id]);
				$sheet->setCellValue($all_col . $all_row, count($charge_cnt));
			}else{
				$sheet->setCellValue($all_col . $all_row, 0);
			}
			$sheet->mergeCells($all_col . $all_row . ":" . $all_col_end . $all_row);

			$all_row++;

		}
		// シート名からindexを取得
		$sheet_index = $spreadsheet->getIndex($spreadsheet->getSheetByName('Sheet1'));
		// シートを削除
		$spreadsheet->removeSheetByIndex($sheet_index);

		// active sheetを一番左のシートにする
		$spreadsheet->setActiveSheetIndex(0);

		$filename = '月別売上明細.xlsx';
		// ダウンロード
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		// ob_end_clean(); //バッファ消去

	}

	public function export_withdrawal(Request $request)
	{
		// 銀行引落コンビニ振込等対照表

		$requestData = $request->all();

		if (empty($requestData['year'])) {
			return redirect()->back()->with('flash_message', '年度を入力してください');
		}

		// ChargeProgressのsales_monthが一番遅いものを取得
		$charge_progress = ChargeProgress::orderBy('sales_month', 'desc')->first();
		// 2023-02から月を取り出したい
		$year_month = explode('-', $charge_progress->sales_month);
		// 1月、2月、3月の場合
		if ($year_month[1] == "01" || $year_month[1] == "02" || $year_month[1] == "03") {
			// $year_month[1]に-1する
			$year = $year_month[0] - 1;
		} else {
			$year = $year_month[0];
		}

		// requestData->yearと$yearを比較して、requestData->yearが大きい場合はバリデーションで前の画面に戻す
		if ((int)$requestData['year'] > $year) {
			return redirect()->back()->with('flash_message', '年度が不正です');
		}





		// $payments = Payment::whereBetween('sale_month', [$requestData['year'] . "-04", $requestData['year'] + 1 . "-03"])->orderBy('school_building_id', 'asc');
		// $sales = Sale::whereBetween('sale_month', [$requestData['year'] . "-04", $requestData['year'] + 1 . "-03"])->orderBy('school_building_id', 'asc');
		// $charges = Charge::whereBetween('charge_month', [$requestData['year'] . "-04", $requestData['year'] + 1 . "-03"])->where('withdrawal_created_flg', 1)->where('withdrawal_confirmed', 1);
		$school_buildings = SchoolBuilding::all();

		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/withdrawal.xlsx'); //template.xlsx 読込
		$day = $requestData['year'] . "-04-01";
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', $requestData['year']);

		$sheet->setCellValue('AR1', '作成日:　　' . date('Y/m/d'));

		$month = date('Y-m', strtotime($day));
		while ($month < $requestData['year'] + 1 . "-04") {
			$month_disp = date("n", strtotime($day));
			$clonedWorksheet = clone $spreadsheet->getSheetByName('Sheet1');
			$sheetname = date('Y年n月', strtotime($day));
			$clonedWorksheet->setTitle($sheetname);
			$spreadsheet->addSheet($clonedWorksheet);
			$sheet = $spreadsheet->getSheetByName($sheetname); //weatherシート取得
			$row = 6;
			foreach ($school_buildings as $school_building) {
				// $sheet = $spreadsheet->getActiveSheet();
				$sheet->setCellValue('C3', $month_disp . "月");
				$sheet->setCellValue('C' . $row, $school_building->name_short);
				$sheet->mergeCells("C" . $row . ":N" . $row);

				// $sales_sum = Sale::where('sale_month', $month)->where('school_building_id', $school_building->id)->sum("sales_sum");
				$sales = Sale::where('sale_month', $month)->where('school_building_id', $school_building->id)->get();

				$charges_sum = 0;
				$sales_sum = $sales->sum("sales_sum");
				$sales_number = array();
				foreach ($sales as $sale) {
					$sales_number[] = $sale->sales_number;
				}
				$sales_number_cnt = is_countable($sales_number) ? count($sales_number) : 0;
				if ($sales_number_cnt > 0) {
					$charges_sum = Charge::whereIn("sales_number", $sales_number)->sum('sum');
				}
				$payments_sum = Payment::where('sale_month', $month)->where('school_building_id', $school_building->id)->sum("payment_amount");
				$sheet->setCellValue('O' . $row, $sales_sum);
				$sheet->mergeCells("O" . $row . ":U" . $row);
				$sheet->setCellValue('V' . $row, $charges_sum);
				$sheet->mergeCells("V" . $row . ":AB" . $row);
				$sheet->setCellValue('AC' . $row, $payments_sum);
				$sheet->mergeCells("AC" . $row . ":AI" . $row);
				$sheet->setCellValue('AJ' . $row, '=O' . $row . '-SUM(V' . $row . ':AI' . $row . ')');
				$sheet->mergeCells("AJ" . $row . ":AP" . $row);
				$row++;
			}
			$sheet->setCellValue('C' . $row, '合計');
			$sheet->mergeCells("C" . $row . ":N" . $row);
			$sheet->setCellValue('O' . $row, '=SUM(O6:O' . ($row - 1) . ')');
			$sheet->mergeCells("O" . $row . ":U" . $row);
			$sheet->setCellValue('V' . $row, '=SUM(V6:V' . ($row - 1) . ')');
			$sheet->mergeCells("V" . $row . ":AB" . $row);
			$sheet->setCellValue('AC' . $row, '=SUM(AC6:AC' . ($row - 1) . ')');
			$sheet->mergeCells("AC" . $row . ":AI" . $row);
			$sheet->setCellValue('AJ' . $row, '=SUM(AJ6:AJ' . ($row - 1) . ')');
			$sheet->mergeCells("AJ" . $row . ":AP" . $row);
			$sheet->getStyle('C6:AP' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			$sheet->getStyle("O6:AP" . $row)->getNumberFormat()->setFormatCode('#,##0');

			$day = date('Y-m-d', strtotime('+1 month' . $day));
			$month = date('Y-m', strtotime($day));
		}
		// active sheetを一番左のシートにする
		$spreadsheet->setActiveSheetIndex(0);

		// Sheet1の取得
		$sheet1 = $spreadsheet->getSheetByName('Sheet1');
		// 削除
		$spreadsheet->removeSheetByIndex($spreadsheet->getIndex($sheet1));

		$filename = '銀行引落コンビニ振込等対照表.xlsx';
		// ダウンロード
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		// header('Cache-Control: max-age=1');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		// ob_end_clean(); //バッファ消去

	}

	public function export_first_sales(Request $request)
	{
		// 入塾者初回売上チェック

		$requestData = $request->all();

		if (empty($requestData['juku_start_date_start']) || empty($requestData['juku_start_date_end'])) {
			return redirect()->back()->with('flash_message', '期間を指定してください。');
		}

		$students = Student::whereBetween('juku_start_date', [$requestData['juku_start_date_start'], $requestData['juku_start_date_end']])->get();
		$head = ['生徒番号', '氏', '名', '校舎No', '入塾日'];
		$key = 0;
		$first = true;

		// studentはcollection。空かどうかを判定するにはcount()を使う
		if ($students->count() == 0) {
			return redirect()->back()->with('flash_message', '該当データがありません。');
		}

		foreach ($students as $student) {

			$lists[$key]['student_no'] = $student->student_no;
			$lists[$key]['surname'] = $student->surname;
			$lists[$key]['name'] = $student->name;
			$lists[$key]['school_building_no'] = $student->number;
			$lists[$key]['juku_start_date'] = $student->juku_start_date;
			$day = $requestData['juku_start_date_start'];
			$month = date('Y-m', strtotime($requestData['juku_start_date_start']));
			while ($month != date('Y/m', strtotime('+2 month ' . $requestData['juku_start_date_end']))) {
				$ng_flg = SalesDetail::where('student_no', $student->student_no)->where('sale_month', date('Y-m', strtotime($day)))->whereNull('charged_month')->WhereNull('scrubed_month')->exists();
				if ($first) {
					$head[] = $month;
				}
				if ($ng_flg == true) {
					$lists[$key][$month] = '×';
				} else {
					$lists[$key][$month] = '○';
				}
				$day = date('Y-m-d', strtotime('+1 month ' . $day));
				$month = date('Y/m', strtotime($day));
			}
			if ($first) {
				$first = false;
			}
			$key++;
		}
		$file_path = storage_path() . '/app/excel/test.csv';
		$f = fopen($file_path, 'w');
		if ($f) {
			// カラムの書き込み
			mb_convert_variables('SJIS', 'UTF-8', $head);
			fputcsv($f, $head);
			// データの書き込み
			foreach ($lists as $list) {
				mb_convert_variables('SJIS', 'UTF-8', $list);
				fputcsv($f, $list);
			}
		}
		// ファイルを閉じる
		fclose($f);

		// HTTPヘッダ
		header("Content-Type: application/octet-stream");
		header('Content-Length: ' . filesize($file_path));
		header('Content-Disposition: attachment; filename=入塾者初回売上チェック.csv');
		readfile($file_path);
		ob_end_clean(); //バッファ消去

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
		$daily_salary = DailySalary::findOrFail($id);
		return view("sales.show", compact("daily_salary"));
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
		$daily_salary = DailySalary::findOrFail($id);
		$schoolbuilding = SchoolBuilding::all()->pluck('name', 'id');
		$job_description = JobDescription::all()->pluck('name', 'id');

		return view("sales.edit", compact("daily_salary", "schoolbuilding", "job_description"));
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

		$daily_salary = DailySalary::findOrFail($id);
		$daily_salary->update($requestData);

		return redirect("/shinzemi/sales")->with("flash_message", "データが更新されました。");
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
		DailySalary::destroy($id);

		return redirect("/shinzemi/sales")->with("flash_message", "データが削除されました。");
	}
}
    //=======================================================================