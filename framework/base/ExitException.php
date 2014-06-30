<?php
/**
 * 翻译日期：20140510
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

/**
 * ExitException（退出异常）代表一个应用的正常终止
 *
 * 不要捕获 ExitException ， Yii 将以优雅地终止应用来处理这个异常。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ExitException extends \Exception
{
    /**
     * @var integer 退出状态码
     */
    public $statusCode;


    /**
     * 构造函数
     * @param integer $status 退出状态码
     * @param string $message 错误消息
     * @param integer $code 错误代码
     * @param \Exception $previous 异常链上之前的异常
     */
    public function __construct($status = 0, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $status;
        parent::__construct($message, $code, $previous);
    }
}
