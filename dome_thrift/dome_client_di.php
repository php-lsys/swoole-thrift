<?php
use DomeInformation\DomeNewsClient;
use LSYS\Swoole\Thrift\ClientProxy;
/**
 * @method DomeNewsClient|ClientProxy news(ClientProxy $client=null,$config=null)
 */
class MyClient extends \LSYS\DI{
    /**
     *
     * @var string default config
     */
    public static $config = 'thrift.client_pool';
    /**
     * @return static
     */
    public static function get(){
        $di=parent::get();
        !isset($di->news)&&$di->news(new \LSYS\DI\MethodCallback(ClientProxy::diMethod(self::$config,DomeNewsClient::class)));
        return $di;
    }
}



go(function(){
    $client=MyClient::get()->product();
    $recv1 = $client->test("dddd111");
    print_r($recv1);
    //主动释放
    $client->release();
});
    
    