<?php

use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\PrecenceController;
use App\Http\Controllers\ReimburseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\VolunteerController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('apply/{entry}/{job}', [VolunteerController::class, 'applyJob']);
Route::get('un-apply/{entry}/{job}', [VolunteerController::class, 'unapplyJob']);
Route::get('monthly-report/{code}', [ReportController::class, 'downloadMonthly'])->name('monthlyReport');
Route::match(['get', 'post'], 'from-fonnte', [BotController::class, 'fromFonnte'])->withoutMiddleware(VerifyCsrfToken::class);
Route::fallback(function () {
    return view('pages.coming');
});
Route::get('auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');

Route::post('abcence/distance', [PrecenceController::class, 'userAttendance']);
Route::redirect('home', '');

Route::controller(VolunteerController::class)->group(function () {
    Route::get('', 'home')->name('volunteer.home');
    Route::get('hall-of-fame', 'hallOfFame')->name('hall-of-fame');
    Route::get('auth/google/callback', 'authenticate');
    Route::get('login', 'login')->name('login');
    Route::get('logout', 'logout')->name('logout')->middleware('auth');
});
Route::get('donation/food-rescue', [DonationController::class, 'rescue'])->name('donation.rescue');
Route::get('donation/charity', [DonationController::class, 'charity'])->name('donation.charity');
Route::middleware('auth')->group(function () {
    Route::get('volunteer/precence/qr', [PrecenceController::class, 'getQrCode'])->name('precence.qr');
    Route::get('sponsor/individu', [SponsorController::class, 'individu'])->name('sponsor.individu');

    Route::controller(HeroController::class)->name('hero.')->group(function () {
        Route::post('hero/contributor', 'contributor')->name('contributor');
        Route::get('hero/faculty/{faculty}', 'faculty')->name('faculty');
    });
    Route::controller(LogController::class)->name('logs.')->group(function () {
        Route::get('logs/system')->name('system');
        Route::get('logs/activity', 'activityLogs')->name('activity');
    });
    Route::post('attendance/precence/{precence}', [PrecenceController::class, 'manual'])->name('attendance.manual');
    Route::resource('volunteer/precence', PrecenceController::class);
    Route::resource('volunteer', VolunteerController::class);
    Route::resource('food', FoodController::class)->except(['show', 'create']);
    Route::get('gallery', [DonationController::class, 'gallery'])->name('gallery');
    Route::resource('sponsor', SponsorController::class);
    Route::resource('beneficiary', BeneficiaryController::class);
    Route::resource('donation', DonationController::class);
    Route::resource('reimburse', ReimburseController::class);
});
Route::middleware('guest')->group(function () {
    Route::get('form', [HeroController::class, 'create'])->name('form.create');
    Route::get('notify', [NotifyController::class, 'form'])->name('notify.form');
    Route::post('form', [HeroController::class, 'store'])->name('hero.store');
});
Route::get('hero/cancel', [HeroController::class, 'cancel'])->name('hero.cancel');
Route::resource('hero', HeroController::class)->except(['show', 'edit', 'create', 'store']);
