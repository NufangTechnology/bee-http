<?php
namespace Bee\Http\Response;

use Swoole\Http\Response;

class Headers extends \Phalcon\Http\Response\Headers
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $_headers = ['Server' => 'bee-server-1.0.0'];

    /**
     * Headers constructor.
     *
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * 发送header信息
     *
     * @return bool
     */
    public function send()
    {
        foreach ($this->_headers as $header => $value) {
            if (empty($value)) {
                $_header = explode(':', $header);
                $this->response->header($_header[0], $_header[1]);
            } else {
                $this->response->header($header, $value);
            }
        }

        return true;
    }
}