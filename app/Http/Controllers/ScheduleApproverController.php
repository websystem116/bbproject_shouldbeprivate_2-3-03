<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ScheduleApprover;
use App\SchoolBuilding;
use Illuminate\Support\Facades\DB;

class ScheduleApproverController extends Controller
{
    /**
     * Display a listing of schedule approvers
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $approvers = ScheduleApprover::with('schoolBuilding')->orderBy('role')->orderBy('name')->get();
        return view('schedule_approvers.index', compact('approvers'));
    }

    /**
     * Show the form for creating a new schedule approver
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $schoolBuildings = SchoolBuilding::orderBy('name')->get();
        return view('schedule_approvers.create', compact('schoolBuildings'));
    }

    /**
     * Store a newly created schedule approver in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:schedule_approvers,email',
            'role' => 'required|in:admin,office,manager',
            'school_building_id' => 'nullable|exists:school_buildings,id',
            'notes' => 'nullable|max:1000'
        ]);

        try {
            ScheduleApprover::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'school_building_id' => $request->school_building_id,
                'is_active' => true,
                'notes' => $request->notes
            ]);

            return redirect()->route('schedule_approvers.index')->with('success', '承認者が登録されました。');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', '承認者の登録に失敗しました。');
        }
    }

    /**
     * Show the form for editing the specified schedule approver
     *
     * @param  \App\ScheduleApprover  $scheduleApprover
     * @return \Illuminate\Http\Response
     */
    public function edit(ScheduleApprover $scheduleApprover)
    {
        $schoolBuildings = SchoolBuilding::orderBy('name')->get();
        return view('schedule_approvers.edit', compact('scheduleApprover', 'schoolBuildings'));
    }

    /**
     * Update the specified schedule approver in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ScheduleApprover  $scheduleApprover
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ScheduleApprover $scheduleApprover)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:schedule_approvers,email,' . $scheduleApprover->id,
            'role' => 'required|in:admin,office,manager',
            'school_building_id' => 'nullable|exists:school_buildings,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|max:1000'
        ]);

        try {
            $scheduleApprover->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'school_building_id' => $request->school_building_id,
                'is_active' => $request->has('is_active'),
                'notes' => $request->notes
            ]);

            return redirect()->route('schedule_approvers.index')->with('success', '承認者情報が更新されました。');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', '承認者情報の更新に失敗しました。');
        }
    }

    /**
     * Remove the specified schedule approver from storage
     *
     * @param  \App\ScheduleApprover  $scheduleApprover
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScheduleApprover $scheduleApprover)
    {
        try {
            $scheduleApprover->delete();
            return redirect()->route('schedule_approvers.index')->with('success', '承認者が削除されました。');
        } catch (\Exception $e) {
            return redirect()->route('schedule_approvers.index')->with('error', '承認者の削除に失敗しました。');
        }
    }
}
