<?php

use App\Http\Middleware\CheckIsActiveMiddleware;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('/', static function () {
    return view('welcome');
})->name('home');

Route::get('/about', static function () {
    return view('front-end.about');
})->name('about');

Route::get('/history', static function () {
    return view('front-end.history');
})->name('history');

Route::get('/memorial', static function () {
    return view('front-end.memorial');
})->name('memorial');

Route::get('/chapel', static function () {
    return view('front-end.chapel');
})->name('chapel');

Route::get('/museum', static function () {
    return view('front-end.museum');
})->name('museum');

// Dashboard route checking if user the user is active and not blocked
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', CheckIsActiveMiddleware::class])
    ->name('dashboard');

// Unsubscribe route
Route::get('/unsubscribe/{token}', static function ($token) {
    Log::info('Received unsubscribe token: '.$token);

    $user = User::where('unsubscribe_token', $token)->first();

    if (! $user) {

        return response('Invalid unsubscribe token.', 404);
    }

    $user->update(['is_subscribed' => false]);

    return view('unsubscribed');
})->name('unsubscribe');

// Volt routes
Volt::route('contact', 'auth.contact')->middleware(ProtectAgainstSpam::class)->name('contact');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // user views
    Volt::route('users/active', 'backend.users.active.page')->name('users.active')->middleware('can:user-read');
    Volt::route('users/blocked', 'backend.users.blocked.page')->name('users.blocked')->middleware('can:user-read');
    Volt::route('users/registrations', 'backend.users.registrations.page')->name('users.registrations')->middleware('can:user-read');

    // activity log views
    Volt::route('activity-log', 'backend.activity.index.page')->name('activity-log')->middleware('can:activity-log-read');

    // mail views
    Volt::route('mail-list', 'backend.email.index.page')->name('mail-list')->middleware('can:mail-read');
    Volt::route('mail-archived', 'backend.email.archived.page')->name('mail-archived')->middleware('can:mail-restore');

    // event views
    Volt::route('event-list', 'backend.events.index.page')->name('event-list')->middleware('can:event-read');

    //article views
    Volt::route('article-list', 'backend.articles.index.page')->name('article-list')->middleware('can:article-read');

    //stories views
    Volt::route('story-list', 'backend.stories.index.page')->name('story-list')->middleware('can:story-read');

    //posts views
    Volt::route('post-list', 'backend.posts.index.page')->name('post-list')->middleware('can:post-read');

    //galleries views
    Volt::route('gallery-list', 'backend.galleries.index.page')->name('gallery-list')->middleware('can:gallery-read');
});

require __DIR__.'/auth.php';
