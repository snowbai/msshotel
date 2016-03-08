<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Wechat]].
 *
 * @see Wechat
 */
class WechatQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return Wechat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Wechat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}