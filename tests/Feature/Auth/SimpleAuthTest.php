<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

test('basic authentication works', function () {
    // Create user manually without factory
    $user = new User();
    $user->name = 'Test User';
    $user->email = 'test@example.com';
    $user->password = Hash::make('password');
    $user->email_verified_at = now();
    $user->save();

    // Try manual authentication
    $authenticated = Auth::attempt([
        'email' => 'test@example.com',
        'password' => 'password'
    ]);

    expect($authenticated)->toBeTrue();
    expect(Auth::check())->toBeTrue();
});

test('login route works without cart logic', function () {
    // Create user manually
    $user = new User();
    $user->name = 'Test User';
    $user->email = 'test@example.com';
    $user->password = Hash::make('password');
    $user->email_verified_at = now();
    $user->save();

    // Skip GET route test for now, focus on POST authentication
    
    // Test POST route directly
    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    // Should redirect (302) not return 500
    expect($response->status())->toBe(302);
    
    // Should be authenticated
    $this->assertAuthenticated();
});
