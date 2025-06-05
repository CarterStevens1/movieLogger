<?php

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        ->assertSeeText('Log Out');
});

it('logs in user successfully', function () {

    User::factory()->create(
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password'
        ]
    );

    // Prefill the form with valid data
    $this->get(route('login'))
        ->assertOk()
        ->assertSeeText('Log In');

    $this->post(route('login'), [
        'email' => 'john@example.com',
        'password' => 'password',
    ])->assertRedirect('/');

    expect(Auth::check())->toBeTrue();
});

it('fails to log in user with invalid credentials', function () {

    User::factory()->create(
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password'
        ]
    );
    // Prefill the form with valid data
    $this->get(route('login'))
        ->assertOk()
        ->assertSeeText('Log In');

    $this->post(route('login'), [
        'email' => 'john@example.com',
        'password' => 'PassworD',
    ])->assertRedirect(route('login'));

    expect(Auth::check())->toBeFalse();
});


it('edits user successfully', function () {
    $user = User::factory()->create(
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password'
        ]
    );

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSeeText('Edit');

    // Change password and submit check success message to see success
    $this->post(route('update'), [
        'current_password' => 'password',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ])->assertSessionHas('success', 'Password updated successfully.');

    $user->refresh();

    expect(Hash::check('newpassword', $user->password))->toBeTrue();
});
