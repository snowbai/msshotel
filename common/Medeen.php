<?php

namespace common;

use Yii;
use yii\web\response;
use yii\helpers\VarDumper;
use yii\data\Pagination;
use yii\base\InvalidParamException;

class Medeen extends \Yii
{


    public static function getApp()
    {
        return self::$app;
    }

    public static function getView()
    {
        $app = self::getApp();
        return $app->getView();
    }

    public static function getRequest()
    {
        $app = self::getApp();
        return $app->request;
    }

    public static function getResponse()
    {
        $app = self::getApp();
        return $app->response;
    }

    public static function getBaseUrl($url = null)
    {
        $app = self::getApp();
        $baseUrl = $app->request->getBaseUrl();
        if ($url !== null)
        {
            $baseUrl .= $url;
        }
        return $baseUrl;
    }

    public static function getHomeUrl($url = null)
    {
        $app = self::getApp();
        $homeUrl = $app->getHomeUrl();
        if ($url !== null)
        {
            $homeUrl .= $url;
        }
        return $homeUrl;
    }

    public static function getWebUrl($url = null)
    {
        $webUrl = self::getAlias('@web');
        if ($url !== null)
        {
            $webUrl .= $url;
        }
        return $webUrl;
    }

    public static function getWebPath($path = null)
    {
        $webPath = self::getAlias('@webroot');
        if ($path !== null)
        {
            $webPath .= $path;
        }
        return $webPath;
    }

    public static function getAppParam($key, $defaultValue = null)
    {
        $app = self::getApp();
        if (array_key_exists($key, $app->params))
        {
            return $app->params[$key];
        }
        return $defaultValue;
    }

    public static function setAppParam($array)
    {
        $app = self::getApp();
        foreach ($array as $key => $value)
        {
            $app->params[$key] = $value;
        }
    }

    public static function getViewParam($key, $defaultValue = null)
    {
        $app = self::getApp();
        $view = $app->getView();
        if (array_key_exists($key, $view->params))
        {
            return $view->params[$key];
        }
        return $defaultValue;
    }

    public static function setViewParam($array)
    {
        $app = self::getApp();
        $view = $app->getView();
        foreach ($array as $name => $value)
        {
            $view->params[$name] = $value;
        }
    }

    public static function hasGetValue($key)
    {
        return isset($_GET[$key]);
    }

    public static function getGetValue($key, $default = NULL)
    {
        if (self::hasGetValue($key))
        {
            return $_GET[$key];
        }
        return $default;
    }

    public static function hasPostValue($key)
    {
        return isset($_POST[$key]);
    }

    public static function getPostValue($key, $default = NULL)
    {
        if (self::hasPostValue($key))
        {
            return $_POST[$key];
        }
        return $default;
    }

    public static function hasRequestValue($key)
    {
        return isset($_REQUEST[$key]);
    }

    public static function getRequestValue($key, $default = NULL)
    {
        if (self::hasRequestValue($key))
        {
            return $_REQUEST[$key];
        }
        return $default;
    }
    public static function getFlash($type,$default=null)
    {
        $app = self::getApp();
        return $app->session->getFlash($type,$default);
    }
    public static function setFlash($type, $message)
    {
        $app = self::getApp();
        $app->session->setFlash($type, $message);
    }

    public static function setWarningMessage($message)
    {
        $app = self::getApp();
        $app->session->setFlash('warning', $message);
    }

    public static function setSuccessMessage($message)
    {
        $app = self::getApp();
        $app->session->setFlash('success', $message);
    }

    public static function setErrorMessage($message)
    {
        $app = self::getApp();
        $app->session->setFlash('error', $message);
    }

    public static function info($var, $category = 'application')
    {
        $dump = VarDumper::dumpAsString($var);
        parent::info($dump, $category);
    }

    public static function getDB()
    {
        $app = self::getApp();
        return $app->db;
    }

    public static function createCommand($sql = null)
    {
        $app = self::getApp();
        $db = $app->db;
        if ($sql !== null)
        {
            return $db->createCommand($sql);
        }
        return $db->createCommand();
    }

    public static function execute($sql)
    {
        $app = self::getApp();
        $db = $app->db;
        $command = $db->createCommand($sql);
        return $command->execute();
    }

    public static function queryAll($sql)
    {
        $app = self::getApp();
        $db = $app->db;
        $command = $db->createCommand($sql);
        return $command->queryAll();
    }

    public static function queryOne($sql)
    {
        $app = self::getApp();
        $db = $app->db;
        $command = $db->createCommand($sql);
        return $command->queryOne();
    }

    public static function getPagedRows($query, $config = [])
    {
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count()
        ]);
        if (isset($config['page']))
        {
            $pages->setPage($config['page'], true);
        }

        if (isset($config['pageSize']))
        {
            $pages->setPageSize($config['pageSize'], true);
        }

        $rows = $query->offset($pages->offset)->limit($pages->limit);

        if (isset($config['orderBy']))
        {
            $rows = $rows->orderBy($config['orderBy']);
        }
        $rows = $rows->all();

        $rowsLable = isset($config['rows']) ? $config['rows'] : 'rows';
        $pagesLable = isset($config['pager']) ? $config['pager'] : 'pager';

        $ret = [];
        $ret[$rowsLable] = $rows;
        $ret[$pagesLable] = $pages;

		    return $ret;
	}

  /**
   * @param $format Yii\web\Response::format
   * @return void
   */
    public static function setResponseFormat($format){
      switch ($format) {
        case Response::FORMAT_XML:
          self::getResponse()->format = Response::FORMAT_XML;
          break;

        case Response::FORMAT_JSON:
          self::getResponse()->format = Response::FORMAT_JSON;
          break;

        case Response::FORMAT_JSONP:
          self::getResponse()->format = Response::FORMAT_JSONP;
          break;

        case Response::FORMAT_WEIXINXML:
          self::getResponse()->format = Response::FORMAT_WEIXINXML;
          break;

        default:
          self::getResponse()->format = Response::FORMAT_HTML;
          break;
      }
    }
    /**
     * 把金额转换成人民币大写
     * @method getCapitalCny
     * @since 0.0.1
     * @param {float} $price 以元为单位的金额数值
     * @return {string}
     */
    public static function getCapitalCny($price){
        if($price > 999999999999999){
            throw new ErrorException('Amount out of range');
        }
        $numbers = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
        $units_integer = ['元', '拾', '佰', '仟', '万', '拾', '佰', '仟', '亿', '拾', '佰', '仟', '万', '拾', '佰', '仟'];
        $units_decimal = ['角', '分'];
        $cny = [];
        $_n = 0;
        list($integers, $decimals) = explode('.', number_format($price, 2, '.', ''));
        foreach(array_reverse(str_split($integers)) as $i => $n)
        {
            if($i > 0 && !($i % 4) && in_array($cny[0], $units_integer))
            {
                array_shift($cny);
            }
            $_cny = $n > 0 || (!($i % 4) && $integers) ? ($n > 0 ? $n : null) . $units_integer[$i] : (!$_n && !$n ? null : $n);
            if($_cny !== null)
            {
                array_unshift($cny, $_cny);
            }
            $_n = $n;
        }
        if($decimals > 0)
        {
            foreach(str_split($decimals) as $i => $n)
            {
                if($n > 0)
                {
                    array_push($cny, $n . $units_decimal[$i]);
                }
            }
        }else{
            if($integers == 0)
            {
                array_push($cny, $numbers[0] . $units_integer[0]);
            }
        array_push($cny, '整');
        }
        return str_replace(array_keys($numbers), $numbers, implode('', $cny));
    }
    /**
     * 把金额转换成以元为单位
     * @method getYuans
     * @since 0.0.1
     * @param {number} $cents 以分为单位的金额
     * @param {boolean} [$float=false] 是否强制以浮点输出
     * @param {number} [$decimals=2] 规定多少位小数
     * @param {string} [$separator=''] 规定用作千位分隔符的字符串
     * @param {string} [$decimalpoint='.'] 规定用作小数点的字符串, 默认'.'
     * @return {number|float}
     */
    public static function getYuans($cents, $float = false, $decimals = 2, $separator = '', $decimalpoint = '.')
    {
        $yuans = $cents / 100;
        return $float ? number_format($yuans, $decimals, $decimalpoint, $separator) : $yuans;
    }

}
