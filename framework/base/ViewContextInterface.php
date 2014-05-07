<?php
/**
 * 翻译日期：20140507
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

/**
 * ViewContextInterface（视图上下文接口）是由想要支持相对视图名的类实现的接口
 *
 * 要实现[[getViewPath()]]方法以返回前缀是相对视图名的视图路径
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
interface ViewContextInterface
{
    /**
     * @return string 以相对视图名为前缀的视图路径
     */
    public function getViewPath();
}
