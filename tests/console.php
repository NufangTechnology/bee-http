<?php
require __DIR__ . '/../vendor/autoload.php';

class HttpServer extends \Bee\Http\Server
{
    /**
     * Server启动在主进程的主线程回调此方法
     *
     * @param \Swoole\Http\Server $server
     */
    public function onStart(\Swoole\Http\Server $server)
    {
        swoole_set_process_name($this->name . ':reactor');
    }

    /**
     * Worker进程/Task进程启动时回调此方法
     *
     * @param \Swoole\Http\Server $server
     * @param integer $workerId
     */
    public function onWorkerStart(\Swoole\Http\Server $server, $workerId)
    {
        if ($server->taskworker) {
            swoole_set_process_name($this->name . ':task');
        } else {
            swoole_set_process_name($this->name . ':worker');
        }
    }

    /**
     * worker进程终止时回调此方法
     *  - 在此函数中回收worker进程申请的各类资源
     *
     * @param \Swoole\Http\Server $server
     * @param integer $workerId
     */
    public function onWorkerStop(\Swoole\Http\Server $server, $workerId)
    {
    }

    /**
     * Http请求进来时回调此方法
     *
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $response->end(json_encode($request->server));
    }

    /**
     * worker进程异常时回调此方法
     *
     * @param \Swoole\Http\Server $server
     * @param integer $workerId
     * @param integer $workerPid
     * @param integer $exitCode
     * @param integer $signal
     *
     * @return mixed
     */
    public function onWorkerError(\Swoole\Http\Server $server, $workerId, $workerPid, $exitCode, $signal)
    {
        // TODO: Implement onWorkerError() method.
    }
}

$httpServer = new HttpServer(
    [
        'name'   => 'bee-http',
        'host'   => '0.0.0.0',
        'port'   => 8000
    ]
);
//$httpServer->start();

switch ($argv[1]) {
    case 'start':
        $httpServer->start();
        break;

    case 'stop':
        $httpServer->stop();
        break;

    case 'restart':
        $httpServer->restart();
        break;

    case 'status':
        $httpServer->status();
        break;

    case 'shutdown':
        $httpServer->shutdown();
        break;
}
