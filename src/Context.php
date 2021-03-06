<?php
namespace Bee\Http;

use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;

/**
 * HTTP 请求处理上下文
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
     * @var string
     */
    private $content = '';

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $logs = [];

    /**
     * @var bool
     */
    private $outputJson = true;

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
     * @param string $value
     * @return void
     */
    public function setContent(string $value = '')
    {
        $this->content = $value;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param $log
     */
    public function setLog($log)
    {
        $this->logs[] = $log;
    }

    /**
     * @return array
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isOutputJson(): bool
    {
        return $this->outputJson;
    }

    /**
     * @param bool $outputJson
     */
    public function setOutputJson(bool $outputJson): void
    {
        $this->outputJson = $outputJson;
    }

    /**
     * 输出对象信息
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'request_uri' => $this->request->getServer('request_uri'),
            'method'      => $this->request->getServer('request_method'),
            'headers'     => $this->request->getHeader(),
            'get'         => $this->request->getQuery(),
            'body'        => $this->request->getJsonRawBody(),
            'content'     => $this->content,
            'data'        => $this->data,
            'logs'        => $this->logs
        ];
    }
}
