<?php

include_once "autoload.php";

$config_instance = \Pendant\SysConfig::getInstance();

//获取ip和端口
$config_ip = $config_instance->getSysConfig()["sys_ip"];
$config_port = $config_instance->getSysConfig()["sys_port"];

$sys_socket = \Pendant\SwooleSysSocket::getInstance($config_ip,$config_port);

//注册触发前的钩子函数
$sys_socket->regBeforeHook(function () use ($config_instance){
    //注入配置文件
    $this->config = $config_instance;
    //加入常用的命令，在运行程序前加入start stop 和 reload 将进程服务化
    global $argv;

    $command = isset($argv[1]) ? $argv[1] : "";

    $server_pid_file = __ROOT__."/Proc/server.pid";
    if(is_file($server_pid_file))
    {
        $server_pid = (int)file_get_contents($server_pid_file);
    }else{
        $server_pid = 0;
    }

    if($command == "start")
    {

        if($server_pid)
        {
            //程序已经运行
            exit("syslog server already run\n");
        }

    }else if($command == "stop")
    {
        //发送信号让程序停止
        if($server_pid) {
            if(!posix_kill($server_pid,SIGTERM))
            {
                exit("syslog server stop failed\n");
            }
        }

        exit("syslog server already stop\n");

    }else if($command == "reload")
    {
        if(!$server_pid)
        {
            exit("syslog server not running\n");
        }

        posix_kill($server_pid,SIGUSR1);
        exit();
    }else{
        exit("must input start|stop|reload\n");
    }
});


//运行程序
$sys_socket->run();
