<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Swoole\Thrift;
use LSYS\Config;
use Thrift\Transport\TSocket;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TTransport;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Protocol\TProtocol;
class ClientProxy{
    /**
     * 根据配置创建对应的客户端代理辅助方法
     * @param string|callable $client
     * @param Config $config
     * @return static
     */
    public static function create($client,Config $config) {
        $config=$config->asArray()+array(
            'socket'=>TSocket::class,
            'args'=>array(
                '127.0.0.1','8099'
            ),
        );
        $socket=(new \ReflectionClass($config['socket']))->newInstanceArgs($config['args']);
        $transport=new TFramedTransport($socket);
        return new static($config,$transport, $client);
    }
    /**
	 * 使用到的配置详细
     * @var array
     */
    protected $config;
    protected $client;
    protected $protocol;
    /**
     * 客户端代理类
     * 代理转发目的:实现在请求前后的一些统一的辅助操作
     * @param Config $config 当前客户端使用的配置文件,一般继承重写会用到
     * @param TTransport $transport 传输对象
     * @param string|callable $client_creater 客户端创建回调行数或客户端类名
     * @param callable $protocol 协议创建回调函数 返回协议对象
     */
    public function __construct(array $config,TTransport $transport,$client_creater,callable $protocol=null) {
        $this->config=$config;
        if(is_callable($protocol)){
            $protocol=call_user_func($transport);
            assert($protocol instanceof TProtocol);
        }else{
            $protocol=new TBinaryProtocolAccelerated($transport);
        }
        if (is_callable($client_creater)) {
            $this->client=call_user_func($client_creater,$protocol);
        }else{
            $this->client=(new \ReflectionClass($client_creater))->newInstance($protocol);
        }
        $this->protocol=$protocol;
    }
    /**
     * 得到使用的TProtocol
     * @return \Thrift\Protocol\TProtocol
     */
    public function getProtocol(){
        return $this->protocol;
    }
    /**
     * 得到实际的客户实例
     * 不建议直接使用,仅用作实例判断
     * @return object
     */
    public function getClient(){
        return $this->client;
    }
    /**
     * 使用完后调用清理,除非局部变量
     * 否则请手动手动清理
     * 清理后本对象将不在可用
     */
    public function release():void{
		if(is_object($this->protocol))@$this->protocol->getTransport()->close();
        $this->protocol=null;
        $this->client=null;
    }
    /**
     * 代理实现
     * 如果要自动附带参数,请重写此方法
     * @param string $method
     * @param array $param_arr
     * @return mixed
     */
    public function __call(string $method,$param_arr) {
        if (!$this->protocol->getTransport()->isOpen()) {
            $this->protocol->getTransport()->open();
        }
        return call_user_func_array([$this->client,$method], $param_arr);
    }
    /**
     * 局部变量自动释放连接用
     */
    public function __destruct() {
        $this->release();
    }
}