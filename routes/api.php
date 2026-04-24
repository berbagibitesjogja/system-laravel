<?php

use App\Http\Controllers\Api\FormJobController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('university/{university}/faculty', [HeroController::class, 'getFaculties']);
Route::get('sponsor/{sponsor}/{start}/{end}', [ReportController::class, 'getDonations']);
Route::get('entries', [FormJobController::class, 'index']);
Route::post('entries', [FormJobController::class, 'update']);

Route::get('botnumber', [BotController::class, 'getBotNumber']);
