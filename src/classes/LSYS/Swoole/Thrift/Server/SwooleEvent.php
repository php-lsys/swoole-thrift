<?php
namespace LSYS\Swoole\Thrift\Server;
use LSYS\EventManager\Event;
class SwooleEvent extends Event
{
    const Receive="receive";
    protected $server;
    protected $swoole_event;
    protected $args;
    public function __construct(TSwooleServer $server,$swoole_event,$args) {
        $this->server=$server;
        $this->swoole_event=$swoole_event;
        $this->args;
    }
    public function swooleServer(){
        return $this->server;
    }
    public function swooleEevent(){
        return $this->swoole_event;
    }
    public function eventArgs(){
        return $this->args;
    }
    public function exec(callable $callback) {
        return call_user_func_array($callback, $this->args);
    }
}
