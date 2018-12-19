<?php
/**
 * Description:数据中间层
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/17
 * Time: 15:50
 */
namespace Controller;

use Pendant\SysFactory;
use Pendant\SysLogProtocol;

class MiddleInterface{

    const SEARCH_PHP = "php";

    const SEARCH_MYSQL = "mysql";

    const SEARCH_NGINX = "nginx";

    public function run()
    {
        //系统日志数据包
        $data = [];
        $data["type"] =
        $data["facility"] = SysLogProtocol::$data["facility"];
        $data["server_ip"] = SysLogProtocol::$data["client_info"]["address"];
        $data["level"] = SysLogProtocol::$data["severity"];
        $data["hostname"] = SysLogProtocol::$data["hostname"];
        $data["happen_time"] = SysLogProtocol::$data["happen_time"];
        $data["body"] = SysLogProtocol::$data["body"];
        $data["create_time"] = time();

        //确定是php 还是mysql 还是nginx 的消息
        $search_body = strtolower($data["body"]);

        $type = 0;

        if(strpos($search_body,self::SEARCH_PHP) !== false)
        {
            $type = 1;
        }elseif (strpos($search_body,self::SEARCH_MYSQL) !== false)
        {
            $type = 2;
        }elseif(strpos($search_body,self::SEARCH_NGINX) !== false)
        {
            $type = 3;
        }
        $data["type"] = $type;

        //落地mysql
        if(SysFactory::$db->insert("syslog",$data))
        {
            //获取最后插入的id 然后放入es 中这里是原子操作不需要担心安全问题
            $insertId = SysFactory::$db->getLastInsertId();
            $data["sys_id"] = (int)$insertId;
            //存入es
            SysFactory::$es_instance->index($data);
        }else{
            //如果出现意外日志写入文件存储防止丢失
            $this->writeLog($data);
        }
    }

    //备用方案数据库写不进去的话我们将他放到日志里，事后可以观看恢复关键日志
    private function writeLog($msg)
    {
        $data = json_encode($msg)."\n";//压缩数据
        file_put_contents(__ROOT__."/Log/".date("Y-m-d").".bak.log",$data,FILE_APPEND);
    }
}
