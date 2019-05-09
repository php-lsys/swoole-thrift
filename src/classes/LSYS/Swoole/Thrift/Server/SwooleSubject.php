<?php
namespace LSYS\Swoole\Thrift\Server;
use LSYS\EventManager\Subject;
/**
 * @method SwooleEvent event();
 */
class SwooleSubject extends Subject{
    protected $swoole_event;
    public function __construct($swoole_event){
        $this->storage= new \SplObjectStorage();
        $this->event_class=SwooleEvent::class;
        $this->swoole_event=$swoole_event;
    }
    public function isMatch(SwooleEvent $event){
        return parent::isMatch($event)&&$event->swooleEvent()==$this->swooleEvent();
    }
    public function swooleEvent() {
        return $this->swoole_event;
    }
}