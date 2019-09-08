<?php

use App\Controllers\UserController;
use Framework\Middlewares\Authentication;
use Framework\Router;

Router::addRoute("/", function ($req) {
    return view('index.html', ['user' => session('user')]);
});

Router::addRoute("/login", function ($req) {
    return view('login.html', ['email' => session('email')]);
})->middleware(Authentication::class);
Router::addRoute("/login", [UserController::class, 'login'], "post");
Router::addRoute("/register", function ($req) {
    return view('register.html');
})->middleware(Authentication::class);
Router::addRoute("/register", [UserController::class, 'register'], "post");
Router::addRoute("/logout", [UserController::class, 'logout']);