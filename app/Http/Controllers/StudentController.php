<?php

namespace App\Http\Controllers;

use Auth;
use App\Student;
use App\Bank;
use App\BranchBank;
use App\Discount;
use App\SchoolBuilding;
use App\School;
use App\HighschoolCourse;
use App\Product;
use App\JukoInfo;
use App\ParentUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Mail\StudentRegistrationMail;
use App\Mail\StudentRegistrationReMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

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

// 罫線引きたい
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border as Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

# Services
use App\Services\PasswordGenerationService;


class StudentController extends Controller
{
	/**
	 * @var PasswordGenerationService
	 */
	protected $passwordGenerationService;

    /**
     * コンストラクタインジェクション
	 *
	 * @param PasswordGenerationService $passwordGenerationService
     */
    public function __construct(PasswordGenerationService $passwordGenerationService)
    {
        $this->passwordGenerationService = $passwordGenerationService;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// $student = Student::get();
		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});
		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		//商品のセレクトリスト
		$products = Product::get();
		$products_select_list = $products->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		//割引のセレクトリスト
		$discounts = Discount::get();
		$discounts_select_list = $discounts->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//検索の処理
		$query = Student::query();
		// $perPage = 25;

		//検索の値取得
		$student_search['id_start'] = $request->get("id_start");
		$student_search['id_end'] = $request->get("id_end");
		$student_search['no_start'] = $request->get("no_start");
		$student_search['no_end'] = $request->get("no_end");
		$student_search['surname'] = $request->get("surname");
		$student_search['name'] = $request->get("name");
		$student_search['surname_kana'] = $request->get("surname_kana");
		$student_search['name_kana'] = $request->get("name_kana");
		$student_search['phone'] = $request->get("phone");
		$student_search['school_id'] = $request->get("school_id");
		$student_search['brothers_flg'] = $request->get("brothers_flg");
		$student_search['fatherless_flg'] = $request->get("fatherless_flg");
		$student_search['rest_flg'] = $request->get("rest_flg");
		$student_search['graduation_flg'] = $request->get("graduation_flg");
		$student_search['withdrawal_flg'] = $request->get("withdrawal_flg");
		$student_search['temporary_flg'] = $request->get("temporary_flg");
		$student_search['grade_start'] = $request->get("grade_start");
		$student_search['grade_end'] = $request->get("grade_end");
		$student_search['school_building_id'] = $request->get("school_building_id");
		$student_search['product_select'] = $request->get("product_select");
		$student_search['discount_select'] = $request->get("discount_select");
		$student_search['suggested_school'] = $request->get("suggested_school");
		$student_search['juku_start_date'] = $request->get("juku_start_date");
		$student_search['juku_end_date'] = $request->get("juku_end_date");
		$student_search['juku_graduation_start_date'] = $request->get("juku_graduation_start_date");
		$student_search['juku_graduation_end_date'] = $request->get("juku_graduation_end_date");
		$student_search['juku_return_start_date'] = $request->get("juku_return_start_date");
		$student_search['juku_return_end_date'] = $request->get("juku_return_end_date");
		$student_search['juku_withdrawal_start_date'] = $request->get("juku_withdrawal_start_date");
		$student_search['juku_withdrawal_end_date'] = $request->get("juku_withdrawal_end_date");
		$student_search['juku_rest_start_date'] = $request->get("juku_rest_start_date");
		$student_search['juku_rest_end_date'] = $request->get("juku_rest_end_date");

		//検索があったら
		// 検索するテキストが入力されている場合のみ
		if (!empty($student_search['id_start']) && empty($student_search['id_end'])) {
			$query->where('students.id', $student_search['id_start']);
		}

		if (!empty($student_search['id_end']) && empty($student_search['id_start'])) {
			$query->where('students.id', $student_search['id_end']);
		}

		if (!empty($student_search['id_start']) && !empty($student_search['id_end'])) {
			$query->whereBetween('students.id', [$student_search['id_start'], $student_search['id_end']]);
		}

		if (
			!empty($student_search['no_start']) && empty($student_search['no_end'])
		) {
			$query->where('students.student_no', $student_search['no_start']);
		}

		if (!empty($student_search['no_end']) && empty($student_search['no_start'])) {
			$query->where('students.student_no', $student_search['no_end']);
		}

		if (!empty($student_search['no_start']) && !empty($student_search['no_end'])) {
			$query->whereBetween('students.student_no', [$student_search['no_start'], $student_search['no_end']]);
		}

		if (!empty($student_search['surname'])) {
			$query->where('surname', 'like', '%' . $student_search['surname'] . '%');
		}

		if (!empty($student_search['name'])) {
			$query->where('name', 'like', '%' . $student_search['name'] . '%');
		}

		if (!empty($student_search['surname_kana'])) {
			$query->where('surname_kana', 'like', '%' . $student_search['surname_kana'] . '%');
		}

		if (!empty($student_search['name_kana'])) {
			$query->where('name_kana', 'like', '%' . $student_search['name_kana'] . '%');
		}

		if (!empty($student_search['phone'])) {
			$query->where('phone1', 'like', "%$student_search[phone]%")->orWhere('phone2', 'like', "%$student_search[phone]%");
		}
		if (!empty($student_search['school_id'])) {
			$query->where('school_id', $student_search['school_id']);
		}

		if (!empty($student_search['brothers_flg'])) {
			$query->where('brothers_flg', $student_search['brothers_flg']);
		}

		if (!empty($student_search['fatherless_flg'])) {
			$query->where('fatherless_flg', $student_search['fatherless_flg']);
		}

		if (!empty($student_search['rest_flg'])) {
			$query->WhereNotNull('juku_rest_date');
		}

		if (!empty($student_search['graduation_flg'])) {
			$query->WhereNotNull('juku_graduation_date');
		}

		if (!empty($student_search['withdrawal_flg'])) {
			$query->whereNotNull('juku_withdrawal_date');
		}

		if (!empty($student_search['temporary_flg'])) {
			$query->where('temporary_flg', $student_search['temporary_flg']);
		}

		if (!empty($student_search['grade_start']) && empty($student_search['grade_end'])) {
			$query->where('grade', $student_search['grade_start']);
		}
		if (!empty($student_search['grade_end']) && empty($student_search['grade_start'])) {
			$query->where('grade', $student_search['grade_end']);
		}
		if (!empty($student_search['grade_start']) && !empty($student_search['grade_end'])) {
			$student_search['grade_start'] = intval($student_search['grade_start']);
			$student_search['grade_end'] = intval($student_search['grade_end']);
			$query->WhereBetween('grade', [$student_search['grade_start'], $student_search['grade_end']]);
		}

		if (!empty($student_search['school_building_id'])) {
			$school_building_id = (int)$student_search['school_building_id'];
			$query->where('school_building_id', $school_building_id);
		}

		if (!empty($student_search['product_select'])) {
			$query->join('juko_infos', 'juko_infos.student_no', '=', 'students.student_no')
				->where('juko_infos.product_id', $student_search['product_select']);
		}

		if (!empty($student_search['discount_select'])) {
			$query->where('discount_id', $student_search['discount_select']);
		}

		if (!empty($student_search['suggested_school'])) {
			$query->where('choice_private_school_name1', 'like', '%' . $student_search['suggested_school'] . '%')
				->orwhere('choice_private_school_name2', 'like', '%' . $student_search['suggested_school'] . '%')
				->orwhere('choice_private_school_name3', 'like', '%' . $student_search['suggested_school'] . '%')
				->orwhere('choice_private_school_name4', 'like', '%' . $student_search['suggested_school'] . '%')
				->orwhere('choice_private_school_name5', 'like', '%' . $student_search['suggested_school'] . '%');
		}

		//入塾日
		if (!empty($student_search['juku_start_date']) && empty($student_search['juku_end_date'])) {
			return redirect("/shinzemi/student")->with("message", "※入塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_end_date']) && empty($student_search['juku_start_date'])) {
			return redirect("/shinzemi/student")->with("message", "※入塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_start_date']) && !empty($student_search['juku_end_date'])) {
			$query->whereBetween('juku_start_date', [$student_search['juku_start_date'], $student_search['juku_end_date']]);
		}

		//卒塾日
		if (!empty($student_search['juku_graduation_start_date']) && empty($student_search['juku_graduation_end_date'])) {
			return redirect("/shinzemi/student")->with("message", "※卒塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_graduation_end_date']) && empty($student_search['juku_graduation_start_date'])) {
			return redirect("/shinzemi/student")->with("message", "※卒塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_graduation_start_date']) && !empty($student_search['juku_graduation_end_date'])) {
			$student_search['graduation_flg'] = 1;
			$query->whereBetween('juku_graduation_date', [$student_search['juku_graduation_start_date'], $student_search['juku_graduation_end_date']]);
		}

		//復塾日
		if (!empty($student_search['juku_return_start_date']) && empty($student_search['juku_return_end_date'])) {
			return redirect("/shinzemi/student")->with("message", "※復塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_return_end_date']) && empty($student_search['juku_return_start_date'])) {
			return redirect("/shinzemi/student")->with("message", "※復塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_return_start_date']) && !empty($student_search['juku_return_end_date'])) {
			$query->whereBetween('juku_return_date', [$student_search['juku_return_start_date'], $student_search['juku_return_end_date']]);
		}

		//退塾日
		if (!empty($student_search['juku_withdrawal_start_date']) && empty($student_search['juku_withdrawal_end_date'])) {
			return redirect("/shinzemi/student")->with("message", "※退塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_withdrawal_end_date']) && empty($student_search['juku_withdrawal_start_date'])) {
			return redirect("/shinzemi/student")->with("message", "※退塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_withdrawal_start_date']) && !empty($student_search['juku_withdrawal_end_date'])) {
			$student_search['withdrawal_flg'] = 1;
			$query->whereBetween('juku_withdrawal_date', [$student_search['juku_withdrawal_start_date'], $student_search['juku_withdrawal_end_date']]);
		}

		//休塾日
		if (!empty($student_search['juku_rest_start_date']) && empty($student_search['juku_rest_end_date'])) {
			return redirect("/shinzemi/student")->with("message", "※休塾日の終了範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_rest_end_date']) && empty($student_search['juku_rest_start_date'])) {
			return redirect("/shinzemi/student")->with("message", "※休塾日の開始範囲を指定してください")->withInput();
		}
		if (!empty($student_search['juku_rest_start_date']) && !empty($student_search['juku_rest_end_date'])) {
			$student_search['rest_flg'] = 1;
			$query->whereBetween('juku_rest_date', [$student_search['juku_rest_start_date'], $student_search['juku_rest_end_date']]);
		}

		if ($request->has('search')) { //検索ぼたんなら
			if ($student_search['withdrawal_flg'] == 1) { //卒塾チェック
				$query->whereNotNull('juku_withdrawal_date');
			}
			if ($student_search['graduation_flg'] == 1) { //退塾チェック
				$query->whereNotNull('juku_graduation_date');
			}
			if ($student_search['rest_flg'] == 1) {
				$query->whereNotNull('juku_rest_date')->whereNull('juku_graduation_date')->whereNull('juku_withdrawal_date');
			}
			if (empty($student_search['withdrawal_flg']) && empty($student_search['graduation_flg']) && empty($student_search['rest_flg'])) { //在塾生徒
				$query->whereNull('juku_rest_date')->whereNull('juku_graduation_date')->whereNull('juku_withdrawal_date');
			}

			$student = $query->get();
			return view("student.index", compact("student", "student_search", "schools_select_list", "schooolbuildings_select_list", "products_select_list", "discounts_select_list"));
		} else { // 未検索時は、レコードがないとエラーになるので、空のコレクションを渡す
			$student = Student::where("id", 0)->get();
			return view("student.index", compact("student", "student_search", "schools_select_list", "schooolbuildings_select_list", "products_select_list", "discounts_select_list"));
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

		//銀行のセレクトリスト
		$banks = Bank::get();
		$banks_select_list = $banks->mapWithKeys(function ($item, $key) {
			return [$item['code'] => $item['code'] . "　" . $item['name']];
		});

		//支店のセレクトリスト
		$branch_banks = BranchBank::get();
		$branch_banks_select_list = $branch_banks->mapWithKeys(function ($item, $key) {
			return [$item['code'] => $item['code'] . "　" . $item['name']];
		});

		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//割引のセレクトリスト
		$discounts = Discount::get();
		$discounts_select_list = $discounts->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});

		//商品のセレクトリスト
		$products = Product::get();
		$products_select_list = $products->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});

		return view("student.create", compact("banks_select_list", "year", "branch_banks_select_list", "schools_select_list", "discounts_select_list", "schooolbuildings_select_list", "products_select_list"));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			"surname" => "required|max:40",
			"name" => "required|max:40",
			"surname_kana" => "required|max:40",
			"name_kana" => "required|max:40",
			"zip_code" => "required|max:8",
			"address1" => "required",
			"phone1" => "required",
			"parent_surname" => "required|max:40",
			"parent_name" => "required|max:40",
			"billing_start_date" => "required",
			"juku_start_date" => "required",
			"payment_methods" => "required",
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
			"parent_surname.required" => "保護者の名前の入力は必須です。",
			"parent_name.required" => "保護者の名前の入力は必須です。",
			"billing_start_date.required" => "請求開始日の入力は必須です。",
			"juku_start_date.required" => "請求開始日の入力は必須です。",
			"payment_methods.required" => "引き落とし方法の入力は必須です。",
			"email.email" => "有効なメールアドレス形式で入力してください。",
			"email.required" => "Eメールの入力は必須です。",

		]);
		DB::beginTransaction();
		try {
			//student_no生成する処理
			$student_latest_cd = Student::withTrashed()->latest('student_no')->first(); //最新のstudent_no取得
			$student_latest_cd_int = intval($student_latest_cd['student_no']); //int型に返還
			$student_no = $student_latest_cd_int + 1; //+1する
			// $student_no = strval($student_no); //文字列に変換
			// $student_no = "0" . $student_no; //頭に0くっつける
			$student_no = str_pad($student_no, 8, "0", STR_PAD_LEFT);
			$request->merge(['student_no' => $student_no]); //配列に追加
			$request->validate([
				'student_no' => 'required|unique:students',
			]);

			$requestData = $request->all();

			// 初期パスワード設定
			$plainPassword = $this->passwordGenerationService->generateExcludedPassword();
			$hashedPassword = Hash::make($plainPassword);
			$requestData['password'] = $hashedPassword;
			$requestData['initial_password'] = $plainPassword;

			$student = Student::create($requestData);

			// メール送信 (本番環境では3/20以降に有効化予定)
			if ($student->email !== null && config('app.send_registration_mail_flg')) {
				Mail::to($student->email)->send(new StudentRegistrationMail($student, $plainPassword));
			}

			DB::commit();
			return redirect("/shinzemi/student")->with("flash_message", "データが登録されました。");
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error('生徒登録エラー: ' . $e->getMessage()); // ログ出力
			return back()->withInput()->withErrors(['error' => '生徒登録中にエラーが発生しました。']); // エラーメッセージ付きでリダイレクト
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	public function get_shools($id)
	{
		$schools = School::where('school_classification', $id)->get();
		// dd($schools);
		// $schools_where_select_list = $schools->mapWithKeys(function ($item, $key) {
		// 	return [$item['id'] => $item['id'] . "　" . $item['name']];
		// });
		return $schools;
	}

	public function get_branch_banks($id)
	{

		$bank = Bank::where('code', $id)->first();
		$branch_banks = BranchBank::where('bank_id', $bank->id)->get();
		return $branch_banks;
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
		$student = Student::findOrFail($id);



		//銀行のセレクトリスト
		$banks = Bank::get();
		$banks_select_list = $banks->mapWithKeys(function ($item, $key) {
			return [$item['code'] => $item['code'] . "　" . $item['name']];
		});

		//支店のセレクトリスト
		if (!empty($student->branch_code)) {
			$student_bank = $student->bank;
			$branch_banks = BranchBank::where('code', $student->branch_code)->where('bank_id', $student_bank->id)->get();
			$branch_banks_select_list = $branch_banks->mapWithKeys(function ($item, $key) {
				return [$item['code'] => $item['code'] . "　" . $item['name']];
			});
		} else {
			$branch_banks = BranchBank::get();
			$branch_banks_select_list = $branch_banks->mapWithKeys(function ($item, $key) {
				return [$item['code'] => $item['code'] . "　" . $item['name']];
			});
		}


		//学校のセレクトリスト
		$schools = School::get();
		$schools_select_list = $schools->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//割引のセレクトリスト
		$discounts = Discount::get();
		$discounts_select_list = $discounts->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['id'] . "　" . $item['name']];
		});

		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});

		//商品のセレクトリスト
		$products = Product::get();
		$products_select_list = $products->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		$disabled = '';
		if (auth()->user()->roles != 1) {
			$disabled = 'disabled';
		}
		$qrCode = QrCode::size(150)->generate($student->id);
		return view("student.edit", compact("student", "year", "banks_select_list", "branch_banks_select_list", "schools_select_list", "discounts_select_list", "discounts_select_list", "schooolbuildings_select_list", "products_select_list", "disabled", "qrCode"));
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
		if (auth()->user()->roles == 1) {
			$this->validate($request, [
				"surname" => "required|max:40",
				"name" => "required|max:40",
				"surname_kana" => "required|max:40",
				"name_kana" => "required|max:40",
				"zip_code" => "required|max:8",
				"address1" => "required",
				"phone1" => "required",
				"parent_surname" => "required|max:40",
				"parent_name" => "required|max:40",
				"billing_start_date" => "required",
				"juku_start_date" => "required",
				"payment_methods" => "required",
				"bank_number" => "max:7",
			], [
				"surname.required" => "名前の入力は必須です。",
				"name.required" => "名前の入力は必須です。",
				"surname_kana.required" => "名前カナの入力は必須です。",
				"name_kana.required" => "名前カナの入力は必須です。",
				"zip_code.required" => "郵便番号の入力は必須です。",
				"zip_code.max" => "郵便番号の入力は8桁です。",
				"address1.required" => "住所の入力は必須です。",
				"phone1.required" => "電話番号の入力は必須です。",
				"parent_surname.required" => "保護者の名前の入力は必須です。",
				"parent_name.required" => "保護者の名前の入力は必須です。",
				"billing_start_date.required" => "請求開始日の入力は必須です。",
				"juku_start_date.required" => "請求開始日の入力は必須です。",
				"payment_methods.required" => "引き落とし方法の入力は必須です。",
				'bank_number.max' => "口座番号の桁数を確認してください。",
			]);
		}
		$requestData = $request->all();
		$student = Student::findOrFail($id);


		// 初期パスワード設定 入塾前生徒から登録された生徒はpasswordがNULLの為、ここで初期パスワードを設定
		if (empty($student->password)) {
			$plainPassword = $this->passwordGenerationService->generateExcludedPassword();
			$hashedPassword = Hash::make($plainPassword);
			$requestData['password'] = $hashedPassword;
			$requestData['initial_password'] = $plainPassword;

			// メール送信 (本番環境では3/20以降に有効化予定)
			if ($requestData['email'] !== null && config('app.send_registration_mail_flg')) {
				Mail::to($requestData['email'])->send(new StudentRegistrationMail($student, $plainPassword));
			}
		}

		$student->update($requestData);

		$url = session("url");
		session()->forget("url");


		if (strpos($url, "student") !== false) {
			return redirect($url)->with("flash_message", "データが更新されました。");
		} else {
			return redirect("/shinzemi/student")->with("flash_message", "データが更新されました。");
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		Student::destroy($id);

		return redirect("/shinzemi/student")->with("flash_message", "データが削除されました。");
	}

	/**
	 * CSV出力処理
	 *
	 * @param  int  $request
	 * @return \Illuminate\Http\Response
	 */
	public function student_info_output(Request $formData)
	{
		$query = Student::query();

		if ($formData->has('student_check')) { //student_checkは生徒No
			$query->whereIn('students.student_no', $formData->student_check);
		}

		$students = $query->get()->sort(function ($first, $second) {
			if ($first['school_building_id'] == $second['school_building_id']) {
				if ($first['grade'] == $second['grade']) {
					return $first['juku_start_date'] < $second['juku_start_date'] ? -1 : 1;
				}
				return $first['grade'] < $second['grade'] ? -1 : 1;
			}
			return $first['school_building_id'] < $second['school_building_id'] ? -1 : 1;
		});

		// スプレッドシート作成
		$spreadsheet = new Spreadsheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('BIZ UDPゴシック');

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('students');

		// 値セット位置
		$student_position = 1;
		$student_horizontal = 1;

		//見出し
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '管理No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '生徒No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '初期パスワード');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '生徒氏名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '生徒氏名カナ');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '生年月日');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '性別');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '郵便番号');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '住所1');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '住所2');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '住所3');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '電話番号1');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '電話番号2');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, 'メールアドレス');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '学校');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '現在学年');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '保護者氏名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '保護者氏名カナ');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹1　名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹1　性別');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹1　学年');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹1　学校No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹2　名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹2　性別');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹2　学年');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹2　学校No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹3　名');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹3　性別');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹3　学年');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹3　学校No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '兄弟姉妹在塾');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, 'ひとり親家庭');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '銀行コード');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '支店コード');

		if (auth()->user()->roles == 1) {
			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, '口座番号');

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, '口座名義');
		}
		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '口座種別');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '支払い方法');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '割引No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '引き落とし停止フラグ');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '引き落とし停止開始日');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '校舎No');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '入塾日');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '請求開始日');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '休塾日');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '復塾日');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '卒塾日');

		$student_horizontal = $student_horizontal + 1;
		$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
		$sheet->setCellValue($column_student . $student_position, '退塾日');


		foreach ($students as $key => $student) {
			// 値リセット
			if ($key = 0) {
				$student_position = 2;
				$student_horizontal = 1;
			} else {
				$student_position = $student_position + 1;
				$student_horizontal = 1;
			}


			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->id);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->student_no);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->initial_password);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->surname . $student->name);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->surname_kana . $student->name_kana);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->birthdate);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, config('const.gender')[$student->gender] ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->zip_code);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->address1);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->address2);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->address3);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->phone1);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->phone2);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->email);


			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->school->name ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, config('const.school_year')[$student->grade] ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->parent_surname . $student->parent_name);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->parent_surname_kana . $student->parent_name_kana);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->brothers_name1);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, config('const.gender')[$student->brothers_gender1] ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, config('const.school_year')[$student->brothers_grade1] ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->brothers_school_no1_school->name ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->brothers_name2);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, config('const.gender')[$student->brothers_gender2] ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, config('const.school_year')[$student->brothers_grade2] ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->brothers_school_no2_school->name ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->brothers_name3);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, config('const.gender')[$student->brothers_gender3] ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, config('const.school_year')[$student->brothers_grade3] ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->brothers_school_no3_school->name ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->brothers_flg);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->fatherless_flg);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->bank_id);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->branch_code);

			if (auth()->user()->roles == 1) {
				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $student->bank_number);

				$student_horizontal = $student_horizontal + 1;
				$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
				$sheet->setCellValue($column_student . $student_position, $student->bank_holder);
			}
			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->bank_type);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->payment_methods);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->discount->name ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->debit_stop_flg);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->debit_stop_start_date);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->schoolbuilding->name ?? "");

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->juku_start_date);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->billing_start_date);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->juku_rest_date);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->juku_return_date);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->juku_graduation_date);

			$student_horizontal = $student_horizontal + 1;
			$column_student = Coordinate::stringFromColumnIndex($student_horizontal);
			$sheet->setCellValue($column_student . $student_position, $student->juku_withdrawal_date);
		}

		$filename = 'student.xlsx';
		ob_end_clean(); // this
		ob_start(); // and this
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

	/**
	 * 生徒パスワードリセット
	 *
	 * @param Request $request
	 * @param int $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function resetPassword(Request $request, $id): JsonResponse
	{
		$student = Student::findOrFail($id);

		DB::beginTransaction();
		try {
			// 関連する parent_user の削除 (物理削除)
			if ($student->parent_user_id) {
				// 紐づく生徒のparent_user_idをNULLにする
				Student::where('parent_user_id', $student->parent_user_id)->update(['parent_user_id' => null]);

				$parentUser = ParentUser::find($student->parent_user_id);
				if ($parentUser) {
					$parentUser->delete();
				}
			}

			// パスワードの再設定
			$plainPassword = $this->passwordGenerationService->generateExcludedPassword();
			$hashedPassword = Hash::make($plainPassword);

			// student テーブルの更新
			$student->password = $hashedPassword;
			$student->initial_password = $plainPassword;
			$student->save();

			DB::commit();
			return response()->json(['message' => '生徒ID: ' . $student->id . ' (' . $student->surname . $student->name . 'さん) のパスワードをリセットしました。']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("パスワードリセットエラー (生徒ID: {$student->id}): " . $e->getMessage());
			return response()->json(['message' => 'パスワードリセット中にエラーが発生しました。'], 500);
		}
	}

	/**
	 * マイページ再案内メール送信
	 */
	public function resendMyPageGuide(Request $request, $id): JsonResponse
	{
		$student = Student::findOrFail($id);

		// メール送信条件の確認
		if ($student->email !== null) {
			try {
				Mail::to($student->email)->send(new StudentRegistrationMail($student, $student->initial_password));
				// 送信成功ログ（個人情報保護のため、メールアドレスはログに残さない）
				Log::info("マイページ再案内メール送信成功 (生徒ID: {$student->id})");
				return response()->json(['message' => $student->surname . $student->name . ' さんにマイページ案内メールを再送信しました。']);
			} catch (\Exception $e) {
				Log::error("マイページ再案内メール送信エラー (生徒ID: {$student->id}): " . $e->getMessage());
				return response()->json(['message' => 'メール送信中にエラーが発生しました。'], 500);
			}
		} else {
			return response()->json(['message' => 'メール送信の条件を満たしていません。'], 400);
		}
	}

	/**
	 * マイページ再案内（不具合報告付き）メール送信
	 */
	public function resendMyPageGuideRe(Request $request, $id): JsonResponse
	{
		$student = Student::findOrFail($id);

		// メール送信条件の確認
		if ($student->email !== null) {
			try {
				Mail::to($student->email)->send(new StudentRegistrationReMail($student, $student->initial_password));
				// 送信成功ログ（個人情報保護のため、メールアドレスはログに残さない）
				Log::info("マイページ再案内メール送信成功 (生徒ID: {$student->id})");
				return response()->json(['message' => $student->surname . $student->name . ' さんにマイページ案内メールを再送信しました。']);
			} catch (\Exception $e) {
				Log::error("マイページ再案内メール送信エラー (生徒ID: {$student->id}): " . $e->getMessage());
				return response()->json(['message' => 'メール送信中にエラーが発生しました。'], 500);
			}
		} else {
			return response()->json(['message' => 'メール送信の条件を満たしていません。'], 400);
		}
	}
}
