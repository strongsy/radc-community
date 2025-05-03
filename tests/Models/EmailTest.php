<?php

namespace Tests\Models;

use App\Models\Email;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('email has correct fillable attributes', static function () {
    $email = new Email;

    // Adjust these based on your actual model
    expect($email->getFillable())->toContain('subject')
        ->toContain('body')
        ->toContain('user_id')
        ->toContain('recipient_email');
});

test('email has correct casts', static function () {
    $email = new Email;
    $casts = $email->getCasts();

    // Adjust these based on your actual model
    expect($casts)->toHaveKey('created_at')
        ->toHaveKey('updated_at');

    // If you have any date fields or JSON fields
    // expect($casts)->toHaveKey('sent_at')->toBe('datetime');
});

test('email can be created with valid data', static function () {
    $user = User::factory()->create();

    $emailData = [
        'subject' => 'Test Subject',
        'body' => 'Test Body Content',
        'user_id' => $user->id,
        'recipient_email' => 'test@example.com',
    ];

    $email = Email::create($emailData);

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->subject)->toBe('Test Subject')
        ->and($email->body)->toBe('Test Body Content')
        ->and($email->user_id)->toBe($user->id)
        ->and($email->recipient_email)->toBe('test@example.com');
});
