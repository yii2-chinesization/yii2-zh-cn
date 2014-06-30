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
 * UnknownClassException（未知类异常）代表由使用未知类引发的异常
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UnknownClassException extends Exception
{
    /**
     * @return string 人性化异常名
     */
    public function getName()
    {
        return 'Unknown Class';
    }
}
