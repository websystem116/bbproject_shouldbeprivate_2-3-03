<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StudentAccess;
use App\AccessUser;
use App\Student;
use App\SchoolBuilding;
use App\School;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class StudentAccessController extends Controller
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

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index()
	{

		return view('student_access.index');
	}

	public function history_index(Request $request)
	{
		$student_search = array();
		$student_search['id_start'] = $request->get("id_start");
		$student_search['id_end'] = $request->get("id_end");
		$student_search['surname'] = $request->get("surname");
		$student_search['name'] = $request->get("name");
		$student_search['surname_kana'] = $request->get("surname_kana");
		$student_search['name_kana'] = $request->get("name_kana");
		$student_search['school_building_id'] = $request->get("school_building_id");

		$query = AccessUser::query();

		if (!empty($student_search['id_start']) && empty($student_search['id_end'])) {
			$query->where('id', $student_search['id_start']);
		}

		if (!empty($student_search['id_end']) && empty($student_search['id_start'])) {
			$query->where('id', $student_search['id_end']);
		}

		if (!empty($student_search['id_start']) && !empty($student_search['id_end'])) {
			$query->whereBetween('id', [$student_search['id_start'], $student_search['id_end']]);
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

		if (!empty($student_search['school_building_id'])) {
			$school_building_id = (int)$student_search['school_building_id'];
			$query->where('school_building_id', $school_building_id);
		}
		//juku_graduation_dateがnullの人だけ取得
		$query->whereNull('juku_graduation_date');
		//juku_withdrawal_dateがnullの人だけ取得
		$query->whereNull('juku_withdrawal_date');
		// idの降順で取得
		$query->orderBy('id', 'desc');
		// 検索結果の件数を取得
		$totalCount = $query->count();

		$accessUsers = $query->with(['schoolbuilding'])->paginate(30);

        // セッションから選択されたユーザーを取得
        $selectedUsers = session('selected_users', []);

        // 画面上で選択されたユーザーを取得
        if ($request->has('selected_users')) {
            $newSelectedUsers = $request->input('selected_users', []);

            // 新たな選択と既存選択をマージしてセッションに保存
            $selectedUsers = array_unique(array_merge($selectedUsers, $newSelectedUsers));
        }

        // 選択解除されたユーザーを削除
        if ($request->has('delete_selected_users')) {
            $deleteSelectedUsers = $request->input('delete_selected_users', []);
            $selectedUsers = array_diff($selectedUsers, $deleteSelectedUsers);
        }

		// ユニークなユーザーIDでセッションを上書き
		session(['selected_users' => $selectedUsers]);

		//校舎のセレクトリスト
		$schooolbuildings = SchoolBuilding::get();
		$schooolbuildings_select_list = $schooolbuildings->mapWithKeys(function ($item, $key) {
			return [$item['id'] => $item['number'] . "　" . $item['name']];
		});
		return view('student_access.history_index', compact(
			'accessUsers',
			'student_search',
			'schooolbuildings_select_list',
			'selectedUsers', // セッションデータを渡す
			'totalCount'
		));	}

	public function getHistoryData(Request $request)
	{
		$accessUser = AccessUser::where('id', $request->user_id)->with(['schoolbuilding'])->first();
		$studentAccesses = StudentAccess::where('student_id', $request->user_id)->orderBy('created_at', 'desc')->get();
		$studentAccesses->map(function ($studentAccess) {
			$studentAccess->access_time = Carbon::parse($studentAccess->access_time)->format('Y/m/d H:i');
			return $studentAccess;
		});

		$pairedAccesses = [];
		$entryTime = null;

		foreach ($studentAccesses as $studentAccess) {
			if ($studentAccess->access_type === 1) {
				// 入室
				$entryTime = $studentAccess->access_time;
			} elseif ($studentAccess->access_type === 2 && $entryTime) {
				// 退室
				$pairedAccesses[] = [
					'entry' => $entryTime,
					'exit' => $studentAccess->access_time,
				];
				$entryTime = null;
			}
		}

		return response()->json(['accessUser' => $accessUser, 'studentAccesses' => $studentAccesses, 'pairedAccesses' => $pairedAccesses]);
	}

	public function store(Request $request)
	{
		$accessUser = AccessUser::where('id', $request->qr_code_data)->first();
		if (!$accessUser) {
			return response()->json(['message' => '生徒が見つかりませんでした。', 'color' => 'red']);
		}

		//最後の打刻取得
		$lastAccess = StudentAccess::where('student_id', $request->qr_code_data)->orderBy('created_at', 'desc')->first();
		$currentDate = Carbon::now()->format('Y-m-d'); // 今日の日付
		$accessType = 1; // デフォルトは「入室」
		//最後の打刻から10分以内なら何もしない
		if ($lastAccess) {
			$lastAccessTime = Carbon::parse($lastAccess->created_at);
			$lastAccessDate = $lastAccessTime->format('Y-m-d'); // 最後の打刻の日付

			if ($lastAccessTime->diffInMinutes(Carbon::now()) < 10) {
				return response()->json([
					'message' => '10分以内に打刻済みです。打刻されませんでした。',
					'status' => 'too_soon',
					'color' => 'red'
				]);
			}
			// 日付が変わっていない場合
			if ($currentDate == $lastAccessDate) {
				$accessType = $lastAccess->access_type == 1 ? 2 : 1; // 入室なら退室、退室なら入室
			} else {
				// 日付が変わっていて、最後の打刻が「入室」だった場合、強制的に「退室」
				if ($lastAccess->access_type == 1) {
					$accessType = 2;
					$createdAt = $accessTime = Carbon::now()->subDay()->setTime(23, 0, 0); // 前日の23:00に設定
					$studentAccess = StudentAccess::create([
						'student_id' => $request->qr_code_data,
						'access_time' => $accessTime,
						'access_type' => $accessType,
						'created_at' => $createdAt,
					]);
					$accessType = 1;
				}
			}
		}
		//打刻
		// $accessType = $lastAccess && $lastAccess->access_type == 1 ? 2 : 1;
		$studentAccess = StudentAccess::create([
			'student_id' => $request->qr_code_data,
			'access_time' => Carbon::now(),
			'access_type' => $accessType,
		]);

		//生徒名
		$studentName = $accessUser->surname . " " . $accessUser->name;
		//保護者名
		$studentParentName = $accessUser->parent_surname ?? $accessUser->surname;
		//メール本文
		$mailMessage = "＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿ \r\n";
		if ($accessType == 1) {
			$accessTypeText = "入室";
		} else {
			$accessTypeText = "退室";
		}
		$mailMessage .= $studentName . "さんが無事" . $accessTypeText . "されました。\r\n";
		$mailMessage .= Carbon::now()->format('m月d日 H時i分') . "\r\n";

		$mailMessage .= "＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿ \r\n";
		$mailMessage .= "(株)進学ゼミナール　TEL:0742‐51-3422 \r\n";
		$mailMessage .= "公式HP：https://www.shinzemi.co.jp/ \r\n";



		//メール送信
		if ($accessUser->email_access) {
			$this->sendMail($accessUser->email_access, $studentName, $studentParentName, $mailMessage);
		}
		if ($accessUser->email_access2) {
			$this->sendMail($accessUser->email_access2,  $studentName, $studentParentName, $mailMessage);
		}

		return response()->json([
			'message' => $accessTypeText . "しました。",
			'status' => $accessType == 1 ? 'entry' : 'exit',
			'color' => 'green'
		]);
	}

	//メール送信処理
	public function sendMail($email, $studentName, $studentParentName, $message)
	{
		if ($email) { //アドレスあれば送信
			$mail_bodys = [
				'user_name' => $studentParentName,
				'user_email' => $email,
				'student_name' => $studentName,
				'message_data' => $message,
			];
			Mail::send(new SendMail($mail_bodys));
		}
	}
}
