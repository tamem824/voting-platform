<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;

class EnsureVotingIsOpen
{
    public function handle($request, Closure $next)
    {
        if (!Setting::isVotingActive()) {
            return redirect()->route('votes.winners')->with('error', 'التصويت غير متاح حالياً');
        }

        return $next($request);
    }
}
