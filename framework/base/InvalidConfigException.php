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
 * InvalidConfigException（无效配置异常）代表由不正确的对象配置所导致的异常
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InvalidConfigException extends Exception
{
    /**
     * @return string 用户友好的此异常名
     */
    public function getName()
    {
        return 'Invalid Configuration';
    }
}
