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

use ReflectionClass;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Container（容器类）实现了一个[dependency injection（DI ，依赖注入）](http://en.wikipedia.org/wiki/Dependency_injection) 容器
 *
 * 一个依赖注入(DI)容器是一个对象，这个对象知道如何实例化和配置其他对象及其所有依赖对象
 * 更多有关 DI 的信息请参阅[Martin Fowler 文章](http://martinfowler.com/articles/injection.html).
 *
 * 容器支持构造函数注入和属性注入
 *
 * 要使用容器，你首先需要通过调用[[set()]]建立类的依赖关系，然后调用[[get()]]创建新的类对象。
 * 容器将自动实例化依赖对象、把它们注入被创建的对象、最后配置和返回新创建的对象。
 *
 * 默认[[\Yii::$container]]指向一个[[\Yii::createObject()]]创建新对象实例所用到的容器实例。
 * 你可以在创建新对象时使用此方法替换 `new` 操作符，这给你带来解决自动依赖和配置缺省属性的好处。
 *
 * 以下是使用容器的一个例子：
 *
 * ```php
 * namespace app\models;
 *
 * use yii\base\Object;
 * use yii\db\Connection;
 * use yii\di\Container;
 *
 * interface UserFinderInterface
 * {
 *     function findUser();
 * }
 *
 * class UserFinder extends Object implements UserFinderInterface
 * {
 *     public $db;
 *
 *     public function __construct(Connection $db, $config = [])
 *     {
 *         $this->db = $db;
 *         parent::__construct($config);
 *     }
 *
 *     public function findUser()
 *     {
 *     }
 * }
 *
 * class UserLister extends Object
 * {
 *     public $finder;
 *
 *     public function __construct(UserFinderInterface $finder, $config = [])
 *     {
 *         $this->finder = $finder;
 *         parent::__construct($config);
 *     }
 * }
 *
 * $container = new Container;
 * $container->set('yii\db\Connection', [
 *     'dsn' => '...',
 * ]);
 * $container->set('app\models\UserFinderInterface', [
 *     'class' => 'app\models\UserFinder',
 * ]);
 * $container->set('userLister', 'app\models\UserLister');
 *
 * $lister = $container->get('userLister');
 *
 * // 等同于：
 *
 * $db = new \yii\db\Connection(['dsn' => '...']);
 * $finder = new UserFinder($db);
 * $lister = new UserLister($finder);
 * ```
 *
 * @property array $definitions 对象定义或已加载共享对象的列表(type or ID =>定义或实例)，只读属性
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Container extends Component
{
    /**
     * @var array 以类型为索引的单例对象
     */
    private $_singletons = [];
    /**
     * @var array 以类型为索引的对象定义
     */
    private $_definitions = [];
    /**
     * @var array 以对象类型为索引的构造函数参数
     */
    private $_params = [];
    /**
     * @var array 缓存的以类名或接口名为索引的 ReflectionClass 对象
     */
    private $_reflections = [];
    /**
     * @var array 以类名或接口名为索引的缓存依赖关系，每个类名关联到一个构造函数参数类型或默认值的列表
     */
    private $_dependencies = [];


    /**
     * 返回被请求类的一个实例
     *
     * 你可以提供在实例创建时所用到的构造函数参数(`$params`)和对象配置(`$config`)
     *
     * 注意，如果此类通过调用[[setSingleton()]]来声明为单例，每次调用本方法时都返回这个类的相同实例。
     * 这种情况，构造函数参数和对象配置只在类首次实例化时使用
     *
     * @param string $class 之前已通过[[set()]]或[[setSingleton()]]注册的类名或别名(如`foo`)
     * @param array $params 构造函数参数值列表，提供的参数顺序是它们在构造函数声明的顺序。
     * 如果你想跳过某些参数，可以用代表构造函数参数列表中的位置的整型来索引保留的参数。
     * @param array $config 用于初始化对象属性的名值对
     * @return object 被请求类的一个实例
     * @throws InvalidConfigException 如果该类不能识别或对应到无效定义
     */
    public function get($class, $params = [], $config = [])
    {
        if (isset($this->_singletons[$class])) {
            // singleton
            return $this->_singletons[$class];
        } elseif (!isset($this->_definitions[$class])) {
            return $this->build($class, $params, $config);
        }

        $definition = $this->_definitions[$class];

        if (is_callable($definition, true)) {
            $params = $this->resolveDependencies($this->mergeParams($class, $params));
            $object = call_user_func($definition, $this, $params, $config);
        } elseif (is_array($definition)) {
            $concrete = $definition['class'];
            unset($definition['class']);

            $config = array_merge($definition, $config);
            $params = $this->mergeParams($class, $params);

            if ($concrete === $class) {
                $object = $this->build($class, $params, $config);
            } else {
                $object = $this->get($concrete, $params, $config);
            }
        } elseif (is_object($definition)) {
            return $this->_singletons[$class] = $definition;
        } else {
            throw new InvalidConfigException("Unexpected object definition type: " . gettype($definition));
        }

        if (array_key_exists($class, $this->_singletons)) {
            // singleton
            $this->_singletons[$class] = $object;
        }

        return $object;
    }

    /**
     * 用此容器注册类定义
     *
     * 如：
     *
     * ```php
     * // 如此注册一个类名，这可以跳过
     * $container->set('yii\db\Connection');
     *
     * // 注册接口
     * // 当一个类依赖于接口，对应的类将实例化为依赖对象
     * $container->set('yii\mail\MailInterface', 'yii\swiftmailer\Mailer');
     *
     * // 注册一个别名，你可以使用 $container->get('foo') 来创建 Connection 的一个实例
     * $container->set('foo', 'yii\db\Connection');
     *
     * // 注册一个带配置的类，配置在类被 get() 实例化时将被应用
     * $container->set('yii\db\Connection', [
     *     'dsn' => 'mysql:host=127.0.0.1;dbname=demo',
     *     'username' => 'root',
     *     'password' => '',
     *     'charset' => 'utf8',
     * ]);
     *
     * // 用类配置来注册别名
     * // 这种情况下， "class" 元素是必填项以指定类
     * $container->set('db', [
     *     'class' => 'yii\db\Connection',
     *     'dsn' => 'mysql:host=127.0.0.1;dbname=demo',
     *     'username' => 'root',
     *     'password' => '',
     *     'charset' => 'utf8',
     * ]);
     *
     * // 注册一个 PHP 回调函数
     * // 当 $container->get('db') 被调用时此回调函数将被执行
     * $container->set('db', function ($container, $params, $config) {
     *     return new \yii\db\Connection($config);
     * });
     * ```
     *
     * 如果具有相同名称的类定义已存在，它将被新的类定义覆盖，可使用[[has()]]核查类定义是否已存在
     *
     * @param string $class 类名、接口名或别名
     * @param mixed $definition 关联到`$class`的定义，可以是以下之一：
     *
     * - PHP 回调函数：当 [[get()]]调用时此回调函数将执行，回调函数的标识是
     * `function ($container, $params, $config)`, 其中`$params`代表构造函数的参数列表，
     * `$config`代表对象配置，而`$container` 代表容器对象。
     * 此回调函数的返回值是[[get()]]返回的被请求的对象实例。
     * - 配置数组：数组包括用于[[get()]]调用时初始化新对象的属性值，`class`元素代表要创建对象所属类，
     * 如果`class`未指定，`$class`将用作类名。
     * - 字符串：类名、接口名或别名
     * @param array $params 构造函数的参数列表，在[[get()]]调用时将被传递到构造函数
     * @return static 容器本身
     */
    public function set($class, $definition = [], array $params = [])
    {
        $this->_definitions[$class] = $this->normalizeDefinition($class, $definition);
        $this->_params[$class] = $params;
        unset($this->_singletons[$class]);
        return $this;
    }

    /**
     * 以此容器注册一个类定义并标记类为单例类
     *
     * 本方法类似于[[set()]]，除了本方法注册的类只有一个实例外。每次调用[[get()]]都返回指定类的同一实例
     *
     * @param string $class 类名、接口名或别名
     * @param mixed $definition 关联到`$class`的定义，更多细节见[[set()]]
     * @param array $params 构造函数参数列表，当[[get()]]调用时此参数将被传递给类的构造函数
     * @return static 容器本身
     * @see set()
     */
    public function setSingleton($class, $definition = [], array $params = [])
    {
        $this->_definitions[$class] = $this->normalizeDefinition($class, $definition);
        $this->_params[$class] = $params;
        $this->_singletons[$class] = null;
        return $this;
    }

    /**
     * 返回一个值，指明容器是否有指定名的定义
     * @param string $class 类名、接口名或别名
     * @return boolean 容器是否有指定名的定义
     * @see set()
     */
    public function has($class)
    {
        return isset($this->_definitions[$class]);
    }

    /**
     * 返回一个值，指明给定名是否对应到一个已注册的单例
     * @param string $class 类名、接口名或别名
     * @param boolean $checkInstance 是否检查单例是否已实例化
     * @return boolean 给定名是否对应到一个已注册的单例，如果`$checkInstance`为 true ，
     * 本方法将返回一个值以表明单例是否已实例化
     */
    public function hasSingleton($class, $checkInstance = false)
    {
        return $checkInstance ? isset($this->_singletons[$class]) : array_key_exists($class, $this->_singletons);
    }

    /**
     * 移除指定名的定义
     * @param string $class 类名、接口名或别名
     */
    public function clear($class)
    {
        unset($this->_definitions[$class], $this->_singletons[$class]);
    }

    /**
     * 标准化类定义
     * @param string $class 类名
     * @param string|array|callable $definition 类定义
     * @return array 已标准化的类定义
     * @throws InvalidConfigException 如果定义无效
     */
    protected function normalizeDefinition($class, $definition)
    {
        if (empty($definition)) {
            return ['class' => $class];
        } elseif (is_string($definition)) {
            return ['class' => $definition];
        } elseif (is_callable($definition, true) || is_object($definition)) {
            return $definition;
        } elseif (is_array($definition)) {
            if (!isset($definition['class'])) {
                if (strpos($class, '\\') !== false) {
                    $definition['class'] = $class;
                } else {
                    throw new InvalidConfigException("A class definition requires a \"class\" member.");
                }
            }
            return $definition;
        } else {
            throw new InvalidConfigException("Unsupported definition type for \"$class\": " . gettype($definition));
        }
    }

    /**
     * 返回对象定义或已加载共享对象的列表
     * @return array 对象定义或已加载共享对象的列表(type or ID => definition or instance).
     */
    public function getDefinitions()
    {
        return $this->_definitions;
    }

    /**
     * 创建指定类的实例
     * 该方法将解析指定类的依赖关系、实例化它们并把它们注入指定类的新实例
     * @param string $class 类名
     * @param array $params 构造函数参数
     * @param array $config 应用到新实例的配置
     * @return object 新创建的指定类的实例
     */
    protected function build($class, $params, $config)
    {
        /** @var ReflectionClass $reflection */
        list ($reflection, $dependencies) = $this->getDependencies($class);

        foreach ($params as $index => $param) {
            $dependencies[$index] = $param;
        }

        if (!empty($dependencies) && is_a($class, 'yii\base\Object', true)) {
            // set $config as the last parameter (existing one will be overwritten)
            $dependencies[count($dependencies) - 1] = $config;
            $dependencies = $this->resolveDependencies($dependencies, $reflection);
            return $reflection->newInstanceArgs($dependencies);
        } else {
            $dependencies = $this->resolveDependencies($dependencies, $reflection);
            $object = $reflection->newInstanceArgs($dependencies);
            foreach ($config as $name => $value) {
                $object->$name = $value;
            }
            return $object;
        }
    }

    /**
     * 合并用户指定的构造函数参数和[[set()]]注册的构造函数参数
     * @param string $class 类名、接口名或别名
     * @param array $params 构造函数参数
     * @return array 合并后的参数
     */
    protected function mergeParams($class, $params)
    {
        if (empty($this->_params[$class])) {
            return $params;
        } elseif (empty($params)) {
            return $this->_params[$class];
        } else {
            $ps = $this->_params[$class];
            foreach ($params as $index => $value) {
                $ps[$index] = $value;
            }
            return $ps;
        }
    }

    /**
     * 返回指定类的依赖关系
     * @param string $class 类名、接口名或别名
     * @return array 指定类的依赖关系
     */
    protected function getDependencies($class)
    {
        if (isset($this->_reflections[$class])) {
            return [$this->_reflections[$class], $this->_dependencies[$class]];
        }

        $dependencies = [];
        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();
        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $param) {
                if ($param->isDefaultValueAvailable()) {
                    $dependencies[] = $param->getDefaultValue();
                } else {
                    $c = $param->getClass();
                    $dependencies[] = Instance::of($c === null ? null : $c->getName());
                }
            }
        }

        $this->_reflections[$class] = $reflection;
        $this->_dependencies[$class] = $dependencies;

        return [$reflection, $dependencies];
    }

    /**
     * 以真正的对象实例取代依赖关系来解析它们
     * @param array $dependencies 依赖关系
     * @param ReflectionClass $reflection 关联到依赖关系的类反射
     * @return array 解析后的依赖关系
     * @throws InvalidConfigException 如果依赖关系不能解析或依赖关系不能执行
     */
    protected function resolveDependencies($dependencies, $reflection = null)
    {
        foreach ($dependencies as $index => $dependency) {
            if ($dependency instanceof Instance) {
                if ($dependency->id !== null) {
                    $dependencies[$index] = $this->get($dependency->id);
                } elseif ($reflection !== null) {
                    $name = $reflection->getConstructor()->getParameters()[$index]->getName();
                    $class = $reflection->getName();
                    throw new InvalidConfigException("Missing required parameter \"$name\" when instantiating \"$class\".");
                }
            }
        }
        return $dependencies;
    }
}
