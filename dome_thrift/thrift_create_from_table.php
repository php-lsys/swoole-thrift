<?php
require __DIR__."/boot.php";
class ThriftTableBuild extends \LSYS\Swoole\Thrift\Tools\TableThriftBuild{
    protected $_db;
    public function __construct(){
        $this->setSaveDir(__DIR__."/thrift")
        ->setNamespace(["php Table"])
        ;
        $this->_pool=\LSYS\Swoole\Coroutine\MySQLPool\DI::get()->swoole_mysql_pool();
        $this->_db=new \LSYS\Model\Database\Swoole\MYSQL($this->_pool);
    }
    public function listTables()
    {
        $sql='SHOW TABLES';
        $out=[];
        foreach ($this->_db->query($sql) as $value) {
            $out[]=array_shift($value);
        }
        return $out;
    }
    public function tablePrefix(){
        return $this->_pool->config()->get("table_prefix","");
    }
    public function message($table,$msg){
        echo $table.$msg."\n";
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Swoole\Thrift\Tools\TableThriftBuild::listColumns()
     */
    public function listColumns($table)
    {
        $columnset=$this->_db->listColumns($table);
        $out=[];
        foreach ($columnset->columnSet() as $value) {
            assert($value instanceof \LSYS\Entity\Column);
            //[$name,$type,$is_null,$commect]
            $out[]=[$value->name(),$value->getType(),$value->isAllowNull(),$value->comment()];
        }
        return $out;
    }
}
go(function () {
    (new ThriftTableBuild())->build();
});