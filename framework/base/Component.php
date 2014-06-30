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

use Yii;

/**
 * Component（组件）是实现*属性*、*事件*和*行为*功能的基类
 *
 * 组件除了其父类[[Object]]实现了的*属性*功能外，还提供了*事件*和*行为*功能。
 *
 * 事件是"注入"自定义代码到已有代码某个位置的方法。如，评论对象在用户添加评论时可以触发"add"事件。
 * 我们可以编写自定义代码并把它附加到"add"事件，以便事件被触发时（如 评论将被添加），
 * 我们的自定义代码就被执行。
 *
 * 事件以名称区分，在定义它的类内部应该唯一，事件名是*区分大小写的*。
 *
 * 一个或多个 PHP 回调函数，称为*事件处理器（事件句柄）*，可以附加到事件上。
 * 你可以调用[[trigger()]]来引发事件。当事件被引发，事件处理器附加后就自动调用。
 *
 * 附加事件处理器到事件，请调用[[on()]]：
 *
 * ~~~
 * $post->on('update', function ($event) {
 *     // 发送电邮通知
 * });
 * ~~~
 *
 * 以上，一个匿名函数被附加到 $post 的"update"事件，你可以附加以下类型的事件处理器：
 *
 * - 匿名函数：`function ($event) { ... }`
 * - 对象方法：`[$object, 'handleAdd']`
 * - 静态类方法：`['Page', 'handleAdd']`
 * - 全局函数：`'handleAdd'`
 *
 * 事件处理器的识别标志如下：
 *
 * ~~~
 * function foo($event)
 * ~~~
 *
 * 其中`$event` 是[[Event]]对象，包括了关联到事件的参数。
 *
 * 你也可以在配置组件时附加处理器到事件。
 * 语法如下：
 *
 * ~~~
 * [
 *     'on add' => function ($event) { ... }
 * ]
 * ~~~
 *
 * 其中`on add`代表附加事件处理器到`add`事件。
 *
 * 有时，你想在附加处理器到事件时关联额外数据到事件处理器并在处理器被调用时访问这些数据。你可以这样做：
 *
 * ~~~
 * $post->on('update', function ($event) {
 *     // 数据可以通过 $event->data 访问
 * }, $data);
 * ~~~
 *
 * 行为是[[Behavior]]或其子类的实例。组件可以附加一个或多个行为。
 * 当行为被附加到组件时，它的公共属性和方法就能直接通过组件访问，如同组件拥有这些属性和方法似的。
 *
 *
 * 附加行为到组件，可以在[[behaviors()]]声明它，或显式调用[[attachBehavior]]。
 * 在[[behaviors()]]声明的行为会自动附加到对应的组件。
 *
 * 也可以在配置组件时附加行为，语法如下：
 *
 * ~~~
 * [
 *     'as tree' => [
 *         'class' => 'Tree',
 *     ],
 * ]
 * ~~~
 *
 * 其中`as tree`代表附加名为`tree`的行为，该数组将被传递给[[\Yii::createObject()]]以创建行为对象。
 *
 * @property Behavior[] $behaviors 附加到本组件的行为列表，本属性是只读的。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Component extends Object
{
    /**
     * @var array 已附加的行为处理器(event name => handlers)
     */
    private $_events = [];
    /**
     * @var Behavior[] 已附加的行为(behavior name => behavior)
     */
    private $_behaviors;

    /**
     * 返回组件属性值
     * 本方法将按以下顺序检查并采取相应行动：
     *
     *  - getter 定义属性：返回 getter 方法的结果
     *  - 行为的属性：返回行为属性值
     *
     * 不要直接调用本方法，因为它是 PHP 魔术方法，当执行`$value = $component->property;`时会隐式调用。
     * @param string $name 属性名
     * @return mixed 属性值或行为的属性值
     * @throws UnknownPropertyException 如果属性未定义
     * @throws InvalidCallException 如果属性只能写入。
     * @see __set()
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            // read property, e.g. getName()
            return $this->$getter();
        } else {
            // behavior property
            $this->ensureBehaviors();
            foreach ($this->_behaviors as $behavior) {
                if ($behavior->canGetProperty($name)) {
                    return $behavior->$name;
                }
            }
        }
        if (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * 设置组件的属性值
     * 本方法将按以下顺序检查并采取相应行动：
     *
     *  -  setter 定义属性：设置属性值
     *  - "on xyz"格式的事件：附加处理器到事件"xyz"
     *  - "as xyz"格式的行为：附加名为"xyz"的行为到组件
     *  - 行为属性：设置行为的属性值
     *
     * 不要直接调用本方法，因为它是 PHP 魔术方法，当执行`$component->property = $value;`时会隐式调用。
     * @param string $name 属性名或事件名
     * @param mixed $value 属性值
     * @throws UnknownPropertyException 如果属性未定义
     * @throws InvalidCallException 如果属性只能读取
     * @see __get()
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            // set property
            $this->$setter($value);

            return;
        } elseif (strncmp($name, 'on ', 3) === 0) {
            // on event: attach event handler
            $this->on(trim(substr($name, 3)), $value);

            return;
        } elseif (strncmp($name, 'as ', 3) === 0) {
            // as behavior: attach behavior
            $name = trim(substr($name, 3));
            $this->attachBehavior($name, $value instanceof Behavior ? $value : Yii::createObject($value));

            return;
        } else {
            // behavior property
            $this->ensureBehaviors();
            foreach ($this->_behaviors as $behavior) {
                if ($behavior->canSetProperty($name)) {
                    $behavior->$name = $value;

                    return;
                }
            }
        }
        if (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * 检查属性值是否为 null
     * 本方法将按以下顺序检查并采取相应行动：
     *
     *  - setter 定义的属性：返回属性值是否为 null
     *  - 行为的属性：返回属性值是否为 null
     *
     * 不要直接调用本方法，因为它是 PHP 魔术方法，当执行`isset($component->property);`时会隐式调用。
     * @param string $name 属性名或事件名
     * @return boolean 指定属性是否为 null
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            // behavior property
            $this->ensureBehaviors();
            foreach ($this->_behaviors as $behavior) {
                if ($behavior->canGetProperty($name)) {
                    return $behavior->$name !== null;
                }
            }
        }

        return false;
    }

    /**
     * 设置组件属性为 null
     * 本方法将按以下顺序检查并采取相应行动：
     *
     *  -  setter 定义的属性：设置属性值为 null
     *  - 行为的属性：设置属性值为 null
     *
     * 不要直接调用本方法，因为它是 PHP 魔术方法，当执行`unset($component->property);`时会隐式调用。
     * @param string $name 属性名
     * @throws InvalidCallException 如果属性是只读的
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);

            return;
        } else {
            // behavior property
            $this->ensureBehaviors();
            foreach ($this->_behaviors as $behavior) {
                if ($behavior->canSetProperty($name)) {
                    $behavior->$name = null;

                    return;
                }
            }
        }
        throw new InvalidCallException('Unsetting an unknown or read-only property: ' . get_class($this) . '::' . $name);
    }

    /**
     * 调用非类方法的指定方法
     *
     * 此方法将检查任何已附加行为是否有指定方法，且如果是可用的就执行它。
     *
     * 不要直接调用本方法，因为它是 PHP 魔术方法，当调用未知方法时会隐式调用。
     * @param string $name 方法名
     * @param array $params 方法参数
     * @return mixed 方法的返回值
     * @throws UnknownMethodException 调用未知方法时
     */
    public function __call($name, $params)
    {
        $this->ensureBehaviors();
        foreach ($this->_behaviors as $object) {
            if ($object->hasMethod($name)) {
                return call_user_func_array([$object, $name], $params);
            }
        }

        throw new UnknownMethodException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

    /**
     * 通过克隆已有对象来创建对象后，本方法将被调用。
     * 它移除了所有行为，因为行为是附加到旧对象的。
     */
    public function __clone()
    {
        $this->_events = [];
        $this->_behaviors = null;
    }

    /**
     * 返回一个值，指示该组件的属性是否已定义。
     * 以下情况说明属性已定义：
     *
     * - 类有 getter 或 setter 方法关联到特定名(这种情况，属性名是不区分大小写的)；
     * - 类有符合特定名的成员变量(当`$checkVars`为 true)；
     * - 已附加的行为有给定名的属性(当`$checkBehaviors`为 true)。
     *
     * @param string $name 属性名
     * @param boolean $checkVars 是否将成员变量视为属性
     * @param boolean $checkBehaviors 是否将行为属性视为组件属性
     * @return boolean 该属性是否已定义
     * @see canGetProperty()
     * @see canSetProperty()
     */
    public function hasProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return $this->canGetProperty($name, $checkVars, $checkBehaviors) || $this->canSetProperty($name, false, $checkBehaviors);
    }

    /**
     * 返回一个值，指示属性是否可读
     * 一个属性可读，如果：
     *
     * - 类有 getter 方法关联到特定名(这种情况，属性名是不区分大小写的)；
     * - 类有符合特定名的成员变量(当`$checkVars`为 true)；
     * - 已附加的行为有给定名的可读属性(当`$checkBehaviors`为 true)。
     *
     * @param string $name 属性名
     * @param boolean $checkVars 是否把成员变量视为属性
     * @param boolean $checkBehaviors 是否把行为属性视为组件属性
     * @return boolean 该属性是否可读
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name)) {
            return true;
        } elseif ($checkBehaviors) {
            $this->ensureBehaviors();
            foreach ($this->_behaviors as $behavior) {
                if ($behavior->canGetProperty($name, $checkVars)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 返回一个值，表明属性是否能设置
     * 属性可写，如果：
     *
     * - 类有 setter 方法关联到特定名(这种情况，属性名是不区分大小写的)；
     * - 类有符合特定名的成员变量(当`$checkVars`为 true)；
     * - 已附加的行为有给定名的可写属性(当`$checkBehaviors`为 true)。
     *
     * @param string $name 属性名
     * @param boolean $checkVars 是否把成员变量视为属性
     * @param boolean $checkBehaviors 是否把行为属性视为组件属性
     * @return boolean 该属性是否可写
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (method_exists($this, 'set' . $name) || $checkVars && property_exists($this, $name)) {
            return true;
        } elseif ($checkBehaviors) {
            $this->ensureBehaviors();
            foreach ($this->_behaviors as $behavior) {
                if ($behavior->canSetProperty($name, $checkVars)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 返回一个值，表明方法是否已定义
     * 方法被定义，如果：
     *
     * - 该类有一个具有指定名称的方法；
     * - 一个附加行为具有给定名称的方法(当`$checkBehaviors`为 true)。
     *
     * @param string $name 方法名
     * @param boolean $checkBehaviors 是否把行为的方法视为此组件的方法
     * @return boolean 方法是否已定义
     */
    public function hasMethod($name, $checkBehaviors = true)
    {
        if (method_exists($this, $name)) {
            return true;
        } elseif ($checkBehaviors) {
            $this->ensureBehaviors();
            foreach ($this->_behaviors as $behavior) {
                if ($behavior->hasMethod($name)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 返回组件要使用的行为列表
     *
     * 子类可以覆写此方法以指定它们想要使用的行为。
     *
     * 本方法的返回值应该是行为对象数组或以行为名索引的配置数组。
     * 行为配置可以是指定行为类的字符串或以下结构的数组：
     *
     * ~~~
     * 'behaviorName' => [
     *     'class' => 'BehaviorClass',
     *     'property1' => 'value1',
     *     'property2' => 'value2',
     * ]
     * ~~~
     *
     * 注意行为类必须继承自[[Behavior]]，行为名可以是字符串或整型，如果是前者，它们标识了唯一的行为，
     * 如果是后者，相应的行为是匿名的且它们的属性和方法通过组件*不可访问*(然而行为仍能响应组件的事件)。
     *
     * 此方法声明的行为将自动附加到组件(按需附加)。
     *
     * @return array 行为配置
     */
    public function behaviors()
    {
        return [];
    }

    /**
     * 返回一个值，表明是否有处理器被附加到指定的事件上
     * @param string $name 事件名
     * @return boolean 是否有任何处理器附加到事件上
     */
    public function hasEventHandlers($name)
    {
        $this->ensureBehaviors();

        return !empty($this->_events[$name]) || Event::hasHandlers($this, $name);
    }

    /**
     * 附加事件处理器到事件
     *
     * 事件处理器必须是有效的 PHP 回调函数，以下是一些示例：
     *
     * ~~~
     * function ($event) { ... }         // 匿名函数
     * [$object, 'handleClick']          // $object->handleClick()
     * ['Page', 'handleClick']           // Page::handleClick()
     * 'handleClick'                     // handleClick()全局函数
     * ~~~
     *
     * 事件处理器必须的定义必须用以下标识符：
     *
     * ~~~
     * function ($event)
     * ~~~
     *
     * 其中`$event` 是[[Event]]对象，包括了关联到事件的参数
     *
     * @param string $name 事件名
     * @param callable $handler 事件处理器
     * @param mixed $data 当事件被触发时传递到事件处理器的数据
     * 当事件处理器被调用时，此数据可用[[Event::data]]访问。
     * @param boolean $append 是否追加新的事件处理器到已有处理器列表的末尾，
     * 如果为 false ，新处理器将插入已存在处理器列表的开头。
     * @see off()
     */
    public function on($name, $handler, $data = null, $append = true)
    {
        $this->ensureBehaviors();
        if ($append || empty($this->_events[$name])) {
            $this->_events[$name][] = [$handler, $data];
        } else {
            array_unshift($this->_events[$name], [$handler, $data]);
        }
    }

    /**
     * 从组件移除一个现有的事件处理器
     * 此方法和[[on()]]是相反的
     * @param string $name 事件名
     * @param callable $handler 拟移除事件处理器
     * 如果是 null ，所有附加到指定事件的处理器全部移除
     * @return boolean 如果找到并移除一个处理器
     * @see on()
     */
    public function off($name, $handler = null)
    {
        $this->ensureBehaviors();
        if (empty($this->_events[$name])) {
            return false;
        }
        if ($handler === null) {
            unset($this->_events[$name]);
            return true;
        } else {
            $removed = false;
            foreach ($this->_events[$name] as $i => $event) {
                if ($event[0] === $handler) {
                    unset($this->_events[$name][$i]);
                    $removed = true;
                }
            }
            if ($removed) {
                $this->_events[$name] = array_values($this->_events[$name]);
            }

            return $removed;
        }
    }

    /**
     * 触发事件
     * 该方法代表着一个事件的发生，它为事件调用所有已附加的处理器，包括类级处理器。
     * @param string $name 事件名
     * @param Event $event 事件参数，如果未设置，缺省[[Event]]对象将被创建
     */
    public function trigger($name, Event $event = null)
    {
        $this->ensureBehaviors();
        if (!empty($this->_events[$name])) {
            if ($event === null) {
                $event = new Event;
            }
            if ($event->sender === null) {
                $event->sender = $this;
            }
            $event->handled = false;
            $event->name = $name;
            foreach ($this->_events[$name] as $handler) {
                $event->data = $handler[1];
                call_user_func($handler[0], $event);
                // stop further handling if the event is handled
                if ($event->handled) {
                    return;
                }
            }
        }
        // invoke class-level attached handlers
        Event::trigger($this, $name, $event);
    }

    /**
     * 返回指定行为对象
     * @param string $name 行为名
     * @return Behavior 行为对象，如果行为不存在返回 null
     */
    public function getBehavior($name)
    {
        $this->ensureBehaviors();

        return isset($this->_behaviors[$name]) ? $this->_behaviors[$name] : null;
    }

    /**
     * 返回组件已附加的所有行为
     * @return Behavior[] 已附加到该组件的行为列表
     */
    public function getBehaviors()
    {
        $this->ensureBehaviors();

        return $this->_behaviors;
    }

    /**
     * 附加一个行为到此组件
     * 此方法将基于给定配置创建行为对象，然后，行为对象将通过调用[[Behavior::attach()]]方法附加到这个组件
     * @param string $name 行为名
     * @param string|array|Behavior $behavior 行为配置，可以是以下之一：
     *
     *  - [[Behavior]]对象
     *  - 指定行为类的字符串
     *  - 对象配置数组，传递到[[Yii::createObject()]]以创建行为对象
     *
     * @return Behavior 行为对象
     * @see detachBehavior()
     */
    public function attachBehavior($name, $behavior)
    {
        $this->ensureBehaviors();

        return $this->attachBehaviorInternal($name, $behavior);
    }

    /**
     * 附加行为列表到组件
     * 每个行为由其名称索引，是[[Behavior]]对象、指明行为类的字符串或用于创建行为的配置数组。
     * @param array $behaviors 要附加到组件的行为列表
     * @see attachBehavior()
     */
    public function attachBehaviors($behaviors)
    {
        $this->ensureBehaviors();
        foreach ($behaviors as $name => $behavior) {
            $this->attachBehaviorInternal($name, $behavior);
        }
    }

    /**
     * 从组件移除一个行为
     * 行为的[[Behavior::detach()]]方法将被调用
     * @param string $name 行为名
     * @return Behavior 被移除的行为，如果行为不存在返回 Null
     */
    public function detachBehavior($name)
    {
        $this->ensureBehaviors();
        if (isset($this->_behaviors[$name])) {
            $behavior = $this->_behaviors[$name];
            unset($this->_behaviors[$name]);
            $behavior->detach();

            return $behavior;
        } else {
            return null;
        }
    }

    /**
     * 从组件移除全部行为
     */
    public function detachBehaviors()
    {
        $this->ensureBehaviors();
        foreach ($this->_behaviors as $name => $behavior) {
            $this->detachBehavior($name);
        }
    }

    /**
     * 确保声明在[[behaviors()]]的行为被附加到组件了
     */
    public function ensureBehaviors()
    {
        if ($this->_behaviors === null) {
            $this->_behaviors = [];
            foreach ($this->behaviors() as $name => $behavior) {
                $this->attachBehaviorInternal($name, $behavior);
            }
        }
    }

    /**
     * 附加行为到组件
     * @param string $name 行为名
     * @param string|array|Behavior $behavior 要附加的行为
     * @return Behavior 已附加的行为
     */
    private function attachBehaviorInternal($name, $behavior)
    {
        if (!($behavior instanceof Behavior)) {
            $behavior = Yii::createObject($behavior);
        }
        if (isset($this->_behaviors[$name])) {
            $this->_behaviors[$name]->detach();
        }
        $behavior->attach($this);

        return $this->_behaviors[$name] = $behavior;
    }
}
