<?php
use Thrift\Transport\TSocket;
use DomeInformation\DomeNewsClient;
use Thrift\Transport\TFramedTransport;
use Thrift\Protocol\TJSONProtocol;
use DomeInformation\DomeAdParam;
require __DIR__."/boot.php";


//TSocket -> fsockopen -> php stream  swoole 可协程化php的stream 所以TSocket可用
$socket = new TSocket("127.0.0.1", 8099);
$socket ->setRecvTimeout(10000);
$transport = new TFramedTransport($socket);

//协议要跟服务器对上
$protocol = new TJSONProtocol($transport);


$client = new DomeNewsClient($protocol);
$transport->open();

//同步方式进行交互
$recv = $client->test1("fdasdfaddd");
print_r($recv);
$recv = $client->test2("fdasdfaddd");
print_r($recv);
// $recv = $client->ad_lists(new AdParam());
// print_r($recv);

// //异步方式进行交互
// $client->send_test('data1');
// $client->send_test('data2');
// $recv = $client->recv_test();
// print_r($recv);
// $recv = $client->recv_test();
// print_r($recv);

$transport->close();

