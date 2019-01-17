<?php
namespace Bee\Http;

use Bee\Http\Response\Headers;
use Bee\Http\Response\Exception;

/**
 * Response
 *
 * @package Ant\Http
 */
class Response
{
    /**
     * @var \Swoole\Http\Response
     */
    protected $response;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var bool
     */
    protected $sent = false;

    /**
     * 数据源
     *
     * @param \Swoole\Http\Response $response
     */
    public function withSource(\Swoole\Http\Response $response)
    {
        $this->response = $response;
        $this->headers  = new Headers($response);

        // 更新发送状态
        $this->sent     = false;
    }

    /**
     * 输出相应内容
     *
     * @throws Exception
     */
    public function send()
    {
        if ($this->sent) {
            throw new Exception('Response was already sent');
        }

        // 发送头信息
        $this->headers->send();
        // 写入内容
        $this->response->write($this->content);
        // 发送相应内容
        $this->response->end();

        // 更新发送状态
        $this->sent = true;
    }
}
