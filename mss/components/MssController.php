<?php

namespace mss\components;

use common\components\CommonController;
use common\Medeen;
use common\MdAuthErrorInfo;
use yii;
use yii\helpers\Json;
/**
 * MSS酒店前台框架基类
 */
class MssController extends CommonController
{
    public $g_id ;
    public $h_id ;

    public $hotel_type;

    public $hotel_info;
    /**
     *init
     */
    public function init()
    {
        parent::init();
        $this->setHotelId();
        return true;
    }

    protected function setHotelId()
    {
        $this->g_id = Medeen::getGetValue('g_id',1);
        $this->h_id = Medeen::getGetValue('h_id',1);
        //$this->getHotelInfo($this->g_id,$this->h_id);
        Hotel::model()->count(['h_id'=>$this->h_id]);
    }

    protected function getHotelInfo($g_id, $h_id)
    {
        $hotel = Hotel::findOne(['g_id'=>$this->g_id,'h_id'=>$this->h_id]);
        $this->hotel_info['hotel_name'] = $hotel->hotel_name;
        $this->hotel_info['hotel_phone'] = $hotel->hotel_phone;
        $this->hotel_info['city_code'] = $hotel->city_code;
    }
    /**
     * beforeaction
     * 判断是否登录了不同酒店的会员
     * 判断酒店是否能访问当前action
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);
        $this->getRoute();

        $isThisHotel = Json::decode( $this->checkMemberIsInThisHotel($this->g_id,$this->h_id));
        if(false === $isThisHotel['isSuccess']&&MdAuthErrorInfo::LOGIN_MEMBER_NOT_THIS_HOTEL==$isThisHotel['error']['code']){
            //
        }
        return true;
    }

    protected function checkHotelAuth($g_id,$h_id){
        $moduleId = $this->getModlueId();
        $controllerId = $this->getControllerId();
        $actionId = $this->getActionId();
        $hotelAuth = HotelAuth::findOne(['g_id'=>$g_id,'h_id'=>$h_id]);
        $frontPermission = MssfrontPermission::findOne(['module_id'=>$moduleId,'controller_id'=>$controllerId,'action_id'=>$actionId]);
        HotelAuthPermission::findAll(['hotel_role_id'=>$hotelAuth->hotel_role_id]);
    }


    /**
     * [checkMemberIsInThisHotel 判断登录用户是否为该酒店，防止微信未退出时，session出错]
     * @method checkMemberIsInThisHotel
     * @param  [int]                   $g_id [description]
     * @param  [int]                   $h_id [description]
     * @return json                        [description]
     */
    protected function checkMemberIsInThisHotel($g_id,$h_id){
        return MssMemberSeesion::checkLogin($g_id,$h_id);
    }
}
