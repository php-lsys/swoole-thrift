<?php
namespace Services\Information;
use Information\AdParam;
use Information\NewsIf;
class NewsHandler implements NewsIf {
    public function test($test){
        return "return:".$test;
    }
    public function ad_lists(AdParam $param)
    {
        $data=[
            new \Information\AdItem(
                [
                    'title'=>"fasdfa",
                    'img'=>"img",
                ]
                ),
        ];
        return new \Information\ResultAd(array(
            'Status'=>true,
            'Data'=>$data,
            'Message'=>"message",
            'Page'=>new \Shared\ResultPage(array(
                'page'=>1
            )),
        ));
    }
    public function test2($test)
    {
        //使用连接池查询数据库
        $mysql=\LSYS\Swoole\Coroutine\MySQLPool\DI::get()->swoole_mysql_pool();
        $connect=$mysql->pop();
        $res=$mysql->query($connect, function()use($connect,$test){
            $test=addslashes($test);
            return $connect->mysql()->query("select sleep(1) as t,'{$test}' as b");
        });
        $mysql->push($connect);
        return $res[0]['b'];
    }
    public function test1($test)
    {   //直接查询数据库,不建议此方式
        $config=
        [
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => '110',
            'fetch_mode' 		=> 1,
            'database' => 'test',
        ];
        $mysql=new  \Swoole\Coroutine\MySQL();
        $mysql->connect($config);
        $test=addslashes($test);
        $res=$mysql->query("select sleep(1) as t,'{$test}' as b");
        return $res[0]['b'];
    }
}