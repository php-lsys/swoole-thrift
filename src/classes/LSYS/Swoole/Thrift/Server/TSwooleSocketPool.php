<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Swoole\Thrift\Server;
use Thrift\Exception\TTransportException;
use Thrift\Transport\TTransport;
use LSYS\Swoole\Coroutine\ClientPool;
/**
 * Sockets implementation of the TTransport interface.
 *
 * @package thrift.transport
 */
class TSwooleSocketPool extends TTransport
{
  /**
   * Handle to PHP socket
   *
   * @var ClientPool
   */
  protected $pool_ = null;
  
  protected $client_;
  protected $node_;
  
  protected $buf="";
  protected $write=0;
  /**
   * Debugging on?
   *
   * @var bool
   */
  protected $debug_ = false;

  /**
   * Debug handler
   *
   * @var mixed
   */
  protected $debugHandler_ = null;
  

  /**
   * Socket constructor
   *
   * @param string $config      
   * @param string    $node   
   * @param string $debugHandler Function to call for error logging
   */
  public function __construct(
        string $config,
        string $node,
        $debugHandler=null
  ){
        $this->pool_ = \LSYS\Swoole\Coroutine\DI::get()->swoole_client_pool($config);
        $this->node_=$node;
        $this->debugHandler_ = $debugHandler ? $debugHandler : 'error_log';
  }
  /**
   * @param resource $handle
   * @return void
   */
  public function setHandle($handle)
  {
    $this->handle_ = $handle;
  }
  /**
   * Sets debugging output on or off
   *
   * @param bool $debug
   */
  public function setDebug(bool $debug)
  {
    $this->debug_ = $debug;
  }
  /**
   * Tests whether this is open
   *
   * @return bool true if the socket is open
   */
  public function isOpen()
  {
      return is_object($this->client_);
  }

  /**
   * Connects the socket.
   */
  public function open()
  {
     $this->client_=$this->pool_->pop($this->node_);
  }

  /**
   * Closes the socket.
   */
  public function close()
  {
      if(is_object($this->client_)){
          $this->pool_->push($this->client_);
          $this->client_=null;
      }
  }
  /**
   * Read from the socket at most $len bytes.
   *
   * This method will not wait for all the requested data, it will return as
   * soon as any data is received.
   *
   * @param int $len Maximum number of bytes to read.
   * @return string Binary data
   */
  public function read($len)
  {
      
      if ($this->write>0) {
          $handle=$this->client_->swoole_client();
          $buf=$handle->recv();
          if(!$buf){
              $msg=$handle->errCode;
              $this->close();
              throw new TTransportException('TSocket: write fail:'.$msg);
          }
          $this->write--;
          $this->buf.=$buf;
      }
      $slen=\Thrift\Factory\TStringFuncFactory::create()->strlen($this->buf);
      if ($slen<$len) {
          $this->close();
          throw new TTransportException('TSocket['.$slen.'] read '.$len.' bytes failed.');
      }
      $data=\Thrift\Factory\TStringFuncFactory::create()->substr($this->buf, 0,$len);
      $this->buf=\Thrift\Factory\TStringFuncFactory::create()->substr($this->buf, $len);
      return $data;
  }

  /**
   * Write to the socket.
   *
   * @param string $buf The data to write
   */
  public function write($buf)
  {
      $handle=$this->client_->swoole_client();
      if($this->write==0){//前面没有未接收的请求,可以重试请求
          $buf=$this->pool_->query($this->client_,function()use($handle,$buf){
              return $handle->send($buf);
          });
      }else{
          if(!$handle->send($buf)){
              $this->close();
              $msg=$handle->errCode;
              throw new TTransportException('TSocket: write fail:'.$msg);
          }
      }
      $this->write++;
   }

  /**
   * Flush output to the socket.
   *
   * Since read(), readAll() and write() operate on the sockets directly,
   * this is a no-op
   *
   * If you wish to have flushable buffering behaviour, wrap this TSocket
   * in a TBufferedTransport.
   */
  public function flush()
  {
    // no-op
  }
  public function __destruct() {
      $this->close();
  }
}
