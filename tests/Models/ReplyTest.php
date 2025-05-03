<?php

namespace Tests\Models;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('reply has correct fillable attributes', static function () {
    $reply = new Reply;

    // Adjust these based on your actual model
    expect($reply->getFillable())->toContain('content')
        ->toContain('user_id')
        ->toContain('parent_id');
});

test('reply has correct casts', static function () {
    $reply = new Reply;
    $casts = $reply->getCasts();

    // Adjust as needed based on your model
    expect($casts)->toHaveKey('created_at')
        ->toHaveKey('updated_at');
});

test('reply belongs to a user', static function () {
    $reply = new Reply;

    expect($reply->user())->toBeInstanceOf(BelongsTo::class);
});

// If replies can have comments
test('reply can have comments', static function () {
    $reply = Reply::factory()->create();

    expect($reply->comments())->toBeInstanceOf(MorphMany::class);
});

// If replies can be nested
test('reply can have parent and child replies', static function () {
    $reply = new Reply;

    expect($reply->parent())->toBeInstanceOf(BelongsTo::class)
        ->and($reply->reply())->toBeInstanceOf(HasOne::class);
});

test('reply can be created with valid data', static function () {
    $user = User::factory()->create();

    $replyData = [
        'content' => 'Test reply content',
        'user_id' => $user->id,
    ];

    $reply = (new Reply)->create($replyData);

    expect($reply)->toBeInstanceOf(Reply::class)
        ->and($reply->content)->toBe('Test reply content')
        ->and($reply->user_id)->toBe($user->id);
});
