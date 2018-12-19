<?php
/**
 * Description:
 * Created by PhpStorm.
 * User: 张磊
 * Date: 2018/12/19
 * Time: 12:22
 */

namespace Controller;

class DingDingInterFace{
    /**
     * 发送钉钉报警
     */
    function sendDingDingAlert($msg,$userGroup=1,$uid=0){
        $alertList = \CloudConfig::get('chelun/community/dingdingalert');
        $userGroups = array(
            1 => $alertList['user'],
            3 => $alertList['admire_shell_user'],
            9 => 'wuyunlin',	// 用来测试发送的
        );

        $user = $userGroups[$userGroup];
        $url = $alertList['url'];

        $data = array(
            'users' => $user,
            'content' => $msg,
        );
        //拼接上一个短链,直接跳转用户中心
        if($uid != 0){
            $jumpUrl = "chelun://user/center/open/" . $uid;
            $pcJumpUrl = "http://community.oa.com//admin.php?c=h&uid=" . $uid;
            $shortUrl = self::genShortUrl($jumpUrl,74,"广告黄图告警",$pcJumpUrl);
            $data['content'] .= "  " . $shortUrl;
        }
        $status = curl::post3($url,$data);
    }
}