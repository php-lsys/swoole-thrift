<?php
return array(
    //  mysql客户端配置示例
    "mysql"=>array(
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '110',
        'fetch_mode' 		=> 1,
        'database' => 'test',
     ),
     //mysql客户端连接池配置示例
    "mysql_pool"=>array(
        "try"=>true,//发送错误重试次数,设置为TRUE为不限制
        "sleep"=>1,//断开连接重连暂停时间
        "master"=>array(
            "size"=>1,//队列长度
			//设置下面两个会清理释放空闲链接
			//"keep_size"=>1,//空闲时保留链接数量
			//"keep_time"=>300,//空闲超过300关闭链接
            "weight"=>1,//权重
            "connection"=>array(//这里配置根据每个连接不同自定义.这里是MYSQL配置
                'host' => '127.0.0.1',
                'port' => 3306,
                'user' => 'root',
                'password' => '110',
                'fetch_mode' 		=> 1,
                'database' => 'test',
            )
        ),
        "slave1"=>array(
            "size"=>1,//队列长度
            "weight"=>1,//权重
            "connection"=>array(
                'host' => '127.0.0.1',
                'port' => 3306,
                'user' => 'root',
                'password' => '110',
                'fetch_mode' 		=> 1,
                'database' => 'test',
            )
        ),
        "slave2"=>array(
            "size"=>1,//队列长度
            "weight"=>1,//权重
            "connection"=>array(
                'host' => '127.0.0.1',
                'port' => 3306,
                'user' => 'root',
                'password' => '110',
                'fetch_mode' 		=> 1,
                'database' => 'test',
            )
        ),
        "slave3"=>array(
            "size"=>1,//队列长度
            "weight"=>1,//权重
            "connection"=>array(
                'host' => '127.0.0.1',
                'port' => 3306,
                'user' => 'root',
                'password' => '110',
                'fetch_mode' 		=> 1,
                'database' => 'test',
            )
        ),
    ),
    //redis客户端配置示例
    "redis"=>array(
     //   'auth'              =>'110',
        'host'             	=> '192.168.1.101',
        'port'             	=> 6379,
        'timeout'			=> '60',
        'db'				=> NULL,
     ),
     //redis客户端连接池配置示例
    "redis_pool"=>array(
        "try"=>true,//发送错误重试次数,设置为TRUE为不限制
        "sleep"=>1,//断开连接重连暂停时间
        "master"=>array(
            "size"=>1,//队列长度
            "weight"=>1,//权重
            "connection"=>array(
              //  'auth'              =>'110',
                'host'             	=> '192.168.1.101',
                'port'             	=> 6379,
                'timeout'			=> '60',
                'db'				=> NULL,
            )
        ),
    ),
    //postgresql客户端连接配置示例
    "postgresql_pool"=>array(
        'dsn' => 'host=127.0.0.1 port=5432 dbname=test user=root password=',
    ),
    //postgresql客户端连接池配置示例
    "postgresql_pool"=>array(
        "try"=>true,//发送错误重试次数,设置为TRUE为不限制
        "sleep"=>1,//断开连接重连暂停时间
        "master"=>array(
            "size"=>1,//队列长度
            "weight"=>1,//权重
            "connection"=>array(
                'dsn' => 'host=127.0.0.1 port=5432 dbname=test user=root password=',
            )
        ),
    ),
    //SOCKET客户端配置示例
    "clienl_dome"=>array(
        'sock_type'=>SWOOLE_SOCK_TCP,
        'host'=>'127.0.0.1',
        'port'=>8099,
        'set'=>array(
            'connect_timeout' => 8.0,
            'open_length_check'     => 1,
            'package_length_type'   => 'N',
            'package_length_offset' => 0,       //第N个字节是包长度的值
            'package_body_offset'   => 4,       //第几个字节开始计算长度
            'package_max_length'    => 8192000,  //协议最大长度
        )
    ),
    //SOCKET客户端连接池配置示例
    "client_pool"=>array(
        "try"=>true,//发送错误重试次数,设置为TRUE为不限制
        "sleep"=>1,//断开连接重连暂停时间
        "app"=>array(
            "size"=>1,//队列长度
            "weight"=>1,//权重
            "connection"=>array(
                'sock_type'=>SWOOLE_SOCK_TCP,
                'host'=>'127.0.0.1',
                'port'=>8099,
                'set'=>array(
                    'connect_timeout' => 8.0,
                    'open_length_check'     => 1,
                    'package_length_type'   => 'N',
                    'package_length_offset' => 0,       //第N个字节是包长度的值
                    'package_body_offset'   => 4,       //第几个字节开始计算长度
                    'package_max_length'    => 8192000,  //协议最大长度
                )
            )
        ),
    ),
);
