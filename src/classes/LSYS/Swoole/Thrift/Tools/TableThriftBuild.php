<?php
namespace LSYS\Swoole\Thrift\Tools;
abstract class TableThriftBuild{
    private $_dir;
    private $_namespace;
    public function __construct($dir){
        $this->setSaveDir($dir);
    }
    public function tablePrefix(){
        return "";
    }
    public function message($table,$msg){}
    /**
     * 设置保存目录
     * @param string $dir
     * @return \LSYS\Model\Tools\TraitBuild
     */
    public function setSaveDir($dir){
        $this->_dir=$dir;
        return $this;
    }
    /**
     * 设置命名空间
     * @param string $namespace
     * @return \LSYS\Model\Tools\TraitBuild
     */
    public function setNamespace(array $namespace){
        $this->_namespace=$namespace;
        return $this;
    }
    /**
     * 根据表名生成model名
     * @param string $table
     * @return string
     */
    protected function fileName(){
        return "table";
    }
    /**
     * Thrift名生成
     * @param string $table
     * @return string
     */
    protected function ThriftName($table){
        return str_replace(" ",'',ucwords(str_replace("_",' ', $table)));
    }
    /**
     * 字段是否可选
     * @param string $column
     * @param bool $is_null
     * @return bool
     */
    protected function ColumnOptional($column,$is_null){
        return $is_null;
    }
    /**
     * 字段名
     * @param string $column
     * @return string
     */
    protected function ColumnName($column){
        return $column;
    }
    /**
     * 字段类型
     * @param string $column
     * @param string $type
     * @return string
     */
    protected function ColumnType($column,$type){
        $type=strtolower($type);
//         bool: A boolean value (true or false)
//         byte: An 8-bit signed integer
//         i16: A 16-bit signed integer
//         i32: A 32-bit signed integer
//         i64: A 64-bit signed integer
//         double: A 64-bit floating point number
//         string: A text string encoded using UTF-8 encoding
        if(strpos($type, "tinyint")!==false){
            return "byte";
        }elseif(strpos($type, "bool")!==false){
            return "bool";
        }elseif(strpos($type, "smallint")!==false){
            return "i16";
        }elseif(strpos($type, "mediumint")!==false){
            return "i32";
        }elseif(strpos($type, "int")!==false){
            return "i32";
        }elseif(strpos($type, "bigint")!==false){
            return "i64";
        }elseif(strpos($type, "float")!==false){
            return "double";
        }elseif(strpos($type, "double")!==false){
            return "double";
        }elseif(strpos($type, "decimal")!==false){
            return "double";
        }elseif(strpos($type, "timestamp")!==false){
            return "i32";
        }elseif(strpos($type, "year")!==false){
            return "i16";
        }
        return "string";
    }
    private function createColumn($columnset) {
        $column=[];
        $i=1;
        foreach ($columnset as $v){
            list($name,$type,$is_null,$commect)=$v;
            //optional string token
            $need=$this->ColumnOptional($name,$is_null)?"optional":"required";
            $type=$this->ColumnType($name,$type);
            $name=$this->ColumnName($name);
            $commit=trim(strval($commect),"\r\n\t");
            if(!empty($commit)){
                $commit=str_replace(["\n","\r"],' ', $commit);
                $commit=str_replace(",",' ', $commit);
                /**
                 * 注释格式
                 */
                $commit="/**\n\t * {$commit}\n\t */\n\t";
            }
            $column[]="{$commit}{$i}:{$need} {$type} {$name}";
            $i++;
        }
        return "\t".implode("\n\t", $column);
    }
    /**
     * 创建代码
     * @throws \Exception
     */
    public function build(){
        $class_dir=rtrim($this->_dir,"\/")."/";
        if(!is_dir($class_dir)){
            throw new \Exception(strtr("dir [:dir] does not exist.", array(":dir"=>$class_dir)));
        }
        $namespace=$this->_namespace;
        $tp=$this->tablePrefix();
        $tables=$this->listTables();
        $body=[];
        
        $names=[];
        foreach ($namespace as $v){
            $names[]="namespace {$v}";
        }
        $body[]=implode("\n", $names);
        
        foreach ($tables as $table){
            if (!empty($tp)){
                if(strpos($table, $tp)!==0){
                    $this->message($table,"not match");
                    continue;
                }
                $table_name = substr($table, strlen($tp));
            }else $table_name = $table;
            
            $this->message($table," create start");
            $columnset=$this->listColumns($table);
            $column=$this->createColumn($columnset);
            $name=$this->ThriftName($table_name);
            $doc="struct {$name}{\n{$column}\n}";
            
            $body[]="//table: ".$table_name."\n\n".$doc;
            $this->message($table," create bulid");
        }
        $filename=$this->fileName();
        
        file_put_contents($class_dir.$filename.".thrift",implode("\n\n\n", $body) );
        $this->message($table," create success");
    }
    /**
     * 表列表
     * @return string[]
     */
    abstract function listTables();
    /**
     * 返回为:字段名 类型 是否为空 注释
     * @return [$name,$type,$is_null,$commect][]
     */
    abstract function listColumns($table);
}