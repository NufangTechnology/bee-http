<?php
namespace Bee\Http;

/**
 * Request
 *
 * @package Ant\Http
 */
class Request extends \Phalcon\Http\Request
{
    /**
     * @var \Swoole\Http\Request
     */
    private $request;

    /**
     * 数据源
     *
     * @param \Swoole\Http\Request $request
     */
    public function withSource(\Swoole\Http\Request $request)
    {
        $_GET     = $request->get ?? [];
        $_POST    = $request->post ?? [];
        $_REQUEST = array_merge($_GET, $_POST);

        // $_SERVER
        $_SERVER = [];
        foreach ($request->server as $key => $value) {
            $_SERVER[strtoupper($key)] = $value;
        }
        foreach ($request->header as $key => $value) {
            $key = strtoupper(strtr($key, '-', '_'));
            $_SERVER[$key] = $value;
        }

        // route url
        if (empty($_GET['_url'])) {
            $_GET['_url'] = $_SERVER['REQUEST_URI'];
        }

        $this->request = $request;
    }

    /**
     * 获取原始数据
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->request->rawContent();
    }

    /**
     * 以json格式获取源数据
     *
     * @param bool $associative
     * @return array|bool|mixed|\stdClass
     */
    public function getJsonRawBody($associative = false)
    {
        $rawBody = $this->getRawBody();

        if (!is_string($rawBody)) {
            return false;
        }

        return json_decode($rawBody, $associative);
    }
}
