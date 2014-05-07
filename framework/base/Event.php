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
 * 事件是所有事件类的基类
 *
 * 它封装了关联到事件的参数
 * [[sender]]属性介绍了谁引发事件
 * [[handled]]属性指明事件是否已处理
 * 如果事件处理器把[[handled]]设置为 true ，剩下未调用的处理器将不再被调用来处理此事件。
 *
 * 此外，当附加一个事件处理器时，其它数据可通过[[data]]属性传递并在事件处理器调用时可用。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Event extends Object
{
    /**
     * @var string 事件名，本属性通过[[Component::trigger()]] 和 [[trigger()]]设置
     * 事件处理器可以使用该属性来检查它处理的是哪个事件
     */
    public $name;
    /**
     * @var object 事件发送者，如果未设置，此属性将被设置为被调用了"trigger()"方法的对象
     * 该属性当此事件是类级事件时也可以是 `null` ，类级事件在静态环境被触发
     */
    public $sender;
    /**
     * @var boolean 事件是否已处理，默认为 false
     * 当处理器设置它为 true 时，事件处理将停止并忽略剩余未调用的事件处理器
     */
    public $handled = false;
    /**
     * @var mixed 当附加事件处理器时，该数据被传递给[[Component::on()]]
     * 注意这个变量根据当前执行的事件处理器不同而变化
     */
    public $data;

    private static $_events = [];

    /**
     * 附加事件处理器到类级事件
     *
     * 当类级事件被触发，附加到该类和所有子类的事件处理器就被调用
     *
     * 例如，以下代码附加了一个事件处理器到`ActiveRecord`的`afterInsert` 事件：
     *
     * ~~~
     * Event::on(ActiveRecord::className(), ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
     *     Yii::trace(get_class($event->sender) . ' is inserted.');
     * });
     * ~~~
     *
     * 处理器将被 *每一个* 成功的 AR 插入调用
     *
     * 有关如何声明一个事件处理器的更多细节请参阅[[Component::on()]]
     *
     * @param string $class 事件要附加的那个类的完全限定名
     * @param string $name 事件名
     * @param callable $handler 事件处理器
     * @param mixed $data 当事件被触发时传递给事件处理器的数据
     * 当事件处理器被调用，该数据可通过[[Event::data]]访问
     * @param boolean $append 是否在现有处理器列表末尾追加新的事件处理器，如果是 false ，新处理器将被插入列表开头
     * @see off()
     */
    public static function on($class, $name, $handler, $data = null, $append = true)
    {
        $class = ltrim($class, '\\');
        if ($append || empty(self::$_events[$name][$class])) {
            self::$_events[$name][$class][] = [$handler, $data];
        } else {
            array_unshift(self::$_events[$name][$class], [$handler, $data]);
        }
    }

    /**
     * 从类级事件移除事件处理器
     *
     * 此方法和[[on()]]正相反
     *
     * @param string $class 事件处理器被附加到的类的完全限定名
     * @param string $name 事件名
     * @param callable $handler 要移除的事件处理器
     * 如果是 null ，所有附加到指定事件的处理器都被移除
     * @return boolean 处理器是否查找到并被移除掉
     * @see on()
     */
    public static function off($class, $name, $handler = null)
    {
        $class = ltrim($class, '\\');
        if (empty(self::$_events[$name][$class])) {
            return false;
        }
        if ($handler === null) {
            unset(self::$_events[$name][$class]);
            return true;
        } else {
            $removed = false;
            foreach (self::$_events[$name][$class] as $i => $event) {
                if ($event[0] === $handler) {
                    unset(self::$_events[$name][$class][$i]);
                    $removed = true;
                }
            }
            if ($removed) {
                self::$_events[$name][$class] = array_values(self::$_events[$name][$class]);
            }

            return $removed;
        }
    }

    /**
     * 返回一个值表明是否有任何处理器已经被附加到指定的类级事件
     * 注意此方法也检查所有父类以查看是否有任何处理器附加到了指定的事件上
     * @param string|object $class 对象或指定类级事件的完全限定类名
     * @param string $name 事件名
     * @return boolean 是否有任何处理器附加到了指定事件
     */
    public static function hasHandlers($class, $name)
    {
        if (empty(self::$_events[$name])) {
            return false;
        }
        if (is_object($class)) {
            $class = get_class($class);
        } else {
            $class = ltrim($class, '\\');
        }
        do {
            if (!empty(self::$_events[$name][$class])) {
                return true;
            }
        } while (($class = get_parent_class($class)) !== false);

        return false;
    }

    /**
     * 触发类级事件
     * 本方法将引发被附加到特定类或其所有父类指定事件的事件处理器的调用
     * @param string|object $class 对象或特定类级事件的完全限定类名
     * @param string $name 事件名
     * @param Event $event 事件参数，如果未设置，缺省的[[Event]]对象将被创建
     */
    public static function trigger($class, $name, $event = null)
    {
        if (empty(self::$_events[$name])) {
            return;
        }
        if ($event === null) {
            $event = new static;
        }
        $event->handled = false;
        $event->name = $name;

        if (is_object($class)) {
            if ($event->sender === null) {
                $event->sender = $class;
            }
            $class = get_class($class);
        } else {
            $class = ltrim($class, '\\');
        }
        do {
            if (!empty(self::$_events[$name][$class])) {
                foreach (self::$_events[$name][$class] as $handler) {
                    $event->data = $handler[1];
                    call_user_func($handler[0], $event);
                    if ($event->handled) {
                        return;
                    }
                }
            }
        } while (($class = get_parent_class($class)) !== false);
    }
}
