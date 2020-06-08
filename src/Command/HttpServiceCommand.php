<?php
/**
 * 启动文件
 * Created by PhpStorm.
 * User: zack
 * Date: 2020/5/2
 * Time: 14:50
 */

namespace Zackx\LumenSwoole\Command;

use App\Services\SwooleHttpService;
use Illuminate\Console\Command;
use Illuminate\Http\Request as BaseRequest;
use Zackx\LumenSwoole\Coroutine\Storage;
use Zackx\LumenSwoole\Server\PidManager;
use Swoole\Process;
use Zackx\LumenSwoole\Transformers\Request as TransformersRequest;

class HttpServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle swoole http server with start | restart | reload | stop | status';


    public function handle()
    {
        $action = $this->argument('action');
        switch ($action) {
            case 'start':
                $this->start();
                break;
            case 'restart':
                $this->restart();
                break;
            case 'reload':
                $this->reload();
                break;
            case 'stop':
                $this->stop();
                break;
            case 'status':
                $this->status();
                break;
            default:
                $this->error('Please type correct action . start | restart | stop | reload | status');
        }
    }

    /**
     * Notes:start
     * User: zack
     * Date: 2020/5/4
     */
    protected function start()
    {
        if ($this->isRunning()) {
            $this->error('swoole http server is already running');
            exit(1);
        }
        $this->info('starting swoole http server...');
        $this->laravel->make('swoole.http')->run();
    }

    /**
     * Notes:restart swoole
     * User: zack
     * Date: 2020/5/4
     */
    protected function restart()
    {
        $this->info('stopping swoole http server...');
        $this->stop();
        $this->start();
    }

    /**
     * Notes:reload
     * User: zack
     * Date: 2020/5/4
     */
    protected function reload()
    {
        if (!$this->isRunning()) {
            $this->error('swoole http server is not running');
            exit(1);
        }
        $this->info('reloading...');
        $this->killProcess(SIGUSR1);
        $this->info('done');
    }

    /**
     * Notes:stop swoole
     * User: zack
     * Date: 2020/5/4
     * @return bool
     */
    protected function stop()
    {
        $pid = $this->laravel->make(PidManager::class)->getPid();
        if (!$pid) {
            $this->error('swoole http server is not running');
            return false;
        }
        if (!$this->killProcess(SIGTERM, 15)) {
            $this->info('swoole closed successful!');
        } else {
            $this->info('swoole closed fail!');
            exit(1);
        }
    }

    /**
     * Notes:
     * User: zack
     * Date: 2020/5/4
     */
    protected function status()
    {
        $status = $this->isRunning();
        if ($status) {
            $this->info('swoole http server is running.');
        } else {
            $this->error('swoole http server is not running!');
        }
    }

    /**
     * Notes:kill swoole
     * @param $sig
     * @param int $wait
     * User: zack
     * Date: 2020/5/4
     * @return bool
     */
    protected function killProcess($sig, $wait = 0)
    {
        $pid = $this->laravel->make(PidManager::class)->getPid();
        @Process::kill(
            $pid,
            $sig
        );
        if ($wait) {
            $start = time();
            do {
                if (! $this->isRunning()) {
                    break;
                }

                usleep(10000);
            } while (time() < $start + $wait);
        }

        return $this->isRunning();
    }

    /**
     * Notes:woole process is running.
     * User: zack
     * Date: 2020/5/4
     * @return bool
     */
    public function isRunning()
    {
        $pid = $this->laravel->make(PidManager::class)->getPid();

        if (empty($pid)) {
            return false;
        }

        return $pid && Process::kill((int) $pid, 0);
    }
}
