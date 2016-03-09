<?php

namespace common\models\base;

/**
 * 错误信息类
 * Class Errorable
 * @package common\models\order\base
 * @author Lyl <inspii@me.com>
 * @since 2.0
 */
class Errorable
{
    /**
     * 错误级别
     */
    const ERROR_LEVEL_USER = 0; //用户操作不正确等
    const ERROR_LEVEL_ILLEGAL = 1; //非法操作
    const ERROR_LEVEL_DATA = 5; //数据操作错误
    const ERROR_LEVEL_SERVICE = 6; //业务处理错误

    /**
     * 错误代码范围
     */
    const ERROR_CODE_RANGE_SYS = [1, 9999]; //系统错误码
    const ERROR_CODE_RANGE_BUSINESS = [10000, 99999]; //业务错误码

    /**
     * 系统错误代码
     */
    const CODE_NONE = 0;
    const CODE_DB_CONNECTION = 10;

    const CODE_DB_CREATE = 100;
    const CODE_DB_UPDATE = 102;
    const CODE_DB_DELETE  = 103;
    const CODE_DB_GET = 104;

    const CODE_SERVICE_OPEN = 1000;
    const CODE_SERVICE_UPDATE = 1001;
    const CODE_SERVICE_DELETE = 1002;
    const CODE_SERVICE_GET = 1003;

    /**
     * 错误自动Log 开启关闭
     */
    protected $_auto_log_on = true;

    /**
     * Log文件名
     */
    protected $_log_file_name = 'errors/default.log';

    /**
     * 静态错误自动Log 开启关闭
     */
    protected static $_auto_log_on_s = true;

    /**
     * 静态错误Log文件名
     */
    protected static $_log_file_name_s = 'errors/default_s.log';

    /**
     * 出错信息
     * @var
     */
    protected $_errors;

    /**
     * 静态错误信息
     * @var
     */
    protected static $_errors_s;

    /**
     * 返回最新出错数据
     * @return mixed
     */
    public function getError()
    {
        return !empty($this->_errors) ? end($this->_errors) : null;
    }

    /**
     * 获取最新出错消息
     * @return string
     */
    public function getErrorMessage()
    {
        $error = !empty($this->_errors) ? end($this->_errors) : null;
        $msg = isset($error['msg']) ? $error['msg'] : '';

        return $msg;
    }

    /**
     * 获取所有出错数据
     * @return mixed
     */
    public function getAllErrors()
    {
        return $this->_errors;
    }

    /**
     * 返回最新静态错误
     * @return mixed
     */
    public function getStaticError()
    {
        return !empty($this->_errors) ?  end(static::$_errors_s) : null;
    }

    /**
     * 获取最新静态错误消息
     * @return string
     */
    public function getStaticErrorMessage()
    {
        $error = !empty($this->_errors) ? end(static::$_errors_s) : null;
        $msg = isset($error['msg']) ? $error['msg'] : '';

        return $msg;
    }

    /**
     * 获取所有静态错误数据
     * @return mixed
     */
    public function getStaticAllErrors()
    {
        return static::$_errors_s;
    }

    /**
     * 添加错误
     * @param $level
     * @param $code
     * @param $msg
     * @param $detail
     */
    protected function _pushError($level, $code, $msg, $detail)
    {
        $this->_errors[] = ['code'=>$code, 'msg'=>$msg, 'detail'=>$detail];
        $this->_logError($level, $code, $msg, $detail);
    }

    /**
     * 取出并去除最后一个错误
     * @return mixed
     */
    protected function _popError()
    {
        return array_pop($this->_errors);
    }

    /**
     * 清空错误数据
     */
    protected function _flushErrors()
    {
        $this->_errors = null;
    }

    /**
     * 添加一个静态错误
     * @param $level
     * @param $code
     * @param $msg
     * @param $detail
     */
    protected static function _pushStaticError($level, $code, $msg, $detail)
    {
        static::$_errors_s[] = ['code'=>$code, 'msg'=>$msg, 'detail'=>$detail];
        static::_logStaticError($level, $code, $msg, $detail);
    }

    /**
     * 取出并去除最后一个静态错误
     * @return mixed
     */
    protected static function _popStaticError()
    {
        return array_pop(static::$_errors_s);
    }

    /**
     * 清空静态错误
     */
    protected static function _flushStaticErrors()
    {
        static::$_errors_s = null;
    }

    /**
     * Log错误信息
     * @param $level
     * @param $code
     * @param $msg
     * @param $detail
     */
    protected function _logError($level, $code, $msg, $detail)
    {
        if(!$this->_auto_log_on) return;

        $error_record = date('Y-m-d H:i:s') . ' '
            . '['. $level . '] '
            . '['. $code . '] '
            . $msg. ': '
            . json_encode($detail). '. '
            . PHP_EOL;

        file_put_contents($this->_log_file_name, $error_record, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log静态错误信息
     * @param $level
     * @param $code
     * @param $msg
     * @param $detail
     */
    protected static function _logStaticError($level, $code, $msg, $detail)
    {
        if(static::$_auto_log_on_s) return;

        $error_record = date('Y-m-d H:i:s') . ' '
            . '['. $level . '] '
            . '['. $code . '] '
            . $msg. ': '
            . json_encode($detail). '. '
            . PHP_EOL;

        file_put_contents(static::$_log_file_name_s, $error_record, FILE_APPEND | LOCK_EX);
    }
}