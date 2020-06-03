<?php
/**
 * 注册
 * Created by PhpStorm.
 * User: zack
 * Date: 2020/5/2
 * Time: 14:50
 */

namespace Zackx\LumenSwoole;

use function foo\func;
use Illuminate\Support\ServiceProvider;
use Zackx\LumenSwoole\Command\HttpServiceCommand;
use Zackx\LumenSwoole\Coroutine\Storage;
use Zackx\LumenSwoole\Server\PidManager;
use Zackx\LumenSwoole\Server\SwooleHttpService;
use Illuminate\Http\Request as BaseRequest;
use Zackx\LumenSwoole\Transformers\Request as TransformersRequest;

class SwooleHttpProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            HttpServiceCommand::class,
        ]);
        $this->loadConfigs();
        $this->loadSingleton();
    }


    /**
     * Notes:Load configurations.
     * User: zack
     * Date: 2020/5/3
     */
    protected function loadConfigs()
    {
        $this->app->configure('swoole_http');
        $this->mergeConfigFrom(
            __DIR__ . '/../config/swoole_http.php',
            'swoole_http'
        );
    }

    /**
     * Notes:load Singleton
     * User: zack
     * Date: 2020/5/3
     */
    protected function loadSingleton()
    {
        $this->app->singleton('swoole.http', function ($app) {
            return new SwooleHttpService($app);
        });
        $this->app->singleton(PidManager::class, function ($app) {
            $pidFile = $app->make('config')->get('swoole_http.pid_file');
            return new PidManager($pidFile);
        });
    }
}
