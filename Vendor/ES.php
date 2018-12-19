<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/18
 * Time: 13:16
 */
namespace Vendor;

use ElasticSearch\Client;

class ES{

    private $config;

    /**
     * @var Client
     */
    public $client;

    public function __construct($config)
    {
        //加载es库
        include_once __ROOT__ . "/Vendor/ElasticSearch/Auto.php";
        $this->config = $config;
        $this->client = Client::connection(sprintf("http://%s:%s/%s/%s", $config["ip"], $config["port"], $config["index"], $config["type"]));
    }


}