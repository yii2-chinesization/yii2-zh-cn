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

/**
 * Application（应用类）是所有应用类的基类
 *
 * @property \yii\web\AssetManager $assetManager 资源管理器组件，只读属性
 * @property \yii\rbac\ManagerInterface $authManager 此应用的认证管理器，
 * 如果认证管理器未配置返回 Null ，只读属性
 * @property string $basePath 此应用的根目录
 * @property \yii\caching\Cache $cache 缓存组件，如果未启用返回 Null ，只读属性
 * @property \yii\db\Connection $db 数据库连接组件，只读属性
 * @property \yii\web\ErrorHandler|\yii\console\ErrorHandler $errorHandler 错误处理器组件，只读属性
 * @property \yii\base\Formatter $formatter 格式器组件，只读属性
 * @property \yii\i18n\I18N $i18n 国际化组件，只读属性
 * @property \yii\log\Dispatcher $log 日志调度器组件，只读属性
 * @property \yii\mail\MailerInterface $mail 邮件收发器接口，只读属性
 * @property \yii\web\Request|\yii\console\Request $request 请求组件，只读属性
 * @property \yii\web\Response|\yii\console\Response $response 响应组件，只读属性
 * @property string $runtimePath 存储运行时文件的目录，缺省为[[basePath]]下的"runtime"子目录
 * @property string $timeZone 本应用使用的时区
 * @property string $uniqueId 本模块的唯一 ID ，只读属性
 * @property \yii\web\UrlManager $urlManager 此应用的 URL 管理器，只读属性
 * @property string $vendorPath 存储 vendor 文件的目录，缺省为[[basePath]]下的"vendor"目录
 * @property View|\yii\web\View $view 用于渲染各种视图文件的视图对象，只读属性
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class Application extends Module
{
    /**
     * @event Event 本应用开始处理请求前唤起的事件
     */
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    /**
     * @event Event 本应用成功处理一个请求后唤起的事件(在响应发送出去前)
     */
    const EVENT_AFTER_REQUEST = 'afterRequest';
    /**
     * 被[[state]]使用的应用状态：应用刚刚启动
     */
    const STATE_BEGIN = 0;
    /**
     * 被[[state]]使用的应用状态：应用正在初始化
     */
    const STATE_INIT = 1;
    /**
     * 被[[state]]使用的应用状态：应用正在触发[[EVENT_BEFORE_REQUEST]]
     */
    const STATE_BEFORE_REQUEST = 2;
    /**
     * 被[[state]]使用的应用状态：应用正在处理请求
     */
    const STATE_HANDLING_REQUEST = 3;
    /**
     * 被[[state]]使用的应用状态：应用正在触发[[EVENT_AFTER_REQUEST]]
     */
    const STATE_AFTER_REQUEST = 4;
    /**
     * 被[[state]]使用的应用状态：应用正要发送响应
     */
    const STATE_SENDING_RESPONSE = 5;
    /**
     * 被[[state]]使用的应用状态：应用已经结束
     */
    const STATE_END = 6;

    /**
     * @var string 控制器类所在的命名空间，如果未设置，将使用"app\controllers"命名空间
     */
    public $controllerNamespace = 'app\\controllers';
    /**
     * @var string 应用名
     */
    public $name = 'My Application';
    /**
     * @var string 应用版本
     */
    public $version = '1.0';
    /**
     * @var string 应用当前使用的字符集（编码）
     */
    public $charset = 'UTF-8';
    /**
     * @var string 终端用户使用的目标语言
     * @see sourceLanguage
     */
    public $language = 'en';
    /**
     * @var string 应用编写语言，主要引用消息和视图文件的编写语言
     * @see language
     */
    public $sourceLanguage = 'en';
    /**
     * @var Controller 当前活动的控制器实例
     */
    public $controller;
    /**
     * @var string|boolean 适用于本应用视图的布局，缺省为'main'，如果是 false ，布局将禁用。
     */
    public $layout = 'main';
    /**
     * @var string 被请求的路由
     */
    public $requestedRoute;
    /**
     * @var Action 被请求的动作，如为 null ，即请求不能解析到某个动作
     */
    public $requestedAction;
    /**
     * @var array 提供给被请求动作的参数
     */
    public $requestedParams;
    /**
     * @var array 已安装 Yii 扩展列表，每个数组元素代表单个扩展，结构如下：
     *
     * ~~~
     * [
     *     'name' => 'extension name',
     *     'version' => 'version number',
     *     'bootstrap' => 'BootstrapClassName',  // 可选项，也可是配置数组
     *     'alias' => [
     *         '@alias1' => 'to/path1',
     *         '@alias2' => 'to/path2',
     *     ],
     * ]
     * ~~~
     *
     * 以上列示的"bootstrap"类将在应用[[bootstrap()|bootstrapping process]]期间被实例化。
     * 如果此类实现 [[BootstrapInterface]]接口，
     * 它的[[BootstrapInterface::bootstrap()|bootstrap()]]方法也被调用。
     */
    public $extensions = [];
    /**
     * @var array 在应用[[bootstrap()|bootstrapping process]]引导阶段要运行的组件列表
     *
     * 每个组件可指定为以下格式之一：
     *
     * - 通过[[components]]指定的应用组件 ID
     * - 通过[[modules]]指定的模块 ID
     * - 类名
     * - 配置数组
     *
     * 在引导过程中，每个组件将被实例化，如果一个组件类实现了[[BootstrapInterface]]接口，
     * 它的[[BootstrapInterface::bootstrap()|bootstrap()]]方法也会被调用。
     */
    public $bootstrap = [];
    /**
     * @var integer 在一个请求处理生命周期中的当前应用状态，此属性由应用管理，不要修改此属性。
     */
    public $state;


    /**
     * 构造函数
     * @param array $config 用于初始化对象属性的名值对数组
     * 注意配置数组必须包括[[id]]和[[basePath]]。
     * @throws InvalidConfigException 如果[[id]]或[[basePath]]的配置缺失
     */
    public function __construct($config = [])
    {
        Yii::$app = $this;

        $this->state = self::STATE_BEGIN;

        $this->registerErrorHandler($config);
        $this->preInit($config);

        Component::__construct($config);
    }

    /**
     * 预初始化应用
     * 该方法在应用构造函数开始时调用，它初始化了数个重要的应用属性。
     * 如果你要覆写此方法，请确保调用了父类实现
     * @param array $config 应用配置
     * @throws InvalidConfigException 如果[[id]]或[[basePath]]的配置缺失
     */
    public function preInit(&$config)
    {
        if (!isset($config['id'])) {
            throw new InvalidConfigException('The "id" configuration for the Application is required.');
        }
        if (isset($config['basePath'])) {
            $this->setBasePath($config['basePath']);
            unset($config['basePath']);
        } else {
            throw new InvalidConfigException('The "basePath" configuration for the Application is required.');
        }

        if (isset($config['vendorPath'])) {
            $this->setVendorPath($config['vendorPath']);
            unset($config['vendorPath']);
        } else {
            // set "@vendor"
            $this->getVendorPath();
        }
        if (isset($config['runtimePath'])) {
            $this->setRuntimePath($config['runtimePath']);
            unset($config['runtimePath']);
        } else {
            // set "@runtime"
            $this->getRuntimePath();
        }

        if (isset($config['timeZone'])) {
            $this->setTimeZone($config['timeZone']);
            unset($config['timeZone']);
        } elseif (!ini_get('date.timezone')) {
            $this->setTimeZone('UTC');
        }

        // merge core components with custom components
        foreach ($this->coreComponents() as $id => $component) {
            if (!isset($config['components'][$id])) {
                $config['components'][$id] = $component;
            } elseif (is_array($config['components'][$id]) && !isset($config['components'][$id]['class'])) {
                $config['components'][$id]['class'] = $component['class'];
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->state = self::STATE_INIT;
        $this->bootstrap();
    }

    /**
     * 初始化扩展并执行引导组件
     * 本方法由[[init()]]在应用已经完全配置后调用，如果要覆写本方法请确保也调用了父类实现
     */
    protected function bootstrap()
    {
        foreach ($this->extensions as $extension) {
            if (!empty($extension['alias'])) {
                foreach ($extension['alias'] as $name => $path) {
                    Yii::setAlias($name, $path);
                }
            }
            if (isset($extension['bootstrap'])) {
                $component = Yii::createObject($extension['bootstrap']);
                if ($component instanceof BootstrapInterface) {
                    Yii::trace("Bootstrap with " . get_class($component) . '::bootstrap()', __METHOD__);
                    $component->bootstrap($this);
                } else {
                    Yii::trace("Bootstrap with " . get_class($component), __METHOD__);
                }
            }
        }

        foreach ($this->bootstrap as $class) {
            $component = null;
            if (is_string($class)) {
                if ($this->has($class)) {
                    $component = $this->get($class);
                } elseif ($this->hasModule($class)) {
                    $component = $this->getModule($class);
                } elseif (strpos($class, '\\') === false) {
                    throw new InvalidConfigException("Unknown bootstrap component ID: $class");
                }
            }
            if (!isset($component)) {
                $component = Yii::createObject($class);
            }

            if ($component instanceof BootstrapInterface) {
                Yii::trace("Bootstrap with " . get_class($component) . '::bootstrap()', __METHOD__);
                $component->bootstrap($this);
            } else {
                Yii::trace("Bootstrap with " . get_class($component), __METHOD__);
            }
        }
    }

    /**
     * 注册错误处理器组件为 PHP 错误处理器
     */
    protected function registerErrorHandler(&$config)
    {
        if (YII_ENABLE_ERROR_HANDLER) {
            if (!isset($config['components']['errorHandler']['class'])) {
                echo "Error: no errorHandler component is configured.\n";
                exit(1);
            }
            $this->set('errorHandler', $config['components']['errorHandler']);
            unset($config['components']['errorHandler']);
            $this->getErrorHandler()->register();
        }
    }

    /**
     * 返回在当前应用内一个模块可区别于其它模块的唯一 ID
     * 由于这是一个应用实例，所以它总是返回一个空字符串
     * @return string 此模块的唯一 ID
     */
    public function getUniqueId()
    {
        return '';
    }

    /**
     * 设置应用和 @app 别名的根目录
     * 本方法只在构造函数开始时被调用
     * @param string $path 应用的根目录
     * @property string 应用的根目录
     * @throws InvalidParamException 如果目录不存在
     */
    public function setBasePath($path)
    {
        parent::setBasePath($path);
        Yii::setAlias('@app', $this->getBasePath());
    }

    /**
     * 运行此应用
     * 这是一个应用的主入口
     * @return integer 退出状态(0 是正常，非零值是非正常退出)
     */
    public function run()
    {
        try {

            $this->state = self::STATE_BEFORE_REQUEST;
            $this->trigger(self::EVENT_BEFORE_REQUEST);

            $this->state = self::STATE_HANDLING_REQUEST;
            $response = $this->handleRequest($this->getRequest());

            $this->state = self::STATE_AFTER_REQUEST;
            $this->trigger(self::EVENT_AFTER_REQUEST);

            $this->state = self::STATE_SENDING_RESPONSE;
            $response->send();

            $this->state = self::STATE_END;

            return $response->exitStatus;

        } catch (ExitException $e) {

            $this->end($e->statusCode, isset($response) ? $response : null);
            return $e->statusCode;

        }
    }

    /**
     * 处理指定请求
     *
     * 这个方法应返回代表请求处理结果的[[Response]]或其子类的实例
     *
     * @param Request $request 要处理的请求
     * @return Response 得到的响应
     */
    abstract public function handleRequest($request);

    private $_runtimePath;

    /**
     * 返回存储运行时文件的目录
     * @return string 存储运行时文件的目录，缺省为[[basePath]]下的"runtime" 子目录
     */
    public function getRuntimePath()
    {
        if ($this->_runtimePath === null) {
            $this->setRuntimePath($this->getBasePath() . DIRECTORY_SEPARATOR . 'runtime');
        }

        return $this->_runtimePath;
    }

    /**
     * 设置存储运行时文件的目录
     * @param string $path 存储运行时文件的目录
     */
    public function setRuntimePath($path)
    {
        $this->_runtimePath = Yii::getAlias($path);
        Yii::setAlias('@runtime', $this->_runtimePath);
    }

    private $_vendorPath;

    /**
     * 返回存储 vendor 文件的目录
     * @return string 存储 vendor 文件的目录，缺省为[[basePath]]下的"vendor"目录
     */
    public function getVendorPath()
    {
        if ($this->_vendorPath === null) {
            $this->setVendorPath($this->getBasePath() . DIRECTORY_SEPARATOR . 'vendor');
        }

        return $this->_vendorPath;
    }

    /**
     * 设置存储 vendor 文件的目录
     * @param string $path 存储 vendor 文件的目录
     */
    public function setVendorPath($path)
    {
        $this->_vendorPath = Yii::getAlias($path);
        Yii::setAlias('@vendor', $this->_vendorPath);
    }

    /**
     * 返回此应用使用的时区
     * 这是 PHP 函数 date_default_timezone_get() 的简单封装，
     * 如果未在 php.ini 或应用配置文件配置时区，它默认将设置为 UTC 。
     * @return string 本应用使用的时区
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function getTimeZone()
    {
        return date_default_timezone_get();
    }

    /**
     * 设置此应用使用的时区
     * 这是 PHP 函数 date_default_timezone_set() 的简单封装，可用时区请参考[php 手册](http://www.php.net/manual/en/timezones.php)
     * @param string $value 本应用使用的时区
     * @see http://php.net/manual/en/function.date-default-timezone-set.php
     */
    public function setTimeZone($value)
    {
        date_default_timezone_set($value);
    }

    /**
     * 返回数据库连接组件
     * @return \yii\db\Connection 数据库连接
     */
    public function getDb()
    {
        return $this->get('db');
    }

    /**
     * 返回日志调度器组件
     * @return \yii\log\Dispatcher 日志调度组件
     */
    public function getLog()
    {
        return $this->get('log');
    }

    /**
     * 返回错误处理器组件
     * @return \yii\web\ErrorHandler|\yii\console\ErrorHandler 错误处理器组件
     */
    public function getErrorHandler()
    {
        return $this->get('errorHandler');
    }

    /**
     * 返回缓存组件
     * @return \yii\caching\Cache 缓存组件，如果此组件未启用返回 Null
     */
    public function getCache()
    {
        return $this->get('cache', false);
    }

    /**
     * 返回格式器组件
     * @return \yii\base\Formatter 格式器组件
     */
    public function getFormatter()
    {
        return $this->get('formatter');
    }

    /**
     * 返回请求组件
     * @return \yii\web\Request|\yii\console\Request 请求组件
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * 返回响应组件
     * @return \yii\web\Response|\yii\console\Response 响应组件
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * 返回视图对象
     * @return View|\yii\web\View 用于渲染各种视图文件的视图对象
     */
    public function getView()
    {
        return $this->get('view');
    }

    /**
     * 返回此应用的 URL 管理器
     * @return \yii\web\UrlManager 此应用的 URL 管理器
     */
    public function getUrlManager()
    {
        return $this->get('urlManager');
    }

    /**
     * 返回国际化(i18n)组件
     * @return \yii\i18n\I18N 国际化组件
     */
    public function getI18n()
    {
        return $this->get('i18n');
    }

    /**
     * 返回邮件收发器组件
     * @return \yii\mail\MailerInterface 邮件收发器接口
     */
    public function getMail()
    {
        return $this->get('mail');
    }

    /**
     * 返回此应用的认证管理器
     * @return \yii\rbac\ManagerInterface 此应用的认证管理器，如果认证管理器未配置返回 null
     */
    public function getAuthManager()
    {
        return $this->get('authManager', false);
    }

    /**
     * 返回资源管理器
     * @return \yii\web\AssetManager 资源管理器
     */
    public function getAssetManager()
    {
        return $this->get('assetManager');
    }

    /**
     * 返回核心应用组件
     * @see set
     */
    public function coreComponents()
    {
        return [
            'log' => ['class' => 'yii\log\Dispatcher'],
            'view' => ['class' => 'yii\web\View'],
            'formatter' => ['class' => 'yii\base\Formatter'],
            'i18n' => ['class' => 'yii\i18n\I18N'],
            'mail' => ['class' => 'yii\swiftmailer\Mailer'],
            'urlManager' => ['class' => 'yii\web\UrlManager'],
            'assetManager' => ['class' => 'yii\web\AssetManager'],
        ];
    }

    /**
     * 终止应用
     * 本方法替换了`exit()` 方法以确保应用的生命周期在终止应用前已完成。
     * @param integer $status 退出状态(值为 0 是正常退出而其他值是非正常退出)
     * @param Response $response 要发送的响应，如未设置，将使用默认的应用组件[[response]]
     * @throws ExitException 如果应用处于测试模式
     */
    public function end($status = 0, $response = null)
    {
        if ($this->state === self::STATE_BEFORE_REQUEST || $this->state === self::STATE_HANDLING_REQUEST) {
            $this->state = self::STATE_AFTER_REQUEST;
            $this->trigger(self::EVENT_AFTER_REQUEST);
        }

        if ($this->state !== self::STATE_SENDING_RESPONSE && $this->state !== self::STATE_END) {
            $this->state = self::STATE_END;
            $response = $response ? : $this->getResponse();
            $response->send();
        }

        if (YII_ENV_TEST) {
            throw new ExitException($status);
        } else {
            exit($status);
        }
    }
}
