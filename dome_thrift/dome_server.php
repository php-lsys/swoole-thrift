<?php
use DomeInformation\DomeNewsProcessor;
use Thrift\Factory\TJSONProtocolFactory;
use LSYS\Loger\Handler\Stdout;
use DomeInformation\DomeNewsIf;
require __DIR__."/boot.php";

LSYS\Loger\DI::get()->loger()->addHandler(new Stdout());

//这个一般放到你框架代码,具体你用什么框架,可自行决定
class DomeHandlerProxy{
    protected $_if;
    public function __construct(DomeNewsIf $newif) {
        $this->_if=$newif;
    }
    public function __call($method,$args){
        //这实现服务拦截校验和拦截
        if(isset($args[0])&&is_object($args[0])){//一般请求参数定义为结构,所以这是对象,如有统一的验证字段
            print_r($args);
            //检测...
        }
        return call_user_func_array([$this->_if,$method], $args);
    }
}


$handler = new \Services\Information\NewsHandler();
$processor = new DomeNewsProcessor(new DomeHandlerProxy($handler));


$swoole = new \Swoole\Server('0.0.0.0', 8099);

//协议一定要跟客户端请求对上
$protocol = new TJSONProtocolFactory();
// 二进制 TBinaryProtocolFactory

$server = new LSYS\Swoole\Thrift\Server\TSwooleServer($processor,$swoole,$protocol, $protocol);
$server->config([
    'worker_num'=>2
])->serve();

