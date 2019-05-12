<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Swoole\Thrift\Server;
use LSYS\EventManager\Subject;
use LSYS\EventManager\Event;
/**
 * @method SwooleEvent event();
 */
class SwooleSubject extends Subject{
    protected $swoole_event;
    public function __construct($swoole_event){
        parent::__construct(SwooleEvent::class);
        $this->swoole_event=$swoole_event;
    }
    public function isMatch(Event $event){
        return parent::isMatch($event)&&$event->swooleEvent()==$this->swooleEvent();
    }
    public function swooleEvent() {
        return $this->swoole_event;
    }
}