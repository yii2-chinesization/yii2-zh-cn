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
use yii\base\InvalidConfigException;

/**
 * Instance（实例类）代表在依赖注入（DI）容器或服务定位器中对指定对象的引用
 *
 * 你可以使用[[get()]]获得被[[id]]引用的真正对象。
 *
 * 实例类主要用于两个地方：
 *
 * - 当配置一个依赖注入容器时，你使用实例来引用一个类名、接口名或别名。
 *   此引用稍后能被容器解析到真正的对象。
 * - 在使用服务定位器的类中用来获得依赖对象。
 *
 * 以下示例演示了如何使用实例配置一个 DI 容器：
 *
 * ```php
 * $container = new \yii\di\Container;
 * $container->set('cache', 'yii\caching\DbCache', Instance::of('db'));
 * $container->set('db', [
 *     'class' => 'yii\db\Connection',
 *     'dsn' => 'sqlite:path/to/file.db',
 * ]);
 * ```
 *
 * 以下例子显示一个类如何从一个服务定位器检索一个组件：
 *
 * ```php
 * class DbCache extends Cache
 * {
 *     public $db = 'db';
 *
 *     public function init()
 *     {
 *         parent::init();
 *         $this->db = Instance::ensure($this->db, 'yii\db\Connection');
 *     }
 * }
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Instance
{
    /**
     * @var string 组件 ID 、类名、接口名或别名
     */
    public $id;

    /**
     * 构造函数
     * @param string $id 组件 ID
     */
    protected function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * 创建一个新的实例对象
     * @param string $id 组件 ID
     * @return Instance 新的实例对象
     */
    public static function of($id)
    {
        return new static($id);
    }

    /**
     * 解析指定引用到真正的对象并确保它是指定类型
     *
     * 引用可以指定为字符串或一个实例对象，如果是前者，它将根据容器类型被视为组件 ID 、类或接口名及别名
     *
     * 如果你不指定一个容器，本方法将首先尝试`Yii::$app`，随后是`Yii::$container`
     *
     * 如：
     *
     * ```php
     * use yii\db\Connection;
     *
     * // 返回 Yii::$app->db
     * $db = Instance::ensure('db', Connection::className());
     * // 或
     * $instance = Instance::of('db');
     * $db = Instance::ensure($instance, Connection::className());
     * ```
     *
     * @param object|string|static $reference 一个对象或一个引用到所需对象
     * 你可以指定一个组件 ID 或一个实例对象形式的引用
     * @param string $type 要核对的类名或接口名，如果是 null ，类型核对将不执行
     * @param ServiceLocator|Container $container 容器，这个参数将被传递到[[get()]]
     * @return object 实例引用的对象或如果是对象的话返回`$reference`自身
     * @throws InvalidConfigException 如果引用无效
     */
    public static function ensure($reference, $type = null, $container = null)
    {
        if ($reference instanceof $type) {
            return $reference;
        } elseif (empty($reference)) {
            throw new InvalidConfigException('The required component is not specified.');
        }

        if (is_string($reference)) {
            $reference = new static($reference);
        }

        if ($reference instanceof self) {
            $component = $reference->get($container);
            if ($component instanceof $type || $type === null) {
                return $component;
            } else {
                throw new InvalidConfigException('"' . $reference->id . '" refers to a ' . get_class($component) . " component. $type is expected.");
            }
        }

        $valueType = is_object($reference) ? get_class($reference) : gettype($reference);
        throw new InvalidConfigException("Invalid data type: $valueType. $type is expected.");
    }

    /**
     * 返回被此实例对象引用的真正对象
     * @param ServiceLocator|Container $container 用于定位被引用对象的容器
     * 如为 null ，此方法首先尝试`Yii::$app`然后`Yii::$container`
     * @return object 本实例对象引用的真实对象
     */
    public function get($container = null)
    {
        if ($container) {
            return $container->get($this->id);
        }
        if (Yii::$app && Yii::$app->has($this->id)) {
            return Yii::$app->get($this->id);
        } else {
            return Yii::$container->get($this->id);
        }
    }
}
