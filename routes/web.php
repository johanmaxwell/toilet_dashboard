<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/signup', function () {
    return view('auth.signup');
})->name('signup');

Route::get('/user-dashboard', function () {
    return view('user-dashboard');
})->middleware('role:user');

Route::get('/admin-dashboard', function () {
    return view('admin-dashboard');
})->middleware('role:admin');

Route::post('/set-role', function (Request $request) {
    $role = $request->input('role');

    session(['role' => $role]);

    return response()->json(['status' => 'ok', 'savedRole' => $role]);
});

Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json(['message' => 'Logged out successfully'], 200);
});
