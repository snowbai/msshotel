<?php

namespace backend\models\promotion\proms_list;

use yii\db\Query;
use common\models\base\Constant;
use common\models\promotion\PromConst;

/**
 * 普通活动活动列表
 * Class OrdinaryList
 * @package backend\models\promotion\activity_list
 */
class OrdinaryList extends PromotionList
{
    /**
     * 获取普通活动列表
     * @param string $prom_name
     * @param string $prom_start_time
     * @param string $prom_end_time
     * @param int $prom_status
     * @return array
     */
    public function getList($prom_name='', $prom_start_time='', $prom_end_time='',$prom_status=-1)
    {
        $query = new Query;
        $query->select('p.name,p.status,p.add_time,p.start_time,p.end_time,o.num,o.remain_num')
            ->from('promotion_activity_ordinary o')
            ->leftJoin('promotion p','p.prom_id=o.prom_id')
            ->where(['p.g_id'=>$this->_g_id,'p.h_id'=>$this->_h_id])
            ->andWhere(['prom_type'=>PromConst::PROMOTION_TYPE_ORDINARY]);

        if(!empty($prom_name)){
            $query->andWhere(['p.name'=>$prom_name]);
        }

        if($prom_status >=0){
            $query->andWhere(['p.status'=>$prom_status]);
        }else{
            $query->andWhere(['<>','p.status',Constant::STATUS_DELETED]);
        }

        if(!empty($prom_start_time)){
            $query->andWhere(['>=','p.start_time',$prom_start_time]);
        }

        if(!empty($prom_end_time)){
            $query->andWhere(['<','p.end_time',$prom_end_time]);
        }

        $promotion_list = $query->orderBy(['p.add_time'=>SORT_DESC])->all();

        return $promotion_list;
    }
}
