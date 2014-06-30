<?php
/**
 * 翻译日期：20140510
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;
use yii\di\ServiceLocator;

/**
 * Module（模块）是所有模块和应用类的基类
 *
 * 一个模块表示一个子应用，自身包括了 MVC 元素，如模型，视图和控制器等。
 *
 * 一个模块可以包括[[modules|sub-modules]]。
 *
 * [[components|Components]] 可以注册到模块使他们能在模块范围内全局访问。
 *
 * @property array $aliases 要定义的路径别名列表，数组键是别名(必须以'@'开头)，
 * 而数组值是对应的路径或别名，[[setAliases()]]查看示例，本属性是只写的。
 * @property string $basePath 此模块根目录
 * @property string $controllerPath 控制器类目录，只读属性。
 * @property string $layoutPath 布局文件根目录，缺省为"[[viewPath]]/layouts"
 * @property array $modules 模块(以 ID 为索引)
 * @property string $uniqueId 模块的唯一 ID ，只读属性
 * @property string $viewPath 视图文件根目录，缺省为"[[basePath]]/view"
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Module extends ServiceLocator
{
    /**
     * @event ActionEvent 在执行控制器动作前唤起的事件
     * 可设置[[ActionEvent::isValid]]为 false 来取消动作的执行
     */
    const EVENT_BEFORE_ACTION = 'beforeAction';
    /**
     * @event ActionEvent 在控制器动作执行后唤起的事件
     */
    const EVENT_AFTER_ACTION = 'afterAction';

    /**
     * @var array 自定义模块参数(name => value).
     */
    public $params = [];
    /**
     * @var string 把此模块从有相同父模块[[module|parent]]的模块中区分出来的唯一 ID
     */
    public $id;
    /**
     * @var Module 此模块的父模块，如果此模块没有父模块就是 Null
     */
    public $module;
    /**
     * @var string|boolean 要应用到此模块视图的布局，此属性引用[[layoutPath]]相关的一个视图名。
     * 如果本属性未设置，将使用父模块[[module|parent module]]的布局值，如果是 false ，此模块禁用布局。
     */
    public $layout;
    /**
     * @var array 从控制器 ID 映射到控制器配置
     * 每个名值对指定单个控制器的配置，控制器配置是字符串或数组，
     * 如果是前者，字符串必须是控制器的完全限定类名；
     * 如果是后者，数组必须包括指定控制器完全限定类名的'class'元素，
     * 且其它名值对用于初始化对应控制器的属性，例如：
     *
     * ~~~
     * [
     *   'account' => 'app\controllers\UserController',
     *   'article' => [
     *      'class' => 'app\controllers\PostController',
     *      'pageTitle' => 'something new',
     *   ],
     * ]
     * ~~~
     */
    public $controllerMap = [];
    /**
     * @var string 控制器类所在的命名空间，如果未设置，将使用本模块命名空间下的"controllers" 子命名空间
     * 例如，如果本模块的命名空间是"foo\bar"，那么默认的控制器命名空间是"foo\bar\controllers"。
     */
    public $controllerNamespace;
    /**
     * @var string 本模块的默认路由，缺省为'default'
     * 路由包括子模块 ID，控制器 ID 和动作 ID
     * 例如：`help`, `post/create`, `admin/post/create`.
     * 如果未给定动作 ID ，将用指定在[[Controller::defaultAction]]的默认值
     */
    public $defaultRoute = 'default';
    /**
     * @var string 本模块的根目录
     */
    private $_basePath;
    /**
     * @var string 本模块的视图根目录
     */
    private $_viewPath;
    /**
     * @var string 本模块的布局根目录
     */
    private $_layoutPath;
    /**
     * @var array 本模块的子模块
     */
    private $_modules = [];


    /**
     * 构造函数
     * @param string $id 本模块的 ID
     * @param Module $parent 父模块(如果有)
     * @param array $config 用于初始化对象属性的名值对
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $this->id = $id;
        $this->module = $parent;
        parent::__construct($config);
    }

    /**
     * 初始化模块
     *
     * 本方法在模块创建和以给定配置初始化属性值后调用，
     * 默认实现将初始化[[controllerNamespace]]，如果它未设置
     *
     * 如果你覆写本方法，请确保你调用了父类实现。
     */
    public function init()
    {
        if ($this->controllerNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->controllerNamespace = substr($class, 0, $pos) . '\\controllers';
            }
        }
    }

    /**
     * 返回当前应用中本模块区别于其它模块的唯一 ID
     * 注意，如果模块是一个应用，将返回空字符串
     * @return string 本模块的唯一 ID
     */
    public function getUniqueId()
    {
        return $this->module ? ltrim($this->module->getUniqueId() . '/' . $this->id, '/') : $this->id;
    }

    /**
     * 返回本模块的根目录
     * 缺省为包含此模块类文件的目录
     * @return string 本模块的根目录
     */
    public function getBasePath()
    {
        if ($this->_basePath === null) {
            $class = new \ReflectionClass($this);
            $this->_basePath = dirname($class->getFileName());
        }

        return $this->_basePath;
    }

    /**
     * 设置本模块的根目录
     * 此方法只能在构造函数开始时调用
     * @param string $path 本模块的根目录，可以是目录名或路径别名
     * @throws InvalidParamException 如果目录不存在
     */
    public function setBasePath($path)
    {
        $path = Yii::getAlias($path);
        $p = realpath($path);
        if ($p !== false && is_dir($p)) {
            $this->_basePath = $p;
        } else {
            throw new InvalidParamException("The directory does not exist: $path");
        }
    }

    /**
     * 根据[[controllerNamespace]]返回包含控制器类的目录
     * 注意为确保此方法能返回一个值，你必须为[[controllerNamespace]]的根命名空间定义一个别名
     * @return string 控制器类所在目录
     * @throws InvalidParamException 如果没有为[[controllerNamespace]]的根命名空间定义别名
     */
    public function getControllerPath()
    {
        return Yii::getAlias('@' . str_replace('\\', '/', $this->controllerNamespace));
    }

    /**
     * 返回本模块的视图文件所在的目录
     * @return string 视图文件的根目录，缺省为"[[basePath]]/view"
     */
    public function getViewPath()
    {
        if ($this->_viewPath !== null) {
            return $this->_viewPath;
        } else {
            return $this->_viewPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'views';
        }
    }

    /**
     * 设置视图文件目录
     * @param string $path 视图文件根目录
     * @throws InvalidParamException 如果目录无效
     */
    public function setViewPath($path)
    {
        $this->_viewPath = Yii::getAlias($path);
    }

    /**
     * 返回本模块的布局文件目录
     * @return string 布局文件根目录，缺省为"[[viewPath]]/layouts".
     */
    public function getLayoutPath()
    {
        if ($this->_layoutPath !== null) {
            return $this->_layoutPath;
        } else {
            return $this->_layoutPath = $this->getViewPath() . DIRECTORY_SEPARATOR . 'layouts';
        }
    }

    /**
     * 设置布局文件目录
     * @param string $path 布局文件根目录
     * @throws InvalidParamException 如果目录无效
     */
    public function setLayoutPath($path)
    {
        $this->_layoutPath = Yii::getAlias($path);
    }

    /**
     * 定义路径别名
     * 此方法调用[[Yii::setAlias()]]来注册路径别名
     * 提供本方法是便于你在配置模块时能定义路径别名
     * @property array 要定义的路径别名列表，数组键是别名(必须以'@'开头)而数组值是相应的路径或别名
     * 查阅[[setAliases()]]的示例了解详情。
     * @param array $aliases 要定义的路径别名列表，
     * 数组键是别名(必须以'@'开头)而数组值是相应的路径或别名，如：
     *
     * ~~~
     * [
     *     '@models' => '@app/models', // 现有别名
     *     '@backend' => __DIR__ . '/../backend',  // 目录
     * ]
     * ~~~
     */
    public function setAliases($aliases)
    {
        foreach ($aliases as $name => $alias) {
            Yii::setAlias($name, $alias);
        }
    }

    /**
     * 检查指定 ID 的子模块是否存在
     * 本方法支持检查子模块和孙模块是否存在
     * @param string $id 模块 ID ，孙模块的话使用本模块相对的 ID 路径(如`admin/content`)
     * @return boolean 是否存在指定模块，已加载和未加载的模块都会考虑
     */
    public function hasModule($id)
    {
        if (($pos = strpos($id, '/')) !== false) {
            // sub-module
            $module = $this->getModule(substr($id, 0, $pos));

            return $module === null ? false : $module->hasModule(substr($id, $pos + 1));
        } else {
            return isset($this->_modules[$id]);
        }
    }

    /**
     * 检索指定 ID 的子模块
     * 本方法支持检索子模块和孙模块
     * @param string $id 模块 ID (区分大小写)，要检索孙模块，
     * 使用本模块 ID 的相对路径(如`admin/content`)
     * @param boolean $load 如果要检索的模块未加载，是否加载
     * @return Module|null 要检索的模块实例，如果模块不存在返回 null
     * @see hasModule()
     */
    public function getModule($id, $load = true)
    {
        if (($pos = strpos($id, '/')) !== false) {
            // sub-module
            $module = $this->getModule(substr($id, 0, $pos));

            return $module === null ? null : $module->getModule(substr($id, $pos + 1), $load);
        }

        if (isset($this->_modules[$id])) {
            if ($this->_modules[$id] instanceof Module) {
                return $this->_modules[$id];
            } elseif ($load) {
                Yii::trace("Loading module: $id", __METHOD__);
                if (is_array($this->_modules[$id]) && !isset($this->_modules[$id]['class'])) {
                    $this->_modules[$id]['class'] = 'yii\base\Module';
                }

                return $this->_modules[$id] = Yii::createObject($this->_modules[$id], [$id, $this]);
            }
        }

        return null;
    }

    /**
     * 添加子模块到此模块
     * @param string $id 模块 ID
     * @param Module|array|null $module 要添加到本模块的子模块，可以是以下之一：
     *
     * - [[Module]]对象
     * - 配置数组：当[[getModule()]]调用来初始化时，此数组用于实例化子模块
     * - null ：指定子模块将从本模块移除
     */
    public function setModule($id, $module)
    {
        if ($module === null) {
            unset($this->_modules[$id]);
        } else {
            $this->_modules[$id] = $module;
        }
    }

    /**
     * 返回本模块的子模块
     * @param boolean $loadedOnly 是否只返回已加载的子模块，如果此参数设置为 false ，
     * 那么将返回本模块的所有已注册子模块,无论是否加载。已加载模块将返回对象，而未加载模块返回配置数组
     * @return array 模块集 (以 ID 为索引)
     */
    public function getModules($loadedOnly = false)
    {
        if ($loadedOnly) {
            $modules = [];
            foreach ($this->_modules as $module) {
                if ($module instanceof Module) {
                    $modules[] = $module;
                }
            }

            return $modules;
        } else {
            return $this->_modules;
        }
    }

    /**
     * 在当前模块注册子模块
     *
     * 每个子模块都要指定为名值对，其中名引用模块 ID 而值为模块或可用于创建模块的配置数组。
     * 在后者这样的情况，[[Yii::createObject()]]将被用来创建模块。
     *
     * 如果新的子模块的 ID 和现有模块相同，现有模块将被静悄悄地覆盖。
     *
     * 以下是注册两个子模块的一个例子：
     *
     * ~~~
     * [
     *     'comment' => [
     *         'class' => 'app\modules\comment\CommentModule',
     *         'db' => 'db',
     *     ],
     *     'booking' => ['class' => 'app\modules\booking\BookingModule'],
     * ]
     * ~~~
     *
     * @param array $modules 模块集(id => 模块配置或实例)
     */
    public function setModules($modules)
    {
        foreach ($modules as $id => $module) {
            $this->_modules[$id] = $module;
        }
    }

    /**
     * 运行由路由指定的控制器动作
     * 本方法解析指定路由并创建相应的子模块、控制器和动作实例，然后它就调用[[Controller::runAction()]]
     * 以给定参数运行此动作。如果路由为空，本方法将使用[[defaultRoute]]。
     * @param string $route 指定动作的路由
     * @param array $params 要传递给动作的参数
     * @return mixed 动作执行结果
     * @throws InvalidRouteException 如果被请求的路由不能成功解析到一个动作
     */
    public function runAction($route, $params = [])
    {
        $parts = $this->createController($route);
        if (is_array($parts)) {
            /** @var Controller $controller */
            list($controller, $actionID) = $parts;
            $oldController = Yii::$app->controller;
            Yii::$app->controller = $controller;
            $result = $controller->runAction($actionID, $params);
            Yii::$app->controller = $oldController;

            return $result;
        } else {
            $id = $this->getUniqueId();
            throw new InvalidRouteException('Unable to resolve the request "' . ($id === '' ? $route : $id . '/' . $route) . '".');
        }
    }

    /**
     * 基于给定路由创建一个控制器实例
     *
     * 路由要对应于此模块，本方法实现以下算法来解析给定的路由：
     *
     * 1. 如果路由为空，使用[[defaultRoute]]；
     * 2. 如果路由的第一节是声明在[[modules]]的有效模块 ID ，
     *    调用模块的`createController()`并传入路由剩下的部分；
     * 3. 如果路由的第一节可在[[controllerMap]]查找到，基于[[controllerMap]]找到的相应配置创建控制器；
     * 4. 给定路由是`abc/def/xyz`格式，将在[[controllerNamespace|controller namespace]]
     * 尝试查找`abc\DefController`或`abc\def\XyzController`类。
     *
     * 如果以上步骤的任何一步解析路由到一个控制器，它将和路由剩下被视为动作 ID 的部分一起返回，
     * 否则，将返回 false 。
     *
     * @param string $route 模块、控制器和动作 ID 共同组成的路由
     * @return array|boolean 如果控制器被成功创建，它将和被请求的动作 ID 一起返回，否则返回 false
     * @throws InvalidConfigException 如果控制器类和其文件不匹配
     */
    public function createController($route)
    {
        if ($route === '') {
            $route = $this->defaultRoute;
        }

        // double slashes or leading/ending slashes may cause substr problem
        $route = trim($route, '/');
        if (strpos($route, '//') !== false) {
            return false;
        }

        if (strpos($route, '/') !== false) {
            list ($id, $route) = explode('/', $route, 2);
        } else {
            $id = $route;
            $route = '';
        }

        // module and controller map take precedence
        $module = $this->getModule($id);
        if ($module !== null) {
            return $module->createController($route);
        }
        if (isset($this->controllerMap[$id])) {
            $controller = Yii::createObject($this->controllerMap[$id], [$id, $this]);

            return [$controller, $route];
        }

        if (($pos = strrpos($route, '/')) !== false) {
            $id .= '/' . substr($route, 0, $pos);
            $route = substr($route, $pos + 1);
        }

        $controller = $this->createControllerByID($id);
        if ($controller === null && $route !== '') {
            $controller = $this->createControllerByID($id . '/' . $route);
            $route = '';
        }

        return $controller === null ? false : [$controller, $route];
    }

    /**
     * 基于给定控制器 ID 创建一个控制器
     *
     * 控制器 ID 要对应到本模块，控制器类应有命名空间并处于[[controllerNamespace]]命名空间下。
     *
     * 注意本方法不核对[[modules]]或[[controllerMap]]。
     *
     * @param string $id 控制器 ID
     * @return Controller 新创建的控制器实例，如果控制器 ID 无效就返回 null
     * @throws InvalidConfigException 如果控制器类和其文件不匹配，本异常在调试模式下才抛出。
     */
    public function createControllerByID($id)
    {
        if (!preg_match('%^[a-z0-9\\-_/]+$%', $id)) {
            return null;
        }

        $pos = strrpos($id, '/');
        if ($pos === false) {
            $prefix = '';
            $className = $id;
        } else {
            $prefix = substr($id, 0, $pos + 1);
            $className = substr($id, $pos + 1);
        }

        $className = str_replace(' ', '', ucwords(str_replace('-', ' ', $className))) . 'Controller';
        $className = ltrim($this->controllerNamespace . '\\' . str_replace('/', '\\', $prefix)  . $className, '\\');
        if (strpos($className, '-') !== false || !class_exists($className)) {
            return null;
        }

        if (is_subclass_of($className, 'yii\base\Controller')) {
            return Yii::createObject($className, [$id, $this]);
        } elseif (YII_DEBUG) {
            throw new InvalidConfigException("Controller class must extend from \\yii\\base\\Controller.");
        } else {
            return null;
        }
    }

    /**
     * 本方法在此模块内的动作被执行前调用
     *
     * 此方法将触发[[EVENT_BEFORE_ACTION]]事件，此方法的返回值将确定动作是否继续运行
     *
     * 如果你要覆写此方法，你的代码应如下：
     *
     * ```php
     * public function beforeAction($action)
     * {
     *     if (parent::beforeAction($action)) {
     *         // 这里是你的自定义代码
     *         return true;  // 或 false ，如果需要
     *     } else {
     *         return false;
     *     }
     * }
     * ```
     *
     * @param Action $action 要执行的动作
     * @return boolean 动作是否继续执行
     */
    public function beforeAction($action)
    {
        $event = new ActionEvent($action);
        $this->trigger(self::EVENT_BEFORE_ACTION, $event);
        return $event->isValid;
    }

    /**
     * 此方法在模块内的动作执行后被调用
     *
     * 此方法将触发[[EVENT_AFTER_ACTION]]事件，此方法的返回值将用作动作返回值
     *
     * 如果你要覆写本方法，代码如下：
     *
     * ```php
     * public function afterAction($action, $result)
     * {
     *     $result = parent::afterAction($action, $result);
     *     // 这里是你的自定义代码
     *     return $result;
     * }
     * ```
     *
     * @param Action $action 刚执行过的动作
     * @param mixed $result 动作返回结果
     * @return mixed 处理后的动作结果
     */
    public function afterAction($action, $result)
    {
        $event = new ActionEvent($action);
        $event->result = $result;
        $this->trigger(self::EVENT_AFTER_ACTION, $event);
        return $event->result;
    }
}
