<?php
namespace Bee\Http;

use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpReqponse;

/**
 * Class Context
 *
 * @package Bee\Http
 */
class Context
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var array
     */
    private $map = [];

    /**
     * Context
     *
     * @param SwooleHttpRequest $request
     * @param SwooleHttpReqponse $response
     */
    public function __construct(SwooleHttpRequest $request, SwooleHttpReqponse $response)
    {
        $this->request->withSource($request);
        $this->response->withSource($response);
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->map[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        if (isset($this->map[$key])) {
            return $this->map[$key];
        }

        return null;
    }
}
