<?php
namespace LSYS\Swoole\Thrift;
use LSYS\Config;
use Thrift\Transport\TSocket;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TTransport;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Protocol\TProtocol;
class ClientProxy{
    /**
     * 创建LSYS\DI\MethodCallback的回调函数辅助方法
     * DI示例:new \LSYS\DI\MethodCallback(ClientProxy::diMethod(self::$config,ProductClient::class))
     * @param string $client 客户端类名
     * @param string $default_config_name 默认config名
     * @return object 返回对应客户端代理实例
     */
    public static function diMethod($default_config_name,$client){
        return function(ClientProxy $client_proxy=null,$config_name=null)use($client,$default_config_name){
            $config=\LSYS\Config\DI::get()->config($config_name?$config_name:$default_config_name);
            if ($client_proxy) {
                return new static($config,$client_proxy->getProtocol()->getTransport(),$client);
            }
            return static::create($client, $config);
        };
    }
    /**
     * 根据配置创建对应的客户端代理辅助方法
     * @param string $client
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
     * @param string $client_creater 客户端创建回调行数或客户端类名
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
    public function release(){
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
    public function __call($method,$param_arr) {
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