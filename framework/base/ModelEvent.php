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
 * ModelEvent（模型事件类）
 *
 * 模型事件代表模型事件所需的参数
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ModelEvent extends Event
{
    /**
     * @var boolean 模型是否处于有效状态，缺省为 true ，如果模型通过验证或某些校对那么它就处于有效状态。
     */
    public $isValid = true;
}
