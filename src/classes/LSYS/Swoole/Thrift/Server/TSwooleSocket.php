<?php
namespace LSYS\Swoole\Thrift\Server;
use Thrift\Exception\TException;
use Thrift\Exception\TTransportException;
use Thrift\Transport\TTransport;
use LSYS\Swoole\Coroutine\Client;
/**
 * Sockets implementation of the TTransport interface.
 *
 * @package thrift.transport
 */
class TSwooleSocket extends TTransport
{
    /**
     * Handle to PHP socket
     *
     * @var Client
     */
    protected $handle_;
    protected $buf="";
    protected $write=0;
    /**
     * Socket constructor
     *
     * @param string $host         Remote hostname
     * @param int    $port         Remote port
     * @param bool   $persist      Whether to use a persistent socket
     * @param string $debugHandler Function to call for error logging
     */
    public function __construct($config) {
        $client=\LSYS\Swoole\Coroutine\DI::get()->swoole_client($config);
        $this->handle_ = $client;
    }
    /**
     * Tests whether this is open
     *
     * @return bool true if the socket is open
     */
    public function isOpen()
    {
        return $this->handle_->isConnected();
    }
    /**
     * Connects the socket.
     */
    public function open()
    {
        if ($this->isOpen()) {
            throw new TTransportException('Socket already connected', TTransportException::ALREADY_OPEN);
        }
        
        try{
            $this->handle_->connectFromConfig();
        }catch (\Exception $e){
            throw new TException($e->getMessage(),$e->getCode());
        }
    }
    /**
     * Closes the socket.
     */
    public function close()
    {
        if(is_object($this->handle_))@$this->handle_->close();
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
            $buf=$this->handle_->recv();
            if(!$buf){
                $msg=$this->handle_->errCode;
                $config=$this->handle_->getConfig();
                $host=$config['host'];
                $port=$config['port'];
                throw new TTransportException('TSocket: write fail:'.$msg.$host.':'.$port);
            }
            $this->write--;
            $this->buf.=$buf;
        }
        $slen=\Thrift\Factory\TStringFuncFactory::create()->strlen($this->buf);
        if ($slen<$len) {
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
        if(!$this->handle_->send($buf)){
            $msg=$this->handle_->errCode;
            $config=$this->handle_->getConfig();
            $host=$config['host'];
            $port=$config['port'];
            throw new TTransportException('TSocket: write fail:'.$msg.$host.':'.$port);
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
}