<?php

it('does not use debugging functions', function () {
    expect(['dd', 'dump', 'ray'])
        ->not->toBeUsed();
})->group('debug');
