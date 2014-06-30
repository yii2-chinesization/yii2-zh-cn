<?php
/**
 * 翻译日期：20140509
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;
use ReflectionClass;

/**
 * Widget(小部件)是所有小部件的基类
 *
 * @property string $id 小部件 ID
 * @property \yii\web\View $view 用于渲染视图或视图文件的视图对象，
 * 注意此属性的类型在 getter 和 setter 不相同，细节见[[getView()]]和[[setView()]]。
 * @property string $viewPath 包括此小部件视图文件的目录，是只读属性。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Widget extends Component implements ViewContextInterface
{
    /**
     * @var integer 用于生成小部件[[id]]的计数器
     * @internal
     */
    public static $counter = 0;
    /**
     * @var string 自动生成小部件 ID 的前缀
     * @see getId()
     */
    public static $autoIdPrefix = 'w';

    /**
     * @var Widget[] 当前正被渲染（未结束）的小部件，此属性由[[begin()]]和[[end()]]维持
     * @internal
     */
    public static $stack = [];

    /**
     * 小部件开始
     * 此方法创建了一个调用类的实例，它将应用传入配置到刚创建的实例，配套的[[end()]]方法稍后必须调用。
     * @param array $config 用于初始化对象属性的名值对
     * @return static 新创建的小部件实例
     */
    public static function begin($config = [])
    {
        $config['class'] = get_called_class();
        /** @var Widget $widget */
        $widget = Yii::createObject($config);
        static::$stack[] = $widget;

        return $widget;
    }

    /**
     * 小部件结束
     * 注意此小部件的渲染结果被直接输出了。
     * @return static 要结束的小部件实例
     * @throws InvalidCallException 如果[[begin()]]和[[end()]]嵌套调用不正确
     */
    public static function end()
    {
        if (!empty(static::$stack)) {
            $widget = array_pop(static::$stack);
            if (get_class($widget) === get_called_class()) {
                echo $widget->run();
                return $widget;
            } else {
                throw new InvalidCallException("Expecting end() of " . get_class($widget) . ", found " . get_called_class());
            }
        } else {
            throw new InvalidCallException("Unexpected " . get_called_class() . '::end() call. A matching begin() is not found.');
        }
    }

    /**
     * 创建一个小部件实例并运行
     * 小部件的渲染结果由此方法返回
     * @param array $config 用来初始化对象属性的名值对
     * @return string 小部件的渲染结果
     */
    public static function widget($config = [])
    {
        ob_start();
        ob_implicit_flush(false);
        /** @var Widget $widget */
        $config['class'] = get_called_class();
        $widget = Yii::createObject($config);
        $out = $widget->run();

        return ob_get_clean() . $out;
    }

    private $_id;

    /**
     * 返回小部件 ID
     * @param boolean $autoGenerate 如果之前未设置是否生成一个 ID
     * @return string 小部件 ID
     */
    public function getId($autoGenerate = true)
    {
        if ($autoGenerate && $this->_id === null) {
            $this->_id = static::$autoIdPrefix . static::$counter++;
        }

        return $this->_id;
    }

    /**
     * 设置小部件 ID
     * @param string $value 小部件 ID
     */
    public function setId($value)
    {
        $this->_id = $value;
    }

    private $_view;

    /**
     * 返回可用于渲染视图或视图文件的视图对象
     * [[render()]]和[[renderFile()]]方法将使用该视图对象实现真正的视图渲染，
     * 如果未设置，默认是"view" 应用组件。
     * @return \yii\web\View 可用于渲染视图或视图文件的视图对象
     */
    public function getView()
    {
        if ($this->_view === null) {
            $this->_view = Yii::$app->getView();
        }

        return $this->_view;
    }

    /**
     * 设置要被此小部件使用的视图对象
     * @param View $view 可用于渲染视图或视图文件的视图对象
     */
    public function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * 执行小部件
     * @return string 小部件执行的输出结果
     */
    public function run()
    {
    }

    /**
     * 渲染视图
     * 要渲染的视图可指定为以下格式之一：
     *
     * - 路径别名(如"@app/views/site/index")；
     * - 应用内的绝对路径(如"//site/index")：以双斜线开头的视图名
     *   真正的视图文件将在应用的[[Application::viewPath|view path]]下查找
     * - 模块内的绝对路径(如"/site/index")：以单斜线开头的视图名
     *   真正的视图文件将在当前活动模块的[[Module::viewPath|view path]]下查找
     * - 相对路径(如"index")：真正的视图路径将在[[viewPath]]下查找
     *
     * 如果视图名不包括文件扩展名，它将使用缺省的`.php` 。
     *
     * @param string $view 视图名
     * @param array $params 视图内可用的名值对参数
     * @return string 渲染结果
     * @throws InvalidParamException 如果视图文件不存在
     */
    public function render($view, $params = [])
    {
        return $this->getView()->render($view, $params, $this);
    }

    /**
     * 渲染视图文件
     * @param string $file 要渲染的视图文件，文件路径或路径别名
     * @param array $params 视图文件的可用参数(名值对)
     * @return string 渲染结果
     * @throws InvalidParamException 如果视图文件不存在
     */
    public function renderFile($file, $params = [])
    {
        return $this->getView()->renderFile($file, $params, $this);
    }

    /**
     * 返回包括小部件视图文件的目录
     * 默认实现返回小部件类文件目录内的'views'子目录
     * @return string 包括小部件视图文件的目录
     */
    public function getViewPath()
    {
        $class = new ReflectionClass($this);

        return dirname($class->getFileName()) . DIRECTORY_SEPARATOR . 'views';
    }
}
