<?php
namespace Bee\Http;

use Bee\Http\Response\Headers;
use Bee\Http\Response\Exception;

/**
 * Response
 *
 * @package Ant\Http
 */
class Response extends \Phalcon\Http\Response
{
    /**
     * @var \Swoole\Http\Response
     */
    protected $response;

    /**
     * @var Headers
     */
    protected $_headers;

    /**
     * Response constructor.
     *
     * @param null $content
     * @param null $code
     * @param null $status
     */
    public function __construct($content = null, $code = null, $status = null)
    {
    }

    /**
     * 数据源
     *
     * @param \Swoole\Http\Response $response
     */
    public function withSource(\Swoole\Http\Response $response)
    {
        $this->response = $response;
        $this->_headers = new Headers($response);

        // 更新发送状态
        $this->_sent    = false;
    }

    /**
     * 输出相应内容
     *
     * @throws Exception
     */
    public function send()
    {
        if ($this->_sent) {
            throw new Exception('Response was already sent');
        }

        // 发送头信息
        $this->_headers->send();
        // 发送相应内容
        $this->response->end($this->_content);

        // 更新发送状态
        $this->_sent = true;
    }
}
