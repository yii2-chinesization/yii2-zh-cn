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
 * InvalidRouteException（无效路由异常）代表由无效路由引起的异常
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InvalidRouteException extends UserException
{
    /**
     * @return string 人性化的异常名
     */
    public function getName()
    {
        return 'Invalid Route';
    }
}
