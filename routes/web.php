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

    // Spatie activity route
    Volt::route('activity/index', 'activity.index.page')->name('activity.index')->middleware('can:activity-log-read');

    // Article routes
    Volt::route('articles/index', 'articles.index.page')->name('articles.index')->middleware('can:article-read');

    // Email routes
    Volt::route('email/index', 'email.index.page')->name('email.index')->middleware('can:email-read');
    Volt::route('email/archive', 'email.archive.page')->name('email.archive')->middleware('can:email-read');

    // Event routes
    Volt::route('events/index', 'events.index.page')->name('events.index')->middleware('can:event-read');
    Volt::route('events/create', 'events.create.page')->name('events.create')->middleware('can:event-create');
    Volt::route('events/{event}/edit/', 'events.edit.page')->name('events.edit')->middleware('can:event-update');
    Volt::route('events/{id}/show/', 'events.show.page')->name('events.show')->middleware('can:event-read');

    // Gallery routes
    Volt::route('gallery/index', 'gallery.index.page')->name('gallery.index')->middleware('can:gallery-read');

    // News routes
    Volt::route('news/index', 'news.index.page')->name('news.index')->middleware('can:news-read');

    // Post routes
    Volt::route('posts/index', 'posts.index.page')->name('posts.index')->middleware('can:post-read');

    // Story routes
    Volt::route('stories/index', 'stories.index.page')->name('stories.index')->middleware('can:story-read');


    // Story routes
    Volt::route('stories/index', 'stories.index.page')->name('stories.index')->middleware('can:story-read');



    // user views
    Volt::route('users/active', 'users.active.page')->name('users.active')->middleware('can:user-read');
    Volt::route('users/blocked', 'users.blocked.page')->name('users.blocked')->middleware('can:user-read');
    Volt::route('users/pending', 'users.pending.page')->name('users.pending')->middleware('can:user-read');


    // Venue routes
    Volt::route('venues/index', 'venues.index.page')->name('venues.index')->middleware('can:venue-read');
});

require __DIR__.'/auth.php';
