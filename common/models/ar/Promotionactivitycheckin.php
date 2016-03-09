<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion_activity_checkin}}".
 *
 * @property integer $id
 * @property integer $check_type
 * @property integer $g_id
 * @property integer $h_id
 * @property string $name
 * @property string $brief
 * @property integer $integral_reward
 * @property string $modify_time
 */
class PromotionActivityCheckin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion_activity_checkin}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['check_type', 'g_id', 'h_id', 'integral_reward'], 'integer'],
            [['modify_time'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['brief'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_type' => '签到类型 1每天一次，2每月三次',
            'g_id' => '集团ID',
            'h_id' => '门店ID',
            'name' => '活动名称',
            'brief' => '活动简介',
            'integral_reward' => '单次奖励积分',
            'modify_time' => '修改时间',
        ];
    }

    /**
     * 获取配置
     * @param $g_id
     * @param $h_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getconfig($g_id, $h_id){
        return $this->find()
            ->where('g_id=:g_id',[':g_id'=>$g_id])
            ->one();
    }
}
