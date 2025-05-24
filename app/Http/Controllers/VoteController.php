<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\Setting;

class VoteController extends Controller
{
    // عرض نموذج التصويت
    public function create()
    {
        $presidentCandidates = Candidate::where('type', 'president')->get();
        $memberCandidates = Candidate::where('type', 'member')->get();

        // كل المرشحين مع حساب الأصوات
        $allCandidates = Candidate::all();

        // حساب عدد الأصوات لكل مرشح
        $candidateVotesCount = Vote::select('candidate_id')
            ->selectRaw('count(*) as total')
            ->groupBy('candidate_id')
            ->pluck('total', 'candidate_id')->toArray();

        $totalVotesCount = Vote::count();

        return view('votes.create', compact(
            'presidentCandidates',
            'memberCandidates',
            'allCandidates',
            'candidateVotesCount',
            'totalVotesCount'
        ));
    }


    // حفظ التصويت
    public function store(Request $request)
    {
        $request->validate([
            'president_id' => 'required|exists:candidates,id',
            'member_ids'   => 'required|array|size:4',
            'member_ids.*' => 'exists:candidates,id',
        ]);

        $voter = auth()->user(); // نفترض أن الناخب مسجل دخول

        if (!Setting::isVotingActive()) {
            return back()->with('error', 'التصويت غير متاح في الوقت الحالي.');
        }

        if ($voter->has_voted) {
            return back()->with('error', 'لقد قمت بالتصويت مسبقًا.');
        }

        $president = Candidate::where('id', $request->president_id)->where('type', 'president')->first();
        if (!$president) {
            return back()->with('error', 'المرشح للرئاسة غير صالح.');
        }

        $members = Candidate::whereIn('id', $request->member_ids)->where('type', 'member')->count();
        if ($members != 4) {
            return back()->with('error', 'يجب اختيار 4 أعضاء بالضبط.');
        }

        // تسجيل التصويت للرئيس
        Vote::create([
            'voter_id' => $voter->id,
            'candidate_id' => $president->id,
        ]);

        // تسجيل التصويت للأعضاء
        foreach ($request->member_ids as $memberId) {
            Vote::create([
                'voter_id' => $voter->id,
                'candidate_id' => $memberId,
            ]);
        }

        $voter->update(['has_voted' => true]);

        return back()->with('success', 'تم تسجيل التصويت بنجاح.');
    }
}
