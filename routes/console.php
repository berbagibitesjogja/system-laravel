<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('que:work --stop-when-empty')->timezone('Asia/Jakarta')->everyFiveSeconds();
Schedule::command('follow-up:all')->timezone('Asia/Jakarta')->dailyAt('08.00')->name('Kirim notifikasi staff');
Schedule::command('follow-up:staff')->timezone('Asia/Jakarta')->dailyAt('08.00')->name('Kirim notifikasi petugas');
Schedule::command('follow-up:all')->timezone('Asia/Jakarta')->dailyAt('16.00')->name('Kirim notifikasi staff');
Schedule::command('follow-up:staff')->timezone('Asia/Jakarta')->dailyAt('17.00')->name('Kirim notifikasi staff');
Schedule::command('follow-up:volunteer')->timezone('Asia/Jakarta')->dailyAt('19.00')->name('Kirim notifikasi petugas');
Schedule::command('clear:backup')->timezone('Asia/Jakarta')->dailyAt('20.00')->name('Hapus backup');
Schedule::command('calendar:send-notification')->timezone('Asia/Jakarta')->dailyAt('21.00')->name('Kirim notifikasi acara');
Schedule::command('report:daily')->timezone('Asia/Jakarta')->dailyAt('23.00')->name('Buat report');
