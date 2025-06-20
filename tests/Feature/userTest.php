<?php

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

it('displays login button if not logged in', function () {
    // Act & Assert
    // Go to route and check text
    getRouteCheckText('home', 'Log In');
});

it('displays logout button if authenticated', function () {
    // Act & Assert
    login();
    // Go to route and check text
    getRouteCheckText('home', 'Log Out');
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
    getRouteCheckText('login', 'Log In');

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
    getRouteCheckText('home', 'Edit');

    // Change password and submit check success message to see success
    $this->post(route('update'), [
        'current_password' => 'password',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ])->assertSessionHas('success', 'Password updated successfully.');

    Auth::user()->refresh();

    expect(Hash::check('newpassword', Auth::user()->password))->toBeTrue();
});

it('can delete a user successfully', function () {
    login();
    // Delete user and check success message
    $this->post(route('destroy'))->assertSessionHas('success', 'User deleted successfully.');
});

it('failes to delete a user due to not being logged in', function () {

    // Delete user with ID and check success message
    $this->post(route('destroy'))->assertSessionHas('error', 'Not logged in. cannot delete user.');
})->skip('Need to finish this test');
