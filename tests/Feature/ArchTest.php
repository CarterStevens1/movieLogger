<?php

use Illuminate\Support\Facades\Redirect;

it('does not use debugging functions', function () {
    expect(['dd', 'dump', 'ray'])
        ->not->toBeUsed();
})->group('debug');

it('uses the redirect facade for redirecting', function () {
    expect(Redirect::class)
        ->toBeUsedIn('App\\Http\\Controllers\\');
})->group('debug');;
