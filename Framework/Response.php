<?php

namespace Framework;

class Response
{
    private $header = [];
    private $content;

    public function __construct(string $t = "")
    {
        $this->content = $t;
    }

    public function addHeader(string $str)
    {
        array_push($this->header, $str);
        return $this;
    }

    public function status(int $code)
    {
        http_response_code($code);
        return $this;
    }

    public function send()
    {
        $out = ob_get_clean();
        ob_end_clean();
        foreach ($this->header as $key => $value) 
        {
            header($value);
        }
        if ($_ENV["DEBUG"])
            echo $out;
        echo $this->content;
    }

    public static function redirect(string $url) : Response
    {
        $res = new Response();
        return $res->addHeader('Location: ' . $url);
    }

    public static function notFound(array $arr = []) : Response
    {
        return Viewer::notFoundPage()->view($arr)
                ->status(404);
    }
}
