<?php
/**
 * @Notes: swoole http
 * @User: zack
 * @version: 2.0
 * @Date: 202/5/3
 * @Time: 17:18
 */

namespace Zackx\LumenSwoole\Server;

use Illuminate\Http\Request as BaseRequest;
use Laravel\Lumen\Application;
use Zackx\LumenSwoole\Coroutine\Storage;
use Swoole\Http\Server;
use Zackx\LumenSwoole\Transformers\Request as TransformersRequest;
use Zackx\LumenSwoole\Transformers\Response;

class SwooleHttpService
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var array
     */
    protected $config;

    /**
     * SwooleHttpService constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app->make('config')->get('swoole_http');
    }

    /**
     * Notes:server
     * User: zack
     * Date: 2020/5/3
     */
    public function initServer()
    {
        $host = empty($this->config['host']) ? '127.0.0.1' : $this->config['host'];
        $port =empty($this->config['port']) ? '9501' : $this->config['port'];
        $this->server = new Server($host, $port);
    }

    /**
     * Notes:init action
     * User: zack
     * Date: 2020/5/3
     */
    public function initCallback()
    {
        $this->server->on('start', [$this, 'onStart']);    //swoole 主进程开启时
        $this->server->on('managerStart', [$this, 'onManagerStart']); //manage 进程生成
        $this->server->on('workerstart', array($this, 'onWorkerStart'));  //worker 进程生成
        $this->server->on('request', array($this, 'onRequest'));       //有链接进入
    }

    /**
     * Notes:
     * User: zack
     * Date: 2020/5/3
     */
    public function run()
    {
        $this->buildRequest();
        $this->initServer();
        $this->server->set($this->config);   //配置
        $this->initCallback();
        $this->server->start();    //开启swoole
    }

    /**
     * Notes:master
     * User: zack
     * Date: 2020/5/3
     */
    public function onStart()
    {
        if (PHP_OS == 'Linux') {
            $name = $this->config['environment'] . ' swoole master';
            swoole_set_process_name($name);
        }
    }

    /**
     * Notes:manager
     * User: zack
     * Date: 2020/5/3
     */
    public function onManagerStart()
    {
        if (PHP_OS == 'Linux') {
            $name = $this->config['environment'] . ' swoole manager';
            swoole_set_process_name($name);
        }
    }

    /**
     * Notes:worker
     * @param $serv
     * @param $worker_id
     * User: zack
     * Date: 2020/5/3
     */
    public function onWorkerStart($serv, $worker_id)
    {
        if (PHP_OS == 'Linux') {
            $name = $this->config['environment'] . ' swoole worker';
            swoole_set_process_name($name);
        }
    }

    /**
     * Notes:request
     * @param $request
     * @param $response
     * User: zack
     * Date: 2020/5/3
     */
    public function onRequest($request, $response)
    {
        storage_swoole_http_requset($request);
        storage_swoole_http_response($response);
        ob_start();
        $request = $this->app->make('request');
        $app = clone $this->app;
        $illuminateResponse = $app->dispatch(
            $request
        );
        $content = ob_get_contents();
        ob_end_clean();
        Storage::cleanUp();
        Response::handle($response, $illuminateResponse, $content);
    }


    public function buildRequest()
    {
        $this->app->bind('request', function ($app) {
            if (Storage::getInstance(TransformersRequest::class)) {
                return Storage::getInstance(TransformersRequest::class);
            }
            $request = TransformersRequest::convertRequest(get_swoole_http_requset());
            Storage::setInstance(TransformersRequest::class, $request);
            return $request;
        });
        $this->app->bind(BaseRequest::class, function ($app) {
            if (Storage::getInstance(TransformersRequest::class)) {
                return Storage::getInstance(TransformersRequest::class);
            }
            $request = TransformersRequest::convertRequest(get_swoole_http_requset());
            Storage::setInstance(TransformersRequest::class, $request);
            return $request;
        });
    }
}
