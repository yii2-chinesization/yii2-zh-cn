<?php
/**
 * 翻译日期：20140505
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;

/**
 * 对象是实现*属性*功能的基类
 *
 * 属性由 getter 方法 (如`getLabel`)，和/或 setter 方法 (如`setLabel`)定义。
 * 如，以下 getter 和 setter 方法定义了一个名为`label`的属性：
 *
 * ~~~
 * private $_label;
 *
 * public function getLabel()
 * {
 *     return $this->_label;
 * }
 *
 * public function setLabel($value)
 * {
 *     $this->_label = $value;
 * }
 * ~~~
 *
 * 属性名是*不区分大小写的*。
 *
 * 属性可以像对象的成员变量一样访问，读取或写入一个属性将导致相应 getter 或 setter 方法的调用。
 * 如：
 *
 * ~~~
 * // 等价于 $label = $object->getLabel();
 * $label = $object->label;
 * // 等价于 $object->setLabel('abc');
 * $object->label = 'abc';
 * ~~~
 *
 * 如果属性只有 getter 方法，没有 setter 方法，则认为它是*只读的*。
 * 这种情况下，尝试修改属性值将导致一个异常。
 *
 * 可以调用[[hasProperty()]], [[canGetProperty()]] 和 [[canSetProperty()]] 来检查属性是否存在。
 *
 * 除了属性功能，对象还引入了重要的对象初始化生命周期。
 * 尤其是，创建对象或其子类的新实例将按顺序牵涉以下生命周期：
 *
 * 1. 类的构造函数被调用；
 * 2. 对象属性根据给定配置初始化；
 * 3. `init()` 方法被调用。
 *
 * 以上的步骤 2 和 3 都发生在类的构造函数结束时。推荐你在`init()` 方法执行对象初始化，
 * 因为这个阶段的对象配置已经被应用了。
 *
 * 为了确保以上生命周期，如果对象的子类需要覆写构造函数，必须如下这样写：
 *
 * ~~~
 * public function __construct($param1, $param2, ..., $config = [])
 * {
 *     ...
 *     parent::__construct($config);
 * }
 * ~~~
 *
 * 即，`$config` 参数 (缺省为`[]`) 要声明为构造函数的最后一个参数，
 * 且要在构造函数结束时调用父类的构造函数。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Object
{
    /**
     * @return string 类的完全限定名
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * 构造函数
     * 默认执行两件事：
     *
     * - 用给定的配置`$config`初始化对象
     * - 调用[[init()]]
     *
     * 如果本方法被子类覆写，建议：
     *
     * - 构造函数的最后一个参数是配置数组，如这里的`$config`
     * - 在构造函数结束时调用父类执行
     *
     * @param array $config 名值对，用于初始化对象属性
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
        $this->init();
    }

    /**
     * 初始化对象
     * 本方法在对象以给定配置初始化后于构造函数结束时调用
     */
    public function init()
    {
    }

    /**
     * 返回对象属性值
     *
     * 不要直接调用此方法，因为它是一个 PHP 魔术方法，
     * 当执行`$value = $object->property;`时将会隐式调用。
     *
     * @param string $name 属性名
     * @return mixed 属性值
     * @throws UnknownPropertyException 如果属性未定义
     * @throws InvalidCallException 如果是只写属性
     * @see __set()
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * 设置对象属性值
     *
     * 不要直接调用此方法，因为它是一个 PHP 魔术方法，
     * 当执行`$object->property = $value;`时将会隐式调用。
     *
     * @param string $name 属性名或事件名
     * @param mixed $value 属性值
     * @throws UnknownPropertyException 如果属性未定义
     * @throws InvalidCallException 如果是只读属性
     * @see __get()
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * 检查指定属性是否设置(not null)
     *
     * 不要直接调用此方法，因为它是一个 PHP 魔术方法，
     * 当执行`isset($object->property)`时将会隐式调用。
     *
     * 注意如果属性未定义，将返回 false
     *
     * @param string $name 属性名或事件名
     * @return boolean 指定属性是否设置(not null)
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }

    /**
     * 设置对象属性为 null
     *
     * 不要直接调用此方法，因为它是一个 PHP 魔术方法，
     * 当执行`unset($object->property)`时将会隐式调用。
     *
     * 注意如果属性未定义，本方法将不做任何事。
     * 如果属性是只读的，它将抛出异常。
     * @param string $name 属性名
     * @throws InvalidCallException 如果属性是只读的
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * 调用指定的非类方法的方法
     *
     * 不要直接调用此方法，因为它是一个 PHP 魔术方法，
     * 当执行未知方法时将会隐式调用。
     * @param string $name 方法名
     * @param array $params 方法参数
     * @throws UnknownMethodException 当调用未知方法
     * @return mixed 方法返回值
     */
    public function __call($name, $params)
    {
        throw new UnknownMethodException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

    /**
     * 返回表明属性是否已定义的值
     * 如果以下这样说明属性已定义：
     *
     * - 类有 getter 或 setter 方法关联到指定名(这种情况下属性名不区分大小写)；
     * - 类有指定名的成员变量(当`$checkVars` 是 true)；
     *
     * @param string $name 属性名
     * @param boolean $checkVars 是否把成员变量看作属性
     * @return boolean 该属性是否已定义
     * @see canGetProperty()
     * @see canSetProperty()
     */
    public function hasProperty($name, $checkVars = true)
    {
        return $this->canGetProperty($name, $checkVars) || $this->canSetProperty($name, false);
    }

    /**
     * 返回值表明属性是否能读取
     * 符合以下情况之一说明属性是可读的：
     *
     * - t类有 getter 方法关联到指定名(这种情况下属性名不区分大小写)；
     * - 类有指定名的成员变量(当`$checkVars` 是 true)；
     *
     * @param string $name 属性名
     * @param boolean $checkVars 是否把成员变量看作属性
     * @return boolean whether 属性是否可读
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name);
    }

    /**
     * 返回值表明属性是否能被设置
     * 符合以下情况之一说明属性是可写的：
     *
     * - 类有 setter 方法关联到指定名(这种情况下属性名不区分大小写)；
     * - 类有指定名的成员变量(当`$checkVars` 是 true)；
     *
     * @param string $name 属性名
     * @param boolean $checkVars 是否把成员变量看作属性
     * @return boolean 属性是否能写入
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'set' . $name) || $checkVars && property_exists($this, $name);
    }

    /**
     * 返回值表明方法是否已定义
     *
     * 默认实现是调用 php 函数`method_exists()`
     * 当你实现了 php 魔术方法`__call()`后，你可以覆写本方法
     * @param string $name 方法名
     * @return boolean 方法名是否已定义
     */
    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }
}
