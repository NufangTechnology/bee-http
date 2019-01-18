<?php
namespace Bee\Http;

use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;

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
    private $data = [];

    /**
     * @var array
     */
    private $runtime = [];

    /**
     * Context
     *
     * @param SwooleHttpRequest $request
     * @param SwooleHttpResponse $response
     */
    public function __construct(SwooleHttpRequest $request, SwooleHttpResponse $response)
    {
        $this->request  = new Request;
        $this->response = new Response;

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
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function setRuntime(string $key, $value): void
    {
        $this->runtime[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getRuntime(string $key)
    {
        return $this->runtime[$key] ?? null;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
