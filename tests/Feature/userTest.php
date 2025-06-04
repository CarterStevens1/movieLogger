<?php

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays login button if not logged in', function () {

    // Act & Assert
    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('Log In');
});

it('displays logout button if authenticated', function () {

    // Act & Assert
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Log Out');;
});
