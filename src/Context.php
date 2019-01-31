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
    private $runtime = [];

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
        return $this->runtime[$key] ?? '';
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
     * @return false|string
     */
    public function __toString()
    {
        // 如果trace写到了二进制数据，舍弃
        $logs = $this->logs;
        foreach ($logs as $key => $log) {
            if (isset($log['trace']) && !json_encode($log['trace'])) {
                unset($logs[$key]['trace']);
            }
        }

        $data = json_encode(
            [
                'header'  => $this->request->getHeader(),
                'server'  => $this->request->getServer(),
                'get'     => $this->request->getQuery(),
                'body'    => $this->request->getJsonRawBody(),
                'runtime' => $this->runtime,
                'data'    => $this->data,
                'logs'    => $logs
            ],
            JSON_UNESCAPED_UNICODE,
            3
        );

        if (empty($data)) {
            $data = 'Context转换为string失败';
        }

        return $data;
    }
}
