<?php

use App\Controllers\UserController;
use App\Middlewares\Authentication;
use Framework\Router;

use function Framework\session;
use function Framework\view;

Router::get("/", function ($req) {
    return view('index.html', ['user' => session('user')]);
});

Router::get("/signin", function ($req) {
    return view('signin.html', ['email' => session('email')]);
})->middleware(Authentication::class);
Router::post("/signin", [UserController::class, 'signin']);
Router::get("/signup", function ($req) {
    return view('signup.html');
})->middleware(Authentication::class);
Router::post("/signup", [UserController::class, 'signup']);
Router::get("/signout", [UserController::class, 'signout']);