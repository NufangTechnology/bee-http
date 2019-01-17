<?php
namespace Bee\Http;

/**
 * Request
 *
 * @package Ant\Http
 */
class Request
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
