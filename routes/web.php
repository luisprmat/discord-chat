<?php

use App\Http\Controllers\ChannelController;
use App\Models\Channel;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::redirect('/', '/general');

Route::middleware(['auth'])->group(function () {
    Route::post('/channels', [ChannelController::class, 'store'])
        ->middleware([HandlePrecognitiveRequests::class])
        ->name('channels.store');

    Route::post('/channels/{channel}/join', [ChannelController::class, 'join'])
        ->name('channels.join');

    Route::post('/channels/{channel}/send', [ChannelController::class, 'send'])
        ->name('channels.send');

    Route::get('/{channel:name}', [ChannelController::class, 'index'])
        ->name('workspace');

    Route::delete('reset', function () {
        Artisan::call('migrate:fresh --force --seed');

        return redirect()->route('workspace', Channel::first());
    })->name('reset');
});
