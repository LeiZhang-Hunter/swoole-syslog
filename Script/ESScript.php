<?php
/**
 * Description:这个脚本是用来清除索引的
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/18
 * Time: 16:53
 */
include_once "../autoload.php";

$es = new \Vendor\ES(\Pendant\SysConfig::getInstance()->getSysConfig("es"));
$es->client->delete();
