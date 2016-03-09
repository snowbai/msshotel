<?php

namespace common\models\mssgh\extensions;

/**
 * 主题管理类
 * Class Theme
 * @package common\models\mssgh\extensions;
 */
class Theme extends Meta
{
    /**
     * 获取当前主题名称
     * @return string
     */
    public function getThemeName()
    {
        return (string) $this->getMetaData('hotel_theme');
    }

    /**
     * 获取当前主题方案
     * @return array|mixed|null|string
     */
    public function getScheme()
    {
        $theme_name = $this->getThemeName();
        if(!empty($theme_name)){
            $scheme = $this->_getThemeScheme($theme_name);
        }else{
            $scheme = null;
        }
        return $scheme;
    }

    /**
     * 获取主题信息
     * @return mixed
     */
    public function getThemeInfo()
    {
        $theme_name = $this->getThemeName();
        if(!empty($theme_name)){
            $theme['name'] = $theme_name;
            $theme['scheme'] = $this->_getThemeScheme($theme_name);
        }else{
            $theme['name'] = '';
            $theme['scheme'] = null;
        }
        return $theme;
    }

    /**
     * 切换主题
     * @param $theme_name
     * @return bool
     */
    public function useTheme($theme_name)
    {
        return $this->setMetaData('hotel_theme', $theme_name);
    }

    /**
     * 设置当前主题方案
     * @param $theme_scheme
     * @return bool
     */
    public function setThemeScheme($theme_scheme)
    {
        $theme_name = $this->getThemeName();
        return $this->_setThemeScheme($theme_name,$theme_scheme);
    }
    /**
     * 设置主题信息
     * @param $theme_name
     * @param null $theme_scheme
     * @return bool
     */
    public function setTheme($theme_name, $theme_scheme=null)
    {
        if(empty($theme_name)){
            return false;
        }

        if($this->useTheme($theme_name)){
            return $this->_setThemeScheme($theme_name,$theme_scheme);
        }else{
            return false;
        }
    }

    /**
     * 设置主题项颜色
     * @param $element
     * @param $color
     * @return bool
     */
    public function setColor($element, $color)
    {
        if(empty($element)){
            return false;
        }

        $theme_info = $this->getThemeInfo();
        $theme_name = $theme_info['name'];
        $theme_scheme = $theme_info['scheme'];

        if(empty($color)){
            unset($theme_scheme['color'][$element]);
        }else{
            $theme_scheme['color'][$element] = $color;
        }

        return $this->_setThemeScheme($theme_name,$theme_scheme);
    }

    /**
     * 设置主题项CSS
     * @param $element
     * @param $css
     * @return bool
     */
    public function setCss($element, $css)
    {
        if(empty($element)){
            return false;
        }
        $theme_info = $this->getThemeInfo();
        $theme_name = $theme_info['name'];
        $theme_scheme = $theme_info['scheme'];

        if(empty($css)){
            unset($theme_scheme['css'][$element]);
        }else{
            $theme_scheme['css'][$element] = $css;
        }

        return $this->_setThemeScheme($theme_name,$theme_scheme);
    }

    /**
     * 设置自定义主题样式
     * @param $content
     * @return bool
     */
    public function setCustom($content)
    {
        $theme_info = $this->getThemeName();
        $theme_name = $theme_info['name'];
        $theme_scheme = $theme_info['scheme'];

        if(empty($content)){
            unset($theme_scheme['custom']);
        }else{
            $theme_scheme['custom'] = $content;
        }

        return $this->_setThemeScheme($theme_name,$theme_scheme);
    }

    /**
     * 获取主题方案
     * @param $theme_name
     * @return array|mixed|string
     */
    protected function _getThemeScheme($theme_name)
    {
        $data = $this->getMetaData('theme_'.$theme_name);
        return !empty($data) ? json_decode($data, true) : null;
    }

    /**
     * 设置主题方案
     * @param $theme_name
     * @param $theme_scheme
     * @return bool
     */
    protected function _setThemeScheme($theme_name, $theme_scheme)
    {
        if(empty($theme_name)){
            return false;
        }

        return $this->setMetaData('theme_'.$theme_name, json_encode($theme_scheme));
    }
}