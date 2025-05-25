<?php
namespace App\Http\Controllers;

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
}
