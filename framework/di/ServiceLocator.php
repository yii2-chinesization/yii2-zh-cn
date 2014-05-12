<?php
/**
 * 翻译日期：20140510
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\di;

use Yii;
use Closure;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * ServiceLocator（服务定位器类）实现了[service locator](http://en.wikipedia.org/wiki/Service_locator_pattern) 设计模式
 *
 * 要使用服务定位器，首先你必须调用[[set()]]或[[setComponents()]]注册组件 ID 、相应的组件定义和定位器。
 * 然后你可以调用[[get()]]以指定 ID 检索组件，定位器将根据定义自动实例化和配置组件。
 *
 * 例如：
 *
 * ```php
 * $locator = new \yii\di\ServiceLocator;
 * $locator->setComponents([
 *     'db' => [
 *         'class' => 'yii\db\Connection',
 *         'dsn' => 'sqlite:path/to/file.db',
 *     ],
 *     'cache' => [
 *         'class' => 'yii\caching\DbCache',
 *         'db' => 'db',
 *     ],
 * ]);
 *
 * $db = $locator->get('db');  // or $locator->db
 * $cache = $locator->get('cache');  // or $locator->cache
 * ```
 *
 * 因为[[\yii\base\Module]]继承自 ServiceLocator ，模块和应用都是服务定位器。
 *
 * @property array $components 组件定义列表或已加载的组件实例列表(ID => 定义或实例)
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ServiceLocator extends Component
{
    /**
     * @var array 以 ID 索引的共享组件实例
     */
    private $_components = [];
    /**
     * @var array 以 ID 为索引的组件定义
     */
    private $_definitions = [];

    /**
     * Getter 魔术方法
     * 此方法可覆写以支持如读取属性般访问组件
     * @param string $name 组件或属性名
     * @return mixed 指定属性值
     */
    public function __get($name)
    {
        if ($this->has($name)) {
            return $this->get($name);
        } else {
            return parent::__get($name);
        }
    }

    /**
     * 核对属性值是否为 null
     * 此方法通过核查指定组件是否已加载来覆写了父类实现
     * @param string $name 属性名或事件名
     * @return boolean 属性值是否为 null
     */
    public function __isset($name)
    {
        if ($this->has($name, true)) {
            return true;
        } else {
            return parent::__isset($name);
        }
    }

    /**
     * 返回一个值表明定位器是否有指定的组件定义或已经实例化组件
     * 本方法根据`$checkInstance`的值将返回不同结果：
     *
     * - 如果`$checkInstance`为 false (缺省)，本方法将返回一个表明定位器是否有指定组件定义的值；
     * - 如果`$checkInstance` 为 true，本方法将返回表明定位器是否已实例化指定组件的值。
     *
     * @param string $id 组件 ID (如`db`)
     * @param boolean $checkInstance 本方法是否核查组件被分享和实例化
     * @return boolean 定位器是否有指定组件定义或实例化组件
     * @see set()
     */
    public function has($id, $checkInstance = false)
    {
        return $checkInstance ? isset($this->_components[$id]) : isset($this->_definitions[$id]);
    }

    /**
     * 返回指定组件实例 ID
     *
     * @param string $id 组件 ID (如`db`)
     * @param boolean $throwException 如果`$id` 之前未注册到此定位器，是否抛出异常
     * @return object|null 指定 ID 的组件，如果`$throwException`为 false 且`$id`
     * 没有注册过，返回 null
     * @throws InvalidConfigException 如果`$id` 引用不存在的组件 ID
     * @see has()
     * @see set()
     */
    public function get($id, $throwException = true)
    {
        if (isset($this->_components[$id])) {
            return $this->_components[$id];
        }

        if (isset($this->_definitions[$id])) {
            $definition = $this->_definitions[$id];
            if (is_object($definition) && !$definition instanceof Closure) {
                return $this->_components[$id] = $definition;
            } else {
                return $this->_components[$id] = Yii::createObject($definition);
            }
        } elseif ($throwException) {
            throw new InvalidConfigException("Unknown component ID: $id");
        } else {
            return null;
        }
    }

    /**
     * 用定位器注册组件定义
     *
     * 例如：
     *
     * ```php
     * // 类名
     * $locator->set('cache', 'yii\caching\FileCache');
     *
     * // 配置数组
     * $locator->set('db', [
     *     'class' => 'yii\db\Connection',
     *     'dsn' => 'mysql:host=127.0.0.1;dbname=demo',
     *     'username' => 'root',
     *     'password' => '',
     *     'charset' => 'utf8',
     * ]);
     *
     * // 匿名函数
     * $locator->set('cache', function ($params) {
     *     return new \yii\caching\FileCache;
     * });
     *
     * // 实例
     * $locator->set('cache', new \yii\caching\FileCache);
     * ```
     *
     * 如果相同 ID 的组件定义已存在，它将会被覆盖。
     *
     * @param string $id 组件 ID (如`db`)
     * @param mixed $definition 要以此定位器注册的组件定义，可以是以下之一：
     *
     * - 类名
     * - 配置数组：数组包括的名值对可用于在调用[[get()]]时初始化新建对象的属性值，
     *   `class` 元素是必填项，代表要创建的对象所使用的类。
     * - PHP 回调函数：匿名函数或代表类方法的数组(如`['Foo', 'bar']`)
     *   回调函数由[[get()]]调用来返回关联到指定组件 ID 的对象
     * - 对象，当[[get()]]被调用就返回该对象
     *
     * @throws InvalidConfigException 如果定义是无效配置数组
     */
    public function set($id, $definition)
    {
        if ($definition === null) {
            unset($this->_components[$id], $this->_definitions[$id]);
            return;
        }

        if (is_object($definition) || is_callable($definition, true)) {
            // an object, a class name, or a PHP callable
            $this->_definitions[$id] = $definition;
        } elseif (is_array($definition)) {
            // a configuration array
            if (isset($definition['class'])) {
                $this->_definitions[$id] = $definition;
            } else {
                throw new InvalidConfigException("The configuration for the \"$id\" component must contain a \"class\" element.");
            }
        } else {
            throw new InvalidConfigException("Unexpected configuration type for the \"$id\" component: " . gettype($definition));
        }
    }

    /**
     * 从定位器移除组件
     * @param string $id 组件 ID
     */
    public function clear($id)
    {
        unset($this->_definitions[$id], $this->_components[$id]);
    }

    /**
     * 返回组件定义或已加载组件实例列表
     * @param boolean $returnDefinitions 是否返回组件定义而不是已加载的组件实例
     * @return array 组件定义或已加载组件实例(ID => 定义或实例)的列表
     */
    public function getComponents($returnDefinitions = true)
    {
        return $returnDefinitions ? $this->_definitions : $this->_components;
    }

    /**
     * 在此定位器注册一套组件定义
     *
     * 这是用[[set()]]遍历获取一套组件定义的方法，参数须是数组，键是组件 ID 而值是对应组件定义。
     *
     * 关于如何指定组件 ID 和定义的更多细节请参阅[[set()]]。
     *
     * 如果已有相同 ID 的组件定义，它将被覆盖。
     *
     * 以下是注册两个组件定义的一个例子：
     *
     * ```php
     * [
     *     'db' => [
     *         'class' => 'yii\db\Connection',
     *         'dsn' => 'sqlite:path/to/file.db',
     *     ],
     *     'cache' => [
     *         'class' => 'yii\caching\DbCache',
     *         'db' => 'db',
     *     ],
     * ]
     * ```
     *
     * @param array $components 组件定义或实例
     */
    public function setComponents($components)
    {
        foreach ($components as $id => $component) {
            $this->set($id, $component);
        }
    }
}
