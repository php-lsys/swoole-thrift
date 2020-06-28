<?php
use Thrift\ClassLoader\ThriftClassLoader;
error_reporting(E_ALL);
$autoload=require __DIR__."/../vendor/autoload.php";
$autoload->setPsr4("",[__DIR__."/src/"]);
LSYS\Config\File::dirs(array(
    __DIR__."/config",
));
$loader = new ThriftClassLoader();
$loader->registerDefinition('Information',  __DIR__.'/src/gen-php');
$loader->registerDefinition('Shared',  __DIR__.'/src/gen-php');
$loader->register();