<?php
use Thrift\Transport\TSocket;
use LSYS\Swoole\Thrift\Server\TSwooleSocket;
use LSYS\Swoole\Thrift\Server\TSwooleSocketPool;
return array(
    //普通方式连接配置
    "client"=>array(
        'socket'=>TSocket::class,
        'args'=>array(
            '127.0.0.1','8099'
        ),
    ),
    //协程客户端连接
    "client_"=>array(
        'socket'=>TSwooleSocket::class,
        'args'=>array(
            'swoole.clienl_dome'
        ),
    ),
    //协程客户端连接池方式连接
    "client_pool"=>array(
        'socket'=>TSwooleSocketPool::class,
        'args'=>array(
            'swoole.client','app*'
        ),
    ),
);
