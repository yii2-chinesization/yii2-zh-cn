<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;

/**
 * Controller（控制器）是所有包括控制器逻辑的类的基类
 *
 * @property Module[] $modules 此控制器所在的模块及所有父模块，本属性是只读的。
 * @property string $route 当前请求的路由(模块 ID, 控制器 ID 和动作 ID)，本属性是只读的。
 * @property string $uniqueId 控制器 ID ，前缀是模块 ID (如果有)，本属性是只读的。
 * @property View|\yii\web\View $view 视图对象，用于渲染视图和视图文件。
 * @property string $viewPath 包括本控制器所有的视图文件的目录，本属性是只读的。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Controller extends Component implements ViewContextInterface
{
    /**
     * @event ActionEvent 正好在执行控制器动作前引发的事件
     * 你可以设置[[ActionEvent::isValid]]为 false 来取消动作的执行。
     */
    const EVENT_BEFORE_ACTION = 'beforeAction';
    /**
     * @event ActionEvent 正好在执行控制器动作后引发的事件
     */
    const EVENT_AFTER_ACTION = 'afterAction';
    /**
     * @var string 控制器 ID
     */
    public $id;
    /**
     * @var Module $module 该控制器所处的模块
     */
    public $module;
    /**
     * @var string 动作 ID ，当动作 ID 在请求中未指定时所使用的动作 ID ，缺省为 'index' 。
     */
    public $defaultAction = 'index';
    /**
     * @var string|boolean 要运用到本控制器视图的布局名，此属性主要作用于[[render()]]的行为。
     * 缺省为 null ，即真正的布局值要继承[[module]]的布局值。如果是 false ，没有布局被使用。
     */
    public $layout;
    /**
     * @var Action 当前被请求的动作，此属性当[[Application]]调用[[run()]]来运行动作时可由[[run()]]设置。
     */
    public $action;
    /**
     * @var View 视图对象，可用来渲染视图或视图文件。
     */
    private $_view;

    /**
     * @param string $id 控制器 ID
     * @param Module $module 该控制器所属的模块
     * @param array $config 用于初始化对象属性的名值对
     */
    public function __construct($id, $module, $config = [])
    {
        $this->id = $id;
        $this->module = $module;
        parent::__construct($config);
    }

    /**
     * 为控制器声明外部动作
     * 此方法的目的是用来覆写以为控制器声明外部动作，它应返回一个数组，
     * 其中数组键是动作 ID ，而数组值是对应的动作类名或动作配置数组，如：
     *
     * ~~~
     * return [
     *     'action1' => 'app\components\Action1',
     *     'action2' => [
     *         'class' => 'app\components\Action2',
     *         'property1' => 'value1',
     *         'property2' => 'value2',
     *     ],
     * ];
     * ~~~
     *
     * 稍后[[\Yii::createObject()]]方法将使用这里提供的配置来创建被请求动作对象
     */
    public function actions()
    {
        return [];
    }

    /**
     * 以指定动作 ID 和参数来运行此控制器内的动作
     * 如果动作 ID 为空，本方法将使用[[defaultAction]]。
     * @param string $id 要执行的动作 ID
     * @param array $params 要传递到此动作的参数(名值对)
     * @return mixed 动作运行结果
     * @throws InvalidRouteException 如果被请求的动作 ID 不能成功解析到某个动作
     * @see createAction()
     */
    public function runAction($id, $params = [])
    {
        $action = $this->createAction($id);
        if ($action === null) {
            throw new InvalidRouteException('Unable to resolve the request: ' . $this->getUniqueId() . '/' . $id);
        }

        Yii::trace("Route to run: " . $action->getUniqueId(), __METHOD__);

        if (Yii::$app->requestedAction === null) {
            Yii::$app->requestedAction = $action;
        }

        $oldAction = $this->action;
        $this->action = $action;

        $modules = [];
        $runAction = true;

        foreach ($this->getModules() as $module) {
            if ($module->beforeAction($action)) {
                array_unshift($modules, $module);
            } else {
                $runAction = false;
                break;
            }
        }

        $result = null;

        if ($runAction) {
            if ($this->beforeAction($action)) {
                $result = $action->runWithParams($params);
                $result = $this->afterAction($action, $result);
            }
        }

        foreach ($modules as $module) {
            /** @var Module $module */
            $result = $module->afterAction($action, $result);
        }

        $this->action = $oldAction;

        return $result;
    }

    /**
     * 运行以路由形式指定的请求
     * 路由可以是此控制器内部的动作 ID ，也可以是由模块 ID 、控制器 ID 和动作 ID 组成的完整路由。
     * 如果路由以斜线'/'开头，此路由的解析将从应用开始；否则，解析从此控制器的父模块开始。
     * @param string $route 要处理的路由，如'view', 'comment/view', '/admin/comment/view' 。
     * @param array $params 要传递给动作的参数
     * @return mixed 动作运行结果
     * @see runAction()
     */
    public function run($route, $params = [])
    {
        $pos = strpos($route, '/');
        if ($pos === false) {
            return $this->runAction($route, $params);
        } elseif ($pos > 0) {
            return $this->module->runAction($route, $params);
        } else {
            return Yii::$app->runAction(ltrim($route, '/'), $params);
        }
    }

    /**
     * 绑定参数到动作
     * 当动作以给定参数运行时，此方法由[[Action]]调用
     * @param Action $action 要绑定参数的动作
     * @param array $params 要绑定到动作的参数
     * @return array 传入动作可以运行的有效参数
     */
    public function bindActionParams($action, $params)
    {
        return [];
    }

    /**
     * 基于给定动作 ID 创建动作
     * 此方法首先检查动作 ID 是否在[[actions()]]已声明，如果是，它将使用那里声明的配置数组来创建动作对象。
     * 否则，此方法将查找名称格式是`actionXyz`的控制器方法，其中`Xyz`表示动作 ID 。
     * 如果找到，代表那个控制器方法的[[InlineAction]]将创建并返回。
     * @param string $id 动作 ID
     * @return Action 新创建的动作实例，如果 ID 没有解析到任何动作将返回 Null 。
     */
    public function createAction($id)
    {
        if ($id === '') {
            $id = $this->defaultAction;
        }

        $actionMap = $this->actions();
        if (isset($actionMap[$id])) {
            return Yii::createObject($actionMap[$id], [$id, $this]);
        } elseif (preg_match('/^[a-z0-9\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
            $methodName = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));
            if (method_exists($this, $methodName)) {
                $method = new \ReflectionMethod($this, $methodName);
                if ($method->getName() === $methodName) {
                    return new InlineAction($id, $this, $methodName);
                }
            }
        }

        return null;
    }

    /**
     * 此方法正好在动作执行前调用
     *
     * 本方法将触发[[EVENT_BEFORE_ACTION]] 事件，本方法的返回值将决定是否继续运行传入动作。
     *
     * 如果你要覆写本方法，你的代码应该像这样：
     *
     * ```php
     * public function beforeAction($action)
     * {
     *     if (parent::beforeAction($action)) {
     *         // 你的自定义代码
     *         return true;  // 或 false 如果需要
     *     } else {
     *         return false;
     *     }
     * }
     * ```
     *
     * @param Action $action 要执行的动作
     * @return boolean 动作是否继续运行
     */
    public function beforeAction($action)
    {
        $event = new ActionEvent($action);
        $this->trigger(self::EVENT_BEFORE_ACTION, $event);
        return $event->isValid;
    }

    /**
     * 此方法正好在动作执行后调用
     *
     * 本方法将触发[[EVENT_AFTER_ACTION]] 事件，该方法的返回值将用作传入动作的返回值。
     *
     * 如果你要覆写本方法，你的代码应该像这样：
     *
     * ```php
     * public function afterAction($action, $result)
     * {
     *     $result = parent::afterAction($action, $result);
     *     // 你的自定义代码
     *     return $result;
     * }
     * ```
     *
     * @param Action $action 刚执行完的动作
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

    /**
     * 返回此控制器的所有祖先模块
     * 数组的第一个模块是最外层的那个(如，应用实例)，而最后一个是最里层的那个。
     * @return Module[] 此控制器所处的模块及所有祖先模块
     */
    public function getModules()
    {
        $modules = [$this->module];
        $module = $this->module;
        while ($module->module !== null) {
            array_unshift($modules, $module->module);
            $module = $module->module;
        }
        return $modules;
    }

    /**
     * @return string 以模块 ID (如果有)为前缀的控制器 ID
     */
    public function getUniqueId()
    {
        return $this->module instanceof Application ? $this->id : $this->module->getUniqueId() . '/' . $this->id;
    }

    /**
     * 返回当前请求的路由
     * @return string 当前请求的路由(模块 ID, 控制器 ID 和动作 ID)
     */
    public function getRoute()
    {
        return $this->action !== null ? $this->action->getUniqueId() : $this->getUniqueId();
    }

    /**
     * 渲染视图并应用可用的布局
     *
     * 要渲染的视图可指定为以下格式：
     *
     * - 路径别名(如"@app/views/site/index")；
     * - 应用内的绝对路径(如"//site/index")：以双斜线开头的视图名；
     *   真正的视图文件将在应用的[[Application::viewPath|view path]]下查找。
     * - 模块内的绝对路径(如"/site/index")：以单斜线开头的视图名；
     *   真正的视图文件将在[[module]]内的[[Module::viewPath|view path]]查找。
     * - 相对路径(如"index")：真正的视图文件将在[[viewPath]]下查找。
     *
     * 应用哪个布局，由以下两步骤确定：
     *
     * 1. 第一步，确定布局名和所处模块：
     *
     * - 如果[[layout]]指定为字符串，把它用作布局名，而[[module]]用作当前模块；
     * - 如果[[layout]] 为 null ，搜索本控制器的所有祖先模块并找到[[Module::layout|layout]]非 null 的
     * 第一个模块，此布局和相应的模块就分别用作布局名和当前模块。
     * 如果这样的模块找不到或对应的布局是一个字符串，它将返回 false ，即没有可用布局。
     *
     * 2.第二步，根据前面找到的布局名和当前模块确定真正的布局文件。布局名可以是：
     *
     * - 路径别名(如"@app/views/layouts/main")；
     * - 绝对路径(如"/main")：以斜线开头的布局名，真正的布局文件在此应用的[[Application::layoutPath|layout path]]下查找；
     * - 相对路径(如"main")：真正的布局文件在当前模块的[[Module::layoutPath|layout path]]下查找。
     *
     * 如果布局名不包括文件扩展名，它将使用缺省的`.php`。
     *
     * @param string $view 视图名
     * @param array $params 视图内可用的参数(名值对)
     * 这些参数在布局不可用。
     * @return string 渲染结果
     * @throws InvalidParamException 如果视图文件或布局文件不存在
     */
    public function render($view, $params = [])
    {
        $output = $this->getView()->render($view, $params, $this);
        $layoutFile = $this->findLayoutFile($this->getView());
        if ($layoutFile !== false) {
            return $this->getView()->renderFile($layoutFile, ['content' => $output], $this);
        } else {
            return $output;
        }
    }

    /**
     * 渲染视图
     * 此方法和[[render()]]不同的是它不应用任何布局
     * @param string $view 视图名，请参考[[render()]]来了解如何指定视图名
     * @param array $params 视图可用参数(名值对)
     * @return string 渲染结果
     * @throws InvalidParamException 如果视图文件不存在
     */
    public function renderPartial($view, $params = [])
    {
        return $this->getView()->render($view, $params, $this);
    }

    /**
     * 渲染视图文件
     * @param string $file 要渲染的视图文件，可用是文件路径或路径别名
     * @param array $params 视图可用的参数(名值对)
     * @return string 渲染结果
     * @throws InvalidParamException 如果视图文件不存在
     */
    public function renderFile($file, $params = [])
    {
        return $this->getView()->renderFile($file, $params, $this);
    }

    /**
     * 返回用于渲染视图或视图文件的视图对象
     * [[render()]], [[renderPartial()]] 和 [[renderFile()]] 方法将使用此视图对象来实现真正的视图渲染。
     * 如果未设置，默认是"view" 应用组件。
     * @return View|\yii\web\View 用于渲染视图或视图文件的视图对象
     */
    public function getView()
    {
        if ($this->_view === null) {
            $this->_view = Yii::$app->getView();
        }
        return $this->_view;
    }

    /**
     * 设置此控制器使用的视图对象
     * @param View|\yii\web\View $view 用于渲染视图或视图文件的视图对象
     */
    public function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * 返回包括此控制器的视图文件的目录
     * 默认实现返回[[module]]内[[viewPath]]目录下名为控制器[[id]]的目录。
     * @return string 该目录包括此控制器的视图文件
     */
    public function getViewPath()
    {
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . $this->id;
    }

    /**
     * 找到适用的布局文件
     * @param View $view 要渲染布局文件的视图对象
     * @return string|boolean 布局文件路径，如果布局不需要返回 false
     * 请参考[[render()]]了解如何指定此参数
     * @throws InvalidParamException 如果用于指定布局的路径别名是无效的
     */
    protected function findLayoutFile($view)
    {
        $module = $this->module;
        if (is_string($this->layout)) {
            $layout = $this->layout;
        } elseif ($this->layout === null) {
            while ($module !== null && $module->layout === null) {
                $module = $module->module;
            }
            if ($module !== null && is_string($module->layout)) {
                $layout = $module->layout;
            }
        }

        if (!isset($layout)) {
            return false;
        }

        if (strncmp($layout, '@', 1) === 0) {
            $file = Yii::getAlias($layout);
        } elseif (strncmp($layout, '/', 1) === 0) {
            $file = Yii::$app->getLayoutPath() . DIRECTORY_SEPARATOR . substr($layout, 1);
        } else {
            $file = $module->getLayoutPath() . DIRECTORY_SEPARATOR . $layout;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $view->defaultExtension;
        if ($view->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }
}
