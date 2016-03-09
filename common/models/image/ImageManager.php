<?php

namespace common\models\image;

use yii\base\Exception;
use common\models\ar\Image as ImageAR;

/**
 * 图片管理
 * Class ImageManager
 * @package common\models\image
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
class ImageManager
{
    /**
     * 类别
     * @var string
     */
    protected $_cat_type;

    /**
     * 类别id
     * @var int
     */
    protected $_cat_id;

    /**
     * 构造函数
     * @param $cat_type
     * @param $cat_id
     */
    public function __construct($cat_type,$cat_id)
    {
        $this->_cat_type = (string) $cat_type;
        $this->_cat_id = (int) $cat_id;
    }

    /**
     * 获取一张图片
     * @param $type
     * @param int $subtype
     * @param int $seq
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getImage($type,$subtype=0,$seq=0)
    {
        $image = ImageAR::find()
            ->where(['cat_type'=>$this->_cat_type,'cat_id'=>$this->_cat_id,'type'=>$type,'subtype'=>$subtype,'seq'=>$seq])
            ->asArray()->one();

        if(!empty($image)){
            $image['url'] = $this->_parseUrl($image['url']);
        }

        return $image;
    }

    /**
     * 获取多张图片
     * @param $type
     * @param int $subtype
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getImages($type=null,$subtype=null)
    {
        $query = ImageAR::find()
            ->where(['cat_type'=>$this->_cat_type,'cat_id'=>$this->_cat_id]);
        if($type!==null){
            $query->andWhere(['type'=>$type]);
        }
        if($subtype!==null){
            $query->andWhere(['subtype'=>$subtype]);
        }

        $images = $query->asArray()->all();
        foreach($images as $image){
            $image['url'] = $this->_parseUrl($image['url']);
        }

        return $images;
    }

    /**
     * 保存一张图片（插入或更新）
     * @param $image_data
     * @return bool
     */
    public function setImage($image_data)
    {
        if(!isset($image_data['type'])) return false;
        $type = $image_data['type'];
        $subtype = isset($image_data['sub_type']) ? $image_data['sub_type'] : 0;
        $seq = isset($image_data['seq']) ? $image_data['seq'] : 0;

        $image_obj = ImageAR::find()
                ->where(['cat_type'=>$this->_cat_type,'cat_id'=>$this->_cat_id,'type'=>$type,'subtype'=>$subtype,'seq'=>$seq])//使用联合索引
                ->one();

        if(empty($image_obj)){
            $image_obj = new ImageAR();
            $image_obj->cat_type = $this->_cat_type;
            $image_obj->cat_id = $this->_cat_id;
            $image_obj->type = $type;
            $image_obj->subtype = $subtype;
            $image_obj->seq = $seq;
        }

        $image_obj->name =isset($image_data['name']) ? $image_data['name'] : '';
        $image_obj->url = isset($image_data['url']) ? $image_data['url'] : '';
        $image_obj->path = isset($image_data['path']) ? $image_data['path'] : '';

        return $image_obj->save();
    }

    /**
     * 保存多张图片（插入或更新）
     * @param $images_data
     * @return bool
     * @throws \yii\db\Exception
     */
    public function setImages($images_data)
    {
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try
        {
            foreach($images_data as $key=>$image_data){
                $success_arr[$key] = $this->setImage($image_data);
                if(!$success_arr[$key]){
                    throw new Exception('Save failed');
                }
            }
            $transaction->commit();
            return true;
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * 清除图片
     * @param $type
     * @param int $subtype
     * @param int $seq
     * @return bool
     */
    public function unsetImage($type,$subtype=0,$seq=0)
    {
        $image = ImageAR::find()
            ->where(['cat_type'=>$this->_cat_type,'cat_id'=>$this->_cat_id,'type'=>$type,'subtype'=>$subtype,'seq'=>$seq])
            ->one();

        if(!empty($image)){
            $image->name = '';
            $image->url = '';
            $image->path = '';
            return $image->save();
        }else{
            return false;
        }
    }

    /**
     * 给相对路径加上绝对路径
     * @param $url
     * @return string
     */
    protected function _parseUrl($url)
    {
        if(strstr($url, 'http://')===false){
            return ImageConst::IMG_PUBLIC.$url;
        }else{
            return $url;
        }
    }
}