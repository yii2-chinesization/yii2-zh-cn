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
 * ViewEvent（视图事件）代表被[[View]]组件触发的事件
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ViewEvent extends Event
{
    /**
     * @var string [[View::renderFile()]]的渲染结果
     * 事件处理器可以修改此属性，而修改后的输出将用[[View::renderFile()]]返回。
     * 本属性只能用于[[View::EVENT_AFTER_RENDER]]事件
     */
    public $output;
    /**
     * @var boolean 是否继续渲染视图文件，[[View::EVENT_BEFORE_RENDER]]的事件处理器
     * 可以设置本属性以决定是否继续渲染当前视图文件
     */
    public $isValid = true;
}
