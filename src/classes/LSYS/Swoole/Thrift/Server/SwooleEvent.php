<?php
namespace LSYS\Swoole\Thrift\Server;
use LSYS\EventManager\Event;
class SwooleEvent extends Event
{
    //SWOOLE 事件列表 $swoole_event 变量得取值
    const Start="Start";
    const Shutdown="Shutdown";
    const WorkerStart="WorkerStart";
    const WorkerStop="WorkerStop";
    const WorkerExit="WorkerExit";
    const Connect="Connect";
    const Receive="Receive";
    const Packet="Packet";
    const Close="Close";
    const Task="Task";
    const Finish="Finish";
    const PipeMessage="PipeMessage";
    const WorkerError="WorkerError";
    const ManagerStart="ManagerStart";
    const ManagerStop="ManagerStop";
    protected $server;
    protected $swoole_event;
    protected $args;
    public function __construct(TSwooleServer $server,$swoole_event,$args) {
        $this->server=$server;
        $this->swoole_event=$swoole_event;
        $this->args=$args;
    }
    public function swooleServer(){
        return $this->server;
    }
    public function swooleEvent(){
        return $this->swoole_event;
    }
    /**
     * @return array
     */
    public function eventArgs(){
        return is_array($this->args)?$this->args:[];
    }
}
