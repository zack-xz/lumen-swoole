使用说明
---
#### 已测试版本
* lumen5.7
* lumen5.8
* lumen6.0
* lumen7.0

#### 安装命令
composer require zackx/lumen-swoole

#### 使用
在`bootstrap/app.php`文件中加入
```
$app->register(\Zackx\LumenSwoole\SwooleHttpProvider::class);
```

#### 启动命令
```
php artisan swoole start
```