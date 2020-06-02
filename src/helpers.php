<?php
declare(strict_types=1);
/**
 * Notes: 帮助方法
 * User: zack
 * version: 2.0
 * Date: 2020/5/3
 * Time: 9:46
 */

if (! function_exists('get_coroutine_id')) {
    /**
     * get this coroutine_id
     * @return int
     */
    function get_coroutine_id()
    {
        return \Swoole\Coroutine::getCid();
    }
}


if (! function_exists('storage_swoole_http_requset')) {
    /**
     * storage swoole_http_request
     */
    function storage_swoole_http_requset($request)
    {
        \Zackx\LumenSwoole\Coroutine\Storage::setInstance('swoole_http_request', $request);
    }
}

if (! function_exists('get_swoole_http_requset')) {
    /**
     * get swoole_http_request
     * @return \swoole_http_request
     */
    function get_swoole_http_requset()
    {
        return \Zackx\LumenSwoole\Coroutine\Storage::getInstance('swoole_http_request');
    }
}

if (! function_exists('storage_swoole_http_response')) {
    /**
     * storage swoole_http_response
     */
    function storage_swoole_http_response($response)
    {
        \Zackx\LumenSwoole\Coroutine\Storage::setInstance('swoole_http_response', $response);
    }
}

if (! function_exists('get_swoole_http_response')) {
    /**
     * get swoole_http_response
     * @return \swoole_http_response
     */
    function get_swoole_http_response()
    {
        return \Zackx\LumenSwoole\Coroutine\Storage::getInstance('swoole_http_response');
    }
}

if (! function_exists('set_header')) {
    /**
     * set header
     */
    function set_header($key, $value)
    {
        if (\Swoole\Coroutine::getCid() < 0) {     //不在swoole 环境
            header($key . ':' . $value);
        } else {
            $response = get_swoole_http_response();
            $response->header($key, $value);
        }
    }
}