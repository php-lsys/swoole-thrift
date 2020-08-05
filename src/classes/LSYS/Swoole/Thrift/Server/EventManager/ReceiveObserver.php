<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Swoole\Thrift\Server\EventManager;
use LSYS\Swoole\Thrift\Server\TSwooleFramedTransport;
use Thrift\Exception\TException;
use Thrift\Exception\TApplicationException;
use Thrift\Type\TMessageType;
use LSYS\EventManager\Event;
use LSYS\EventManager\EventObserver;
use LSYS\Swoole\Thrift\Server\TSwooleServer;
class ReceiveObserver implements EventObserver
{
    protected $swoole_server;
    public function __construct(TSwooleServer $swoole_server){
        $this->swoole_server=$swoole_server;
    }
    public function eventNotify(Event $event)
    {
        $data=$event->getData();
        if(!is_array($data))return ;
        call_user_func_array([$this,'onReceive'], $data);
    }
    public function eventName()
    {
        return SwooleEvent::Receive;
    }
    protected function onReceive($serv, $fd, $from_id, $data)
    {
        $transport=new TSwooleFramedTransport();
        $transport->setHandle($fd);
        $transport->buffer = $data;
        $transport->server = $serv;
        $inputProtocol = $this->swoole_server->inputProtocolFactory()->getProtocol($transport);
        $outputProtocol =$this->swoole_server->outputProtocolFactory()->getProtocol($transport);
        $processor=$this->swoole_server->processor();
        try {
            $processor->process($inputProtocol, $outputProtocol);
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
                if(method_exists($e, "getTraceAsString")&&!\LSYS\Core::envIs(\LSYS\Core::PRODUCT)){
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
