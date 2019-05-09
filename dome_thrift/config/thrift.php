<?php
use Thrift\Transport\TSocket;
use LSYS\Swoole\Thrift\Server\TSwooleSocket;
use LSYS\Swoole\Thrift\Server\TSwooleSocketPool;
return array(
    //普通方式连接配置,服务器环境中,此方式存在问题.不建议使用此在服务器中使用
    //这个仅在 FPM 环境中使用
    "client"=>array(
        'socket'=>TSocket::class,
        'args'=>array(
            '127.0.0.1','8099'
        ),
    ),
    //协程客户端连接,当使用在SWOOLE服务器环境中时,请使用此方式
    "client_"=>array(
        'socket'=>TSwooleSocket::class,
        'args'=>array(
            'swoole.clienl_dome'//这个定义在 config/swoole.php 中定义
        ),
    ),
    //协程客户端连接池方式连接
    "client_pool"=>array(
        'socket'=>TSwooleSocketPool::class,
        'args'=>array(
            'swoole.client_pool','app*'//这个定义在 config/swoole.php 中定义
        ),
    ),
);
