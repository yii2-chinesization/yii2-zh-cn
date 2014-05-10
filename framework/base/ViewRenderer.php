<?php
/**
 * 翻译日期：20140509
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

/**
 * ViewRenderer (视图渲染器) 是视图渲染器类的基类
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class ViewRenderer extends Component
{
    /**
     * 渲染一个视图文件
     *
     * 此方法在[[View]]尝试渲染一个视图时就被它调用
     * 子类必须实现此方法以渲染给定视图文件
     *
     * @param View $view 用来渲染视图文件的视图对象
     * @param string $file 视图文件
     * @param array $params 要传递给视图文件的参数
     * @return string 渲染结果
     */
    abstract public function render($view, $file, $params);
}
