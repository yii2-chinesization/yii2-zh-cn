<?php
/**
 * @since 中文版翻译日期：20140505
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii;

use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\UnknownClassException;
use yii\log\Logger;
use yii\di\Container;

/**
 * 定义应用启动时间戳
 */
defined('YII_BEGIN_TIME') or define('YII_BEGIN_TIME', microtime(true));
/**
 * 该常量定义框架安装目录
 */
defined('YII_PATH') or define('YII_PATH', __DIR__);
/**
 * 该常量定义应用是否用调试模式，默认为 false
 */
defined('YII_DEBUG') or define('YII_DEBUG', false);
/**
 * 该常量定义了应用运行在哪个环境。缺省为'prod'，即生产环境。
 * 你可以在引导脚本定义这个常量，值可以是'prod' (生产), 'dev' (开发), 'test', 'staging', 等。
 */
defined('YII_ENV') or define('YII_ENV', 'prod');
/**
 * 应用是否运行在生产环境
 */
defined('YII_ENV_PROD') or define('YII_ENV_PROD', YII_ENV === 'prod');
/**
 * 应用是否运行在开发环境
 */
defined('YII_ENV_DEV') or define('YII_ENV_DEV', YII_ENV === 'dev');
/**
 * 应用是否运行在测试环境
 */
defined('YII_ENV_TEST') or define('YII_ENV_TEST', YII_ENV === 'test');

/**
 * 此常量定义错误处理是否启用，缺省为 true 。
 */
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);

/**
 * BaseYii 是 Yii 框架的核心辅助类
 *
 * 不要直接使用 BaseYii ，而是使用它的子类[[\Yii]]，可以在这个子类自定义 BaseYii 的方法。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BaseYii
{
    /**
     * @var array 类图，被 Yii 自动加载机制所使用
     * 数组键是类名（没有前面的反斜线），数组值是相应的类文件路径（或路径别名）
     * 本属性主要影响[[autoload()]]如何工作
     * @see autoload()
     */
    public static $classMap = [];
    /**
     * @var \yii\console\Application|\yii\web\Application 应用实例
     */
    public static $app;
    /**
     * @var array 已注册的路径别名
     * @see getAlias()
     * @see setAlias()
     */
    public static $aliases = ['@yii' => __DIR__];
    /**
     * @var Container 依赖注入容器，用于[[createObject()]]
     * 你可以参阅[[Container::set()]]来设置类必须的依赖和同名的初始属性值
     * @see createObject()
     * @see Container
     */
    public static $container;


    /**
     * 返回代表 Yii 框架当前版本的字符串
     * @return string  Yii 框架当前版本
     */
    public static function getVersion()
    {
        return '2.0.0-dev';
    }

    /**
     * 翻译路径别名到真实路径
     *
     * 翻译根据以下流程完成：
     *
     * 1. 如果给定别名没有以'@'开头，就原样返回；
     * 2. 否则，查找已注册最长的别名来匹配给定别名的开始部分。
     * 如果存在，把给定别名的匹配部分替换为相应的已注册路径。
     * 3. 根据`$throwException` 参数抛出异常或返回 false 。
     *
     * 例如：默认'@yii'是注册为 Yii 框架目录的别名，即'/path/to/yii'，
     * 那么别名'@yii/web'就翻译为'/path/to/yii/web'。
     *
     * 如果你已经注册了两个别名'@foo' 和 '@foo/bar'，然后'@foo/bar/config'
     * 将把'@foo/bar'部分 (而不是'@foo')替换为相应的已注册路径。
     * 因为长别名有优先权。
     *
     * 然而，如果拟翻译别名是'@foo/barbar/config'，那么将用'@foo'而不是'@foo/bar'来替换，
     * 因为'/'才是边界符。
     *
     * 注意，本方法不检查返回的路径是否存在。
     *
     * @param string $alias 要被翻译的别名
     * @param boolean $throwException 如果给定别名是无效的，是否抛出异常。
     * 如果是 false 且给的是无效别名，本方法将返回 false 。
     * @return string|boolean 别名相应的路径，如果根别名之前没有注册就返回 false 。
     * @throws InvalidParamException 当 $throwException 为 true 时如果别名无效就抛出异常。
     * @see setAlias()
     */
    public static function getAlias($alias, $throwException = true)
    {
        if (strncmp($alias, '@', 1)) {
            // 不是别名
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
            } else {
                foreach (static::$aliases[$root] as $name => $path) {
                    if (strpos($alias . '/', $name . '/') === 0) {
                        return $path . substr($alias, strlen($name));
                    }
                }
            }
        }

        if ($throwException) {
            throw new InvalidParamException("Invalid path alias: $alias");
        } else {
            return false;
        }
    }

    /**
     * 返回给定别名的根别名部分。
     * 根别名之前已通过[[setAlias()]]注册。
     * 如果给定别名匹配多个根别名，将返回最长的那个。
     * @param string $alias 别名
     * @return string|boolean 根别名，如果没有找到根别名返回 false 。
     */
    public static function getRootAlias($alias)
    {
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $root;
            } else {
                foreach (static::$aliases[$root] as $name => $path) {
                    if (strpos($alias . '/', $name . '/') === 0) {
                        return $name;
                    }
                }
            }
        }

        return false;
    }

    /**
     * 注册路径别名
     *
     * 路径别名是表示长路径（文件路径、 URL 等）的简称。
     * 例如，我们使用'@yii'作为 Yii 框架目录的路径别名。
     *
     * 路径别名必须以'@'开头，以便它能容易地和非别名路径容易区分。
     *
     * 注意本方法不检查给定路径是否存在，它所做的只是将别名和路径关联起来。
     *
     * 给定路径末尾的任何一个 '/' and '\' 都会去掉。
     *
     * @param string $alias 别名 (如"@yii")，以'@'开头
     * 当[[getAlias()]]执行别名翻译时可以包括正斜杠作为边界符。
     * @param string $path 别名对应的路径，如果是 null，别名将会删除，
     * 末尾的'/'和'\'将剪掉。路径可以是：
     *
     * - 目录或文件路径(如`/tmp`, `/tmp/main.txt`)
     * -  URL (如`http://www.yiiframework.com`)
     * - 路径别名(如`@yii/base`)，这种情况下将先调用[[getAlias()]]把路径别名转为实际的路径。
     *
     * @throws InvalidParamException 如果 $path 是无效别名
     * @see getAlias()
     */
    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if ($path !== null) {
            $path = strncmp($path, '@', 1) ? rtrim($path, '\\/') : static::getAlias($path);
            if (!isset(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [$alias => $path];
                }
            } elseif (is_string(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [
                        $alias => $path,
                        $root => static::$aliases[$root],
                    ];
                }
            } else {
                static::$aliases[$root][$alias] = $path;
                krsort(static::$aliases[$root]);
            }
        } elseif (isset(static::$aliases[$root])) {
            if (is_array(static::$aliases[$root])) {
                unset(static::$aliases[$root][$alias]);
            } elseif ($pos === false) {
                unset(static::$aliases[$root]);
            }
        }
    }

    /**
     * 类自动加载器
     * 本方法当 PHP 遇到未知类时会自动调用
     * 本方法将尝试根据以下流程包含类文件：
     *
     * 1. 在[[classMap]]搜索；
     * 2. 如果类有命名空间(如`yii\base\Component`)，本方法将尝试包含关联到相应路径别名的文件(如`@yii/base/Component.php`)；
     *
     * 本自动加载器允许加载的类要求遵循[PSR-4 标准](http://www.php-fig.org/psr/psr-4/)
     * 并用路径别名定义了最高层命名空间或子命名空间。
     *
     * 例如：当别名`@yii` 和 `@yii/bootstrap` 已定义，`yii\bootstrap`命名空间的类将使用
     * `@yii/bootstrap` 别名加载，此别名指向的目录是 bootstrap 扩展文件安装的目录，
     * 而其他`yii`命名空间的所有类就从 Yii 框架目录加载。
     *
     * @param string $className 完整合格类名，没有前面的反斜线"\"。
     * @throws UnknownClassException 如果类文件的类不存在
     */
    public static function autoload($className)
    {
        if (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];
            if ($classFile[0] === '@') {
                $classFile = static::getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
            $classFile = static::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }

        include($classFile);

        if (YII_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new UnknownClassException("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }

    /**
     * 用给定配置创建新对象
     *
     * 你可以将此方法视为`new`操作符的增强版本。
     * 本方法支持基于类名、配置数组或匿名函数创建一个对象。
     *
     * 以下是一些用法示例：
     *
     * ```php
     * // 用类名创建对象
     * $object = Yii::createObject('yii\db\Connection');
     *
     * // 用配置数组创建对象
     * $object = Yii::createObject([
     *     'class' => 'yii\db\Connection',
     *     'dsn' => 'mysql:host=127.0.0.1;dbname=demo',
     *     'username' => 'root',
     *     'password' => '',
     *     'charset' => 'utf8',
     * ]);
     *
     * // 创建一个对象并给其构造函数传入两个参数
     * $object = \Yii::createObject('MyClass', [$param1, $param2]);
     * ```
     *
     * 使用[[\yii\di\Container|依赖注入容器]]，本方法还能识别依赖对象、实例化它们并注入新建对象。
     *
     * @param string|array|callable $type 对象类型，可以指定为以下形态：
     *
     * - 字符串：表示拟创建对象的类名。
     * - 配置数组：该数组必须包括一个`class` 元素来表示对象类，其他键值对将用于初始化相应的对象属性。
     * - PHP 回调函数：匿名函数或表示类方法的数组(`[$class or $object, $method]`)，
     *   回调函数应返回拟创建对象的实例。
     *
     * @param array $params 构造函数参数
     * @return object 新建对象
     * @throws InvalidConfigException 如果配置无效
     * @see \yii\di\Container
     */
    public static function createObject($type, array $params = [])
    {
        if (is_string($type)) {
            return static::$container->get($type, $params);
        } elseif (is_array($type) && isset($type['class'])) {
            $class = $type['class'];
            unset($type['class']);
            return static::$container->get($class, $params, $type);
        } elseif (is_callable($type, true)) {
            return call_user_func($type, $params);
        } elseif (is_array($type)) {
            throw new InvalidConfigException('Object configuration must be an array containing a "class" element.');
        } else {
            throw new InvalidConfigException("Unsupported configuration type: " . gettype($type));
        }
    }

    private static $_logger;

    /**
     * @return Logger 消息记录器
     */
    public static function getLogger()
    {
        if (self::$_logger !== null) {
            return self::$_logger;
        } else {
            return self::$_logger = static::createObject('yii\log\Logger');
        }
    }

    /**
     * 设置记录器对象
     * @param Logger $logger 记录器对象
     */
    public static function setLogger($logger)
    {
        self::$_logger = $logger;
    }

    /**
     * 踪迹消息日志
     * 踪迹消息日志主要用于开发目的以查看某些代码的执行工作流
     * @param string $message 待记录的消息
     * @param string $category 消息类别
     */
    public static function trace($message, $category = 'application')
    {
        if (YII_DEBUG) {
            static::getLogger()->log($message, Logger::LEVEL_TRACE, $category);
        }
    }

    /**
     * 错误消息日志
     * 错误消息通常在应用执行期不可捕获的错误发生时记录
     * @param string $message 待记录消息
     * @param string $category 消息类别
     */
    public static function error($message, $category = 'application')
    {
        static::getLogger()->log($message, Logger::LEVEL_ERROR, $category);
    }

    /**
     * 警告消息日志
     * 警告消息通常在发生错误但执行仍能继续时记录
     * @param string $message 要记录的消息
     * @param string $category 消息的类别
     */
    public static function warning($message, $category = 'application')
    {
        static::getLogger()->log($message, Logger::LEVEL_WARNING, $category);
    }

    /**
     * Logs an informative message.
     * An informative message is typically logged by an application to keep record of
     * something important (e.g. an administrator logs in).
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public static function info($message, $category = 'application')
    {
        static::getLogger()->log($message, Logger::LEVEL_INFO, $category);
    }

    /**
     * Marks the beginning of a code block for profiling.
     * This has to be matched with a call to [[endProfile]] with the same category name.
     * The begin- and end- calls must also be properly nested. For example,
     *
     * ~~~
     * \Yii::beginProfile('block1');
     * // some code to be profiled
     *     \Yii::beginProfile('block2');
     *     // some other code to be profiled
     *     \Yii::endProfile('block2');
     * \Yii::endProfile('block1');
     * ~~~
     * @param string $token token for the code block
     * @param string $category the category of this log message
     * @see endProfile()
     */
    public static function beginProfile($token, $category = 'application')
    {
        static::getLogger()->log($token, Logger::LEVEL_PROFILE_BEGIN, $category);
    }

    /**
     * Marks the end of a code block for profiling.
     * This has to be matched with a previous call to [[beginProfile]] with the same category name.
     * @param string $token token for the code block
     * @param string $category the category of this log message
     * @see beginProfile()
     */
    public static function endProfile($token, $category = 'application')
    {
        static::getLogger()->log($token, Logger::LEVEL_PROFILE_END, $category);
    }

    /**
     * Returns an HTML hyperlink that can be displayed on your Web page showing "Powered by Yii Framework" information.
     * @return string an HTML hyperlink that can be displayed on your Web page showing "Powered by Yii Framework" information
     */
    public static function powered()
    {
        return 'Powered by <a href="http://www.yiiframework.com/" rel="external">Yii Framework</a>';
    }

    /**
     * Translates a message to the specified language.
     *
     * This is a shortcut method of [[\yii\i18n\I18N::translate()]].
     *
     * The translation will be conducted according to the message category and the target language will be used.
     *
     * You can add parameters to a translation message that will be substituted with the corresponding value after
     * translation. The format for this is to use curly brackets around the parameter name as you can see in the following example:
     *
     * ```php
     * $username = 'Alexander';
     * echo \Yii::t('app', 'Hello, {username}!', ['username' => $username]);
     * ```
     *
     * Further formatting of message parameters is supported using the [PHP intl extensions](http://www.php.net/manual/en/intro.intl.php)
     * message formatter. See [[\yii\i18n\I18N::translate()]] for more details.
     *
     * @param string $category the message category.
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     * @return string the translated message.
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        if (static::$app !== null) {
            return static::$app->getI18n()->translate($category, $message, $params, $language ?: static::$app->language);
        } else {
            $p = [];
            foreach ((array) $params as $name => $value) {
                $p['{' . $name . '}'] = $value;
            }

            return ($p === []) ? $message : strtr($message, $p);
        }
    }

    /**
     * 以初始化属性值配置对象
     * @param object $object 待配置对象
     * @param array $properties 给定的属性初始值，是名值对形式。
     * @return object 对象本身
     */
    public static function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }

    /**
     * 返回对象的公共成员变量
     * 本方法提供给我们获取对象的公共成员变量
     * 它不同于"get_object_vars()" ，因为后者如果在对象自己的内部调用将返回包括私有和受保护变量的
     * 所有可访问的非静态变量。
     * @param object $object 要处理的对象
     * @return array 对象的公共成员变量
     */
    public static function getObjectVars($object)
    {
        return get_object_vars($object);
    }
}
