<?php

namespace App\Http\Controllers;

use Auth;
use App\AccessUser;
use App\SchoolBuilding;
use App\Product;
use Carbon\Carbon;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Nullable;

// use phpspreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpParser\Node\Stmt\Foreach_;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Align;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Support\Facades\Validator;

class AccessUserController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{

		// セレクトリスト生成
		$schoolbuildings_select_list = SchoolBuilding::pluck('name', 'id')->map(function ($name, $id) {
			return $id.'　'.$name;
		});
	
		// 検索条件を取得 (デフォルト値を含める)
		$default_search = [
			'id_start' => null, 'id_end' => null,
			'surname' => null, 'name' => null, 'surname_kana' => null, 'name_kana' => null,
			'school_building_id' => null,
		];
		$student_search = array_merge($default_search, $request->only(array_keys($default_search)));

	
		// クエリビルダで条件を適用
		$query = AccessUser::query();
	
		// 数値範囲の検索を共通化
		$this->applyRangeCondition($query, 'students.id', $student_search['id_start'], $student_search['id_end']);
		if (!empty($student_search['school_building_id'])) {
			$query->where('school_building_id', $student_search['school_building_id']);
		}

		// 個別条件を追加
		$filters = [
			'surname' => 'like',
			'name' => 'like',
			'surname_kana' => 'like',
			'name_kana' => 'like',
		];
	
		foreach ($filters as $field => $operator) {
			if (!empty($student_search[$field])) {
				$query->where($field, $operator, $operator == 'like' ? "%{$student_search[$field]}%" : $student_search[$field]);
			}
		}
		// 検索結果を取得
		$student = $request->has('search') ? $query->get() : collect();
	
		// ビューにデータを渡す
		return view('access_user.index', compact(
			'student', 'student_search', 'schoolbuildings_select_list'
		));
	}
	
	// 範囲検索の適用
	private function applyRangeCondition($query, $field, $start, $end)
	{
		if (!empty($start) && empty($end)) {
			$query->where($field, $start);
		} elseif (empty($start) && !empty($end)) {
			$query->where($field, $end);
		} elseif (!empty($start) && !empty($end)) {
			$query->whereBetween($field, [$start, $end]);
		}
	}
	
	// 日付範囲検索の適用
	private function applyDateRange($query, $field, $start, $end)
	{
		if (!empty($start) && !empty($end)) {
			$query->whereBetween($field, [$start, $end]);
		} elseif (!empty($start) || !empty($end)) {
			throw new \Exception("日付範囲の開始日と終了日を両方指定してください。");
		}
	}
	
		/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$year = date('Y', strtotime('-3 month')); //4月から年度変わる　表示よう

		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});

		return view("access_user.create", compact("schooolbuildings_select_list"));

	}

	public function store(Request $request)
	{
		$validation = [
			"surname" => "required|max:40",
			"name" => "required|max:40",
			"school_building_id" => "required",
			"email_access" => "required",
		];
		$message = [
			"surname.required" => "名前の入力は必須です。",
			"name.required" => "名前の入力は必須です。",
			"school_building_id.required" => "校舎の入力は必須です。",
			"email_access.required" => "メールアドレス1の入力は必須です。",
		];

		$validator = Validator::make($request->all(), $validation, $message);
		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}
		AccessUser::create($request->all());

		return redirect()->route('student_access.history_index')->with('flash_message', 'データが登録されました。');
	}




	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{

		$url = url()->previous();
		// sessionにURLを保存
		session(["url" => $url]);
		$year = date('Y', strtotime('-3 month')); //4月から年度変わる
		$student = AccessUser::findOrFail($id);

		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		$qrCode = QrCode::size(150)->generate($student->id);
		return view("access_user.edit", compact("student", "schooolbuildings_select_list","qrCode"));
	}

	/**
	* Update the specified resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
   public function update(Request $request, $id)
   {
	$validation = [
		"surname" => "required|max:40",
		"name" => "required|max:40",
		"school_building_id" => "required",
		"email_access" => "required",
	];
	$message = [
		"surname.required" => "名前の入力は必須です。",
		"name.required" => "名前の入力は必須です。",
		"school_building_id.required" => "校舎の入力は必須です。",
		"email_access.required" => "メールアドレス1の入力は必須です。",
	];

	$validator = Validator::make($request->all(), $validation, $message);
	if ($validator->fails()) {
		return back()->withErrors($validator)->withInput();
	}
	   $requestData = $request->all();
	   // dd($request->all());
	   $student = AccessUser::findOrFail($id);
	   $student->update($requestData);

	   // get session url
	   $url = session("url");
	   session()->forget("url");


	   if (strpos($url, "student_access") !== false) {
		   return redirect($url)->with("flash_message", "データが更新されました。");
	   } else {
		   return redirect()->route('student_access.history_index')->with("flash_message", "データが更新されました。");
	   }
   }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request,$id)
	{
		AccessUser::destroy($id);
    // 削除前にデータの存在を確認
    // $accessUser = AccessUser::find($id);

    // if (!$accessUser) {
    //     return redirect()->back()->with('error_message', 'データが見つかりません。');
    // }

    // // データを削除
    // $accessUser->delete();
		// 検索条件を保持してリダイレクト
		$queryParams = $request->except(['_token', '_method', 'id']); // 不要なパラメータを除外
		return redirect()->route('student_access.history_index', $queryParams)
							->with('flash_message', 'データが削除されました。');
	}


}
