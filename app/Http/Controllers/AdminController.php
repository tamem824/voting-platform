<?php
namespace App\Http\Controllers;

use App\Models\VoteLog;
use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Setting;

class AdminController extends Controller
{
    public function votedVoters()
    {
        $voters = Voter::where('has_voted', true)->get();
        return view('admin.voters', compact('voters'));
    }

    public function settings()
    {
        $setting = Setting::first(); // assuming one row
        return view('admin.settings', compact('setting'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'starting_vote' => 'required|date',
            'ending_vote' => 'required|date|after:starting_vote',
            'is_active' => 'required|boolean',
        ]);

        $setting = Setting::first();
        $setting->update([
            'starting_vote' => $request->starting_vote,
            'ending_vote' => $request->ending_vote,
            'is_active' => $request->is_active,
        ]);

        return back()->with('success', 'تم تحديث إعدادات التصويت بنجاح.');
    }
    public function voteLogs()
    {
        $logs = VoteLog::with('voter')->latest()->paginate(20);
        return view('admin.vote_logs', compact('logs'));
    }
    public function show($id)
    {
        $voter = Voter::findOrFail($id);
        return view('admin.show', compact('voter'));
    }

}
