<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%promotion}}".
 *
 * @property integer $prom_id
 * @property integer $prom_type
 * @property integer $g_id
 * @property integer $h_id
 * @property string $name
 * @property string $brief
 * @property string $intro
 * @property string $coming_name
 * @property string $coming_brief
 * @property string $coming_intro
 * @property string $start_time
 * @property string $end_time
 * @property string $book_phone1
 * @property string $book_phone2
 * @property integer $mem_day_lmt_num
 * @property integer $mem_day_win_lmt_num
 * @property integer $mem_total_lmt_num
 * @property integer $mem_total_win_lmt_num
 * @property integer $use_advanced_lmt
 * @property integer $perm_type
 * @property integer $is_recommended
 * @property integer $status
 * @property integer $list_order
 * @property integer $extension_type
 * @property string $extension_value
 * @property string $recommend_time
 * @property string $modify_time
 * @property string $add_time
 */
class Promotion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promotion}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prom_type', 'g_id', 'h_id', 'mem_day_lmt_num', 'mem_day_win_lmt_num', 'mem_total_lmt_num', 'mem_total_win_lmt_num', 'use_advanced_lmt', 'perm_type', 'is_recommended', 'status', 'list_order', 'extension_type'], 'integer'],
            [['intro', 'coming_intro'], 'string'],
            [['start_time', 'end_time', 'recommend_time', 'modify_time', 'add_time'], 'safe'],
            [['name', 'coming_name'], 'string', 'max' => 64],
            [['brief', 'coming_brief'], 'string', 'max' => 256],
            [['book_phone1', 'book_phone2'], 'string', 'max' => 20],
            [['extension_value'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'prom_id' => '活动ID',
            'prom_type' => '活动类型',
            'g_id' => '集团ID',
            'h_id' => '门店ID',
            'name' => '活动名称',
            'brief' => '活动简介',
            'intro' => '活动介绍',
            'coming_name' => '即将开始时活动名称',
            'coming_brief' => '即将开始时活动简介',
            'coming_intro' => '即将开始时活动介绍',
            'start_time' => '活动开始时间',
            'end_time' => '活动结束时间',
            'book_phone1' => '预订电话一',
            'book_phone2' => '预订电话二',
            'mem_day_lmt_num' => '会员每天限制数量',
            'mem_day_win_lmt_num' => '会员每天中奖限制次数',
            'mem_total_lmt_num' => '会员活动期间限制数量',
            'mem_total_win_lmt_num' => '会员活动期间中奖限制次数',
            'use_advanced_lmt' => '是否使用高级限制',
            'perm_type' => '参加活动权限限制',
            'is_recommended' => '是否推荐',
            'status' => '活动状态',
            'list_order' => '排序',
            'extension_type' => '扩展类型',
            'extension_value' => '扩展值',
            'recommend_time' => '推荐时间',
            'modify_time' => '修改时间',
            'add_time' => '添加时间',
        ];
    }
}
