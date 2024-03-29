<?php

namespace App\Controllers;

use App\Models\User;
use Framework\Request;

use function Framework\redirect;
use function Framework\session;

class UserController
{
    public static function signin(Request $req)
    {
        $email = $req->post('email');
        $password = $req->post('password');
        $user = User::where(["email" => $email]);
        if (password_verify($password, $user->password_hash))
        {
            $before = session('before_signin');
            $u = ['username' => $user->username, 'user_email' => $user->email];
            session('signed_in', true);
            session('user', $u);
            if ($before)
            {    
                session()->delete('signed_in');
                return redirect(strval($before));
            }
            else
                return redirect('/');
        }
        else
        {
            session()->flash('email', $email);
            return redirect('/signin');
        }
    }

    public static function signup(Request $req)
    {
        $user = new User();
        $user->email = $req->post('email');
        $user->username = $req->post('username');
        $user->password_hash = password_hash($req->post('password'), PASSWORD_DEFAULT);
        $user->save();
        session('signed_in', true);
        session('user', $user);
        return redirect('/');
    }

    public static function signout(Request $req)
    {
        session()->delete('signed_in');
        session()->delete('user');
        return redirect('/');
    }
}