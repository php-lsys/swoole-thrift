<?php
namespace LSYS\Swoole\Thrift\Server;
use Thrift\Factory\TTransportFactory;
use Thrift\Factory\TProtocolFactory;
use Thrift\Exception\TException;
use Thrift\Exception\TApplicationException;
use Thrift\Type\TMessageType;
use Thrift\Type\TType;
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
        TProtocolFactory $outputProtocolFactory) {
            $this->processor_ = $processor;
            $this->server_ = $server;
            $this->inputProtocolFactory_ = $inputProtocolFactory;
            $this->outputProtocolFactory_ = $outputProtocolFactory;
    }
    function config(array $config){
        $this->config=array_merge($this->config,$config);
        return $this;
    }
    /**
     * Serves the server. This should never return
     * unless a problem permits it to do so or it
     * is interrupted intentionally
     */
    public function serve(){
        $this->server_->on('receive', [$this, 'onReceive']);
        $this->server_->set($this->config+(array)$this->server_->setting);
        return $this->server_->start();
    }
    /**
     * Stops the server serving
     *
     * @abstract
     * @return void
     */
    public function stop(){
        $this->server_->shutdown();
    }
    public function onReceive($serv, $fd, $from_id, $data)
    {
        $transport=new TSwooleFramedTransport();
        $transport->setHandle($fd);
        $transport->buffer = $data;
        $transport->server = $serv;
        $inputProtocol = $this->inputProtocolFactory_->getProtocol($transport);
        $outputProtocol = $this->outputProtocolFactory_->getProtocol($transport);
        try {
            $this->processor_->process($inputProtocol, $outputProtocol);
        } catch (\Exception $e) {
            $rseqid=0;
            $fname=null;
            $trace=$e->getTrace();
            if(is_array($trace)){
                array_pop($trace);
                $trace=array_pop($trace);
                if(strpos($trace['function'], 'process_')===0){
                    $fname=substr($trace['function'], 8);
                    $rseqid=array_shift($trace['args']);
                }
            }
            if(!$e instanceof TException||!method_exists($e, "write")){
                \LSYS\Loger\DI::get()->loger()->add(\LSYS\Loger::ERROR,$e);
                $message=$e->getMessage().":".$e->getCode();
                if(\LSYS\Core::$environment!=\LSYS\Core::PRODUCT&&method_exists($e, "getTraceAsString")){
                    $message.="\n".$e->getTraceAsString();//非线上环境 把堆栈输出,方便调试
                }
                $e = new TApplicationException($message, TApplicationException::UNKNOWN);
            }
            $outputProtocol->writeMessageBegin($fname, TMessageType::EXCEPTION, $rseqid);
            $e->write($outputProtocol);
            $outputProtocol->writeMessageEnd();
            $outputProtocol->getTransport()->flush();
        }
    }
}
