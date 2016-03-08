<?php
namespace common\components;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use common\Medeen;

class BaseActiveRecord extends \yii\db\ActiveRecord
{

    // Query
    public static function find()
    {
        return new ActiveQuery(get_called_class());
    }

    public static function leftJoinWith($with)
    {
        $query = self::find();
        $query->joinWith($with, true, 'LEFT JOIN');
        return $query;
    }

    public static function rightJoinWith($with)
    {
        $query = self::find();
        $query->joinWith($with, true, 'RIGHT JOIN');
        return $query;
    }

    public static function findOne($condition = null, $orderBy = null, $with = null, $params = [])
    {
        $query = self::findQuery($condition, $orderBy, $with, $params);
        return $query->one();
    }

    public static function findAll($condition = null, $orderBy = null, $limit = null, $with = null, $params = [])
    {
        $query = self::findQuery($condition, $orderBy, $with, $params);
        if ($limit != null)
        {
            $query->limit($limit);
        }
        return $query->all();
    }

    public static function findQuery($condition = null, $orderBy = null, $with = null, $params = [])
    {
        $query = static::find();

        if ($with !== null)
        {
            if (is_string($with))
            {
                $query->innerJoinWith($with);
            }
            else
            {
                if (isset($with['type']))
                {
                    $type = $with['type'];
                }
                else if (isset($with[1]))
                {
                    $type = $with[1];
                }
                else
                {
                    $type = 'INNER JOIN';
                }

                $query->joinWith($with[0], true, $type);
            }
        }

        if ($condition !== null && ! empty($condition))
        {
            if (! ArrayHelper::isAssociative($condition))
            {
                // query by primary key
                $primaryKey = static::primaryKey();
                if (isset($primaryKey[0]))
                {
                    $condition = [
                        $primaryKey[0] => $condition
                    ];
                }
                else
                {
                    throw new InvalidConfigException('"' . get_called_class() . '" must have a primary key.');
                }
            }
            $query->andWhere($condition);
        }

        if ($orderBy != null)
        {
            $query->orderBy($orderBy);
        }
        return $query;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate())
        {
            if ($this->hasAttribute('created_at'))
            {
                if ($this->created_at == null || $this->created_at == '')
                {
                    $this->created_at = time();
                }
            }
            if ($this->hasAttribute('updated_at'))
            {
                $this->updated_at = time();
            }

            return true;
        }
        return false;
    }

    public function afterValidate()
    {
        parent::afterValidate();
        if ($this->hasErrors())
        {
            Medeen::setErrorMessage($this->getFirstErrors());
            Medeen::info($this->errors, self::className());
        }
    }
}
