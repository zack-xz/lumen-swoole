<?php

/**
 * swoole http 配置文件
 */
return [
    'environment' => env('SWOOLE_HTTP_ENVIRONMENT', 'dev'),    //环境
    'host' => env('SWOOLE_HTTP_HOST', '0.0.0.0'),    //可访问 ip 所有 用 0.0.0.0  有nginx 代理尽量使用127.0.0.1 仅本机可访问
    'port' => env('SWOOLE_HTTP_PORT', '9501'),           //占用端口
    'daemonize' => env('SWOOLE_HTTP_DAEMONIZE', '0'),         //是否守护进程启动
    'dispatch_mode' => env('SWOOLE_HTTP_DISPATCH_MODE', '3'),       //数据包分发策略  https://wiki.swoole.com/wiki/page/277.html
    'reactor_num' => env('SWOOLE_HTTP_REACTOR_NUM', swoole_cpu_num()),         //reactor 线程数
    'worker_num' => env('SWOOLE_HTTP_WORKER_NUM', ceil(swoole_cpu_num() * 1.5)),         //worker 进程数
    'max_request' => env('SWOOLE_HTTP_MAX_REQUEST', '1000'),    //worker进程的最大任务数  解决由于程序编码不规范导致的PHP进程内存泄露问题。PHP应用程序有缓慢的内存泄漏，但无法定位到具体原因、无法解决，可以通过设置max_request临时解决。
    'log_file' =>  env('SWOOLE_HTTP_LOG_FILE', storage_path('logs/swoole.log')),    //日志文件
    'log_level' => env('SWOOLE_HTTP_LOG_LEVEL', 5),      //记录日志等级 低于log_level设置的日志信息不会抛出   https://wiki.swoole.com/wiki/page/538.html
    'pid_file' => env('SWOOLE_HTTP_PID_FILE', storage_path('logs/swoole.pid')),      //pid 进程文件
    'open_tcp_nodelay' => 1,     //启用open_tcp_nodelay，开启后TCP连接发送数据时会关闭Nagle合并算法，立即发往客户端连接。在某些场景下，如http服务器，可以提升响应速度
    'package_max_length' => env('SWOOLE_HTTP_PACKAGE_MAX_LENGTH', 20) * 1024 * 1024,    //最大数据包尺寸
    'buffer_output_size' => env('SWOOLE_HTTP_BUFFER_OUTPUT_SIZE', 32) * 1024 * 1024,      //发送输出缓存区内存最大尺寸
    'socket_buffer_size' => env('SWOOLE_HTTP_SOCKET_BUFFER_SIZE', 128) * 1024 * 1024,      //客户端连接的缓存区最大长度
    'request_slowlog_timeout' => 2, //2秒
    'request_slowlog_file' => storage_path('logs/man.log'),
    'trace_event_worker' => true, //跟踪 Task 和 Worker 进程
];
