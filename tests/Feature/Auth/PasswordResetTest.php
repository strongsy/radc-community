<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test(/**
 * @throws Exception
 */ 'reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    (new Livewire\Volt\Volt)->test('auth.forgot-password')
        ->set('email', $user->email)
        ->call('sendPasswordResetLink');

    (new Illuminate\Support\Facades\Notification)->assertSentTo($user, ResetPassword::class);
});

test(/**
 * @throws Exception
 */ 'reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    (new Livewire\Volt\Volt)->test('auth.forgot-password')
        ->set('email', $user->email)
        ->call('sendPasswordResetLink');

    (new Illuminate\Support\Facades\Notification)->assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test(/**
 * @throws Exception
 */ 'password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    (new Livewire\Volt\Volt)->test('auth.forgot-password')
        ->set('email', $user->email)
        ->call('sendPasswordResetLink');

    (new Illuminate\Support\Facades\Notification)->assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = (new Livewire\Volt\Volt)->test('auth.reset-password', ['token' => $notification->token])
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('resetPassword');

        $response
            ->assertHasNoErrors()
            ->assertRedirect(route('login', absolute: false));

        return true;
    });
});
