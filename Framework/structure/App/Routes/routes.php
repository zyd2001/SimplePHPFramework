<?php

use App\Controllers\UserController;
use App\Middlewares\Authentication;
use Framework\Router;

use function Framework\session;
use function Framework\view;

Router::addRoute("/", function ($req) {
    return view('index.html', ['user' => session('user')]);
});

Router::addRoute("/signin", function ($req) {
    return view('signin.html', ['email' => session('email')]);
})->middleware(Authentication::class);
Router::addRoute("/signin", [UserController::class, 'signin'], "post");
Router::addRoute("/signup", function ($req) {
    return view('signup.html');
})->middleware(Authentication::class);
Router::addRoute("/signup", [UserController::class, 'signup'], "post");
Router::addRoute("/signout", [UserController::class, 'signout']);