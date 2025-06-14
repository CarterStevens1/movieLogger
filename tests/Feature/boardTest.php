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

// it('can share a board with another user', function () {
// });

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
