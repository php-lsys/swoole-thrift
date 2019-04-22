<?php
use Information\NewsClient;
use Thrift\Protocol\TJSONProtocol;
require __DIR__."/boot.php";

//客户端带连接池实现

go(function () {
    $client_pool=LSYS\Swoole\Coroutine\ClientPool\DI::get()->swoole_client_pool();
    $connect=$client_pool->pop("app1*");
    
    //协议要跟服务器对上
    $res=$client_pool->query($connect, function()use($connect){
        $protocol = new TJSONProtocol($connect->transport());
        $client = new NewsClient($protocol);
        $res=$client->test("fdasdfaddd");
        return $res;
    });
    var_dump($res);
    
    $client_pool->push($connect);
     
});

