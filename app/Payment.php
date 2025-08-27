<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'student_id',
		'payment_date',
		'sale_month',
		'school_building_id',
		'payment_amount',
		'pay_method',
		'summary',
	];
	public function school_building()
	{
		return $this->belongsTo('App\SchoolBuilding');
	}
	public function student()
	{
		return $this->belongsTo('App\Student', 'student_id', 'student_no');
	}

	public static function setTotalAmount($sheet, $row, $totalAmount)
	{
		// ARからAWまでのセルの書式を設定して縮小して全体を表示
		$sheet->getStyle('AR' . $row . ":AW" . $row)
			->getAlignment()
			->setShrinkToFit(true);
		// ARからAWまでのセルを右寄せにする
		$sheet->getStyle('AR' . $row . ":AW" . $row)
			->getAlignment()
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		// 行の高さを20に設定
		$sheet->getRowDimension($row)->setRowHeight(20);
		// 合計金額をセット
		$sheet->setCellValue('AR' . $row, $totalAmount);

		// （　合計　）という文言をAEにセット
		$sheet->setCellValue('AE' . $row, '（　合計　）');
	}

	// 行の各セルをmergeする
	public static function mergeCells($sheet, $row)
	{
		// セルを結合
		$sheet->mergeCells('A' . $row . ":F" . $row);
		$sheet->mergeCells('G' . $row . ":M" . $row);
		$sheet->mergeCells('N' . $row . ":X" . $row);
		$sheet->mergeCells('Y' . $row . ":AD" . $row);
		$sheet->mergeCells('AE' . $row . ":AQ" . $row);
		$sheet->mergeCells('AR' . $row . ":AW" . $row);

		// 行の高さを20に設定
		$sheet->getRowDimension($row)->setRowHeight(20);
	}
}
