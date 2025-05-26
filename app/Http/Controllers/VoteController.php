<?php

namespace App\Http\Controllers;

use App\Models\VoteLog;
use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    // عرض نموذج التصويت
    public function create()
    {
        $candidates = Candidate::all();
        $presidentCandidates = $candidates->where('type', 'president');
        $memberCandidates = $candidates->where('type', 'member');
        $settings = Setting::isVotingActive();
        $is_voted = Voter::hasVoted(auth()->id());

        // حساب عدد الأصوات لكل مرشح
        $candidateVotesCount = Vote::select('candidate_id')
            ->selectRaw('count(*) as total')
            ->groupBy('candidate_id')
            ->pluck('total', 'candidate_id')
            ->toArray();

        $totalVotesCount = Vote::count();

        return view('votes.create', compact(
            'presidentCandidates',
            'memberCandidates',
            'candidates',
            'candidateVotesCount',
            'totalVotesCount'
            , 'settings',
            'is_voted'
        ));
    }

    // حفظ التصويت
    public function store(Request $request)
    {
        $request->validate([
            'president_id' => 'required|exists:candidates,id',
            'member_ids' => 'required|array|size:4',
            'member_ids.*' => 'exists:candidates,id',
        ]);

        $voter = auth()->user();

        if (!Setting::isVotingActive()) {
            return back()->with('error', 'التصويت غير متاح في الوقت الحالي.');
        }

        if ($voter->has_voted || Vote::where('voter_id', $voter->id)->exists()) {
            return back()->with('error', 'لقد قمت بالتصويت مسبقًا.');
        }

        $president = Candidate::where('id', $request->president_id)
            ->where('type', 'president')
            ->first();

        if (!$president) {
            return back()->with('error', 'المرشح للرئاسة غير صالح.');
        }

        $validMembersCount = Candidate::whereIn('id', $request->member_ids)
            ->where('type', 'member')
            ->count();

        if ($validMembersCount !== 4) {
            return back()->with('error', 'يجب اختيار 4 أعضاء بالضبط.');
        }

        DB::transaction(function () use ($voter, $president, $request) {
            // تصويت للرئيس
            Vote::create([
                'voter_id' => $voter->id,
                'candidate_id' => $president->id,
            ]);

            // تصويت للأعضاء
            foreach ($request->member_ids as $memberId) {
                Vote::create([
                    'voter_id' => $voter->id,
                    'candidate_id' => $memberId,
                ]);
            }

            // تحديث حالة المصوت
            $voter->update(['has_voted' => true]);

            // تسجيل معلومات الجهاز
            $userAgent = $request->header('User-Agent');
            $platform = php_uname('s') ?? 'unknown';

            VoteLog::create([
                'voter_id' => $voter->id,
                'ip_address' => $request->ip(),
                'browser' => $this->getBrowser($userAgent),
                'platform' => $platform,
                'user_agent' => $userAgent,
            ]);
        });

        return back()->with('success', 'تم تسجيل التصويت بنجاح.');
    }


    // API: نتائج التصويت بصيغة JSON
    public function results()
    {
        $candidates = Candidate::all();

        // إجمالي الأصوات حسب نوع المرشح
        $presidentVotesCount = Vote::whereHas('candidate', fn($q) => $q->where('type', 'president'))->count();
        $memberVotesCount = Vote::whereHas('candidate', fn($q) => $q->where('type', 'member'))->count();

        // عدد الأصوات لكل مرشح
        $votesPerCandidate = Vote::select('candidate_id', DB::raw('count(*) as total'))
            ->groupBy('candidate_id')
            ->pluck('total', 'candidate_id');

        $data = $candidates->map(function ($candidate) use ($votesPerCandidate, $presidentVotesCount, $memberVotesCount) {
            $votes = $votesPerCandidate[$candidate->id] ?? 0;

            $total = $candidate->type === 'president' ? $presidentVotesCount : $memberVotesCount;
            $percentage = $total > 0 ? round(($votes / $total) * 100, 2) : 0;

            return [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'type' => $candidate->type,
                'photo' => $candidate->photo ? asset('storage/' . $candidate->photo) : null,
                'votes' => $votes,
                'percentage' => $percentage,
            ];
        });

        return response()->json($data);
    }
    private function getBrowser($userAgent)
    {
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        if (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) return 'Opera';
        if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) return 'Internet Explorer';
        return 'Unknown';
    }


}
