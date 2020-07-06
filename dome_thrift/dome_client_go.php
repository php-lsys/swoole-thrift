<?php
use DomeInformation\DomeNewsClient;
use Thrift\Protocol\TJSONProtocol;
use LSYS\Swoole\Thrift\Server\TSwooleSocket;
use Thrift\Transport\TFramedTransport;
use Thrift\Protocol\TBinaryProtocol;
use LSYS\Swoole\Thrift\Server\TSwooleSocketPool;
require __DIR__."/boot.php";

go(function () {
    $socket = new TSwooleSocket("swoole.clienl_dome");
    $transport = new TFramedTransport($socket);
    $transport->open();
    //协议要跟服务器对上
    $protocol = new TJSONProtocol($transport);
    $protocol = new TBinaryProtocol($transport);
    $client = new DomeNewsClient($protocol);
    $res=$client->test("fdasdfaddd");
    $transport->close();
    var_dump($res);
});

go(function () {
    $socket = new TSwooleSocketPool("swoole.client_pool","app");
    $transport = new TFramedTransport($socket);
    $transport->open();
    //协议要跟服务器对上
    $protocol = new TJSONProtocol($transport);
    $protocol = new TBinaryProtocol($transport);
    $client = new DomeNewsClient($protocol);
    $res=$client->test("fdasdfaddd");
    $transport->close();
    var_dump($res);
});
        