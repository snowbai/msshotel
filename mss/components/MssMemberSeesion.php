<?php

namespace mss\components;

use common\Medeen;
use common\MdAuthErrorInfo;
use yii\helpers\Json;

use yii\web\Cookie;
use mss\models\HotelmemberLocalauth;
use mss\models\HotelmemberOauth;

/**
 * Mss 用户登录状态处理
 */
class MssMemberSeesion
{
    /**
     * [checkLogin description]
     * @method checkLogin
     * @param  [int]     $gid [description]
     * @param  [int]     $hid [description]
     * @return [json]          [description]
     */
    public static function checkLogin($gid,$hid)
    {
        $member = self::getMemberInSession();
        $memberAry = Json::decode($member);
        if(false === $memberAry['isSuccess']){
            //Json::encode([''])
            return $member;
        }else{
            if($gid != $hid){

            }else{
                $tmp = explode('#' ,$memberAry['data']['mss_hotel_member']);
                if($tmp[0] != $gid){
                    self::removeMemberSession();
                    return Json::encode(['isSuccess' => false,'error' =>['code'=>MdAuthErrorInfo::LOGIN_MEMBER_NOT_THIS_HOTEL,'messgae'=>'登录用户不是该酒店会员']]);
                }else{
                    return $member;
                }
            }
        }
    }
    /**
     * 登录后会员session处理
     * @method setLoginSession
     * @param  HotelmemberLocalauth $auth [description]
     */
    public static function setLoginSession(HotelmemberLocalauth $auth)
    {
        $memberId = $auth->member_id;
        $gid = $auth->g_id;
        $hid = $auth->h_id;
        /* @var \yii\web\Session $session */
        $session = Medeen::getApp()->get('session');
        $key = 'mss_hotel_member' ;
        $session->set($key, $gid.'#'.$hid.'#'.$memberId);

    }

    /**
     * [removeMemberInSession description]
     * @method removeMemberInSession
     * @return [boolean]                [description]
     */
    public static function removeMemberInSession()
    {
        /* @var \yii\web\Session $session */
        $session = Medeen::getApp()->get('session');
        $session->remove('mss_hotel_member');
        return true;
    }

    /**
     * [getMemberInSession description]
     * @method getMemberInSession
     * @return [json]             [description]
     */
    public static function getMemberInSession()
    {
        /* @var \yii\web\Session $session */
        $session = Medeen::getApp()->get('session');
        return (null != $session->get('mss_hotel_member')) ? Json::encode(['isSuccess' => true,'data' => ['mss_hotel_member' => $session->get('mss_hotel_member')]]) : Json::encode(['isSuccess' => false,'error' =>['code'=>MdAuthErrorInfo::MEMBER_NOT_LOGIN,'message' =>'未找到登录用户'] ]) ;

    }


    public static function doOauth(HotelmemberOauth $oauth){
        if(empty($oauth->member_id)&&$oauth->is_auto_login==1&&$oauth->is_mss==1){
            self::doOauthCookie($oauth->oauth_openid);
            return Json::encode(['isSuccess' => false,'error'=>['code'=>MdAuthErrorInfo::HOTEL_MEMBER_OAUTH_NOMEMBER,'message'=>'请登录绑定']]);
        }elseif(!empty($oauth->member_id)){
            $hotel_member = HotelmemberLocalauth::findOne(['member_id' => $oauth->member_id]);
            self::doOauthCookie($oauth->oauth_openid);
            self::setLoginSession($hotel_member);
            return Json::encode(['isSuccess' => true]);
        }
    }

    public static function doOauthCookie($openid){
        $oauthCookie = Medeen::getResponse()->cookies;
        $oauthCookie->add(new \yii\web\Cookie([
            'name' => 'wechat_openid',
            'value' => base64_encode($openid),
             'expire'=>time()+3600
        ]));
    }

    public static function doOauthUrl($url ,$openid){
        return $url.'wx_id&'.$openid;
    }
}
