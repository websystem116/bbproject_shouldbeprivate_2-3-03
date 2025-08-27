<?php

namespace App\Http\Controllers;

ini_set("memory_limit", "2048M");

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;
use Validate;
use DB;
use DateTime;
use App\User;
use App\Salary;
use App\SalaryDetail;
use App\SchoolBuilding;
use App\School;
use App\JobDescription;
use App\OtherJobDescription;
use App\JobDescriptionWage;
use App\OtherJobDescriptionWage;
use App\DailySalary;
use App\BranchBank;
use App\DailyOtherSalary;
use App\Company;
use App\IncomeTax;
use App\TransportationExpense;
use League\CommonMark\Inline\Element\Code;
// use phpspreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpParser\Node\Stmt\Foreach_;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Align;

// 罫線引きたい
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;


//python 実行用
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

//=======================================================================
class SalaryOutputController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request)
	{
		$tightening_date['min'] = Salary::min('tightening_date');

		$tightening_date['max'] = Salary::max('tightening_date');
		return view("salary_output.index", compact('tightening_date'));
	}
	public function school_building_index(Request $request)
	{
		$school_buildings = SchoolBuilding::all();
		return view("salary_output.school_building_index", compact("school_buildings"));
	}
	public function working_school_building_index(Request $request)
	{
		$school_buildings = SchoolBuilding::all();
		return view("salary_output.working_school_building_index", compact("school_buildings"));
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
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */

	public function export_salary_list(Request $request)
	{
		// 非常勤給与一覧表

		$year_month = $request->get('year_month');
		//Excel出力
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/part_timer_salary.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();
		$month = explode('-', $year_month);
		$job_descriptions = JobDescription::all();
		$other_job_descriptions = OtherJobDescription::all();
		$income_taxes = IncomeTax::all();
		if($year_month >= "2024-07"){
			$salaries = Salary::where('tightening_date', $year_month)->leftJoin('users', 'users.id', '=', 'salaries.user_id')
			->orderByRaw('users.last_name_kana asc, users.first_name_kana asc')->get();
			$salaries = Salary::select('salaries.*')
			->join('users', 'salaries.user_id', '=', 'users.id')
			->orderByRaw('users.last_name_kana asc, users.first_name_kana asc')->
			where('salaries.tightening_date', $year_month)->get();
		}else{
				$salaries = Salary::where('tightening_date', $year_month)->get();
			}
		$daily_salaries = DailySalary::where('work_month', $year_month)->get();
		$sheet->setCellValue('M1', '発行日　' . date('Y年m月d日'));
		$sheet_title = $month[0] . "年" . $month[1] . "月度　非常勤給与一覧";
		$sheet->setCellValue('A1', $sheet_title);
		$col_no = 5;
		$total_income_tax_cost = 0;
		$total_deduction = 0;
		$total_salary = 0;
		$count_monthly_tightening = 0;
		$total_monthly_tightening = 0;
		$payment_amount_sum = array();
		foreach ($job_descriptions as $job_description) {
			$payment_amount_sum[$job_description->id] = 0;
		}
		$other_payment_amount_sum = array();
		foreach ($other_job_descriptions as $other_job_description) {
			$other_payment_amount_sum[$other_job_description->id] = 0;
		}
		$row_first = 5;
		$row = $row_first;
		// $attendance_cnt = 0;
		foreach ($salaries as $salary) {
			// 該当年月の給与付与対象の非常勤の先生別に出力

			$sheet->getRowDimension($row)->setRowHeight(55);
			$col_no = 5;
			$user = $salary->user;
			if (!empty($user)) {
				$sheet->setCellValue('A' . $row,  $user->user_id);
				// 直前のセルの横位置を中央にする
				$sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER);

				$sheet->setCellValue('B' . $row, $user->last_name  . $user->first_name);
				// 直前のセルの横位置を中央にする
				$sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER);

				$sheet->setCellValue('C' . $row, $user->school_buildings->name);
				// 直前のセルの横位置を中央にする
				$sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_CENTER);

				$daily_salary = $daily_salaries->where('user_id', $salary->user_id);
				$daily_salary = $daily_salary->groupBy('work_date');
				// 合計出勤日数
				$sum_daily_salary[] = $daily_salary->count();
				// 出勤日数
				$sheet->setCellValue('D' . $row, $daily_salary->count());
				$salary_sum = $salary->salary;
				$deduction_sum = $salary->health_insurance + $salary->welfare_pension + $salary->employment_insurance;
				$salary_sum = $salary_sum - $deduction_sum;
				$sheet->getStyle('D' . $row)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$income_taxs = $income_taxes->filter(
					function ($value) use ($salary_sum) {
						return $value['or_more'] <= $salary_sum && $value['less_than'] >= $salary_sum;
					}
				);
				foreach ($income_taxs as $value) {
					$income_tax = $value;
				}
				$salary->user->description_column;
				$salary->user->dependents_count;
				if ($salary->user->description_column == 1) {

					switch ($salary->user->dependents_count) {
						case 1:
							$income_tax_cost = $income_tax->support1;
							break;
						case 2:
							$income_tax_cost = $income_tax->support2;
							break;
						case 3:
							$income_tax_cost = $income_tax->support3;
							break;
						case 4:
							$income_tax_cost = $income_tax->support4;
							break;
						case 5:
							$income_tax_cost = $income_tax->support5;
							break;
						case 6:
							$income_tax_cost = $income_tax->support6;
							break;
						case 7:
							$income_tax_cost = $income_tax->support7;
							break;
						default:
							$income_tax_cost = $income_tax->support0;
					}
				} else {
					$income_tax_cost = $income_tax->otsu;
				}
				if ($income_tax_cost == 3) {
					$income_tax_cost = floor($salary_sum * 3.063 / 100);
				}
				$total_income_tax[] = $income_tax_cost;
				$salary_details = $salary->salary_detail;
				foreach ($job_descriptions as $job_description) {
					$salary_detail = $salary_details->where('job_description_id', $job_description->id)->where('description_division', 1)->first();
					$col = Coordinate::stringFromColumnIndex($col_no);

					if ($row == $row_first) {
						// 見出し行の項目名出力処理
						$sheet->getRowDimension($row - 1)->setRowHeight(50);
						$sheet->insertNewColumnBefore($col, 1);
						$sheet->setCellValue($col . 4, $job_description->name . "\n支給額");
					}
					$payment_amount = 0;

					if (!empty($salary_detail)) {
						$payment_amount = $salary_detail['payment_amount'];
					}
					// $job_description_id別に合計額を増やしていく
					$payment_amount_sum[$job_description->id] +=  $payment_amount;


					$sheet->setCellValue($col . $row, number_format($payment_amount));
					$sheet->getStyle($col . $row)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
					$sheet->getColumnDimension($col)->setWidth(11);

					$col_no++;
				}
				foreach ($other_job_descriptions as $other_job_description) {
					$salary_detail = $salary_details->where('job_description_id', $other_job_description->id)->where('description_division', 2)->first();
					$col = Coordinate::stringFromColumnIndex($col_no);
					if ($row == $row_first) {
						// 項目名出力処理
						$sheet->insertNewColumnBefore($col, 1);
						$sheet->setCellValue($col . 4, $other_job_description->name . "\n支給額");
					}
					$payment_amount = 0;
					if (!empty($salary_detail)) {
						$payment_amount = $salary_detail['payment_amount'];
					}

					// $job_description_id別に合計額を増やしていく
					$other_payment_amount_sum[$other_job_description->id] +=  $payment_amount;

					$sheet->setCellValue($col . $row, number_format($payment_amount));
					$sheet->getStyle($col . $row)
						->getAlignment()
						->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
					$sheet->getColumnDimension($col)->setWidth(11);
					$col_no++;
				}

				$col = Coordinate::stringFromColumnIndex($col_no);
				// その他支給
				$sheet->setCellValue($col . $row, number_format($salary->other_payment_amount));
				$sheet->getStyle($col . $row)
					->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$sheet->getStyle($col . $row)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$col_no++;


				$col = Coordinate::stringFromColumnIndex($col_no);
				// 住民税
				$sheet->setCellValue($col . $row, number_format($salary->municipal_tax));

				$sheet->getStyle($col . $row)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$col_no++;
				// 所得税

				$col = Coordinate::stringFromColumnIndex($col_no);
				$sheet->setCellValue($col . $row, number_format($income_tax_cost));

				$sheet->getStyle($col . $row)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$total_income_tax_cost += $income_tax_cost;
				$col_no++;
				$col = Coordinate::stringFromColumnIndex($col_no);
				// 控除額合計
				$deduction = $deduction_sum + $income_tax_cost + $salary->municipal_tax;
				$sheet->setCellValue($col . $row, $deduction);
				$total_deduction += $deduction;
				$sheet->getStyle($col . $row)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$col_no++;
				$col = Coordinate::stringFromColumnIndex($col_no);
				//年末調整
				$sheet->setCellValue($col . $row, number_format($salary->year_end_adjustment));
				$sheet->getStyle($col . $row)
					->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				// 交通費
				$col_no++;
				$col = Coordinate::stringFromColumnIndex($col_no);
				$sheet->setCellValue($col . $row, number_format($salary->transportation_expenses));
				$sheet->getStyle($col . $row)
					->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				// 総支給額
				$salary_subtotal = $salary->salary + $salary->transportation_expenses + $salary->other_payment_amount;
				$salary_subtotal = $salary_subtotal + $salary->year_end_adjustment - $deduction;
				$total_salary += $salary_subtotal;
				$col_no++;
				$col = Coordinate::stringFromColumnIndex($col_no);
				$sheet->setCellValue($col . $row, number_format($salary_subtotal));
				$sheet->getStyle($col . $row)
					->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				// 月次入力有無
				$col_no++;
				$col = Coordinate::stringFromColumnIndex($col_no);
				if ($salary->monthly_completion == 1) {
					$sheet->setCellValue($col . $row, "済");
				} else {
					$sheet->setCellValue($col . $row, "未");
				}
				// セルの横位置を中央にする
				$sheet->getStyle($col . $row)
					->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


				// 上長承認有無
				// $col_no += 2;
				// $col = Coordinate::stringFromColumnIndex($col_no);
				// $col_end = Coordinate::stringFromColumnIndex($col_no + 1);
				// $sheet->mergeCells($col . $row . ":" . $col_end . $row);
				// if ($salary->monthly_approval == 1) {
				// 	$sheet->setCellValue($col . $row, "済");
				// } else {
				// 	$sheet->setCellValue($col . $row, "未");
				// }
				// // セルの横位置を中央にする
				// $sheet->getStyle($col . $row)
				// 	->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				// 給与承認有無
				$col_no++;
				$col = Coordinate::stringFromColumnIndex($col_no);
				if ($salary->salary_approval == 1) {
					$sheet->setCellValue($col . $row, "済");
					$count_monthly_tightening++;
				} else {
					$sheet->setCellValue($col . $row, "未");
				}
				// セルの横位置を中央にする
				$sheet->getStyle($col . $row)
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				// $row行の文字サイズを17にする
				$sheet->getStyle($row . ":" . $row)->getFont()->setSize(16);
				$total_monthly_tightening++;
				$row++;
			}
		}

		$sheet->getStyle('A6:' . $col . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$col_no = 5;
		$sheet->setCellValue('A' . $row, "合計");
		$sheet->getStyle('A' . $row)
			->getAlignment()
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

		// 文字サイズを17にする
		$sheet->getStyle('A' . $row)->getFont()->setSize(17);

		// 出勤日数合計
		// $sum_daily_salary配列の値を合計する
		$sum_daily_salary = array_sum($sum_daily_salary);

		$col = Coordinate::stringFromColumnIndex($col_no - 1);
		$sheet->setCellValue($col . $row, number_format($sum_daily_salary));
		$sheet->getStyle($col . $row)
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		foreach ($payment_amount_sum as $key => $value) {
			$col = Coordinate::stringFromColumnIndex($col_no);

			$sheet->setCellValue($col . $row, number_format($value));
			$sheet->getStyle($col . $row)
				->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$col_no++;
		}

		foreach ($other_payment_amount_sum as $key => $value) {
			$col = Coordinate::stringFromColumnIndex($col_no);

			$sheet->setCellValue($col . $row, number_format($value));
			$sheet->getStyle($col . $row)
				->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			$col_no++;
		}



		// その他支給
		$total_other_payment = $salaries->map(function ($salary) {
			// 先生別の給与の総支給額の合計用変数
			return $salary->other_payment_amount;
		})->sum();
		$col = Coordinate::stringFromColumnIndex($col_no);

		$sheet->setCellValue($col . $row, number_format($total_other_payment));
		$sheet->getStyle($col . $row)
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$col_no++;

		$total_municipal_tax = $salaries->map(function ($salary) {
			// 先生別の給与の総支給額の合計用変数
			return $salary->municipal_tax;
		})->sum();
		// 住民税
		$col = Coordinate::stringFromColumnIndex($col_no);
		$sheet->setCellValue($col . $row, number_format($total_municipal_tax));
		$sheet->getStyle($col . $row)
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$col_no++;

		// 所得税
		$col = Coordinate::stringFromColumnIndex($col_no);
		$sheet->setCellValue($col . $row, number_format($total_income_tax_cost));
		$sheet->getStyle($col . $row)
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$col_no++;

		// 控除額合計
		$col = Coordinate::stringFromColumnIndex($col_no);
		$sheet->setCellValue($col . $row, number_format($total_deduction));
		$sheet->getStyle($col . $row)
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$col_no++;


		//年末調整
		$total_year_end_adjustment = $salaries->map(function ($salary) {
			// 先生別の給与の総支給額の合計用変数
			return $salary->year_end_adjustment;
		})->sum();
		$col = Coordinate::stringFromColumnIndex($col_no);
		$sheet->setCellValue($col . $row, number_format($total_year_end_adjustment));
		$sheet->getStyle($col . $row)
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$col_no++;


		// 交通費
		$total_transportation_expenses = $salaries->map(function ($salary) {
			// 先生別の給与の総支給額の合計用変数
			return $salary->transportation_expenses;
		})->sum();
		$col = Coordinate::stringFromColumnIndex($col_no);

		$sheet->setCellValue($col . $row, number_format($total_transportation_expenses));
		$sheet->getStyle($col . $row)
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$col_no++;

		// 総支給額合計の転記
		$col = Coordinate::stringFromColumnIndex($col_no);
		$sheet->setCellValue($col . $row, number_format($total_salary));
		$sheet->getStyle($col . $row)
			->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$col_no++;

		// 月次入力有無
		// 上長承認有無
		// $col_no += 2;
		// $col = Coordinate::stringFromColumnIndex($col_no);
		// $col_end_total_salaries = Coordinate::stringFromColumnIndex($col_no + 1);
		// $sheet->mergeCells($col . $row . ":" . $col_end_total_salaries . $row);
		$col_no++;

		// 給与承認有無
		$col = Coordinate::stringFromColumnIndex($col_no);
		$str = "給与承認完了数　" . $count_monthly_tightening . "　/　" . $total_monthly_tightening . "　";
		$sheet->setCellValue($col . '2', $str);

		// 行の文字サイズを変更
		// $sheet->getStyle($row . ":" . $row)->getFont()->setSize(12);


		// 行の高さを変更
		$sheet->getRowDimension($row)->setRowHeight(30);

		$sheet->getStyle('A5:' . $col . ($row))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('A4:' . $col . ($row))
			->getAlignment()->setWrapText(true);
		$spreadsheet->getActiveSheet()->getPageSetup()
			->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$spreadsheet->getActiveSheet()->getPageSetup()
			->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:' . $col . ($row));
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

		$spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.1);
		$spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.1);

		$spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);

		$sheet->setAutoFilter("A4:" . $col . ($row));
		// ダウンロード
		$writer = new Xlsx($spreadsheet);
		$filename = '非常勤給与一覧.xlsx';
		// ダウンロード
		ob_end_clean(); // this
		ob_start(); // and this
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');

		// $writer->save(storage_path() . '/app/excel/salary_list' . auth()->user()->id . '.xlsx');
		// $orderNote = '/var/www/html/shinzemi/storage/app/excel/salary_list' . auth()->user()->id . '.xlsx';
		// $command = "export HOME=/tmp && libreoffice --headless --convert-to pdf --outdir /var/www/html/shinzemi/storage/app/excel/ /var/www/html/shinzemi/storage/app/excel/ " . $orderNote;
		// exec($command);
		// $DLFileName = '非常勤給与一覧' . '.pdf';

		// $file_path_excel = '/var/www/html/shinzemi/storage/app/excel/salary_list' . auth()->user()->id . '.xlsx';
		// $file_path_pdf = '/var/www/html/shinzemi/storage/app/excel/salary_list' . auth()->user()->id . '.pdf';

		// //タイプをダウンロードと指定
		// header('Content-Type: application/pdf');

		// //ファイルのサイズを取得してダウンロード時間を表示する
		// header('Content-Length: ' . filesize($file_path_pdf));

		// //ダウンロードの指示・ダウンロード時のファイル名を指定
		// header('Content-Disposition: attachment; filename="' . $DLFileName . '"');
		// //ファイルを読み込んでダウンロード
		// readfile($file_path_pdf);

		// //保存したエクセルとpdf削除
		// unlink($file_path_pdf);
		// // unlink($file_path_excel);

		// ob_end_clean(); //バッファ消去
		// exit;
	}


	public function export_school_building_salary_list(Request $request)
	{
		$requestData = $request->all();
		$year_month = $requestData['year_month'];
		$month = explode('-', $year_month);
		// dd($year_month);
		$salaries = Salary::where('salaries.tightening_date', 'like', $year_month)->get();
		// $salaries = Salary::where('tightening_date', 'like', $year_month)->get();
		$job_descriptions = JobDescription::all();
		$other_job_descriptions = OtherJobDescription::all();
		$school_buildings = SchoolBuilding::all();
		$income_taxes = IncomeTax::all();
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/school_building_salary.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', $month[0]);
		$sheet->setCellValue('D1', $month[1]);
		$sheet->setCellValue('V1', '作成日:　　' . date('Y/m/d'));
		$not_description_column_names = [];
		foreach ($salaries as $salary) {
			$salary_sum = $salary->salary;
			$deduction_sum = $salary->health_insurance + $salary->welfare_pension + $salary->employment_insurance;
			$salary_sum = $salary_sum - $deduction_sum;

			$income_taxs = $income_taxes->filter(
				function ($value) use ($salary_sum) {
					return $value['or_more'] <= $salary_sum && $value['less_than'] >= $salary_sum;
				}
			);
			foreach ($income_taxs as $value) {
				$income_tax = $value;
			}
			if (empty($salary->user->description_column)) {
				$not_description_column_names[] = $salary->user->full_name;
			}
			// $salary->user->description_column;
			// $salary->user->dependents_count;
			if ($salary->user->description_column == 1) {

				switch ($salary->user->dependents_count) {
					case 1:
						$income_tax_cost = $income_tax->support1;
						break;
					case 2:
						$income_tax_cost = $income_tax->support2;
						break;
					case 3:
						$income_tax_cost = $income_tax->support3;
						break;
					case 4:
						$income_tax_cost = $income_tax->support4;
						break;
					case 5:
						$income_tax_cost = $income_tax->support5;
						break;
					case 6:
						$income_tax_cost = $income_tax->support6;
						break;
					case 7:
						$income_tax_cost = $income_tax->support7;
						break;
					default:
						$income_tax_cost = $income_tax->support0;
				}
			} else {
				$income_tax_cost = $income_tax->otsu;
			}
			if ($income_tax_cost == 3) {
				$income_tax_cost = floor($salary_sum * 3.063 / 100);
			}

			$lists[$salary->user->school_building]['school_building_name'] = $salary->user->school_buildings->name;
			if (isset($lists[$salary->user->school_building]['other_payment_amount'])) {
				$lists[$salary->user->school_building]['other_payment_amount'] += $salary->other_payment_amount;
			} else {
				$lists[$salary->user->school_building]['other_payment_amount'] = $salary->other_payment_amount;
			}
			if (isset($lists[$salary->user->school_building]['other_deduction_amount'])) {
				$lists[$salary->user->school_building]['other_deduction_amount'] += $salary->other_deduction_amount;
			} else {
				$lists[$salary->user->school_building]['other_deduction_amount'] = $salary->other_deduction_amount;
			}
			if (isset($lists[$salary->user->school_building]['transportation_expenses'])) {
				$lists[$salary->user->school_building]['transportation_expenses'] += $salary->transportation_expenses;
			} else {
				$lists[$salary->user->school_building]['transportation_expenses'] = $salary->transportation_expenses;
			}
			if (isset($lists[$salary->user->school_building]['year_end_adjustment'])) {
				$lists[$salary->user->school_building]['year_end_adjustment'] += $salary->year_end_adjustment;
			} else {
				$lists[$salary->user->school_building]['year_end_adjustment'] = $salary->year_end_adjustment;
			}
			if (isset($lists[$salary->user->school_building]['deduction'])) {
				$lists[$salary->user->school_building]['deduction'] += $salary->health_insurance;
			} else {
				$lists[$salary->user->school_building]['deduction'] = $salary->health_insurance;
			}
			if (isset($lists[$salary->user->school_building]['deduction'])) {
				$lists[$salary->user->school_building]['deduction'] += $salary->welfare_pension;
			} else {
				$lists[$salary->user->school_building]['deduction'] = $salary->welfare_pension;
			}
			//雇用保険
			if (isset($lists[$salary->user->school_building]['deduction'])) {
				$lists[$salary->user->school_building]['deduction'] += $salary->employment_insurance;
			} else {
				$lists[$salary->user->school_building]['deduction'] = $salary->employment_insurance;
			}
			//住民税
			if (isset($lists[$salary->user->school_building]['deduction'])) {
				$lists[$salary->user->school_building]['deduction'] += $salary->municipal_tax;
			} else {
				$lists[$salary->user->school_building]['deduction'] = $salary->municipal_tax;
			}
			//所得税
			if (isset($lists[$salary->user->school_building]['deduction'])) {
				$lists[$salary->user->school_building]['deduction'] += $income_tax_cost;
			} else {
				$lists[$salary->user->school_building]['deduction'] = $income_tax_cost;
			}
			$deduction = $salary->health_insurance + $salary->welfare_pension + $salary->employment_insurance + $salary->municipal_tax + $income_tax_cost;
			$year_end_adjustment = $salary->year_end_adjustment;
			$transportation_expenses = $salary->transportation_expenses;
			$other_payment_amount = $salary->other_payment_amount;
			$salary_sabtotal = $salary->salary + $transportation_expenses + $other_payment_amount;
			$salary_sabtotal = $salary_sabtotal + $year_end_adjustment - $deduction;

			if (isset($lists[$salary->user->school_building]['salary'])) {
				$lists[$salary->user->school_building]['salary'] += $salary_sabtotal;
			} else {
				$lists[$salary->user->school_building]['salary'] = $salary_sabtotal;
			}
			$salary_details = $salary->salary_detail;

			foreach ($salary_details as $salary_detail) {
				if (isset($lists[$salary->user->school_building][$salary_detail->job_description_id]['payment_amount'][$salary_detail->description_division])) {
					$lists[$salary->user->school_building][$salary_detail->job_description_id]['payment_amount'][$salary_detail->description_division] += $salary_detail->payment_amount;
				} else {
					$lists[$salary->user->school_building][$salary_detail->job_description_id]['payment_amount'][$salary_detail->description_division] = $salary_detail->payment_amount;
				}
			}
		}
		$not_description_column_name_cnt = is_countable($not_description_column_names) ? count($not_description_column_names) : 0;
		if ($not_description_column_name_cnt > 0) {

			$not_description_column_names = array_unique($not_description_column_names);
			$not_description_column_name = implode(',', $not_description_column_names);
			$error_string =  '下記ユーザーの摘要欄の設定がされていません。';
			$user_error_string =  $not_description_column_name;
			return redirect()->route('salary_output.school_building_index')->with('error', $error_string)->with('error_user', $user_error_string);
		}
		$row = 6;
		$job_description_total_amount = array(); //仕事内容の合計金額
		foreach ($job_descriptions as $job_description) {
			$job_description_total_amount[$job_description->id] = 0;
		}
		$other_job_description_total_amount = array(); //その他仕事内容の合計金額
		foreach ($other_job_descriptions as $other_job_description) {
			$other_job_description_total_amount[$other_job_description->id] = 0;
		}

		$other_payment_amount_sum = 0;
		$other_deduction_amount_sum = 0;
		$year_end_adjustment_sum = 0;
		$transportation_expenses_sum = 0;
		$salary_sum = 0;

		$last_school_buildings = $school_buildings->last(); //最後の校舎取得

		foreach ($school_buildings as $school_building_key => $school_building) {
			$sheet->setCellValue('A' . $row, $school_building->name);
			$sheet->mergeCells("A" . $row . ":G" . $row);

			$col_no = 8;
			foreach ($job_descriptions as $job_description_key => $job_description) {
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				if ($row == 6) {
					$sheet->insertNewColumnBefore($col, 3);
					$sheet->setCellValue($col . 4, $job_description->name . "\n支給額");
					$sheet->mergeCells($col . "4:" . $col_end . 5);
				}
				if (!isset($lists[$school_building->id][$job_description->id]['payment_amount'][1])) {
					$lists[$school_building->id][$job_description->id]['payment_amount'][1] = 0;
				}
				$sheet->setCellValue($col . $row, number_format($lists[$school_building->id][$job_description->id]['payment_amount'][1]));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				//合計加算していく
				$job_description_total_amount[$job_description->id] += $lists[$school_building->id][$job_description->id]['payment_amount'][1];
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$col_no += 3;
			}
			foreach ($other_job_descriptions as $other_job_description_key => $other_job_description) {
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				if ($row == 6) {
					$sheet->insertNewColumnBefore($col, 3);
					$sheet->setCellValue($col . 4, $other_job_description->name . "\n支給額");
					$sheet->mergeCells($col . "4:" . $col_end . 5);
				}
				if (!isset($lists[$school_building->id][$other_job_description->id]['payment_amount'][2])) {
					$lists[$school_building->id][$other_job_description->id]['payment_amount'][2] = 0;
				}
				$sheet->setCellValue($col . $row, number_format($lists[$school_building->id][$other_job_description->id]['payment_amount'][2]));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				//合計加算していく
				$other_job_description_total_amount[$other_job_description->id] += $lists[$school_building->id][$other_job_description->id]['payment_amount'][2];
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$col_no += 3;
			}
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			if (!isset($lists[$school_building->id]['other_payment_amount'])) {
				$lists[$school_building->id]['other_payment_amount'] = 0;
			}
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['other_payment_amount']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			$other_payment_amount_sum += $lists[$school_building->id]['other_payment_amount'];
			$col_no += 3;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			if (!isset($lists[$school_building->id]['deduction'])) {
				$lists[$school_building->id]['deduction'] = 0;
			}
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['deduction']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			$other_deduction_amount_sum += $lists[$school_building->id]['deduction'];
			$col_no += 3;

			if (!isset($lists[$school_building->id]['year_end_adjustment'])) {
				$lists[$school_building->id]['year_end_adjustment'] = 0;
			}

			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['year_end_adjustment']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			if (isset($lists[$school_building->id]['year_end_adjustment'])) {
				$year_end_adjustment_sum += $lists[$school_building->id]['year_end_adjustment'];
			}
			$col_no += 3;

			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			if (!isset($lists[$school_building->id]['transportation_expenses'])) {
				$lists[$school_building->id]['transportation_expenses'] = 0;
			}
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['transportation_expenses']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			$transportation_expenses_sum += $lists[$school_building->id]['transportation_expenses'];
			$col_no += 3;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			if (!isset($lists[$school_building->id]['salary'])) {
				$lists[$school_building->id]['salary'] = 0;
			}
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['salary']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			$salary_sum += $lists[$school_building->id]['salary'];
			$row++;

			//合計金額
			if ($school_building->id === $last_school_buildings->id) { //最後なら
				$sheet->setCellValue('A' . $row, "合計金額");
				$sheet->mergeCells("A" . $row . ":G" . $row);
				$col_no = 8;
				foreach ($job_descriptions as $job_description) { //仕事内容
					$col = Coordinate::stringFromColumnIndex($col_no);
					$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
					$sheet->setCellValue($col . $row, number_format($job_description_total_amount[$job_description->id])); //合計金額
					$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
					$sheet->mergeCells($col . $row . ":" . $col_end . $row);

					$col_no += 3;
				}
				foreach ($other_job_descriptions as $other_job_description) { //その他の仕事内容
					$col = Coordinate::stringFromColumnIndex($col_no);
					$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
					$sheet->setCellValue($col . $row, number_format($other_job_description_total_amount[$other_job_description->id])); //合計金額
					$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
					$sheet->mergeCells($col . $row . ":" . $col_end . $row);

					$col_no += 3;
				}
				//その他支給・控除・年末調整などの合計
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				$sheet->setCellValue($col . $row, number_format($other_payment_amount_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ

				$col_no += 3;

				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				$sheet->setCellValue($col . $row, number_format($other_deduction_amount_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ

				$col_no += 3;

				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$sheet->setCellValue($col . $row, number_format($year_end_adjustment_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				$col_no += 3;

				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				$sheet->setCellValue($col . $row, number_format($transportation_expenses_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				$col_no += 3;

				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				$sheet->setCellValue($col . $row, number_format($salary_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				$col_no += 3;
				$row++;
			}
		}


		$sheet->getStyle('A6:' . $col_end . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('A4:' . $col_end . ($row - 1))
			->getAlignment()->setWrapText(true);

		$filename = '校舎別非常勤給与一覧.xlsx';
		// ダウンロード
		ob_end_clean(); // this
		ob_start(); // and this
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function export_worked_school_building_salary_list(Request $request)
	{
		$requestData = $request->all();
		$year_month = $requestData['year_month'];
		$month = explode('-', $year_month);
		// dd($year_month);
		$salaries = Salary::where('salaries.tightening_date', 'like', $year_month)->get();
		// $salaries = Salary::where('tightening_date', 'like', $year_month)->get();
		$job_descriptions = JobDescription::all();
		$other_job_descriptions = OtherJobDescription::all();
		$school_buildings = SchoolBuilding::all();
		$income_taxes = IncomeTax::all();
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/school_building_salary.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', $month[0]);
		$sheet->setCellValue('D1', $month[1]);
		$sheet->setCellValue('V1', '作成日:　　' . date('Y/m/d'));
		$not_description_column_names = [];

		foreach ($salaries as $salary) {
			$salary_sum = $salary->salary;
			$deduction_sum = $salary->health_insurance + $salary->welfare_pension + $salary->employment_insurance;
			$salary_sum = $salary_sum - $deduction_sum;
			$user_ids[] = $salary->user_id;
			$salary_user = $salary->user;
			$user_school_buildings[$salary_user->id] = $salary_user->school_building;
			$income_taxs = $income_taxes->filter(
				function ($value) use ($salary_sum) {
					return $value['or_more'] <= $salary_sum && $value['less_than'] >= $salary_sum;
				}
			);
			foreach ($income_taxs as $value) {
				$income_tax = $value;
			}
			if (empty($salary_user->description_column)) {
				$not_description_column_names[] = $salary_user->full_name;
			}
			// $salary_user->description_column;
			// $salary_user->dependents_count;
			if ($salary_user->description_column == 1) {

				switch ($salary_user->dependents_count) {
					case 1:
						$income_tax_cost = $income_tax->support1;
						break;
					case 2:
						$income_tax_cost = $income_tax->support2;
						break;
					case 3:
						$income_tax_cost = $income_tax->support3;
						break;
					case 4:
						$income_tax_cost = $income_tax->support4;
						break;
					case 5:
						$income_tax_cost = $income_tax->support5;
						break;
					case 6:
						$income_tax_cost = $income_tax->support6;
						break;
					case 7:
						$income_tax_cost = $income_tax->support7;
						break;
					default:
						$income_tax_cost = $income_tax->support0;
				}
			} else {
				$income_tax_cost = $income_tax->otsu;
			}
			if ($income_tax_cost == 3) {
				$income_tax_cost = floor($salary_sum * 3.063 / 100);
			}

			$lists[$salary_user->school_building]['school_building_name'] = $salary_user->school_buildings->name;
			if (isset($lists[$salary_user->school_building]['other_payment_amount'])) {
				$lists[$salary_user->school_building]['other_payment_amount'] += $salary->other_payment_amount;
			} else {
				$lists[$salary_user->school_building]['other_payment_amount'] = $salary->other_payment_amount;
			}
			if (isset($lists[$salary_user->school_building]['other_deduction_amount'])) {
				$lists[$salary_user->school_building]['other_deduction_amount'] += $salary->other_deduction_amount;
			} else {
				$lists[$salary_user->school_building]['other_deduction_amount'] = $salary->other_deduction_amount;
			}
			if (isset($lists[$salary_user->school_building]['transportation_expenses'])) {
				$lists[$salary_user->school_building]['transportation_expenses'] += $salary->transportation_expenses;
			} else {
				$lists[$salary_user->school_building]['transportation_expenses'] = $salary->transportation_expenses;
			}
			if (isset($lists[$salary_user->school_building]['year_end_adjustment'])) {
				$lists[$salary_user->school_building]['year_end_adjustment'] += $salary->year_end_adjustment;
			} else {
				$lists[$salary_user->school_building]['year_end_adjustment'] = $salary->year_end_adjustment;
			}
			if (isset($lists[$salary_user->school_building]['deduction'])) {
				$lists[$salary_user->school_building]['deduction'] += $salary->health_insurance;
			} else {
				$lists[$salary_user->school_building]['deduction'] = $salary->health_insurance;
			}
			if (isset($lists[$salary_user->school_building]['deduction'])) {
				$lists[$salary_user->school_building]['deduction'] += $salary->welfare_pension;
			} else {
				$lists[$salary_user->school_building]['deduction'] = $salary->welfare_pension;
			}
			//雇用保険
			if (isset($lists[$salary_user->school_building]['deduction'])) {
				$lists[$salary_user->school_building]['deduction'] += $salary->employment_insurance;
			} else {
				$lists[$salary_user->school_building]['deduction'] = $salary->employment_insurance;
			}
			//住民税
			if (isset($lists[$salary_user->school_building]['deduction'])) {
				$lists[$salary_user->school_building]['deduction'] += $salary->municipal_tax;
			} else {
				$lists[$salary_user->school_building]['deduction'] = $salary->municipal_tax;
			}
			//所得税
			if (isset($lists[$salary_user->school_building]['deduction'])) {
				$lists[$salary_user->school_building]['deduction'] += $income_tax_cost;
			} else {
				$lists[$salary_user->school_building]['deduction'] = $income_tax_cost;
			}
			$deduction = $salary->health_insurance + $salary->welfare_pension + $salary->employment_insurance + $salary->municipal_tax + $income_tax_cost;
			$year_end_adjustment = $salary->year_end_adjustment;
			$transportation_expenses = $salary->transportation_expenses;
			$other_payment_amount = $salary->other_payment_amount;
			$salary_sabtotal = $salary->salary + $transportation_expenses + $other_payment_amount;
			$salary_sabtotal = $salary_sabtotal + $year_end_adjustment - $deduction;

			if (isset($lists[$salary_user->school_building]['salary'])) {
				$lists[$salary_user->school_building]['salary'] += $salary_sabtotal;
			} else {
				$lists[$salary_user->school_building]['salary'] = $salary_sabtotal;
			}
			$salary_details = $salary->salary_detail;

			foreach ($salary_details as $salary_detail) {
				if (isset($lists[$salary_user->school_building][$salary_detail->job_description_id]['payment_amount'][$salary_detail->description_division])) {
					$lists[$salary_user->school_building][$salary_detail->job_description_id]['payment_amount'][$salary_detail->description_division] += $salary_detail->payment_amount;
				} else {
					$lists[$salary_user->school_building][$salary_detail->job_description_id]['payment_amount'][$salary_detail->description_division] = $salary_detail->payment_amount;
				}
			}
		}

		$not_description_column_name_cnt = is_countable($not_description_column_names) ? count($not_description_column_names) : 0;
		if ($not_description_column_name_cnt > 0) {

			$not_description_column_names = array_unique($not_description_column_names);
			$not_description_column_name = implode(',', $not_description_column_names);
			$error_string =  '下記ユーザーの摘要欄の設定がされていません。';
			$user_error_string =  $not_description_column_name;
			return redirect()->route('salary_output.working_school_building_index')->with('error', $error_string)->with('error_user', $user_error_string);
		}
		$daily_salary_details = DailySalary::where('work_month', 'like', $year_month)
			->whereIn('user_id', $user_ids)
			->orderBy('user_id', 'asc')
			->orderBy('school_building_id', 'asc')
			->orderBy('job_description_id', 'asc')
			->get();
		$daily_other_salary_details = DailyOtherSalary::where('work_month', 'like', $year_month)
			->whereIn('user_id', $user_ids)
			->orderBy('user_id', 'asc')
			->orderBy('school_building', 'asc')
			->orderBy('job_description', 'asc')
			->get();
		$transportation_expense_details = TransportationExpense::where('work_month', 'like', $year_month)
			->whereIn('user_id', $user_ids)
			->orderBy('user_id', 'asc')
			->orderBy('school_building', 'asc')
			->get();
		$job_description_wages = JobDescriptionWage::whereIn('user_id', $user_ids)->get();
		$other_job_description_wages = OtherJobDescriptionWage::whereIn('user_id', $user_ids)->get();
		$working_time = 0;
		$current_user = "";
		$current_school_building = "";
		$current_job_description = "";
		foreach ($job_description_wages as $job_description_wage) {
			$wages[$job_description_wage->user_id][$job_description_wage->job_description_id] = ($job_description_wage->wage == "") ? 0 : $job_description_wage->wage;
		}
		foreach ($other_job_description_wages as $other_job_description_wage) {
			$other_wages[$other_job_description_wage->user_id][$other_job_description_wage->other_job_description_id] =  ($other_job_description_wage->wage == "") ? 0 : $other_job_description_wage->wage;
		}
		// dump('システム管理者　テスト中');
		// dump('しばらくお待ちください。');

		foreach ($daily_salary_details as $daily_salary_detail) {
			$current_job_description = $daily_salary_detail->job_description_id;
			$current_user = $daily_salary_detail->user_id;
			$working_time = $daily_salary_detail->working_time;

			$school_building_id = $daily_salary_detail->user->school_building;
			$current_school_building = $daily_salary_detail->school_building_id;
			if (!empty($current_user)) {
				$daily_working_hour = ($working_time == 0) ? 0 : ceil(($working_time * 10) / 60) / 10;
				$payment_amount = ceil($daily_working_hour * $wages[$current_user][$current_job_description]);
				if ($current_school_building != $user_school_buildings[$current_user]) {
					if (empty($lists[$current_school_building][$current_job_description]['payment_amount'][1])) {
						$lists[$current_school_building][$current_job_description]['payment_amount'][1] = $payment_amount;
					} else {
						$lists[$current_school_building][$current_job_description]['payment_amount'][1] += $payment_amount;
					}
					if (!empty($lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][1])) {
						// if($user_school_buildings[$current_user]==43
						// && $current_job_description==19){
						// 	dump($lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][1]);
						// }
						$lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][1] -= $payment_amount;
						// echo __LINE__ . "行目:::" . $current_school_building . ";;;;;" . $user_school_buildings[$current_user];
						// if($user_school_buildings[$current_user]==43 && $current_job_description==19){
						// 	dump($working_time);
						// 	dump($daily_working_hour);
						// 	dump($wages[$current_user][$current_job_description]);
						// 	dump($current_user);
						// 	dump($current_school_building);
						// 	dump($lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][1]);
						// 	dump($payment_amount);
						// }
					}else{
						$lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][1] = 0;
						$lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][1] -= $payment_amount;
					}
					if (!empty($lists[$user_school_buildings[$current_user]]['salary'])) {
						$lists[$user_school_buildings[$current_user]]['salary'] -= $payment_amount;
					}else{
						$lists[$user_school_buildings[$current_user]]['salary'] = 0;
						$lists[$user_school_buildings[$current_user]]['salary'] -= $payment_amount;
					}
					if (!empty($lists[$current_school_building]['salary'])) {
						$lists[$current_school_building]['salary'] += $payment_amount;
					} else {
						$lists[$current_school_building]['salary'] = $payment_amount;
					}
				}
				$working_time = 0;
			}
			// if ($lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][1] < 0) {
			// 	dump($lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][1]);
			// 	dump($current_user);
			// }

			// $current_user = $daily_salary_detail->user_id;
			// $current_school_building = $school_building_id;
			// $current_job_description = $daily_salary_detail->job_description_id;
		}
		// dd($lists);
		// dd('動作確認中');
		$current_user = "";
		$current_school_building = "";
		$payment_amount = 0;
		$current_job_description = "";

		foreach ($daily_other_salary_details as $daily_other_salary_detail) {
			// if(empty($daily_other_salary_detail->users)){
			// 	dd($daily_other_salary_detail);
			// }
			$school_building_id = $daily_other_salary_detail->users->school_building;
			$current_school_building = $daily_other_salary_detail->school_building;
			$current_job_description = $daily_other_salary_detail->job_description;
			$current_user = $daily_other_salary_detail->user_id;
			$payment_amount = 0;
			if(!empty($other_wages[$current_user][$current_job_description])){
				$payment_amount = $other_wages[$current_user][$current_job_description];
			}

			if (!empty($current_user)) {

					if ($current_school_building != $user_school_buildings[$current_user]) {
						if (empty($lists[$current_school_building][$current_job_description]['payment_amount'][2])) {
							$lists[$current_school_building][$current_job_description]['payment_amount'][2] = $payment_amount;
						} else {
							$lists[$current_school_building][$current_job_description]['payment_amount'][2] += $payment_amount;
						}
						if (!empty($lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][2])) {
							$lists[$user_school_buildings[$current_user]][$current_job_description]['payment_amount'][2] -= $payment_amount;
							// echo __LINE__ . "行目:::" . $current_school_building . ";;;;;" . $user_school_buildings[$current_user];
							// dump($current_user);
							// dump($current_user);
							// dump($payment_amount);
						}
						if (!empty($lists[$user_school_buildings[$current_user]]['salary'])) {
							$lists[$user_school_buildings[$current_user]]['salary'] -= $payment_amount;
						}
						if (!empty($lists[$current_school_building]['salary'])) {
							$lists[$current_school_building]['salary'] += $payment_amount;
						} else {
							$lists[$current_school_building]['salary'] = $payment_amount;
						}
					}
					$payment_amount = 0;
			}
			if(empty($other_wages[$current_user][$daily_other_salary_detail->job_description])){
				$other_wages[$current_user][$daily_other_salary_detail->job_description]=0;
			}
			$payment_amount += $other_wages[$current_user][$daily_other_salary_detail->job_description];

		}
		// dd('test');
		$current_user = "";
		$current_school_building = "";
		foreach ($transportation_expense_details as $transportation_expense_detail) {
			$school_building_id = $transportation_expense_detail->users->school_building;
			$current_user = $transportation_expense_detail->user_id;
			$current_school_building = $transportation_expense_detail->school_building;
			$payment_amount = 0;
			if($transportation_expense_detail->round_trip_flg == 1){
				$payment_amount = $transportation_expense_detail->fare;
			}elseif(!empty($transportation_expense_detail->unit_price)){
				$payment_amount = $transportation_expense_detail->unit_price;
			}
			if (!empty($current_user)) {

					if ($current_school_building != $user_school_buildings[$current_user]) {
						if (empty($lists[$current_school_building]['transportation_expenses'])) {
							$lists[$current_school_building]['transportation_expenses'] = $payment_amount;
						} else {
							$lists[$current_school_building]['transportation_expenses'] += $payment_amount;
						}
						if (!empty($lists[$user_school_buildings[$current_user]]['transportation_expenses'])) {
							$lists[$user_school_buildings[$current_user]]['transportation_expenses'] -= $payment_amount;
							// if($user_school_buildings[$current_user] == 47){
							// 	dump($current_user);
							// 	dump($current_school_building);
							// 	dump($lists[$user_school_buildings[$current_user]]['transportation_expenses']);
							// 	dump($payment_amount);
							// }
						}else{
							$lists[$user_school_buildings[$current_user]]['transportation_expenses'] = 0;
							$lists[$user_school_buildings[$current_user]]['transportation_expenses'] -= $payment_amount;
						}
						if (!empty($lists[$user_school_buildings[$current_user]]['salary'])) {
							$lists[$user_school_buildings[$current_user]]['salary'] -= $payment_amount;
						}else{
							$lists[$user_school_buildings[$current_user]]['salary'] = 0;
							$lists[$user_school_buildings[$current_user]]['salary'] -= $payment_amount;
						}
						if (!empty($lists[$current_school_building]['salary'])) {
							$lists[$current_school_building]['salary'] += $payment_amount;
						} else {
							$lists[$current_school_building]['salary'] = $payment_amount;
						}
					}
					$payment_amount = 0;
				}
			$payment_amount += empty($transportation_expense_detail->unit_price) ? 0 : $transportation_expense_detail->unit_price;
			if ($transportation_expense_detail->round_trip_flg == 1) {
				$payment_amount += empty($transportation_expense_detail->unit_price) ? 0 : $transportation_expense_detail->unit_price;
			}
		}



		$row = 6;
		$job_description_total_amount = array(); //仕事内容の合計金額
		foreach ($job_descriptions as $job_description) {
			$job_description_total_amount[$job_description->id] = 0;
		}
		$other_job_description_total_amount = array(); //その他仕事内容の合計金額
		foreach ($other_job_descriptions as $other_job_description) {
			$other_job_description_total_amount[$other_job_description->id] = 0;
		}

		$other_payment_amount_sum = 0;
		$other_deduction_amount_sum = 0;
		$year_end_adjustment_sum = 0;
		$transportation_expenses_sum = 0;
		$salary_sum = 0;

		$last_school_buildings = $school_buildings->last(); //最後の校舎取得

		foreach ($school_buildings as $school_building_key => $school_building) {
			$sheet->setCellValue('A' . $row, $school_building->name);
			$sheet->mergeCells("A" . $row . ":G" . $row);

			$col_no = 8;
			foreach ($job_descriptions as $job_description_key => $job_description) {
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				if ($row == 6) {
					$sheet->insertNewColumnBefore($col, 3);
					$sheet->setCellValue($col . 4, $job_description->name . "\n支給額");
					$sheet->mergeCells($col . "4:" . $col_end . 5);
				}
				if (!isset($lists[$school_building->id][$job_description->id]['payment_amount'][1])) {
					$lists[$school_building->id][$job_description->id]['payment_amount'][1] = 0;
				}
				$sheet->setCellValue($col . $row, number_format($lists[$school_building->id][$job_description->id]['payment_amount'][1]));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				//合計加算していく
				$job_description_total_amount[$job_description->id] += $lists[$school_building->id][$job_description->id]['payment_amount'][1];
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$col_no += 3;
			}
			foreach ($other_job_descriptions as $other_job_description_key => $other_job_description) {
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				if ($row == 6) {
					$sheet->insertNewColumnBefore($col, 3);
					$sheet->setCellValue($col . 4, $other_job_description->name . "\n支給額");
					$sheet->mergeCells($col . "4:" . $col_end . 5);
				}
				if (!isset($lists[$school_building->id][$other_job_description->id]['payment_amount'][2])) {
					$lists[$school_building->id][$other_job_description->id]['payment_amount'][2] = 0;
				}
				$sheet->setCellValue($col . $row, number_format($lists[$school_building->id][$other_job_description->id]['payment_amount'][2]));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				//合計加算していく
				$other_job_description_total_amount[$other_job_description->id] += $lists[$school_building->id][$other_job_description->id]['payment_amount'][2];
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$col_no += 3;
			}
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			if (!isset($lists[$school_building->id]['other_payment_amount'])) {
				$lists[$school_building->id]['other_payment_amount'] = 0;
			}
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['other_payment_amount']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			$other_payment_amount_sum += $lists[$school_building->id]['other_payment_amount'];
			$col_no += 3;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			if (!isset($lists[$school_building->id]['deduction'])) {
				$lists[$school_building->id]['deduction'] = 0;
			}
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['deduction']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			$other_deduction_amount_sum += $lists[$school_building->id]['deduction'];
			$col_no += 3;

			if (!isset($lists[$school_building->id]['year_end_adjustment'])) {
				$lists[$school_building->id]['year_end_adjustment'] = 0;
			}

			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['year_end_adjustment']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			if (isset($lists[$school_building->id]['year_end_adjustment'])) {
				$year_end_adjustment_sum += $lists[$school_building->id]['year_end_adjustment'];
			}
			$col_no += 3;

			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			if (!isset($lists[$school_building->id]['transportation_expenses'])) {
				$lists[$school_building->id]['transportation_expenses'] = 0;
			}
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['transportation_expenses']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			$transportation_expenses_sum += $lists[$school_building->id]['transportation_expenses'];
			$col_no += 3;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			if (!isset($lists[$school_building->id]['salary'])) {
				$lists[$school_building->id]['salary'] = 0;
			}
			$sheet->setCellValue($col . $row, number_format($lists[$school_building->id]['salary']));
			$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
			//合計加算していく
			$salary_sum += $lists[$school_building->id]['salary'];
			$row++;

			//合計金額
			if ($school_building->id === $last_school_buildings->id) { //最後なら
				$sheet->setCellValue('A' . $row, "合計金額");
				$sheet->mergeCells("A" . $row . ":G" . $row);
				$col_no = 8;
				foreach ($job_descriptions as $job_description) { //仕事内容
					$col = Coordinate::stringFromColumnIndex($col_no);
					$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
					$sheet->setCellValue($col . $row, number_format($job_description_total_amount[$job_description->id])); //合計金額
					$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
					$sheet->mergeCells($col . $row . ":" . $col_end . $row);

					$col_no += 3;
				}
				foreach ($other_job_descriptions as $other_job_description) { //その他の仕事内容
					$col = Coordinate::stringFromColumnIndex($col_no);
					$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
					$sheet->setCellValue($col . $row, number_format($other_job_description_total_amount[$other_job_description->id])); //合計金額
					$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
					$sheet->mergeCells($col . $row . ":" . $col_end . $row);

					$col_no += 3;
				}
				//その他支給・控除・年末調整などの合計
				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				$sheet->setCellValue($col . $row, number_format($other_payment_amount_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ

				$col_no += 3;

				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				$sheet->setCellValue($col . $row, number_format($other_deduction_amount_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ

				$col_no += 3;

				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$sheet->setCellValue($col . $row, number_format($year_end_adjustment_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				$col_no += 3;

				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				$sheet->setCellValue($col . $row, number_format($transportation_expenses_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				$col_no += 3;

				$col = Coordinate::stringFromColumnIndex($col_no);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 2);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);

				$sheet->setCellValue($col . $row, number_format($salary_sum));
				$sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Align::HORIZONTAL_RIGHT); //右寄せ
				$col_no += 3;
				$row++;
			}
		}


		$sheet->getStyle('A6:' . $col_end . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		$spreadsheet->getActiveSheet()->getStyle('A4:' . $col_end . ($row - 1))
			->getAlignment()->setWrapText(true);

		$filename = '校舎別非常勤給与一覧.xlsx';
		// ダウンロード
		ob_end_clean(); // this
		ob_start(); // and this
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function export_salary_index()
	{
		// dd('export_salary_index');
		return view('salary_output.export_salary_index');
	}
	public function export_salary(Request $request)
	{

		$request = $request->all();
		// 年月
		$year_month = $request['year_month'];
		// 振込日
		$date = $request['date'];

		function mb_str_pad2($input, $pad_length, $pad_string = " ", $pad_style = STR_PAD_RIGHT, $encoding = "UTF-8")
		{
			$mb_pad_length = strlen($input) - mb_strlen($input, $encoding) + $pad_length;
			return str_pad($input, $mb_pad_length, $pad_string, $pad_style);
		}
		$str = "11100000056437ｼﾝｶﾞｸｾﾞﾐﾅｰﾙ                             ";
		$str .= date("md", strtotime($date));
		$str .= "0162ﾅﾝﾄｷﾞﾝｺｳ       100ｶﾞｸｴﾝﾏｴｼﾃﾝ     10205083                 \r\n";
		$branch_banks = BranchBank::all();
		foreach ($branch_banks as $branch_bank) {
			$banks_branch_bank[$branch_bank->bank_id][$branch_bank->code]['name_kana'] = $branch_bank->name_kana;
		}
		$income_taxes = IncomeTax::all();

		$salaries = salary::where("tightening_date", $year_month)->get();
		$cnt = 0;
		$sum = 0;
		foreach ($salaries as $salary) {
			$user = $salary->user;
			$bank = $user->bank;
			if (empty($bank->code)) {
				$error_string = "次のユーザーの銀行の登録がありません。登録してから再度データ出力を行ってください。 ";
				return redirect()->route('salary_output.export_salary_index')->with('error', $error_string)->with('error_user', $user->full_name);
			}

			$bank_code = $bank->code;
			$bank_name_kana = $bank->name_kana;
			$branch_bank_code = $user->branch_id;
			if (empty($banks_branch_bank[$bank->id][$user->branch_id]['name_kana'])) {
				dd($user->id);
			}
			$branch_bank_name_kana = $banks_branch_bank[$bank->id][$user->branch_id]['name_kana'];
			$bank_number = $user->account_number;
			$bank_name_kana = mb_convert_kana($bank_name_kana, 'k', 'utf-8');
			$bank_name_kana = mb_convert_kana($bank_name_kana, 'h', 'utf-8');
			$branch_bank_name_kana = mb_convert_kana($branch_bank_name_kana, 'k', 'utf-8');
			$branch_bank_name_kana = mb_convert_kana($branch_bank_name_kana, 'h', 'utf-8');
			$bank_name_kana = mb_str_pad2($bank_name_kana, 15);
			$branch_bank_name_kana = mb_str_pad2($branch_bank_name_kana, 15);
			$name = mb_convert_kana($user->recipient_name, 'sk', 'utf-8');
			$bank_type = config("const.account_type")[$user->account_type];
			switch ($bank_type) {
				case '普通':
					$bank_type_no = 1;
					break;
				case '当座':
					$bank_type_no = 2;
					break;
				default:
					$bank_type_no = 1;
					break;
			}
			$deduction_sum = $salary->health_insurance + $salary->welfare_pension + $salary->employment_insurance;
			$salary_sum = $salary->salary - $deduction_sum;
			$income_taxs = $income_taxes->filter(
				function ($value) use ($salary_sum) {
					return $value['or_more'] <= $salary_sum && $value['less_than'] >= $salary_sum;
				}
			);
			foreach ($income_taxs as $value) {
				$income_tax = $value;
			}
			$user->description_column;
			$user->dependents_count;
			if ($user->description_column == 1) {

				switch ($user->dependents_count) {
					case 1:
						$income_tax_cost = $income_tax->support1;
						break;
					case 2:
						$income_tax_cost = $income_tax->support2;
						break;
					case 3:
						$income_tax_cost = $income_tax->support3;
						break;
					case 4:
						$income_tax_cost = $income_tax->support4;
						break;
					case 5:
						$income_tax_cost = $income_tax->support5;
						break;
					case 6:
						$income_tax_cost = $income_tax->support6;
						break;
					case 7:
						$income_tax_cost = $income_tax->support7;
						break;
					default:
						$income_tax_cost = $income_tax->support0;
				}
			} else {
				$income_tax_cost = $income_tax->otsu;
			}
			if ($income_tax_cost == 3) {
				$income_tax_cost = floor($salary_sum * 3.063 / 100);
			}
			$deduction = $deduction_sum + $income_tax_cost + $salary->municipal_tax;

			$salary_sabtotal = $salary->salary + $salary->transportation_expenses + $salary->other_payment_amount;
			$salary_sabtotal = $salary_sabtotal + $salary->year_end_adjustment - $deduction;

			$name = mb_convert_kana($name, 'h', 'utf-8');
			$name = mb_str_pad2($name, 30);
			$str .= "2";
			$str .= $bank_code . $bank_name_kana;
			$str .= $branch_bank_code . $branch_bank_name_kana; //
			$str .= "    "; //
			$str .= $bank_type_no; //預金種目
			$str .= str_pad($bank_number, 7, "0", STR_PAD_LEFT); //口座番号
			$str .= $name;
			$str .= str_pad($salary_sabtotal, 10, "0", STR_PAD_LEFT); //振込金額
			$str .= "0";
			$str .= str_pad($user->id, 20, "0", STR_PAD_LEFT); //振込金額
			$str .= "0        "; //
			$str .= "\r\n"; //
			$sum += $salary_sabtotal;
			$cnt++;
		}
		$str .= "8"; //
		$str .= str_pad($cnt, 6, "0", STR_PAD_LEFT); //
		$str .= str_pad($sum, 12, "0", STR_PAD_LEFT); //
		$str .= str_repeat(' ', 101);
		$str .= "\r\n"; //
		$str .= "9"; //
		$str .= str_repeat(' ', 119);
		$fileName = "非常勤給与データ作成" . date("Y-m", strtotime($year_month));
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename=' . $fileName);
		echo mb_convert_encoding($str, "SJIS", "UTF-8");  //←UTF-8のままで良ければ不要です。
		exit;
		// file_put_contents($filename, $str);
	}
	public function export_wage_ledger_index(Request $request)
	{
		$hire_date_list = User::get_hire_date_list();
		$school_buildings = SchoolBuilding::all()->pluck('name', 'id');
		$users_name = User::whereNull('retirement_date')->where('employment_status', 3)->get()->pluck('full_name', 'full_name');

		$search_name = $request->get("name");
		$search_school_building = $request->get("school_building");
		$search_employment_status = $request->get("employment_status");
		$search_occupation = $request->get("occupation");
		$search_work_status = $request->get("work_status");
		$search_user_id = $request->get("user_id");
		$search_join_year = $request->get("join_year");
		$search_retire_year = $request->get("retire_year");
		$search_names = [];
		$user_search['name'] = $search_name;
		$user_search['school_building'] = $search_school_building;
		$user_search['employment_status'] = $search_employment_status;
		$user_search['occupation'] = $search_occupation;
		$user_search['work_status'] = $search_work_status;
		$user_search['user_id'] = $search_user_id;
		$user_search['join_year'] = $search_join_year;
		$user_search['retire_year'] = $search_retire_year;
		if (!empty($search_name)) {
			$search_names = explode(",", str_replace("　", ",", $search_name));
		}
		if (!(empty($user_search['name']) && empty($user_search['school_building']) && empty($user_search['employment_status']) && empty($user_search['occupation']) && empty($user_search['work_status']) && empty($user_search['user_id']) && empty($user_search['join_year']) && empty($user_search['retire_year']))) {
			$users = User::when(!empty($search_names), function ($query) use ($search_names) {
				return $query->where('last_name', $search_names[0])->where('first_name', $search_names[1]);
			})->when(!empty($search_school_building), function ($query) use ($search_school_building) {
				return $query->where('school_building', $search_school_building);
			})->where('employment_status', 3)->when(!empty($search_occupation), function ($query) use ($search_occupation) {
				return $query->where('occupation', $search_occupation);
			})->whereNull('retirement_date')->when(!empty($search_user_id), function ($query) use ($search_user_id) {
				return $query->where('user_id', $search_user_id);
			})->when(!empty($search_join_year), function ($query) use ($search_join_year) {
				return $query->whereYear('hiredate', $search_join_year);
			})->get();
			$users_count = $users->count();

			if ($users_count > 0) {
				$users_info['users_total'] = $users_count;
				$users_info['first_item'] = 1;
				$users_info['last_item'] = $users_count;
			} else {
				$users_info['users_total'] = 0;
				$users_info['first_item'] = 0;
				$users_info['last_item'] = 0;
			}
		} else {
			$users = [];
			$users_info['users_total'] = 0;
			$users_info['first_item'] = 0;
			$users_info['last_item'] = 0;
		}
		for ($i = 2011; $i <= date('Y'); $i++) {
			$year[$i] = $i;
		}
		return view("salary_output.export_wage_ledger_index", compact("users", "users_name", "school_buildings", "user_search", "year", "hire_date_list", "users_info"));
	}
	public function export_wage_ledger(Request $request)
	{
		$requestData = $request->all();
		$user_ids = $requestData['user_ids'];
		$year = $requestData['year'];

		$salaries = Salary::whereIn('user_id', $user_ids)->where('tightening_date', 'like', $year . '%')->get();
		// return redirect()->route('salary_output.export_wage_ledger_index')->with('error', '該当する情報がありませんでした。');
		$income_taxes = IncomeTax::all();
		$daily_salaries = DailySalary::whereIn('user_id', $user_ids)->where('work_month', 'like', $year . '%')->get();
		// $salaries = Salary::where('tightening_date', 'like', $year_month)->get();
		$job_descriptions = JobDescription::all();
		$other_job_descriptions = OtherJobDescription::all();
		$school_buildings = SchoolBuilding::all();
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/wage_ledger.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', $year . ' 年度　賃金台帳');
		foreach ($salaries as $salary) {
			$salary_details = $salary->salary_detail;
			foreach ($salary_details as $salary_detail) {
				if (empty($lists[$salary->user_id][$salary->tightening_date][$salary_detail->description_division][$salary_detail->job_description_id])) {
					$lists[$salary->user_id][$salary->tightening_date][$salary_detail->description_division][$salary_detail->job_description_id] = 0;
				}
				$lists[$salary->user_id][$salary->tightening_date][$salary_detail->description_division][$salary_detail->job_description_id] += $salary_detail->payment_amount;

			}
			$user_name[$salary->user_id] = $salary->user->full_name;

			$user_info[$salary->user_id] = $salary->user;

			$lists[$salary->user_id][$salary->tightening_date]['other_payment_amount'] = $salary->other_payment_amount;
			$lists[$salary->user_id][$salary->tightening_date]['health_insurance'] = $salary->health_insurance;
			$lists[$salary->user_id][$salary->tightening_date]['welfare_pension'] = $salary->welfare_pension;
			$lists[$salary->user_id][$salary->tightening_date]['employment_insurance'] = $salary->employment_insurance;
			$lists[$salary->user_id][$salary->tightening_date]['municipal_tax'] = $salary->municipal_tax;
			$salary_sum = $salary->salary + $salary->transportation_expenses;
			$lists[$salary->user_id][$salary->tightening_date]['transportation_expenses'] = $salary->transportation_expenses;
			$lists[$salary->user_id][$salary->tightening_date]['salary_sum'] = $salary_sum;
			$income_taxs = $income_taxes->filter(
				function ($value) use ($salary_sum) {
					return $value['or_more'] < $salary_sum && $value['less_than'] > $salary_sum;
				}
			);
			foreach ($income_taxs as $value) {
				$income_tax = $value;
			}

			$salary->user->description_column;
			$salary->user->dependents_count;
			if ($salary->user->description_column == 1) {

				switch ($salary->user->dependents_count) {
					case 1:
						$income_tax_cost = $income_tax->support1;
						break;
					case 2:
						$income_tax_cost = $income_tax->support2;
						break;
					case 3:
						$income_tax_cost = $income_tax->support3;
						break;
					case 4:
						$income_tax_cost = $income_tax->support4;
						break;
					case 5:
						$income_tax_cost = $income_tax->support5;
						break;
					case 6:
						$income_tax_cost = $income_tax->support6;
						break;
					case 7:
						$income_tax_cost = $income_tax->support7;
						break;
					default:
						$income_tax_cost = $income_tax->support0;
				}
			} else {
				$income_tax_cost = $income_tax->otsu;
			}
			$lists[$salary->user_id][$salary->tightening_date]['income_tax'] = $income_tax_cost;
		}
		foreach ($daily_salaries as $daily_salary) {
			if (empty($lists[$daily_salary->user_id][$daily_salary->work_month]['work_date'])) {
				$lists[$daily_salary->user_id][$daily_salary->work_month]['work_date'][] = $daily_salary->work_date;
			} else {
				if (!in_array($daily_salary->work_date, $lists[$daily_salary->user_id][$daily_salary->work_month]['work_date'])) {
					$lists[$daily_salary->user_id][$daily_salary->work_month]['work_date'][] = $daily_salary->work_date;
				}
			}
			if (empty($lists[$daily_salary->user_id][$daily_salary->work_month]['number_of_hours'])) {
				$lists[$daily_salary->user_id][$daily_salary->work_month]['number_of_hours'] = $daily_salary->working_time;
			} else {
				$lists[$daily_salary->user_id][$daily_salary->work_month]['number_of_hours'] += $daily_salary->working_time;
			}
		}
		$row_cnt = 0;
		if ($job_descriptions->count() > 7) {
			$row_cnt = $job_descriptions->count() - 7;
			// 交通費分
			$row_cnt++;
			$sheet->insertNewRowBefore(13, $row_cnt);
		}
		$row = 8;

		foreach ($job_descriptions as $job_description) {
			$sheet->setCellValue("B" . $row, $job_description->name);
			$row++;
		}
		$sheet->setCellValue("B" . $row, '交通費');
		$row++;

		$error_string = '';
		foreach ($user_ids as $user_id) {
			if (!empty($user_name[$user_id])) {
				$clonedWorksheet = clone $spreadsheet->getSheetByName('Sheet1');
				$sheetname = $user_name[$user_id];
				$clonedWorksheet->setTitle($sheetname);
				$spreadsheet->addSheet($clonedWorksheet);
				$sheet = $spreadsheet->getSheetByName($sheetname);

				// F2に生年月日
				$sheet->setCellValue("F2", $user_info[$user_id]->birthday);

				// H2に入社日
				$sheet->setCellValue("H2", $user_info[$user_id]->hire_date);

				// J2に所属
				$sheet->setCellValue("J2", $user_info[$user_id]->school_buildings->name);

				// L2に氏名
				$sheet->setCellValue("L2", $user_name[$user_id]);

				// O2に性別　sex　1:男性 2:女性
				if ($user_info[$user_id]->sex == 1) {
					$sheet->setCellValue("O2", "男性");
				}
				if ($user_info[$user_id]->sex == 2) {
					$sheet->setCellValue("O2", "女性");
				}


				for ($month = 1; $month <= 12; $month++) {
					$col_no = $month + 2;
					$col = Coordinate::stringFromColumnIndex($col_no);

					$year_month = $year . "-" . sprintf('%02d', $month);
					$number_of_days = 0;
					if (!empty($lists[$user_id][$year_month]['work_date'])) {
						$number_of_days = is_countable($lists[$user_id][$year_month]['work_date']) ? count($lists[$user_id][$year_month]['work_date']) : 0;
					}
					$number_of_hours = 0;
					if (!empty($lists[$user_id][$year_month]['number_of_hours'])) {
						$number_of_hours = floor($lists[$user_id][$year_month]['number_of_hours']/60*10)/10;
					}

					$sheet->setCellValue($col . '4', $month . " 月分");
					$sheet->setCellValue($col . '5', $number_of_days);
					$sheet->setCellValue($col . '6', $number_of_hours);
					$row = 8;
					foreach ($job_descriptions as $job_description) {
						$payment_amount = 0;
						if (!empty($lists[$user_id][$year_month][1][$job_description->id])) {
							$payment_amount = $lists[$user_id][$year_month][1][$job_description->id];
						}
						$sheet->getRowDimension($row)->setRowHeight(14);
						$sheet->setCellValue($col . $row, $payment_amount);

						// 直前のセルの書式を数字にしてカンマ
						$sheet->getStyle($col . $row)
							->getNumberFormat()
							->setFormatCode('#,##0');


						$row++;
					}
					$sheet->getRowDimension($row)->setRowHeight(14);
					$transportation_expenses = 0;

					if (!empty($lists[$user_id][$year_month]['transportation_expenses'])) {
						$transportation_expenses = $lists[$user_id][$year_month]['transportation_expenses'];
					}

					$sheet->setCellValue($col . $row, $transportation_expenses);

						// 直前のセルの書式を数字にしてカンマ
						$sheet->getStyle($col . $row)
							->getNumberFormat()
							->setFormatCode('#,##0');


							$row++;
					for ($i = 0; $i < $row_cnt; $i++) {
						$sheet->getRowDimension($row)->setRowHeight(14);
					}
					$row = 15 + $row_cnt;
					$sheet->setCellValue($col . $row, "=SUM(" . $col . "8:" . $col . ($row - 1) . ")");

					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$row++;
					$other_payment_amount = 0;
					if (!empty($lists[$user_id][$year_month]['other_payment_amount'])) {
						$other_payment_amount = $lists[$user_id][$year_month]['other_payment_amount'];
					}
					$sheet->setCellValue($col . $row, $other_payment_amount);

					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$row++;
					$sheet->setCellValue($col . $row, "=SUM(" . $col . ($row - 2) . ":" . $col . ($row - 1)  . ")");
					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$row++;

					$health_insurance = 0;
					$welfare_pension = 0;
					$employment_insurance = 0;
					$municipal_tax = 0;
					$income_tax = 0;
					$salary_sum = 0;
					if (!empty($lists[$user_id][$year_month]['health_insurance'])) {
						$health_insurance = $lists[$user_id][$year_month]['health_insurance'];
					}
					if (!empty($lists[$user_id][$year_month]['welfare_pension'])) {
						$welfare_pension = $lists[$user_id][$year_month]['welfare_pension'];
					}
					if (!empty($lists[$user_id][$year_month]['employment_insurance'])) {
						$employment_insurance = $lists[$user_id][$year_month]['employment_insurance'];
					}
					if (!empty($lists[$user_id][$year_month]['municipal_tax'])) {
						$municipal_tax = $lists[$user_id][$year_month]['municipal_tax'];
					}
					if (!empty($lists[$user_id][$year_month]['income_tax'])) {
						$income_tax = $lists[$user_id][$year_month]['income_tax'];
					}
					if (!empty($lists[$user_id][$year_month]['salary_sum'])) {
						$salary_sum = $lists[$user_id][$year_month]['salary_sum'];
					}
					$sheet->setCellValue($col . $row, $health_insurance);

					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$row++;
					$sheet->setCellValue($col . $row, $welfare_pension);
					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$row++;
					$sheet->setCellValue($col . $row, $employment_insurance);

					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$row++;
					$sheet->setCellValue($col . $row, $municipal_tax);

					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$row++;
					$sheet->setCellValue($col . $row, $income_tax);

					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');

					$row++;
					$row++;
					$deduction_sum = $health_insurance + $welfare_pension + $employment_insurance + $municipal_tax;
					$sheet->setCellValue($col . $row, $deduction_sum);

					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');


					$row++;
					$row++;
					$sheet->setCellValue($col . $row, $salary_sum);
					// 直前のセルの書式を数字にしてカンマ
					$sheet->getStyle($col . $row)
						->getNumberFormat()
						->setFormatCode('#,##0');
				}
			} else {

				$user_for_error = User::find($user_id);
				$error_string .= $user_for_error->FullName . 'さん、';
			}
		}

		if (!empty($error_string)) {
			return redirect()->back()->withInput()->with('error_message', $error_string . 'のデータがありません。');
		}


		// Sheet1が名前のシートを削除する
		$spreadsheet->removeSheetByIndex($spreadsheet->getIndex(
			$spreadsheet->getSheetByName("Sheet1")
		));

		// アクティブシートの設定を一番左にしたい
		$spreadsheet->setActiveSheetIndex(0);

		$filename = '賃金台帳.xlsx';
		// ダウンロード
		ob_end_clean(); // this
		ob_start(); // and this
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

	public function export_payslip(Request $request)
	{
		$requestData = $request->all();
		$salaries = Salary::whereIn('user_id', $requestData['salary_ids'])->where('tightening_date', $requestData['work_month'])->get();
		$income_taxes = IncomeTax::all();

		$month = explode('-', $requestData['work_month']);
		$job_descriptions = JobDescription::all();
		$other_job_descriptions = OtherJobDescription::all();
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/payslip.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', $month[0]);
		$sheet->setCellValue('D1', $month[1]);
		foreach ($salaries as $salary) {
			$user = $salary->user;
			$sheetname = $user->user_id . $user->last_name . $user->first_name;
			$clonedWorksheet = clone $spreadsheet->getSheetByName('Sheet1');
			$clonedWorksheet->setTitle($sheetname);
			$spreadsheet->addSheet($clonedWorksheet);
			$sheet = $spreadsheet->getSheetByName($sheetname); //weatherシート取得

			$sheet->setCellValue('E2', $user->user_id);
			$sheet->setCellValue('K2', $user->last_name . $user->first_name);
			$sheet->setCellValue('V2', $user->school_buildings->name);
			$salary_details = $salary->salary_detail;
			foreach ($salary_details as $salary_detail) {
				$list[$user->id][$salary_detail->job_description_id][$salary_detail->description_division]['payment_amount'] = $salary_detail->payment_amount;
				if (empty($list[$user->id][$salary_detail->job_description_id][$salary_detail->description_division]['count'])) {
					$list[$user->id][$salary_detail->job_description_id][$salary_detail->description_division]['count'] = 0;
				}
				$list[$user->id][$salary_detail->job_description_id][$salary_detail->description_division]['count']++;
				if (empty($list[$user->id][$salary_detail->job_description_id][$salary_detail->description_division]['count'])) {
					$list[$user->id][$salary_detail->job_description_id][$salary_detail->description_division]['count'] = 0;
				}
			}

			$deduction_sum = $salary->health_insurance + $salary->welfare_pension + $salary->employment_insurance;
			$salary_sum = $salary->salary - $deduction_sum;
			$income_taxs = $income_taxes->filter(
				function ($value) use ($salary_sum) {
					return $value['or_more'] <= $salary_sum && $value['less_than'] >= $salary_sum;
				}
			);
			foreach ($income_taxs as $value) {
				$income_tax = $value;
			}
			$salary->user->description_column;
			$salary->user->dependents_count;
			if ($salary->user->description_column == 1) {

				switch ($salary->user->dependents_count) {
					case 1:
						$income_tax_cost = $income_tax->support1;
						break;
					case 2:
						$income_tax_cost = $income_tax->support2;
						break;
					case 3:
						$income_tax_cost = $income_tax->support3;
						break;
					case 4:
						$income_tax_cost = $income_tax->support4;
						break;
					case 5:
						$income_tax_cost = $income_tax->support5;
						break;
					case 6:
						$income_tax_cost = $income_tax->support6;
						break;
					case 7:
						$income_tax_cost = $income_tax->support7;
						break;
					default:
						$income_tax_cost = $income_tax->support0;
				}
			} else {
				$income_tax_cost = $income_tax->otsu;
			}
			if ($income_tax_cost == 3) {
				$income_tax_cost = floor($salary_sum * 3.063 / 100);
			}
			$deduction = $deduction_sum + $income_tax_cost + $salary->municipal_tax;
			$salary_sabtotal = $salary->salary + $salary->transportation_expenses + $salary->other_payment_amount;
			$salary_sabtotal = $salary_sabtotal + $salary->year_end_adjustment - $deduction;



			$col_no = 5;
			$row = 8;
			$max_row = 12;
			foreach ($job_descriptions as $job_description) {
				if ($col_no > 28) {
					$sheet->getStyle('B' . $row . ':AB' . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
					$col_no = 5;
					$row += 2;
					if ($row == $max_row) {
						$max_row += 2;
						$sheet->setCellValue("B" . $row, "明細");
						$sheet->setCellValue("B" . ($row + 1), "金額(円)");
						$sheet->mergeCells("B" . $row . ":" . "D" . $row);
						$sheet->mergeCells("B" . ($row + 1) . ":" . "D" . ($row + 1));
					}
				}
				$col = Coordinate::stringFromColumnIndex($col_no);
				$sheet->setCellValue($col . $row, $job_description->name);
				$payment_amount = empty($list[$user->id][$job_description->id][1]['payment_amount']) ? 0 : $list[$user->id][$job_description->id][1]['payment_amount'];
				$sheet->setCellValue($col . ($row + 1), $payment_amount);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 3);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
				$col_no += 4;
			}
			foreach ($other_job_descriptions as $other_job_description) {
				if ($col_no > 28) {
					$sheet->getStyle('B' . $row . ':AB' . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
					$col_no = 5;
					$row += 2;
					if ($row == $max_row) {
						$max_row += 2;
						$sheet->setCellValue("B" . $row, "明細");
						$sheet->setCellValue("B" . ($row + 1), "金額(円)");
						$sheet->mergeCells("B" . $row . ":" . "D" . $row);
						$sheet->mergeCells("B" . ($row + 1) . ":" . "D" . ($row + 1));
					}
				}

				$col = Coordinate::stringFromColumnIndex($col_no);
				$sheet->setCellValue($col . $row, $other_job_description->name);
				$payment_amount = empty($list[$user->id][$other_job_description->id][2]['payment_amount']) ? 0 : $list[$user->id][$other_job_description->id][2]['payment_amount'];
				$sheet->setCellValue($col . ($row + 1), $payment_amount);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 3);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
				$col_no += 4;
			}
			if ($col_no > 28) {
				$sheet->getStyle('B' . $row . ':AB' . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$col_no = 5;
				$row += 2;
				if ($row == $max_row) {
					$max_row += 2;
					$sheet->setCellValue("B" . $row, "明細");
					$sheet->setCellValue("B" . ($row + 1), "金額(円)");
					$sheet->mergeCells("B" . $row . ":" . "D" . $row);
					$sheet->mergeCells("B" . ($row + 1) . ":" . "D" . ($row + 1));
				}
			}
			$col = Coordinate::stringFromColumnIndex($col_no);
			$sheet->setCellValue($col . $row, "交通費");
			$sheet->setCellValue($col . ($row + 1), $salary->transportation_expenses);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 3);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
			$col_no += 4;
			if ($col_no > 28) {
				$sheet->getStyle('B' . $row . ':AB' . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$col_no = 5;
				$row += 2;
				if ($row == $max_row) {
					$max_row += 2;
					$sheet->setCellValue("B" . $row, "明細");
					$sheet->setCellValue("B" . ($row + 1), "金額(円)");
					$sheet->mergeCells("B" . $row . ":" . "D" . $row);
					$sheet->mergeCells("B" . ($row + 1) . ":" . "D" . ($row + 1));
				}
			}
			$col = Coordinate::stringFromColumnIndex($col_no);
			$sheet->setCellValue($col . $row, "その他支給額");
			$sheet->setCellValue($col . ($row + 1), $salary->other_payment_amount);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 3);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
			$col_no += 4;
			if ($col_no > 28) {
				$sheet->getStyle('B' . $row . ':AB' . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$col_no = 5;
				$row += 2;
				if ($row == $max_row) {
					$max_row += 2;
					$sheet->setCellValue("B" . $row, "明細");
					$sheet->setCellValue("B" . ($row + 1), "金額(円)");
					$sheet->mergeCells("B" . $row . ":" . "D" . $row);
					$sheet->mergeCells("B" . ($row + 1) . ":" . "D" . ($row + 1));
				}
			}
			$col = Coordinate::stringFromColumnIndex($col_no);
			$sheet->setCellValue($col . $row, "その他控除額");
			$sheet->setCellValue($col . ($row + 1), $deduction);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 3);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
			$col_no += 4;
			if ($col_no > 28) {
				$sheet->getStyle('B' . $row . ':AB' . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
				$col_no = 5;
				$row += 2;
				if ($row == $max_row) {
					$max_row += 2;
					$sheet->setCellValue("B" . $row, "明細");
					$sheet->setCellValue("B" . ($row + 1), "金額(円)");
					$sheet->mergeCells("B" . $row . ":" . "D" . $row);
					$sheet->mergeCells("B" . ($row + 1) . ":" . "D" . ($row + 1));
				}
			}
			$col = Coordinate::stringFromColumnIndex($col_no);
			$sheet->setCellValue($col . $row, "年末調整");
			$sheet->setCellValue($col . ($row + 1), $salary->year_end_adjustment);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 3);
			$sheet->mergeCells($col . $row . ":" . $col_end . $row);
			$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
			$sheet->getStyle('B' . $row . ':' . $col_end . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			$row += 2;
			$sheet->setCellValue("Y" . $row, "総支給額");
			$sheet->setCellValue("Y" . ($row + 1), $salary_sabtotal);
			$sheet->mergeCells("Y" . $row . ":" . "AB" . $row);
			$sheet->mergeCells("Y" . ($row + 1) . ":AB" . ($row + 1));
			$sheet->getStyle('Y' . $row . ":AB" . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			$row += 3;
			$sheet->setCellValue("B" . $row, "連絡欄");
			$sheet->getStyle('B' . $row . ':AB' . ($row + 5))->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			$row += 2;
			$sheet->setCellValue("C" . $row, "その他支給事由");
			$sheet->setCellValue("G" . $row, $salary->other_payment_reason);
			$row += 2;
			$sheet->setCellValue("C" . $row, "その他控除事由");
			$sheet->setCellValue("G" . $row, $salary->other_deduction_reason);
			$row += 3;
			$sheet->setCellValue("B" . $row, "会議明細");
			$row += 2;
			$sheet->mergeCells("B" . $row . ":D" . $row);
			$sheet->setCellValue("B" . ($row + 1), "参加回数");
			$sheet->mergeCells("B" . ($row + 1) . ":D" . ($row + 1));

			$col_no = 5;
			$max_row = 26;

			foreach ($other_job_descriptions as $other_job_description) {
				if ($col_no > 28) {
					$sheet->getStyle('B' . $row . ":AB" . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

					$col_no = 5;
					$row += 2;
					if ($row == $max_row) {
						$max_row += 2;
						$sheet->setCellValue("B" . ($row + 1), "参加回数");
						$sheet->mergeCells("B" . $row . ":" . "D" . $row);
						$sheet->mergeCells("B" . ($row + 1) . ":" . "D" . ($row + 1));
					}
				}

				$col = Coordinate::stringFromColumnIndex($col_no);
				$sheet->setCellValue($col . $row, $other_job_description->name);
				$join_count = empty($list[$user->id][$other_job_description->id][2]['count']) ? 0 : $list[$user->id][$other_job_description->id][2]['count'];
				$sheet->setCellValue($col . ($row + 1), $join_count);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 3);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
				$col_no += 4;
			}
			$col = Coordinate::stringFromColumnIndex($col_no - 1);

			$sheet->getStyle('B' . $row . ":" . $col . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			$row += 3;
			$sheet->setCellValue("B" . $row, "出勤日");
			$sheet->setCellValue("F" . $row, "校舎名");
			$sheet->setCellValue("N" . $row, "会議種別");
			$sheet->setCellValue("R" . $row, "備考");
			$sheet->mergeCells("B" . $row . ":E" . $row);
			$sheet->mergeCells("F" . $row . ":M" . $row);
			$sheet->mergeCells("N" . $row . ":Q" . $row);
			$sheet->mergeCells("R" . $row . ":AB" . $row);
			$sheet->getStyle('B' . $row . ":AB" . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			$row++;

			$daily_other_salaries = DailyOtherSalary::with(['other_job_descriptions' => function ($query) {
				$query->withTrashed();
			}])
			->where('work_month', $requestData['work_month'])
			->where('user_id', $user->id)
			->orderBy('work_date', 'asc')
			->get();
			foreach ($daily_other_salaries as $daily_other_salary) {
				$sheet->setCellValue("B" . $row, date('Y/m/d', strtotime($daily_other_salary->work_date)));
				$sheet->setCellValue("F" . $row, $daily_other_salary->school_buildings->name_short);
				$sheet->setCellValue("N" . $row, $daily_other_salary->other_job_descriptions->name);
				$sheet->setCellValue("R" . $row, $daily_other_salary->remarks);
				$sheet->mergeCells("B" . $row . ":E" . $row);
				$sheet->mergeCells("F" . $row . ":M" . $row);
				$sheet->mergeCells("N" . $row . ":Q" . $row);
				$sheet->mergeCells("R" . $row . ":AB" . $row);
				$sheet->getStyle('B' . $row . ":AB" . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

				$row++;
			}
			$row += 3;
			$sheet->setCellValue("B" . $row, "業務明細");
			$row += 2;
			$max_row = $row + 2;
			$sheet->setCellValue("B" . ($row + 1), "業務時間(時間)");
			$sheet->mergeCells("B" . $row . ":" . "F" . $row);
			$sheet->mergeCells("B" . ($row + 1) . ":" . "F" . ($row + 1));
			$daily_salaries = DailySalary::where('work_month', $requestData['work_month'])->where('user_id', $user->id)->orderBy('work_date', 'asc')->get();
			$col_no = 7;

			foreach ($job_descriptions as $job_description) {
				$daily_working_time = $daily_salaries->where('job_description_id', $job_description->id)->sum("working_time");
				if ($col_no > 26) {
					$sheet->getStyle('B' . $row . ":Z" . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

					$col_no = 7;
					$row += 2;
					if ($row == $max_row) {
						$max_row += 2;
						$sheet->setCellValue("B" . ($row + 1), "業務時間(時間)");
						$sheet->mergeCells("B" . $row . ":" . "F" . $row);
						$sheet->mergeCells("B" . ($row + 1) . ":" . "F" . ($row + 1));
					}
				}

				$col = Coordinate::stringFromColumnIndex($col_no);
				$sheet->setCellValue($col . $row, $job_description->name);

				$daily_working_hour = ($daily_working_time == 0) ? 0 : ceil(($daily_working_time * 10) / 60) / 10;
				$sheet->setCellValue($col . ($row + 1), $daily_working_hour);
				$col_end = Coordinate::stringFromColumnIndex($col_no + 3);
				$sheet->mergeCells($col . $row . ":" . $col_end . $row);
				$sheet->mergeCells($col . ($row + 1) . ":" . $col_end . ($row + 1));
				$col_no += 4;
			}
			$col = Coordinate::stringFromColumnIndex($col_no - 1);

			$sheet->getStyle('B' . $row . ":" . $col . ($row + 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			$row += 3;
			$sheet->setCellValue("B" . $row, "出勤日");
			$sheet->setCellValue("F" . $row, "校舎名");
			$sheet->setCellValue("M" . $row, "業務内容");
			$sheet->setCellValue("Q" . $row, "時間(分)");
			$sheet->setCellValue("T" . $row, "備考");
			$sheet->mergeCells("B" . $row . ":E" . $row);
			$sheet->mergeCells("F" . $row . ":L" . $row);
			$sheet->mergeCells("M" . $row . ":P" . $row);
			$sheet->mergeCells("Q" . $row . ":S" . $row);
			$sheet->mergeCells("T" . $row . ":AB" . $row);
			$sheet->getStyle('B' . $row . ":AB" . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			$row++;


			foreach ($daily_salaries as $daily_salary) {

				$sheet->setCellValue("B" . $row, date('Y/m/d', strtotime($daily_salary->work_date)));
				$sheet->setCellValue("F" . $row, $daily_salary->school_building->name_short);
				$sheet->setCellValue("M" . $row, $daily_salary->job_description->name);
				$sheet->setCellValue("Q" . $row, $daily_salary->working_time);
				$sheet->setCellValue("T" . $row, $daily_salary->remarks);
				$sheet->mergeCells("B" . $row . ":E" . $row);
				$sheet->mergeCells("F" . $row . ":L" . $row);
				$sheet->mergeCells("M" . $row . ":P" . $row);
				$sheet->mergeCells("Q" . $row . ":S" . $row);
				$sheet->mergeCells("T" . $row . ":AB" . $row);
				$sheet->getStyle('B' . $row . ":AB" . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

				$row++;
			}
			$row += 3;
			$sheet->setCellValue("B" . $row, "交通費明細");
			$row += 2;
			$max_row = $row + 2;
			$transportation_expenses = TransportationExpense::where('work_month', $requestData['work_month'])->where('user_id', $user->id)->orderBy('work_date', 'asc')->get();
			$sheet->setCellValue("B" . $row, "出勤日");
			$sheet->setCellValue("E" . $row, "校舎名");
			$sheet->setCellValue("K" . $row, "路線名");
			$sheet->setCellValue("N" . $row, "乗車駅");
			$sheet->setCellValue("Q" . $row, "下車駅");
			$sheet->setCellValue("T" . $row, "運賃(円)");
			$sheet->setCellValue("W" . $row, "備考");
			$sheet->mergeCells("B" . $row . ":D" . $row);
			$sheet->mergeCells("E" . $row . ":J" . $row);
			$sheet->mergeCells("K" . $row . ":M" . $row);
			$sheet->mergeCells("N" . $row . ":P" . $row);
			$sheet->mergeCells("Q" . $row . ":S" . $row);
			$sheet->mergeCells("T" . $row . ":V" . $row);
			$sheet->mergeCells("W" . $row . ":AB" . $row);
			$sheet->getStyle('B' . $row . ":AB" . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			$row++;
			foreach ($transportation_expenses as $transportation_expense) {
				$sheet->setCellValue("B" . $row, date('Y/m/d', strtotime($transportation_expense->work_date)));
				$sheet->setCellValue("E" . $row, $transportation_expense->school_buildings->name_short);
				$sheet->setCellValue("K" . $row, $transportation_expense->route);
				$sheet->setCellValue("N" . $row, $transportation_expense->boarding_station);
				$sheet->setCellValue("Q" . $row, $transportation_expense->get_off_station);
				if ($transportation_expense->round_trip_flg == 1) {
					$sheet->setCellValue("T" . $row, $transportation_expense->fare);
				} else {
					$sheet->setCellValue("T" . $row, $transportation_expense->unit_price);
				}
				$sheet->setCellValue("W" . $row, $transportation_expense->remarks);
				$sheet->mergeCells("B" . $row . ":D" . $row);
				$sheet->mergeCells("E" . $row . ":J" . $row);
				$sheet->mergeCells("K" . $row . ":M" . $row);
				$sheet->mergeCells("N" . $row . ":P" . $row);
				$sheet->mergeCells("Q" . $row . ":S" . $row);
				$sheet->mergeCells("T" . $row . ":V" . $row);
				$sheet->mergeCells("W" . $row . ":AB" . $row);
				$sheet->getStyle('B' . $row . ":AB" . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

				$row++;
			}
			// $spreadsheet->getActiveSheet()->getStyle('A4:' . $col_end . ($row - 1))
			// 	->getAlignment()->setWrapText(true);
			$spreadsheet->getActiveSheet()->getPageSetup()
				->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			$spreadsheet->getActiveSheet()->getPageSetup()->setPrintArea('A1:AB' . ($row - 1));
			$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);
		}
		$sheetIndex = $spreadsheet->getIndex(
			$spreadsheet->getSheetByName('Sheet1')
		);
		$spreadsheet->removeSheetByIndex($sheetIndex);


		$filename = '給与明細.xlsx';
		$writer = new Xlsx($spreadsheet);
		$writer->save(storage_path() . '/app/excel/payslip' . auth()->user()->id . '.xlsx');
		$orderNote = '/var/www/html/shinzemi/storage/app/excel/payslip' . auth()->user()->id . '.xlsx';
		$command = "export HOME=/tmp && libreoffice --headless --convert-to pdf --outdir /var/www/html/shinzemi/storage/app/excel/ /var/www/html/shinzemi/storage/app/excel/ " . $orderNote;
		exec($command);
		$DLFileName = '給与明細' . '.pdf';
		//$DLFileName = mb_convert_encoding($DLFileName, "SJIS", "UTF-8"); // IEでの文字化け対応

		$file_path_excel = '/var/www/html/shinzemi/storage/app/excel/payslip' . auth()->user()->id . '.pdf';
		$file_path_pdf = '/var/www/html/shinzemi/storage/app/excel/payslip' . auth()->user()->id . '.pdf';

		//タイプをダウンロードと指定
		header('Content-Type: application/pdf');

		//ファイルのサイズを取得してダウンロード時間を表示する
		header('Content-Length: ' . filesize($file_path_pdf));

		//ダウンロードの指示・ダウンロード時のファイル名を指定
		// if ($TempSave == "2") {
		// 	header('Content-Disposition: inline;filename="' . $DLFileName . '"');
		// } else {
		header('Content-Disposition: attachment; filename="' . $DLFileName . '"');
		// }
		//ファイルを読み込んでダウンロード
		readfile($file_path_pdf);

		//変換したPDFをダウンロード
		// echo file_get_contents('./excel/' . $saveFileName . '.pdf');

		//保存したエクセルとpdf削除
		unlink($file_path_pdf);
		unlink($file_path_excel);

		ob_end_clean(); //バッファ消去
		exit;
	}

	public function export_part_timer_list(Request $request)
	{
		//Excel出力
		$reader = new XlsxReader();
		$spreadsheet = $reader->load(storage_path() . '/app/template/part_timer_list.xlsx'); //template.xlsx 読込
		$sheet = $spreadsheet->getActiveSheet();
		$job_descriptions = JobDescription::all();
		$other_job_descriptions = OtherJobDescription::all();
		$users = User::whereNull('retirement_date')->where('employment_status', 3)->get();
		$sheet->setCellValue('AP1', '発行日　' . date('Y年m月d日'));
		$sheet->setCellValue('A1', date('Y'));
		$sheet->setCellValue('E1', date('m', strtotime('-1 month')));
		$row = 5;
		$index = 1;
		foreach ($users as $user) {
			$salary = Salary::where('user_id', $user->id)->orderBy('tightening_date', 'asc')->first();
			$first_working_month = "";
			if (!empty($salary)) {
				$first_working_month = $salary->tightening_date;
			}
			$sheet->setCellValue('A' . $row, $index);
			$sheet->setCellValue('B' . $row, $user->user_id);
			$area = "";
			if (!empty($user->school_buildings->area)) {
				$area = config('const.area')[$user->school_buildings->area];
			}
			$sheet->setCellValue('C' . $row, $area);
			$sheet->setCellValue('D' . $row, $first_working_month);
			if (!empty($user->hiredate)) {
				$dateTime1 = $user->hiredate;
				$objDatetime1 = new DateTime($dateTime1);
				$objDatetime2 = new DateTime(date('Y-m-d', strtotime('-1 month')));
				$objInterval = $objDatetime1->diff($objDatetime2);
				$sheet->setCellValue('E' . $row, $objInterval->format('%y'));
			}

			$sheet->setCellValue('F' . $row, $user->school_buildings->name_short);
			$sheet->setCellValue('G' . $row, $user->last_name . " " . $user->first_name);
			$attendant_flg = Salary::where('user_id', $user->id)->where('tightening_date', date('Y-m', strtotime('-1 month')))->exists();
			if ($attendant_flg) {
				$sheet->setCellValue('H' . $row, '◯');
			} else {
				$sheet->setCellValue('H' . $row, '×');
			}
			if ($user->description_column == 1) {
				$sheet->setCellValue('I' . $row, '甲');
			} elseif ($user->description_column == 2) {
				$sheet->setCellValue('I' . $row, '乙');
			} else {
				$sheet->setCellValue('I' . $row, '未入力');
			}
			$col_no = 10;

			$sheet->setCellValue('J3', '基本時給');

			foreach ($job_descriptions as $job_description) {
				$col = Coordinate::stringFromColumnIndex($col_no);
				$sheet->setCellValue($col . '4', $job_description->name);
				$job_description_wages = JobDescriptionWage::where('job_description_id', $job_description->id)->where('user_id', $user->id)->first();
				if (!empty($job_description_wages->wage)) {
					$wage = $job_description_wages->wage;
					$sheet->setCellValue($col . $row, $wage);
				}
				$col_no++;
			}
			foreach ($other_job_descriptions as $other_job_description) {
				$col = Coordinate::stringFromColumnIndex($col_no);
				$sheet->setCellValue($col . '4', $other_job_description->name);
				$other_job_description_wages = OtherJobDescriptionWage::where('other_job_description_id', $job_description->id)->where('user_id', $user->id)->first();
				if (!empty($other_job_description_wages->wage)) {
					$wage = $other_job_description_wages->wage;
					$sheet->setCellValue($col . $row, $wage);
				}
				$col_no++;
			}
			$col = Coordinate::stringFromColumnIndex(($col_no - 1));
			$sheet->mergeCells("J3:" . $col . "3");

			$col = Coordinate::stringFromColumnIndex($col_no);
			$col_end = Coordinate::stringFromColumnIndex($col_no + 2);

			$sheet->setCellValue($col . '3', '備考');
			$sheet->setCellValue($col . '4', '休職');
			$sheet->mergeCells($col . "3:" . $col_end . "3");

			if (!$attendant_flg) {
				$sheet->setCellValue($col . $row, '休職');
			}

			$col_no++;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$attendant_flg2 = Salary::where('user_id', $user->id)->where('tightening_date', date('Y-m', strtotime('-2 month')))->exists();
			$sheet->setCellValue($col . '4', '退職');
			if (!$attendant_flg && !$attendant_flg2) {
				$sheet->setCellValue($col . $row, '退職');
			}
			$col_no++;
			$col = Coordinate::stringFromColumnIndex($col_no);
			$sheet->setCellValue($col . '4', '記入欄');
			$row++;
			$index++;
		}
		$sheet->getStyle('A3:' . $col . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

		$filename = '非常勤一覧.xlsx';
		// ダウンロード
		ob_end_clean(); // this
		ob_start(); // and this
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
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 *
	 * @return \Illuminate\View\View
	 */
	public function show($id)
	{
		// $daily_salary = DailySalary::findOrFail($id);
		// return view("sales.show", compact("daily_salary"));
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
		// $daily_salary = DailySalary::findOrFail($id);
		// $schoolbuilding = SchoolBuilding::all()->pluck('name', 'id');
		// $job_description = JobDescription::all()->pluck('name', 'id');

		// return view("sales.edit", compact("daily_salary", "schoolbuilding", "job_description"));
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
		// $this->validate($request, [
		// 	"code" => "nullable|max:4", //string('code',4)->nullable()
		// 	"name" => "required|max:15", //string('name',15)->nullable()
		// 	"name_kana" => "nullable|max:40", //string('name_kana',40)->nullable()
		// ]);
		// $requestData = $request->all();

		// $daily_salary = DailySalary::findOrFail($id);
		// $daily_salary->update($requestData);

		// return redirect("/shinzemi/sales")->with("flash_message", "データが更新されました。");
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
		// DailySalary::destroy($id);

		// return redirect("/shinzemi/sales")->with("flash_message", "データが削除されました。");
	}
}
    //=======================================================================
