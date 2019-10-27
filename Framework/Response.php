<?php

namespace Framework;

/**
 * class Response
 */
class Response
{
    private $header = [];
    private $content;

    /**
     * make a new Response with content $t
     *
     * @param string $t
     */
    public function __construct(string $t = "")
    {
        $this->content = $t;
    }

    /**
     * add a header to the Response
     *
     * @param string $str
     * @return Response
     */
    public function addHeader(string $str) : Response
    {
        array_push($this->header, $str);
        return $this;
    }

    /**
     * change the status code of the Response
     *
     * @param integer $code
     * @return Response
     */
    public function status(int $code) : Response
    {
        http_response_code($code);
        return $this;
    }

    /**
     * return the Reponse content if no argument given
     * or set the content
     *
     * @param string $c
     * @return void|string
     */
    public function content(string $c = null)
    {
        if ($c)
            $this->content = $c;
        else
            return $this->content;
    }

    /**
     * send the Response content. Not for User use
     *
     * @return void
     */
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

    /**
     * make a redirect Response
     *
     * @param string $url
     * @return Response
     */
    public static function redirect(string $url) : Response
    {
        $res = new Response();
        return $res->addHeader('Location: ' . $url);
    }

    /**
     * make a 404 not found Response
     *
     * @param array $arr
     * @return Response
     */
    public static function notFound(array $arr = []) : Response
    {
        return Viewer::notFoundPage()->view($arr)
                ->status(404);
    }

    /**
     * make a exception Response
     * default status 500
     *
     * @param array $arr
     * @return Response
     */
    public static function exception(array $arr = []) : Response
    {
        return Viewer::exceptionPage()->view($arr)
                ->status(500);
    }

    /**
     * make a json Response
     * if $data is not a string, the function will json_encode $data
     *
     * @param mixed $data
     * @return Response
     */
    public static function json($data) : Response
    {
        if (!is_string($data))
            $data = json_encode($data);
        $res = new Response($data);
        return $res->addHeader('Content-Type: application/json');
    }
}
