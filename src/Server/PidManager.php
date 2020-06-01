<?php
/**
 * Notes: pid管理
 * User: zack
 * version: 2.0
 * Date: 2020/5/3
 * Time: 10:00
 */

namespace Zackx\LumenSwoole\Server;

class PidManager
{
    protected $pidFile;

    public function __construct(string $pidFile)
    {
        $this->pidFile = $pidFile;
    }

    /**
     * Notes: get swoole master pid
     * User: zack
     * Date: 2020/5/3
     * @return bool
     */
    public function getPid()
    {
        return file_exists($this->pidFile) ?
            intval(file_get_contents($this->pidFile)) :
            0;
    }

    /**
     * Notes:Delete pid file
     * User: zack
     * Date: 2020/5/3
     * @return bool
     */
    public function delete(): bool
    {
        if (is_writable($this->pidFile)) {
            return unlink($this->pidFile);
        }

        return false;
    }
}
