<?php

namespace Framework;

/**
 * if both arguments given, set $i in the session to $value
 * @return mixed if only $i given, returns the matched value in the session 
 * @return Session if no arguments given
 */
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

/**
 * return current Request instance
 * 
 * @return Request
 */
function request() : Request
{
    return Request::req();
}

/**
 * return json object from json request
 *
 * @return mixed
 */
function jsonReq()
{
    return json_decode(request()->rawPost);
}

/**
 * return a response redirect to $url
 *
 * @param string $url
 * @return Response
 */
function redirect(string $url) : Response
{
    return Response::redirect($url);
}

/**
 * return a view response with given $name and data $arr
 *
 * @param string $name
 * @param array $arr
 * @return Response
 */
function view(string $name, array $arr = []) : Response
{
    $view = new Viewer($name);
    return $view->view($arr);
}

/**
 * Generate CSRF Token
 *
 * @return string
 */
function csrf_token() : string
{
    $token = session('csrf_token');
    if (!$token)
    {
        $token = bin2hex(random_bytes(6));
        session('csrf_token', $token);
    }
    return $token;
}

/**
 * helper functions for assets path
 *
 * @param string $src
 * @return string
 */
function js(string $src) : string
{
    return 'assets/js/' . $src;
}
function css(string $src) : string
{
    return 'assets/css/' . $src;
}