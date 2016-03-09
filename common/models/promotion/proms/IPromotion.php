<?php

namespace common\models\promotion\proms;

/**
 * 促销活动接口类
 * Interface IPromotion
 * @package common\models\promotion\proms
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
interface IPromotion
{
    /**
     * 创建活动
     * @param $group_id
     * @param $hotel_id
     * @param $prom_data
     * @param $act_data
     * @return mixed
     */
    public static function create($group_id, $hotel_id, $prom_data, $act_data);

    /**
     * 获取活动对象
     * @param $prom_id
     * @param $prom_ar
     * @param $act_ar
     * @return mixed
     */
    public static function getInstance($prom_id, $prom_ar=null, $act_ar=null);

    /**
     * 获取活动类型
     * @return mixed
     */
    public static function getType();

    /**
     * 获取活动ID
     * @return mixed
     */
    public function getId();

    /**
     * 获取活动信息
     * @return mixed
     */
    public function getInfo();

    /**
     * 更新活动信息
     * @param $prom_data
     * @param $act_data
     * @return mixed
     */
    public function update($prom_data, $act_data);

    /**
     * 更新活动基本信息
     * @param $prom_data
     * @return mixed
     */
    public function updateInfo($prom_data);

    /**
     * 更新活动状态
     * @param $status
     * @return mixed
     */
    public function updateStatus($status);

    /**
     * 添加子活动
     * @param $prom_id
     * @param $act_data
     * @param $get_id
     * @param $attr
     * @return mixed
     */
    public static function addActivity($prom_id, $act_data, $get_id=false, $attr=null);

    /**
     * 获取子活动
     * @param $get_array
     * @param $attr
     * @return mixed
     */
    public function getActivity($get_array=false, $attr=null);

    /**
     * 更新子活动
     * @param $act_data
     * @param $attr
     * @return mixed
     */
    public function updateActivity($act_data, $attr=null);
}