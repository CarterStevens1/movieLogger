<?php

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

it('displays login button if not logged in', function () {
    // Act & Assert
    // Go to route and check text
    checkRoute('home', 'Log In');
});

it('displays logout button if authenticated', function () {
    // Act & Assert
    login();
    // Go to route and check text
    checkRoute('home', 'Log Out');
});

it('logs in user successfully', function () {

    $tempUser = User::factory()->create(
        [
            'password' => 'password'
        ]
    );

    $this->post(route('login'), [
        'email' => $tempUser->email,
        'password' => 'password',
    ])->assertRedirect('/');

    expect(Auth::check())->toBeTrue();
});

it('fails to log in user with invalid credentials', function () {

    $tempUser = User::factory()->create();
    // Go to route and check text
    checkRoute('login', 'Log In');

    $this->post(route('login'), [
        'email' => $tempUser->email,
        'password' => 'PassworD',
    ])->assertRedirect(route('login'));

    expect(Auth::check())->toBeFalse();
});


it('edits user successfully', function () {

    login(User::factory()->create(
        [
            'password' => 'password'
        ]
    ));
    // Go to route and check text
    checkRoute('home', 'Edit');

    // Change password and submit check success message to see success
    $this->post(route('update'), [
        'current_password' => 'password',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ])->assertSessionHas('success', 'Password updated successfully.');

    Auth::user()->refresh();

    expect(Hash::check('newpassword', Auth::user()->password))->toBeTrue();
});
