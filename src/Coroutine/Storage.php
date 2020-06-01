<?php
declare(strict_types=1);
/**
 * Notes: 协程存储器， 在单个请求中共用
 * User: zack
 * version: 2.0
 * Date: 2020/5/3
 * Time: 10:00
 */

namespace Zackx\LumenSwoole\Coroutine;

class Storage
{
    private static $instance = [];

    private static $globalVariable = [];

    /**
     * Notes:Storage instance
     * @param $key
     * @param $instance
     * User: zack
     * Date: 2020/5/3
     */
    public static function setInstance($key, $instance)
    {
        self::$instance[get_coroutine_id()][$key] = $instance;
    }

    /**
     * Notes:get instance
     * @param $key
     * User: zack
     * Date: 2020/5/3
     * @return |null
     */
    public static function getInstance($key)
    {
        return isset(self::$instance[get_coroutine_id()][$key]) ?
            self::$instance[get_coroutine_id()][$key] :
            null;
    }

    /**
     * Notes:clean up coroutine storage
     * User: zack
     * Date: 2020/5/3
     */
    public static function cleanUp()
    {
        self::$instance[get_coroutine_id()] = null;
        self::$globalVariable[get_coroutine_id()] = null;
    }
}