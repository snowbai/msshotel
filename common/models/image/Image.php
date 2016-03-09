<?php

namespace common\models\image;

/**
 * 图片处理类
 * Class Image
 * @package common\models\image
 */
class Image
{
    /**
     * 图片合法后缀
     * @var array
     */
    protected static $_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp');

    /**
     * 判断是否为真实图片
     * @param $img_path
     * @return bool
     */
    public static function isReal($img_path)
    {
        return boolval(@getimagesize($img_path));
    }

    /**
     * 判断后缀是否正确
     * @param $img_path
     * @return bool
     */
    public static function isExtensionOk($img_path)
    {
        $ext = strtolower(pathinfo($img_path, PATHINFO_EXTENSION));
        return in_array($ext, self::$_extensions);
    }

    /**
     * 验证图片是否合法
     * @param $img_path
     * @return bool
     */
    public static function validate($img_path)
    {
        if(!self::isExtensionOk($img_path)){
            return false;
        }
        if(self::isReal($img_path)){
            return false;
        }
        return true;
    }

    /**
     * 获取图片尺寸
     * @param $img_path
     * @return array
     */
    public static function getImageSize($img_path)
    {
        list($width, $height) = getimagesize($img_path);
        return array($width, $height);
    }

    /**
     * 调整图片大小
     * @param $pic
     * @param $width
     * @param $height
     * @param $save_path
     * @return bool
     */
    public static function resizeImage($pic,$width,$height,$save_path)
    {
        if(!is_file($pic)) return false;
        if(!is_dir(dirname($save_path))) return false;

        $ext = strtolower(pathinfo($save_path, PATHINFO_EXTENSION));

        switch($ext){
            case 'jpg':
            case 'jpeg':
                $img=@imagecreatefromjpeg($pic);
                if(!$img) return false;
                $tmp_img=imagecreatetruecolor($width,$height);
                imagecopyresampled($tmp_img,$img,0,0,0,0,$width,$height,imagesx($img),imagesy($img));
                imagejpeg($tmp_img,$save_path);
                imagedestroy($img);
                imagedestroy($tmp_img);
                break;
            case 'gif':
                $img=@imagecreatefromgif($pic);
                if(!$img) return false;
                $tmp_img=imagecreatetruecolor($width,$height);
                imagecopyresampled($tmp_img,$img,0,0,0,0,$width,$height,imagesx($img),imagesy($img));
                imagegif($tmp_img,$save_path);
                imagedestroy($img);
                imagedestroy($tmp_img);
                break;
            case 'png':
                $img=@imagecreatefrompng($pic);
                if(!$img) return false;
                $tmp_img=imagecreatetruecolor($width,$height);
                imagecopyresampled($tmp_img,$img,0,0,0,0,$width,$height,imagesx($img),imagesy($img));
                imagepng($tmp_img,$save_path);
                imagedestroy($img);
                imagedestroy($tmp_img);
                break;
            case 'bmp':
                $img=@imagecreatefromwbmp($pic);
                if(!$img) return false;
                $tmp_img=imagecreatetruecolor($width,$height);
                imagecopyresampled($tmp_img,$img,0,0,0,0,$width,$height,imagesx($img),imagesy($img));
                imagewbmp($tmp_img,$save_path);
                imagedestroy($img);
                imagedestroy($tmp_img);
                break;
            default:
                return false;
        }

        return $save_path;
    }
}
