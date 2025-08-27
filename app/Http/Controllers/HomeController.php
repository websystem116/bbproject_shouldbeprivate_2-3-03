<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChargeProgress;
use App\Schedule;

class HomeController extends Controller
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
		$charge_progress = ChargeProgress::latest('id')->first();
		$charge_progress_exist = false;
		$charge_confirm_exist = false;

		if (empty($charge_progress->monthly_processing_date)) {
			$charge_progress_exist = true;
		}
		if ($charge_progress->charge_confirm_flg == 1) {
			$charge_confirm_exist = true;
		}

		// 承認待ちのスケジュール数を取得
		// 管理者や承認権限を持つユーザーのみ表示
		$pending_schedules_count = 0;
		if (auth()->user()->roles == 1 || auth()->user()->roles == 2) {
			$pending_schedules_count = Schedule::where('status', 'pending')->count();
		}

		// dd($charge_progress_exist);
		return view('home', compact('charge_progress_exist', 'charge_confirm_exist', 'pending_schedules_count'));
	}
}
