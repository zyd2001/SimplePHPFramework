<?php

use Framework\Request;
use Framework\Response;
use Framework\Session;
use Framework\Viewer;

function session(string $i = null, $value = null)
{
    $session = Session::s();
    if ($value === null && $i === null)
    {
        return $session;
    }
    else if ($value === null)
    {
        return $session->get($i);
    }
    else
    {
        $session->set($i, $value);
    }
}

function request()
{
    return Request::req();
}

function redirect($url)
{
    return Response::redirect($url);
}

function view($name, array $arr = [])
{
    $view = new Viewer($name);
    return $view->view($arr);
}

function csrf_token()
{
    $token = session('csrf_token');
    if (!$token)
    {
        $token = bin2hex(random_bytes(6));
        session('csrf_token', $token);
    }
    return $token;
}

function js($src)
{
    return 'assets/js' . $src;
}
function css($src)
{
    return 'assets/css' . $src;
}