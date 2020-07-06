<?php
require __DIR__."/boot.php";
/**
 * @method DomeNewsClient|ClientProxy news()
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
        !isset($di->news)&&$di->news(new \LSYS\DI\MethodCallback(function(){
            $config=\LSYS\Config\DI::get()->config(self::$config);
            return \LSYS\Swoole\Thrift\ClientProxy::create(DomeInformation\DomeNewsClient::class, $config);
        }));
        return $di;
    }
}



go(function(){
    $client=MyClient::get()->news();
    $recv1 = $client->test("dddd111");
    print_r($recv1);
    //主动释放
    $client->release();
});
    
    