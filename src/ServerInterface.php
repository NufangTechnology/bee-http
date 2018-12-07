<?php
namespace Bee\Http;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

/**
 * Interface ServerInterface
 *
 * @package Bee\Http
 */
interface ServerInterface
{
    /**
     * Server启动在主进程的主线程回调此方法
     *
     * @param Server $server
     */
    public function onStart(Server $server);

    /**
     * Server正常结束时回调此方法
     *
     * @param Server $server
     */
    public function onShutdown(Server $server);

    /**
     * Worker进程/Task进程启动时回调此方法
     *
     * @param Server $server
     * @param integer $workerId
     */
    public function onWorkerStart(Server $server, $workerId);

    /**
     * worker进程终止时回调此方法
     *  - 在此函数中回收worker进程申请的各类资源
     *
     * @param Server $server
     * @param integer $workerId
     */
    public function onWorkerStop(Server $server, $workerId);

    /**
     * 异步重启特性
     *  - 旧的Worker进程在退出时，事件循环的每个周期结束时调用onWorkerExit通知Worker进程退出
     *  - 在onWorkerExit中尽可能地移除/关闭异步的Socket连接，
     *  - 最终底层检测到Reactor中事件监听的句柄数量为0时退出进程。
     *
     * @param Server $server
     * @param $workerId
     */
    public function onWorkerExit(Server $server, $workerId);

    /**
     * Http请求进来时回调此方法
     *
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response);

    /**
     * task异步回调处理任务时回调此方法
     *
     * @param Server $server
     * @param integer $taskId
     * @param integer $workerId
     * @param mixed $data
     */
    public function onTask(Server $server, $taskId, $workerId, $data);

    /**
     * worker进程都低的任务完成后回调此方法
     *
     * @param Server $server
     * @param integer $taskId
     * @param mixed $data
     */
    public function onFinish(Server $server, $taskId, $data);

    /**
     * worker进程异常时回调此方法
     *
     * @param Server $server
     * @param integer $workerId
     * @param integer $workerPid
     * @param integer $exitCode
     * @param integer $signal
     *
     * @return mixed
     */
    public function onWorkerError(Server $server, $workerId, $workerPid, $exitCode, $signal);
}
