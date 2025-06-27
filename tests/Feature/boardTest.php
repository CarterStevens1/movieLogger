<?php

use App\Models\Board;
use App\Models\BoardColumns;
use App\Models\BoardRows;
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

it('has base columns on board creation', function () {
    // Create a user
    $user = User::factory()->create();
    // Authenticate the user
    login($user);
    // Create a board
    $board = Board::factory()->create();
    // Assert that the board has 20 columns
    expect($board->columns->count())->toBe(20);
});
it('has base rows on board creation', function () {
    // Create a user
    $user = User::factory()->create();
    // Authenticate the user
    login($user);
    // Create a board
    $board = Board::factory()->create();
    // Assert that the board has 50 rows
    expect($board->rows->count())->toBe(50);
});


describe('Column Name Generation', function () {
    it('generates correct Excel-style column names', function () {
        expect(BoardColumns::generateLabel(0))->toBe('A');
        expect(BoardColumns::generateLabel(25))->toBe('Z');
        expect(BoardColumns::generateLabel(26))->toBe('AA');
        expect(BoardColumns::generateLabel(51))->toBe('AZ');
        expect(BoardColumns::generateLabel(52))->toBe('BA');
        expect(BoardColumns::generateLabel(701))->toBe('ZZ');
        expect(BoardColumns::generateLabel(702))->toBe('AAA');
    });
});

describe('Row Name Generation', function () {
    it('generates correct Excel-style row names', function () {
        expect(BoardRows::generateLabel(1))->toBe('1');
        expect(BoardRows::generateLabel(10))->toBe('10');
        expect(BoardRows::generateLabel(11))->toBe('11');
        expect(BoardRows::generateLabel(20))->toBe('20');
    });
});

it('can add column to board with default columns', function () {
    // Create a user
    $user = User::factory()->create();
    // Authenticate the user
    login($user);
    // Create a board
    $board = Board::factory()->create();
    // Add a column to the board
    $column = BoardColumns::factory()->create([
        'board_id' => $board->id,
        'label' => 'Test Column',
    ]);

    expect($board->fresh()->columns->count())->toBe(21);
});

it('can add row to board with default rows', function () {
    // Create a user
    $user = User::factory()->create();
    // Authenticate the user
    login($user);
    // Create a board
    $board = Board::factory()->create();
    // Add a row to the board
    $row = BoardRows::factory()->create([
        'board_id' => $board->id,
        'label' => 'Test Row',
    ]);

    expect($board->fresh()->rows->count())->toBe(51);
});
