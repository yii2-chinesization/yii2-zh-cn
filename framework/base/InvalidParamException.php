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
 * InvalidParamException（无效参数异常）代表由传递无效参数给方法所引起的异常
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InvalidParamException extends \BadMethodCallException
{
    /**
     * @return string 人性化的异常名称
     */
    public function getName()
    {
        return 'Invalid Parameter';
    }
}
