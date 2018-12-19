<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 15:12
 */

namespace Pendant;

use Controller\MiddleInterface;
use ElasticSearch\Client;
use Vendor\DB;
use Vendor\ES;

class SysFactory {
    private static $swoole_server;

    private static $instance;

    /**
     * @var DB
     */
    public static $db;

    /**
     * @var MiddleInterface
     */
    public static $interface_instance;

    /**
     * @var Client
     */
    public static $es_instance;

    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //注入swoole_server
    public function regSwooleServer($swoole_server)
    {
        self::$swoole_server = $swoole_server;
    }

    //进程启动前加载常用的类
    public function onWorkerStart()
    {
        if(self::$swoole_server instanceof \swoole_server)
        {
            self::$swoole_server->on("WorkerStart",function ($serv, $worker_id){
                //加载到实例
                SysFactory::$interface_instance = new MiddleInterface();

                SysFactory::$db = new DB(SysConfig::getInstance()->getSysConfig("db"));

                $es = new ES(SysConfig::getInstance()->getSysConfig("es"));
                SysFactory::$es_instance = $es->client;
            });
        }
    }


    //注入接收函数
    public function onReceive()
    {
        if(self::$swoole_server instanceof \swoole_server)
        {
            self::$swoole_server->on("Packet",function($serv, $data, $clientInfo){
                //拆解包头把包头数据放到全局变量
                $result = SysLogProtocol::parse($data);
                if(!$result)
                    return;

                SysLogProtocol::$data["client_info"] = $clientInfo;
                if(!SysLogProtocol::$data["client_info"])
                    return;

                SysLogProtocol::$data["receive_time"] = time();


                call_user_func_array([SysFactory::$interface_instance,'run'],[]);
            });
        }
    }
}