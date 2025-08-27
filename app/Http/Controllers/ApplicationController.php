<?php

namespace App\Http\Controllers;

use Auth;
use App\School;
use App\Application;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		return view('application.index');
	}

	public function acceptIndex(Request $request)
	{
		//校舎の申込内容リスト
		$application_type_list = [
			'0'=>'入会',
			'1'=>'体験',
			'2'=>'コース変更',
			'3'=>'転籍',
			'4'=>'休塾',
			'5'=>'退塾',
			'6'=>'講習会',
		];
		//商品のステータスリスト
		$status_list = [
			'0'=>'未承認',
			'1'=>'承認',
			'2'=>'キャンセル',
		];

		//検索の処理
		$query = Application::query();
		// $perPage = 25;

		//検索の値取得
		$application_search['reqest_start_date'] = $request->get("srch_reqest_start_date");
		$application_search['reqest_end_date'] = $request->get("srch_reqest_end_date");
		$application_search['created_by'] = $request->get("srch_created_by");
		$application_search['application_type'] = $request->get("srch_application_type");
		$application_search['status'] = $request->get("srch_status");

		//検索があったら
		//申込日
		if (!empty($application_search['reqest_start_date'])) {
			$query->where('reqest_date', '>=', $application_search['reqest_start_date']);
		}
		if (!empty($application_search['reqest_end_date'])) {
			$query->where('reqest_date', '<=', $application_search['reqest_end_date']);
		}

		// 申込者
		if (!empty($application_search['created_by'])) {
			$query->where('created_by', 'like', '%' . $application_search['created_by'] . '%');
		}

		// 申込内容
		if (isset($application_search['application_type'])) {
			$query->where('application_type', $application_search['application_type']);
		}

		// ステータス
		if (isset($application_search['status'])) {
			$query->where('status', $application_search['status']);
		}

		if ($request->has('search')) { //検索ぼたんなら
			$applications = $query->get();
		}else{
			$applications = Application::where("id", 0)->get();
		}
		return view("application.lists", compact("applications", "application_search", "application_type_list", "status_list"));
	}

	public function admissionIndex()
	{
		return view('application.admission_index');
	}

	public function admissionStudentCreate(Request $request)
	{
		$old_data = $request->all();
		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		return view("application.admission_student_create", compact("schools_select_list", "old_data"));
	}

	public function admissionCourseCreate(Request $request)
	{
		$this->validate($request, [
			"surname" => "required|max:40",
			"name" => "required|max:40",
			"surname_kana" => "required|max:40",
			"name_kana" => "required|max:40",
			"zip_code" => "required|max:8",
			"address1" => "required",
			"phone1" => "required",
			"email" => "required|email",

		], [
			"surname.required" => "名前の入力は必須です。",
			"name.required" => "名前の入力は必須です。",
			"surname_kana.required" => "名前カナの入力は必須です。",
			"name_kana.required" => "名前カナの入力は必須です。",
			"zip_code.required" => "郵便番号の入力は必須です。",
			"zip_code.max" => "郵便番号の入力は8桁です。",
			"address1.required" => "住所の入力は必須です。",
			"phone1.required" => "電話番号の入力は必須です。",
			"email.email" => "有効なメールアドレス形式で入力してください。",
			"email.required" => "Eメールの入力は必須です。",

		]);

		$old_data = $request->all();

		return view("application.admission_course_create", compact("old_data"));
	}

	public function admissionOthersCreate(Request $request)
	{
		$old_data = $request->all();
		return view("application.admission_others_create", compact("old_data"));
	}

	public function admissionConfirm(Request $request)
	{
		$old_data = $request->all();
		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		return view("application.admission_confirm", compact("schools_select_list", "old_data"));
	}	

	public function admissionSign(Request $request)
	{
		$old_data = $request->all();

		return view("application.admission_sign", compact("old_data"));
	}	

	public function admissionStore(Request $request)
	{
		try {
			//application_no生成する処理
			$application_latest_cd = Application::withTrashed()->latest('application_no')->first(); //最新のapplication_no取得
			if($application_latest_cd)
				$application_latest_cd_int = intval($application_latest_cd['application_no']); //int型に返還
			else
				$application_latest_cd_int = 0;
			$application_no = $application_latest_cd_int + 1; //+1する
			$application_no = str_pad($application_no, 8, "0", STR_PAD_LEFT);


			$request->merge(['application_no' => $application_no]); //配列に追加
			$request->validate([
				'application_no' => 'required|unique:applications',
			]);

			// 申込書詳細
			$requestData = $request->all();
			unset($requestData['application_no']);
			unset($requestData['_token']);
			unset($requestData['sign_image']);
			$jsonDatailInfo = json_encode($requestData);
		
			// サインファイルを作成
			$imageData = $request->input('sign_image');
			if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
				$image = substr($imageData, strpos($imageData, ',') + 1);
				$type = strtolower($type[1]); // png, jpg, gif

				if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
					return back()->withInput()->withErrors(['error' => '無効な画像形式です']);
				}

				$image = base64_decode($image);

				if ($image === false) {
					return back()->withInput()->withErrors(['error' => 'Base64のデコードに失敗しました']);
				}
			} else {
				return back()->withInput()->withErrors(['error' => '無効な画像データです']);
			}
			$fileName = 'signature_' . time() . '.' . $type;
			$filePath = public_path('uploads/signatures');

			if (!File::exists($filePath)) {
				File::makeDirectory($filePath, 0755, true);
			}
			File::put($filePath . '/' . $fileName, $image);	
			$sign_filepath = 'uploads/signatures/' . $fileName;
			

			$application = new Application();
			$application->reqest_date = date("Y-m-d H:i:s");
			$application->application_no = $application_no;
			$application->application_type = 0; // 入会
			$application->description = '入会';
			$application->detail = $jsonDatailInfo;
			$application->status = 0; // 未承認
			$application->created_by = Auth::user()->last_name . Auth::user()->first_name;
			$application->sign_filepath = $sign_filepath;
			$application->save();

			return redirect("/shinzemi/application")->with("flash_message", "入会データが登録されました。");
		} catch (\Exception $e) {
			Log::error('入会登録エラー: ' . $e->getMessage()); // ログ出力
			return back()->withInput()->withErrors(['error' => '入会登録中にエラーが発生しました。']);
		}

		return view("application.admission_sign");
	}

	public function acceptDetail($id, Request $request)
	{
		$application = Application::findOrFail($id);
		if($application)
			$detail_data = json_decode($application->detail);
		else
			$detail_data = null;
		$edit_id = $id;

		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		$charged_by = isset($application->charged_by)?$application->charged_by:null;
		$sign_filepath = isset($application->sign_filepath)?$application->sign_filepath:null;

		return view("application.detail_admission", compact("detail_data", "edit_id", "schools_select_list", "charged_by", "sign_filepath"));
	}

	public function acceptProcess(Request $request)
	{
		$id = $request->input('edit_id');
		$act = $request->input('act');
		$charged_by = $request->input('charged_by');
		$application = Application::findOrFail($id);

		if($act == 'accept'){
			if($application){
				$application->status = 1; // 承認
				$application->charged_by = $charged_by;
				$application->allowed_by = Auth::user()->last_name . Auth::user()->first_name;
				$application->save();
				return redirect("/shinzemi/application/accept_index")->with("flash_message", "申込書を承認しました。");
			}
		}else if($act == 'cancel'){
			if($application){
				$application->status = 2; // キャンセル
				$application->charged_by = $charged_by;
				$application->allowed_by = Auth::user()->last_name . Auth::user()->first_name;
				$application->save();
				return redirect("/shinzemi/application/accept_index")->with("flash_message", "申込書をキャンセルしました。");
			}
		}

		if($application)
			$detail_data = json_decode($application->detail);
		else
			$detail_data = null;
		$edit_id = $id;

		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		return view("application.detail_admission", compact("detail_data", "edit_id", "schools_select_list"));
	}	
}
