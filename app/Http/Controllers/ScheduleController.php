<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Schedule;
use App\ScheduleApprover;
use App\SchoolBuilding;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the schedules with calendar view
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get all school buildings for dropdown
        $schoolBuildings = SchoolBuilding::orderBy('name')->get();
        
        // Get current date or requested date
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $schoolBuildingId = $request->get('school_building_id');

        // Get schedules for the specified month
        $schedules = Schedule::getSchedulesForMonth($year, $month, $schoolBuildingId);

        // Get pending schedules for approval section (if user has permission)
        $pendingSchedules = collect();
        if ($this->canApproveSchedules()) {
            $pendingSchedules = Schedule::getPendingSchedules($schoolBuildingId);
        }

        // Generate calendar data with holidays
        $calendarData = $this->generateCalendarData($year, $month, $schedules);

        return view('schedules.index', compact(
            'schoolBuildings',
            'schedules',
            'calendarData',
            'year',
            'month',
            'schoolBuildingId',
            'pendingSchedules'
        ))->with('availableColors', Schedule::getAvailableColors());
    }

    /**
     * Show the form for creating a new schedule
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $schoolBuildings = SchoolBuilding::orderBy('name')->get();
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $selectedSchoolBuildingId = $request->get('school_building_id');
        $availableColors = Schedule::getAvailableColors();

        return view('schedules.create', compact(
            'schoolBuildings',
            'selectedDate',
            'selectedSchoolBuildingId',
            'availableColors'
        ));
    }

    public function store(Request $request)
    {
        // Handle both single and multiple dates
        $scheduleDates = $request->input('schedule_dates', []);
        
        // If old format is used, convert to array
        if ($request->has('schedule_date') && !empty($request->schedule_date)) {
            $scheduleDates = [$request->schedule_date];
        }
        
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable',
            'color' => 'required|in:' . implode(',', array_keys(Schedule::getAvailableColors())),
            'schedule_dates' => 'required|array|min:1',
            'schedule_dates.*' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'school_building_id' => 'required|exists:school_buildings,id'
        ]);

        try {
            DB::beginTransaction();

            $createdSchedules = [];
            $uniqueDates = array_unique($scheduleDates);
            
            foreach ($uniqueDates as $date) {
                if (!empty($date)) {
                    $schedule = Schedule::create([
                        'title' => $request->title,
                        'content' => $request->content,
                        'color' => $request->color,
                        'schedule_date' => $date,
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'school_building_id' => $request->school_building_id,
                        'created_by' => Auth::id(),
                        'status' => 'pending'
                    ]);
                    
                    $createdSchedules[] = $schedule;
                }
            }

            // Send notification to approvers for all created schedules
            foreach ($createdSchedules as $schedule) {
                $this->sendApprovalNotification($schedule);
            }

            DB::commit();

            $dateCount = count($createdSchedules);
            $firstDate = Carbon::parse($uniqueDates[0]);
            
            $successMessage = $dateCount > 1 
                ? "スケジュールが{$dateCount}日分登録されました。承認をお待ちください。"
                : 'スケジュールが登録されました。承認をお待ちください。';

            return redirect()->route('schedules.index', [
                'year' => $firstDate->year,
                'month' => $firstDate->month,
                'school_building_id' => $request->school_building_id
            ])->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'スケジュールの登録に失敗しました。');
        }
    }

    /**
     * Show the form for editing the specified schedule
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        // Check permission
        if (!$this->canEditSchedule($schedule)) {
            return redirect()->route('schedules.index')->with('error', 'この予定を編集する権限がありません。');
        }

        $schoolBuildings = SchoolBuilding::orderBy('name')->get();

        return view('schedules.edit', compact('schedule', 'schoolBuildings'));
    }

    /**
     * Update the specified schedule in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Check permission
        if (!$this->canEditSchedule($schedule)) {
            return redirect()->route('schedules.index')->with('error', 'この予定を編集する権限がありません。');
        }

        // Handle both single and multiple dates
        $scheduleDates = $request->input('schedule_dates', []);
        
        // If old format is used, convert to array
        if ($request->has('schedule_date') && !empty($request->schedule_date)) {
            $scheduleDates = [$request->schedule_date];
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'nullable',
            'color' => 'required|in:' . implode(',', array_keys(Schedule::getAvailableColors())),
            'schedule_dates' => 'required|array|min:1',
            'schedule_dates.*' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'school_building_id' => 'required|exists:school_buildings,id'
        ]);

        try {
            DB::beginTransaction();

            $uniqueDates = array_unique($scheduleDates);
            $dateCount = count($uniqueDates);
            
            if ($dateCount === 1) {
                // Single date - update existing schedule
                $schedule->update([
                    'title' => $request->title,
                    'content' => $request->content,
                    'color' => $request->color,
                    'schedule_date' => $uniqueDates[0],
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'school_building_id' => $request->school_building_id,
                    'status' => 'pending', // Reset to pending when updated
                    'approved_by' => null,
                    'approved_at' => null,
                    'approval_note' => null
                ]);
                
                $this->sendApprovalNotification($schedule);
                $updatedSchedules = [$schedule];
            } else {
                // Multiple dates - delete original and create new ones
                $originalScheduleData = [
                    'title' => $request->title,
                    'content' => $request->content,
                    'color' => $request->color,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'school_building_id' => $request->school_building_id,
                    'created_by' => $schedule->created_by,
                    'status' => 'pending'
                ];
                
                // Delete the original schedule
                $schedule->delete();
                
                // Create new schedules for each date
                $updatedSchedules = [];
                foreach ($uniqueDates as $date) {
                    if (!empty($date)) {
                        $newSchedule = Schedule::create(array_merge($originalScheduleData, [
                            'schedule_date' => $date
                        ]));
                        
                        $this->sendApprovalNotification($newSchedule);
                        $updatedSchedules[] = $newSchedule;
                    }
                }
            }

            DB::commit();

            $firstDate = Carbon::parse($uniqueDates[0]);
            
            $successMessage = $dateCount > 1 
                ? "スケジュールが{$dateCount}日分更新されました。再度承認をお待ちください。"
                : 'スケジュールが更新されました。再度承認をお待ちください。';

            return redirect()->route('schedules.index', [
                'year' => $firstDate->year,
                'month' => $firstDate->month,
                'school_building_id' => $request->school_building_id
            ])->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'スケジュールの更新に失敗しました。');
        }
    }

    /**
     * Remove the specified schedule from storage
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        // Check permission
        if (!$this->canEditSchedule($schedule) && !$this->canApproveSchedules()) {
            return redirect()->route('schedules.index')->with('error', 'この予定を削除する権限がありません。');
        }

        try {
            $schedule->delete();
            return redirect()->route('schedules.index')->with('success', 'スケジュールが削除されました。');
        } catch (\Exception $e) {
            return redirect()->route('schedules.index')->with('error', 'スケジュールの削除に失敗しました。');
        }
    }

    /**
     * Show schedule approval page
     *
     * @return \Illuminate\Http\Response
     */
    public function approval(Request $request)
    {
        // Check if user can approve schedules
        if (!$this->canApproveSchedules()) {
            return redirect()->route('schedules.index')->with('error', 'スケジュール承認の権限がありません。');
        }

        $schoolBuildingId = $request->get('school_building_id');
        $schoolBuildings = SchoolBuilding::orderBy('name')->get();
        
        $pendingSchedules = Schedule::getPendingSchedules($schoolBuildingId);

        return view('schedules.approval', compact(
            'pendingSchedules',
            'schoolBuildings',
            'schoolBuildingId'
        ));
    }

    /**
     * Approve or reject a schedule
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function updateApproval(Request $request, Schedule $schedule)
    {
        // Check if user can approve schedules
        if (!$this->canApproveSchedules()) {
            return redirect()->route('schedules.index')->with('error', 'スケジュール承認の権限がありません。');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'approval_note' => 'nullable|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $schedule->update([
                'status' => $request->status,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_note' => $request->approval_note
            ]);

            // Send notification to creator
            $this->sendApprovalResultNotification($schedule);

            DB::commit();

            $statusText = $request->status === 'approved' ? '承認' : '却下';
            return redirect()->route('schedules.index')->with('success', "スケジュールが{$statusText}されました。");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'スケジュール承認の処理に失敗しました。');
        }
    }

    /**
     * Bulk approve or reject schedules
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkApproval(Request $request)
    {
        // Check if user can approve schedules
        if (!$this->canApproveSchedules()) {
            return redirect()->route('schedules.index')->with('error', 'スケジュール承認の権限がありません。');
        }

        $request->validate([
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:schedules,id',
            'action' => 'required|in:approve,reject',
            'approval_note' => 'nullable|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $scheduleIds = $request->schedule_ids;
            $status = $request->action === 'approve' ? 'approved' : 'rejected';
            $approvalNote = $request->approval_note;

            // Get the schedules to update
            $schedules = Schedule::whereIn('id', $scheduleIds)
                ->where('status', 'pending')
                ->get();

            if ($schedules->isEmpty()) {
                throw new \Exception('承認待ちのスケジュールが見つかりません。');
            }

            // Update all selected schedules
            foreach ($schedules as $schedule) {
                $schedule->update([
                    'status' => $status,
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'approval_note' => $approvalNote
                ]);

                // Send notification to creator
                $this->sendApprovalResultNotification($schedule);
            }

            DB::commit();

            $statusText = $status === 'approved' ? '承認' : '却下';
            $count = $schedules->count();
            return redirect()->route('schedules.index')->with('success', "{$count}件のスケジュールが一括{$statusText}されました。");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', '一括処理に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * Generate calendar data for display with enhanced weekend and holiday styling (Monday start)
     *
     * @param int $year
     * @param int $month
     * @param \Illuminate\Database\Eloquent\Collection $schedules
     * @return array
     */
    private function generateCalendarData($year, $month, $schedules)
    {
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();
        
        // Start from Monday (1) instead of Sunday (0)
        $startWeek = $firstDay->copy()->startOfWeek(Carbon::MONDAY);
        $endWeek = $lastDay->copy()->endOfWeek(Carbon::MONDAY);

        $calendar = [];
        $currentDay = $startWeek->copy();

        // Group schedules by date
        $schedulesByDate = $schedules->groupBy(function ($schedule) {
            return $schedule->schedule_date->format('Y-m-d');
        });

        // Get Japanese holidays for the full calendar range (not just the current month)
        $holidays = $this->getJapaneseHolidaysForRange($startWeek, $endWeek);

        while ($currentDay->lte($endWeek)) {
            $dateString = $currentDay->format('Y-m-d');
            $daySchedules = $schedulesByDate->get($dateString, collect());

            $calendar[] = [
                'date' => $currentDay->copy(),
                'is_current_month' => $currentDay->month === $month,
                'schedules' => $daySchedules,
                'is_weekend' => $currentDay->isWeekend(),
                'is_saturday' => $currentDay->isSaturday(),
                'is_sunday' => $currentDay->isSunday(),
                'is_holiday' => array_key_exists($dateString, $holidays),
                'holiday_name' => $holidays[$dateString] ?? null
            ];

            $currentDay->addDay();
        }

        return $calendar;
    }

    /**
     * Get Japanese holidays for a date range (to cover full calendar view)
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getJapaneseHolidaysForRange($startDate, $endDate)
    {
        $holidays = [];
        
        // Get unique months and years in the range
        $months = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $key = $currentDate->year . '-' . $currentDate->month;
            if (!in_array($key, $months)) {
                $months[] = $key;
                
                // Get holidays for this month
                $monthHolidays = $this->getJapaneseHolidays($currentDate->year, $currentDate->month);
                $holidays = array_merge($holidays, $monthHolidays);
            }
            $currentDate->addDay();
        }
        
        return $holidays;
    }

    /**
     * Enhanced Japanese holidays system with comprehensive holiday support
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    private function getJapaneseHolidays($year, $month)
    {
        $holidays = [];

        // Fixed holidays
        $fixedHolidays = [
            '01-01' => '元日',
            '02-11' => '建国記念の日',
            '02-23' => '天皇誕生日', // Changed from 12-23 to 02-23 in 2020
            '04-29' => '昭和の日',
            '05-03' => '憲法記念日',
            '05-04' => 'みどりの日',
            '05-05' => 'こどもの日',
            '08-11' => '山の日',
            '11-03' => '文化の日',
            '11-23' => '勤労感謝の日'
        ];

        // Add fixed holidays for the month
        foreach ($fixedHolidays as $date => $name) {
            list($holidayMonth, $holidayDay) = explode('-', $date);
            if ((int)$holidayMonth == $month) {
                try {
                    $holidayDate = Carbon::create($year, $month, (int)$holidayDay);
                    $holidays[$holidayDate->format('Y-m-d')] = $name;
                } catch (\Exception $e) {
                    // Skip invalid dates (like Feb 30)
                    continue;
                }
            }
        }

        // Variable holidays
        try {
            if ($month == 1) {
                // 成人の日 (2nd Monday of January)
                $comingOfAgeDay = $this->getNthWeekdayOfMonth($year, 1, Carbon::MONDAY, 2);
                if ($comingOfAgeDay) {
                    $holidays[$comingOfAgeDay->format('Y-m-d')] = '成人の日';
                }
            }

            if ($month == 3) {
                // 春分の日 (Vernal Equinox Day - calculated)
                $vernalEquinox = $this->calculateVernalEquinox($year);
                if ($vernalEquinox) {
                    $holidays[$vernalEquinox->format('Y-m-d')] = '春分の日';
                }
            }

            if ($month == 7) {
                // 海の日 (3rd Monday of July)
                $seaDay = $this->getNthWeekdayOfMonth($year, 7, Carbon::MONDAY, 3);
                if ($seaDay) {
                    $holidays[$seaDay->format('Y-m-d')] = '海の日';
                }
            }

            if ($month == 9) {
                // 敬老の日 (3rd Monday of September)
                $respectForAgedDay = $this->getNthWeekdayOfMonth($year, 9, Carbon::MONDAY, 3);
                if ($respectForAgedDay) {
                    $holidays[$respectForAgedDay->format('Y-m-d')] = '敬老の日';
                }

                // 秋分の日 (Autumnal Equinox Day - calculated)
                $autumnalEquinox = $this->calculateAutumnalEquinox($year);
                if ($autumnalEquinox) {
                    $holidays[$autumnalEquinox->format('Y-m-d')] = '秋分の日';
                }
            }

            if ($month == 10) {
                // スポーツの日 (2nd Monday of October)
                $sportsDay = $this->getNthWeekdayOfMonth($year, 10, Carbon::MONDAY, 2);
                if ($sportsDay) {
                    $holidays[$sportsDay->format('Y-m-d')] = 'スポーツの日';
                }
            }
        } catch (\Exception $e) {
            // Continue without variable holidays if there's an error
        }

        // Handle substitute holidays (振替休日)
        $holidays = $this->addSubstituteHolidays($holidays, $year, $month);

        // Handle Golden Week extended holidays
        if ($month == 5) {
            $holidays = $this->addGoldenWeekExtensions($holidays, $year);
        }

        return $holidays;
    }

    /**
     * Get the nth occurrence of a weekday in a month
     *
     * @param int $year
     * @param int $month
     * @param int $weekday (Carbon constants: MONDAY, TUESDAY, etc.)
     * @param int $occurrence (1st, 2nd, 3rd, etc.)
     * @return Carbon|null
     */
    private function getNthWeekdayOfMonth($year, $month, $weekday, $occurrence)
    {
        try {
            $firstDay = Carbon::create($year, $month, 1);
            $firstWeekday = $firstDay->copy()->next($weekday);
            
            // If the first occurrence is in the same month
            if ($firstWeekday->month == $month) {
                $targetDate = $firstWeekday->copy()->addWeeks($occurrence - 1);
            } else {
                $targetDate = $firstWeekday->copy()->addWeeks($occurrence);
            }
            
            // Check if the target date is still in the same month
            return $targetDate->month == $month ? $targetDate : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Calculate Vernal Equinox Day (春分の日)
     *
     * @param int $year
     * @return Carbon|null
     */
    private function calculateVernalEquinox($year)
    {
        // Simplified calculation - actual calculation is more complex
        // This provides a good approximation for most years
        if ($year >= 1851 && $year <= 1899) {
            $day = 19.8277;
        } elseif ($year >= 1900 && $year <= 1979) {
            $day = 21.124;
        } elseif ($year >= 1980 && $year <= 2099) {
            $day = 20.8431;
        } elseif ($year >= 2100 && $year <= 2150) {
            $day = 21.8510;
        } else {
            // Default to March 20 for years outside the range
            return Carbon::create($year, 3, 20);
        }
        
        $day += 0.242194 * ($year - 1851) - floor(($year - 1851) / 4);
        $calculatedDay = floor($day);
        
        return Carbon::create($year, 3, $calculatedDay);
    }

    /**
     * Calculate Autumnal Equinox Day (秋分の日)
     *
     * @param int $year
     * @return Carbon|null
     */
    private function calculateAutumnalEquinox($year)
    {
        // Simplified calculation - actual calculation is more complex
        if ($year >= 1851 && $year <= 1899) {
            $day = 22.7020;
        } elseif ($year >= 1900 && $year <= 1979) {
            $day = 23.73;
        } elseif ($year >= 1980 && $year <= 2099) {
            $day = 23.2488;
        } elseif ($year >= 2100 && $year <= 2150) {
            $day = 24.2488;
        } else {
            // Default to September 23 for years outside the range
            return Carbon::create($year, 9, 23);
        }
        
        $day += 0.242194 * ($year - 1851) - floor(($year - 1851) / 4);
        $calculatedDay = floor($day);
        
        return Carbon::create($year, 9, $calculatedDay);
    }

    /**
     * Add substitute holidays (振替休日)
     *
     * @param array $holidays
     * @param int $year
     * @param int $month
     * @return array
     */
    private function addSubstituteHolidays($holidays, $year, $month)
    {
        foreach ($holidays as $dateString => $name) {
            try {
                $holidayDate = Carbon::parse($dateString);
                
                // If holiday falls on Sunday, the next Monday becomes a substitute holiday
                if ($holidayDate->isSunday()) {
                    $substituteDate = $holidayDate->copy()->addDay();
                    
                    // Only add if it's not already a holiday
                    if (!array_key_exists($substituteDate->format('Y-m-d'), $holidays)) {
                        $holidays[$substituteDate->format('Y-m-d')] = '振替休日';
                    }
                }
            } catch (\Exception $e) {
                // Skip invalid dates
                continue;
            }
        }
        
        return $holidays;
    }

    /**
     * Add Golden Week special extensions
     *
     * @param array $holidays
     * @param int $year
     * @return array
     */
    private function addGoldenWeekExtensions($holidays, $year)
    {
        try {
            // If there's a weekday between two holidays, it becomes a holiday (国民の休日)
            $mayHolidays = [];
            foreach ($holidays as $dateString => $name) {
                $date = Carbon::parse($dateString);
                if ($date->month == 5) {
                    $mayHolidays[] = $date->day;
                }
            }
            
            sort($mayHolidays);
            
            // Check for gaps between holidays in May
            for ($i = 0; $i < count($mayHolidays) - 1; $i++) {
                $currentDay = $mayHolidays[$i];
                $nextDay = $mayHolidays[$i + 1];
                
                // If there's exactly one day gap and it's a weekday
                if ($nextDay - $currentDay == 2) {
                    $gapDay = $currentDay + 1;
                    $gapDate = Carbon::create($year, 5, $gapDay);
                    
                    if (!$gapDate->isWeekend()) {
                        $holidays[$gapDate->format('Y-m-d')] = '国民の休日';
                    }
                }
            }
        } catch (\Exception $e) {
            // Continue without Golden Week extensions if there's an error
        }
        
        return $holidays;
    }

    /**
     * Check if user can edit schedule
     *
     * @param \App\Schedule $schedule
     * @return bool
     */
    private function canEditSchedule(Schedule $schedule)
    {
        $user = Auth::user();
        
        // Admin and office can edit any schedule
        if (in_array($user->roles, [1, 2])) {
            return true;
        }

        // Creator can edit their own pending schedules
        return $schedule->created_by === $user->id && $schedule->isPending();
    }

    /**
     * Check if user can approve schedules
     *
     * @return bool
     */
    private function canApproveSchedules()
    {
        $user = Auth::user();
        
        // Make sure roles is treated as integer for comparison
        $userRole = $user ? (int)$user->roles : null;
        return in_array($userRole, [1, 2]); // 1 = admin, 2 = office
    }

    /**
     * Send approval notification to approvers
     *
     * @param \App\Schedule $schedule
     * @return void
     */
    private function sendApprovalNotification(Schedule $schedule)
    {
        // TODO: Implement email notification to approvers
        // This would integrate with the existing email system
    }

    /**
     * Send approval result notification to creator
     *
     * @param \App\Schedule $schedule
     * @return void
     */
    private function sendApprovalResultNotification(Schedule $schedule)
    {
        // TODO: Implement email notification to schedule creator
        // This would integrate with the existing email system
    }

    /**
     * Show schedule history page
     * 作成者の場合: 自分が作成したスケジュールの履歴を表示
     * 承認者の場合: 自分が承認したスケジュールの履歴を表示
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $userRole = (int)$user->roles;
        
        // Filter parameters
        $schoolBuildingId = $request->get('school_building_id');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $perPage = $request->get('per_page', 20);
        
        // Get school buildings for filter
        $schoolBuildings = SchoolBuilding::orderBy('name')->get();
        
        // Base query depending on user role
        if (in_array($userRole, [1, 2])) {
            // 承認者の場合: 自分が承認/却下したスケジュールの履歴
            $query = Schedule::with(['schoolBuilding', 'creator'])
                ->whereNotNull('approved_by')
                ->where('approved_by', $user->id);
            $viewType = 'approver';
        } else {
            // 作成者の場合: 自分が作成したスケジュールの履歴
            $query = Schedule::with(['schoolBuilding', 'approver'])
                ->where('created_by', $user->id)
                ->whereIn('status', ['approved', 'rejected']); // 承認済みまたは却下されたもののみ
            $viewType = 'creator';
        }
        
        // Apply filters
        if ($schoolBuildingId) {
            $query->where('school_building_id', $schoolBuildingId);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($dateFrom) {
            $query->where('schedule_date', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->where('schedule_date', '<=', $dateTo);
        }
        
        // Order by most recent first
        $schedules = $query->orderBy('approved_at', 'desc')
                          ->orderBy('schedule_date', 'desc')
                          ->paginate($perPage);
        
        // Append query parameters to pagination links
        $schedules->appends($request->query());
        
        return view('schedules.history', compact(
            'schedules',
            'schoolBuildings',
            'schoolBuildingId',
            'status',
            'dateFrom',
            'dateTo',
            'perPage',
            'viewType'
        ));
    }

    /**
     * Get schedule history data for AJAX requests
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoryData(Request $request)
    {
        $user = Auth::user();
        $userRole = (int)$user->roles;
        
        // Base query depending on user role
        if (in_array($userRole, [1, 2])) {
            // 承認者の場合
            $query = Schedule::with(['schoolBuilding', 'creator'])
                ->whereNotNull('approved_by')
                ->where('approved_by', $user->id);
        } else {
            // 作成者の場合
            $query = Schedule::with(['schoolBuilding', 'approver'])
                ->where('created_by', $user->id)
                ->whereIn('status', ['approved', 'rejected']);
        }
        
        // Apply filters
        if ($request->has('school_building_id') && $request->school_building_id) {
            $query->where('school_building_id', $request->school_building_id);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->where('schedule_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('schedule_date', '<=', $request->date_to);
        }
        
        // Get results with pagination
        $schedules = $query->orderBy('approved_at', 'desc')
                          ->orderBy('schedule_date', 'desc')
                          ->paginate($request->get('per_page', 20));
        
        return response()->json([
            'success' => true,
            'data' => $schedules->items(),
            'pagination' => [
                'current_page' => $schedules->currentPage(),
                'last_page' => $schedules->lastPage(),
                'per_page' => $schedules->perPage(),
                'total' => $schedules->total(),
                'from' => $schedules->firstItem(),
                'to' => $schedules->lastItem(),
            ]
        ]);
    }
}