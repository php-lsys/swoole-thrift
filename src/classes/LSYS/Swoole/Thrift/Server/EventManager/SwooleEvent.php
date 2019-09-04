<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Swoole\Thrift\Server\EventManager;
use LSYS\EventManager\Event;
use LSYS\Swoole\Thrift\Server\TSwooleServer;
class SwooleEvent extends Event
{
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
    public function __construct(TSwooleServer $server,$name,$data) {
        $this->server=$server;
        parent::__construct($name,$data);
    }
    /**
     * @return \LSYS\Swoole\Thrift\Server\TSwooleServer
     */
    public function TSwooleServer(){
        return $this->server;
    }
}
