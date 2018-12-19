<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 14:39
 */
namespace Pendant;

class SwooleSysSocket{

    /**
     * @var SwooleSysSocket
     */
    private static $instance;

    /**
     * @var \swoole_server
     */
    private static $swoole_server;

    /**
     * @var \Closure
     */
    private static $beforeHook;

    /**
     * @var \Closure
     */
    private static $finishHook;

    /**
     * @var SysConfig
     */
    public $config;

    private $ip;

    private $port;


    public function __construct($ip,$port)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    public function regBeforeHook($beforeFunction)
    {
        self::$beforeHook = \Closure::bind($beforeFunction,$this);
    }

    public function regFinishHook($endFunction)
    {
        self::$finishHook = \Closure::bind($endFunction,$this);
    }

    /**
     * Description:获取系统实例
     */
    public static function getInstance($ip,$port)
    {
        if(!self::$instance)
        {
            self::$instance = new self($ip,$port);
        }
        return self::$instance;
    }

    public function run()
    {
        if(is_callable(self::$beforeHook))
        {
            call_user_func_array(self::$beforeHook,[]);
        }

        self::$swoole_server = new \swoole_server($this->ip,$this->port,SWOOLE_PROCESS, SWOOLE_SOCK_UDP);


        $sys_factory = SysFactory::getInstance();

        $sys_factory->regSwooleServer(self::$swoole_server);

        $sys_factory->onWorkerStart();

        $sys_factory->onReceive();

        //加入配置文件
        self::$swoole_server->set($this->config->getSysConfig());

        //运行程序
        self::$swoole_server->start();
    }
}