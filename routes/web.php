<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoteController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/vote', [VoteController::class, 'create'])->name('votes.create');
    Route::post('/vote', [VoteController::class, 'store'])->name('votes.store');
});
Route::match(['get', 'post'], '/login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login');


