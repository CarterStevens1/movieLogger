<?php

use Illuminate\Support\Facades\Route;

it('displays login button if not logged in', function () {

    // Act & Assert
    // go to home check okay and see log in text and see if you can go to the login url
    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('Log In');
});
