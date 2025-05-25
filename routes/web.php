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



Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'votedVoters'])->name('admin.voters');
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings/update', [App\Http\Controllers\AdminController::class, 'updateSettings'])->name('admin.settings.update');
});

