<?php

use App\Models\Board;
use App\Models\User;


it('can create a board', function () {
    Board::factory()->create();
    // Assert that the board was created
    expect(Board::count())->toBe(1);
});

it('can create a board and assign it to a user', function () {
    // Create a user
    $user = User::factory()->create();
    // Authenticate the user
    login($user);
    // Create a board
    $board = Board::factory()->create([
        'user_id' => $user->id,
    ]);
    // Assert that the board was assigned to the user
    expect($board->user_id)->toBe($user->id);
});

it('can delete a board assigned to a user', function () {
    // Create a user
    $user = User::factory()->create();
    // Authenticate the user
    login($user);
    // Create a board
    $board = Board::factory()->create([
        'user_id' => $user->id,
    ]);
    // Delete the board
    $board->delete();
    // Assert that the board was deleted
    expect(Board::count())->toBe(0);
});


it('can edit a board assigned to a user', function () {
    // Create a user
    $user = User::factory()->create();
    // Authenticate the user
    login($user);
    // Create a board
    $board = Board::factory()->create([
        'user_id' => $user->id,
    ]);
    // Edit the board
    $board->name = 'New Name';
    $board->save();
    // Assert that the board was edited
    expect($board->name)->toBe('New Name');
});

it('can share a board with another user', function () {
    // Create users
    $user = User::factory()->create();
    $user2 = User::factory()->create();

    // Authenticate the user
    login($user);

    // Create a board
    $board = Board::factory()->create([
        'user_id' => $user->id,
    ]);

    // Share the board with the user2
    $board->sharedUsers()->attach($user2->id, [
        'board_owner_id' => $board->user_id,
    ]);

    // Assert that the board was shared with the user2
    expect($board->sharedUsers()->where('user_id', $user2->id)->exists())->toBeTrue();
});

it('can unshare a board with another user', function () {
    // Create users
    $user = User::factory()->create();
    $user2 = User::factory()->create();

    // Authenticate the user
    login($user);

    // Create a board
    $board = Board::factory()->create([
        'user_id' => $user->id,
    ]);

    // Share the board with the user2
    $board->sharedUsers()->attach($user2->id, [
        'board_owner_id' => $board->user_id,
    ]);

    // Assert that the board was shared with the user2
    expect($board->sharedUsers()->where('user_id', $user2->id)->exists())->toBeTrue();

    // Unshare the board with user2
    $board->sharedUsers()->detach($user2->id);

    // Refresh board to ensure relationship is updated
    $board->refresh();

    // Assert that the board is no longer shared with user2
    expect($board->sharedUsers()->where('user_id', $user2->id)->exists())->toBeFalse();
});

// it('can add columns and rows to a board', function () {

//     // Create a user
//     $user = User::factory()->create();
//     // Authenticate the user
//     login($user);
//     // Create a board
//     $board = Board::factory()->create();
//     // Assign the board to the user
//     $board->user()->associate($user);
//     // Create a column and add it to the board  
//     // Create a row and add it to the board
//     // Assert that the column and row were added
// });

describe('Column Name Generation', function() {
    it('generates correct Excel-style column names', function() {
        expect(generateColumnName(1))->toBe('A');
        expect(generateColumnName(26))->toBe('Z');
        expect(generateColumnName(27))->toBe('AA');
        expect(generateColumnName(52))->toBe('AZ');
        expect(generateColumnName(53))->toBe('BA');
        expect(generateColumnName(702))->toBe('ZZ');
        expect(generateColumnName(703))->toBe('AAA');
    });
});
