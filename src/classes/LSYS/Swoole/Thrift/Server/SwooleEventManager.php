<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Swoole\Thrift\Server;
use LSYS\EventManager;
class SwooleEventManager extends EventManager
{
    public function swooleEvent() {
        $swoole_event=array();
        foreach ($this->storage as $v){
            assert($v instanceof SwooleSubject);
            $swoole_event[]=$v->swooleEvent();
        }
        return array_unique($swoole_event);
    }
}
