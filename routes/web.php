<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoteController;



Route::middleware(['auth'])->group(function () {
    Route::get('/', [VoteController::class, 'create'])->name('votes.create');
    Route::get('/vote', [VoteController::class, 'create'])->name('votes.create');
    Route::post('/vote', [VoteController::class, 'store'])->name('votes.store');
    Route::get('/vote/results', [VoteController::class, 'results'])->name('votes.results');
});
Route::match(['get', 'post'], '/login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login');


