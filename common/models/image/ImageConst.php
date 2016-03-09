<?php

namespace common\models\image;

/**
 * 图片常数类
 * Class ImageConst
 * @package common\models\ImageConst
 */
class ImageConst
{
    /**
     * 图片模块配置
     */
    const IMG_PUBLIC = FRONT_PUBLIC.'images/uploads/'; //公网访问路径
    const IMG_DIR = FRONT_DIR."images/uploads/"; //文件保存路径

    /**
     * 图片类别
     */
    const CAT_GROUP = 'group_image'; //集团图片
    const CAT_HOTEL = 'hotel_image'; //酒店图片
    const CAT_ROOM = 'room_image'; //客房模块图片
    const CAT_PROMOTION = 'prom_image'; //促销模块图片
    const CAT_DINNER = 'dinner_image'; //餐饮模块图片
    const CAT_EXT_MODULE = 'ext_module_image'; //扩展模块图片

    /**
     * 各类别图片类型
     */
    //酒店图片类型
    const GROUP_IMG_MENU = 1; //菜单图
    const GROUP_IMG_SERVICE= 2; //服务图
    const GROUP_IMG_LOGO = 3; //Logo
    const GROUP_IMG_BACKGROUND = 4; //背景图
    const GROUP_IMG_ABOUT = 5; //介绍图

    //酒店图片类型
    const HOTEL_IMG_MENU = 1; //菜单图
    const HOTEL_IMG_SERVICE= 2; //服务图
    const HOTEL_IMG_LOGO = 3; //Logo
    const HOTEL_IMG_BACKGROUND = 4; //背景图
    const HOTEL_IMG_ABOUT = 5; //介绍图

    //客房图片类型
    const ROOM_IMG_THUMB = 1;    //缩略图
    const ROOM_IMG_INFO = 2;     //详情图

    //活动图片类型
    const PROM_IMG_THUMB = 1;    //缩略图
    const PROM_IMG_INFO = 2;     //详情图

    //餐饮图片类型
    const DINNER_IMG_THUMB = 1;    //缩略图
    const DINNER_IMG_INFO = 2;     //详情图

    //扩展模块图片类型
    const EXT_MOD_IMG_THUMB = 1;    //缩略图
    const EXT_MOD_IMG_INFO = 2;     //详情图
}
