<?php

namespace common\models\mssgh;

/**
 * 酒店集团常熟类
 * Class MssghConst
 * @package common\models\mssgh
 */
class MssghConst
{
    //默认菜单
    const HOTEL_DEFAULT_MENUS = [
        0=>['menu_name'=>'首页','menu_url'=>'hotel/index'],
        1=>['menu_name'=>'介绍','menu_url'=>'hotel/intro'],
        2=>['menu_name'=>'地图','menu_url'=>'hotel/map'],
        3=>['menu_name'=>'评价','menu_url'=>'hotel/comments'],
        4=>['menu_name'=>'客房','menu_url'=>'room/index'],
        5=>['menu_name'=>'餐饮','menu_url'=>'dinner/index'],
        6=>['menu_name'=>'促销','menu_url'=>'promotion/index'],
        7=>['menu_name'=>'康乐','menu_url'=>'kangle/index'],
        8=>['menu_name'=>'宴会','menu_url'=>'banquet/index'],
        9=>['menu_name'=>'会议','menu_url'=>'meeting/index'],
        //100-999 自定义
    ];

    //酒店设施默认项目
    const HOTEL_DEFAULT_FACILITY_FEATURES = [
        0=>['name'=>'会员免押金'],
        1=>['name'=>'0秒退房'],
        2=>['name'=>'可口的早餐'],
        3=>['name'=>'手机开门'],
        4=>['name'=>'水上运动'],
        5=>['name'=>'游泳池'],
        6=>['name'=>'海景房'],
        7=>['name'=>'江景房'],
        8=>['name'=>'温泉'],
        10=>['name'=>'精品主题房'],
        11=>['name'=>'八大菜系'],
        12=>['name'=>'情侣房'],
        13=>['name'=>'亲子房'],
        14=>['name'=>'落地窗'],
        15=>['name'=>'圆床房'],
        //100-200 自定义
    ];

    const HOTEL_DEFAULT_FACILITY_SERVICES = [
        0=>['name'=>'接机服务'],
        1=>['name'=>'叫车服务'],
        2=>['name'=>'行李寄存'],
        3=>['name'=>'送餐服务'],
        4=>['name'=>'洗衣服务'],
        5=>['name'=>'儿童看护'],
        6=>['name'=>'叫醒服务'],
        7=>['name'=>'24小时热水'],
        8=>['name'=>'免费洗漱用品'],
        //100-200 自定义
    ];

    const HOTEL_DEFAULT_FACILITY_FACILITIES = [
        0=>['name'=>'收费停车场'],
        1=>['name'=>'免费停车场'],
        2=>['name'=>'Wifi覆盖'],
        3=>['name'=>'中餐厅'],
        4=>['name'=>'西餐厅'],
        5=>['name'=>'日式餐厅'],
        6=>['name'=>'大堂吧'],
        7=>['name'=>'酒吧'],
        8=>['name'=>'KTV'],
        9=>['name'=>'多功能厅'],
        10=>['name'=>'游泳池'],
        11=>['name'=>'儿童乐园'],
        12=>['name'=>'美容中心'],
        13=>['name'=>'吸烟区'],
        //100-200 自定义
    ];
}
