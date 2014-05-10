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
 * UnknownPropertyException（未知属性异常）代表由访问未知对象属性所引起的异常
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UnknownPropertyException extends Exception
{
    /**
     * @return string 人性化异常名
     */
    public function getName()
    {
        return 'Unknown Property';
    }
}
