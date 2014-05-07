<?php
/**
 * 翻译日期：20140506
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

/**
 * Behavior（行为）是所有行为类的基类
 *
 * 行为可用来增强现有组件的功能而无需修改组件代码。
 * 尤其是，行为能"注入"它自己的方法和属性到组件并让它们能通过组件直接访问。
 * 行为也能响应在组件中被触发的事件来拦截正常的代码执行。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Behavior extends \yii\base\Object
{
    /**
     * @var Component 拥有行为的组件
     */
    public $owner;

    /**
     * 为[[owner]]组件的事件声明事件处理器
     *
     * 子类可以覆写此方法以声明什么样的 PHP 回调函数才能附加到[[owner]]组件的事件。
     *
     * 当行为附加到拥有它的组件时，回调函数也被附加到[[owner]]组件的事件；
     * 当行为从组件移除时，它们也被分离出事件。
     *
     * 回调函数可以是以下之一：
     *
     * - 此行为的方法：`'handleClick'`, 等价于`[$this, 'handleClick']`
     * - 对象方法：`[$object, 'handleClick']`
     * - 静态方法：`['Page', 'handleClick']`
     * - 匿名函数：`function ($event) { ... }`
     *
     * 以下是示例：
     *
     * ~~~
     * [
     *     Model::EVENT_BEFORE_VALIDATE => 'myBeforeValidate',
     *     Model::EVENT_AFTER_VALIDATE => 'myAfterValidate',
     * ]
     * ~~~
     *
     * @return array 事件(数组键)和对应的事件处理器方法(数组值)
     */
    public function events()
    {
        return [];
    }

    /**
     * 附加行为对象到组件
     * 默认实现将设置[[owner]]属性并附加[[events]]声明的事件处理器
     * 如果你覆写此方法请确保你调用了父类的实现
     * @param Component $owner 此行为要附加的目标组件
     */
    public function attach($owner)
    {
        $this->owner = $owner;
        foreach ($this->events() as $event => $handler) {
            $owner->on($event, is_string($handler) ? [$this, $handler] : $handler);
        }
    }

    /**
     * 从组件移除行为对象
     * 默认的实现将 unset [[owner]]属性并移除在[[events]]声明的所有事件处理器
     * 如果你要覆写此方法请确保你调用了父类实现
     */
    public function detach()
    {
        if ($this->owner) {
            foreach ($this->events() as $event => $handler) {
                $this->owner->off($event, is_string($handler) ? [$this, $handler] : $handler);
            }
            $this->owner = null;
        }
    }
}
