<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Swoole\Thrift\Server;
use Thrift\Factory\TProtocolFactory;
use Thrift\Protocol\TBinaryProtocolAccelerated;
class TBinaryProtocolAcceleratedFactory implements TProtocolFactory
{
    private $strictRead_ = false;
    private $strictWrite_ = false;
    public function __construct(bool $strictRead=false, bool $strictWrite=false)
    {
        $this->strictRead_ = $strictRead;
        $this->strictWrite_ = $strictWrite;
    }
    public function getProtocol($trans)
    {
        return new TBinaryProtocolAccelerated($trans, $this->strictRead_, $this->strictWrite_);
    }
}