<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Swoole\Thrift\Server;
use Thrift\Factory\TTransportFactory;
use Thrift\Factory\TProtocolFactory;
use LSYS\Swoole\Thrift\Server\SwooleObserver\ReceiveObserver;
/**
 * Simple implemtation of a Thrift server.
 *
 * @package thrift.server
 */
class TSwooleServer
{
    protected $config=array(
        'worker_num'            => 1,
        'dispatch_mode'         => 1, //1: 轮循, 3: 争抢
        'open_length_check'     => true, //打开包长检测
        'package_max_length'    => 8192000, //最大的请求包长度,8M
        'package_length_type'   => 'N', //长度的类型，参见PHP的pack函数
        'package_length_offset' => 0,   //第N个字节是包长度的值
        'package_body_offset'   => 4,   //从第几个字节计算长度
    );
    /**
     * Processor to handle clients
     */
    protected $processor_;
    /**
     * @var \Swoole\Server
     */
    protected $server_;
    /**
     * Input protocol factory
     *
     * @var TProtocolFactory
     */
    protected $inputProtocolFactory_;
    
    /**
     * Output protocol factory
     *
     * @var TProtocolFactory
     */
    protected $outputProtocolFactory_;
    /**
     * @var SwooleEventManager
     */
    protected $eventManager_;
    /**
     * Sets up all the factories, etc
     *
     * @param object $processor
     * @param \Swoole\Server $server
     * @param TTransportFactory $inputTransportFactory
     * @param TTransportFactory $outputTransportFactory
     * @param TProtocolFactory $inputProtocolFactory
     * @param TProtocolFactory $outputProtocolFactory
     * @return void
     */
    public function __construct($processor,
        \Swoole\Server $server,
        TProtocolFactory $inputProtocolFactory,
        TProtocolFactory $outputProtocolFactory,
        SwooleEventManager $event_manager=null) {
            $this->processor_ = $processor;
            $this->server_ = $server;
            $this->inputProtocolFactory_ = $inputProtocolFactory;
            $this->outputProtocolFactory_ = $outputProtocolFactory;
            if(is_null($event_manager)) $event_manager=new SwooleEventManager();
            $this->eventManager_=$event_manager;
            if(!in_array(SwooleEvent::Receive,$event_manager->swooleEvent())){
                $event_manager->attach((new SwooleSubject(SwooleEvent::Receive))->attach(new ReceiveObserver()));
            }
    }
    /**
     * get or set config
     * @param array $config
     * @return string[]|\LSYS\Swoole\Thrift\Server\TSwooleServer
     */
    public function config(array $config=null){
        if(is_null($config))return $this->config;
        $this->config=array_merge($this->config,$config);
        return $this;
    }
    /**
     * @return \LSYS\Swoole\Thrift\Server\SwooleEventManager
     */
    public function eventManager() {
        return $this->eventManager_;
    }
    /**
     * get swoole object
     * @return \Swoole\Server
     */
    public function swooleServer() {
        return $this->server_;
    }
    /**
     * @return \Thrift\Factory\TProtocolFactory
     */
    public function inputProtocolFactory() {
        return $this->inputProtocolFactory_;
    }
    /**
     * @return object
     */
    public function processor() {
        return $this->processor_;
    }
    /**
     * @return \Thrift\Factory\TProtocolFactory
     */
    public function outputProtocolFactory() {
        return $this->outputProtocolFactory_;
    }
    /**
     * Serves the server. This should never return
     * unless a problem permits it to do so or it
     * is interrupted intentionally
     */
    public function serve(){
        foreach ($this->eventManager_->swooleEvent() as $event) {
            $this->server_->on($event,function()use($event){
                $this->eventManager_->dispatch(new SwooleEvent($this,$event,func_get_args()));
            });
        }
        $this->server_->set($this->config+(array)$this->server_->setting);
        return $this->server_->start();
    }
    /**
     * Stops the server serving
     * @return void
     */
    public function stop(){
        $this->server_->shutdown();
    }
}
