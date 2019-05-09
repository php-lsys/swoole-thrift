<?php
namespace LSYS\Swoole\Thrift\Server\SwooleObserver;
use LSYS\Swoole\Thrift\Server\SwooleSubject;
use LSYS\Swoole\Thrift\Server\TSwooleFramedTransport;
use Thrift\Exception\TException;
use Thrift\Exception\TApplicationException;
use Thrift\Type\TMessageType;
class ReceiveObserver implements \SplObserver
{
    protected $subject;
    /**
	 * @param SwooleSubject $subject 
	 * @return void 
	 */
    public function update($subject)
    {
        $this->subject=$subject;
        call_user_func_array([$this,'onReceive'], $subject->event()->eventArgs());
    }
    protected function onReceive($serv, $fd, $from_id, $data)
    {
        $transport=new TSwooleFramedTransport();
        $transport->setHandle($fd);
        $transport->buffer = $data;
        $transport->server = $serv;
        $inputProtocol = $this->subject->event()->swooleServer()->inputProtocolFactory()->getProtocol($transport);
        $outputProtocol =$this->subject->event()->swooleServer()->outputProtocolFactory()->getProtocol($transport);
        $processor=$this->subject->event()->swooleServer()->processor();
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
