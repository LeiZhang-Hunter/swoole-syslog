<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/16
 * Time: 14:42
 */

return [
    "sys_ip"=>"0.0.0.0",
    "sys_port"=>"514",
    "open_cpu_affinity"=>1,
    "open_eof_split"=>true,
    "package_eof"=>PHP_EOL,
    "worker_num"=>4,
    "daemonize"=>true,
    "pid_file"=>__ROOT__."/Proc/server.pid",
    "log_file"=>__ROOT__."/Log/swoole.log"
];