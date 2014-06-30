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
 * NotSupportedException（未被框架支持异常）代表由访问未受支持的功能所引起的异常
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class NotSupportedException extends Exception
{
    /**
     * @return string 人性化异常名
     */
    public function getName()
    {
        return 'Not Supported';
    }
}
