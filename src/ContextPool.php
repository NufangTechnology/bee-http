<?php
namespace Bee\Http;

class ContextPool
{
    /**
     * @var Context[]
     */
    public static $pool = [];

    /**
     * 可以任意协程获取到context
     *
     * @return Context|null
     */
    public static function getContext()
    {
        $id = Coroutine::getPid();
        if (isset(self::$pool[$id])) {
            return self::$pool[$id];
        }

        return null;
    }

    /**
     * @desc 清除context
     */
    public static function clear()
    {
        $id = Coroutine::getPid();
        if (isset(self::$pool[$id])) {
            unset(self::$pool[$id]);
        }
    }

    /**
     * @param $context
     * @desc 设置context
     */
    public static function set($context)
    {
        $id = Coroutine::getPid();
        self::$pool[$id] = $context;
    }
}
