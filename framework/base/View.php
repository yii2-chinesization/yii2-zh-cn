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
use yii\helpers\FileHelper;
use yii\widgets\Block;
use yii\widgets\ContentDecorator;
use yii\widgets\FragmentCache;

/**
 * View（视图类）表示 MVC 模式中的视图对象
 *
 * View 为渲染目的提供了一系列方法(如[[render()]])
 *
 * @property string|boolean $viewFile 当前要渲染的视图文件，如果没有视图文件被渲染就用 false ，这是只读属性。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class View extends Component
{
    /**
     * @event Event 由[[beginPage()]]触发的事件
     */
    const EVENT_BEGIN_PAGE = 'beginPage';
    /**
     * @event Event 由[[endPage()]]触发的事件
     */
    const EVENT_END_PAGE = 'endPage';
    /**
     * @event ViewEvent 由[[renderFile()]]恰好在它渲染视图文件前触发的事件
     */
    const EVENT_BEFORE_RENDER = 'beforeRender';
    /**
     * @event ViewEvent [[renderFile()]]恰好在它渲染了视图文件后触发的事件
     */
    const EVENT_AFTER_RENDER = 'afterRender';

    /**
     * @var ViewContextInterface [[renderFile()]]方法被调用的上下文
     */
    public $context;
    /**
     * @var mixed 视图模板共享的自定义参数
     */
    public $params = [];
    /**
     * @var array 以支持的对应文件扩展名为索引的适用渲染器列表
     * 每个渲染器可以是视图渲染器对象或创建渲染器对象的配置
     * 例如，以下配置都启用了 Smarty 和 Twig 视图渲染器：
     *
     * ~~~
     * [
     *     'tpl' => ['class' => 'yii\smarty\ViewRenderer'],
     *     'twig' => ['class' => 'yii\twig\ViewRenderer'],
     * ]
     * ~~~
     *
     * 如果给定的视图文件没有适用的渲染器，视图文件将视作普通 PHP 文件并由[[renderPhpFile()]]渲染。
     */
    public $renderers;
    /**
     * @var string 视图文件缺省扩展名，如果视图文件没有扩展名，此变量将被追加到视图文件名后。
     */
    public $defaultExtension = 'php';
    /**
     * @var Theme|array|string 主题对象或创建主题对象的配置，如果未设置，意味着主题未启用。
     */
    public $theme;
    /**
     * @var array 指定输出块列表，数组键是块名而值是相应的块内容。
     * 可以调用[[beginBlock()]]和[[endBlock()]]来捕获视图的小片段。
     * 之后这些块内容可以在别处通过此属性访问。
     */
    public $blocks;
    /**
     * @var array 当前活动片段缓存小部件的列表，此属性用于内部实现内容缓存功能。不要直接使用它。
     * @internal
     */
    public $cacheStack = [];
    /**
     * @var array 为嵌入动态内容的占位符的列表，本属性用于内部实现内容缓存功能，不要直接使用它。
     * @internal
     */
    public $dynamicPlaceholders = [];

    /**
     * @var array 当前正被渲染的视图文件，可能同时有多个视图文件被渲染，
     * 因为一个视图可以在另一个视图内被渲染。
     */
    private $_viewFiles = [];


    /**
     * 初始化视图组件
     */
    public function init()
    {
        parent::init();
        if (is_array($this->theme)) {
            if (!isset($this->theme['class'])) {
                $this->theme['class'] = 'yii\base\Theme';
            }
            $this->theme = Yii::createObject($this->theme);
        } elseif (is_string($this->theme)) {
            $this->theme = Yii::createObject($this->theme);
        }
    }

    /**
     * 渲染视图
     *
     * 要渲染的视图可指定为以下格式：
     *
     * - 路径别名(如"@app/views/site/index")；
     * - 应用内的绝对路径(如"//site/index")：以双斜线开头的视图名，
     *   真正的视图文件将在当前应用的[[Application::viewPath|view path]]下查找。
     * - 当前模块内的绝对路径(如"/site/index")：以单斜线开头的视图名，
     *   真正的视图文件将在[[Controller::module|current module]]的[[Module::viewPath|view path]]查找。
     * - 相对路径(如"index")：不以`@`或`/`开头的视图名，
     *   相应的视图文件将在视图的`$context`属性的[[ViewContextInterface::getViewPath()|view path]]下查找。
     *   如果`$context` 未给出，它将在包括当前正被渲染视图的目录下查找
     *   (如，当在一个视图内渲染另一个视图时就会发生这个)。
     *
     * @param string $view 视图名
     * @param array $params 提取到视图文件且可用的参数（名值对）
     * @param object $context 被分配到视图的上下文，稍后可通过视图的[[context]]访问。
     * 如果上下文执行[[ViewContextInterface]]，它也能用于根据相应的相对视图名定位视图文件。
     * @return string 渲染结果
     * @throws InvalidParamException 如果视图不能解析或视图文件不存在
     * @see renderFile()
     */
    public function render($view, $params = [], $context = null)
    {
        $viewFile = $this->findViewFile($view, $context);
        return $this->renderFile($viewFile, $params, $context);
    }

    /**
     * 基于给定视图名查找视图文件
     * @param string $view 视图名或视图文件的路径别名，请参考[[render()]]了解如何指定此参数
     * @param object $context 赋给视图的上下文，稍后通过视图中的[[context]]访问。
     * 如果上下文实现了[[ViewContextInterface]]，它也可以用于根据相对视图名定位视图文件。
     * @return string 视图文件路径，注意此文件可能不存在
     * @throws InvalidCallException 如果相对视图名已给定，而没有活动上下文确定相应的视图文件
     */
    protected function findViewFile($view, $context = null)
    {
        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = Yii::getAlias($view);
        } elseif (strncmp($view, '//', 2) === 0) {
            // e.g. "//layouts/main"
            $file = Yii::$app->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
        } elseif (strncmp($view, '/', 1) === 0) {
            // e.g. "/site/index"
            if (Yii::$app->controller !== null) {
                $file = Yii::$app->controller->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
            } else {
                throw new InvalidCallException("Unable to locate view file for view '$view': no active controller.");
            }
        } elseif ($context instanceof ViewContextInterface) {
            $file = $context->getViewPath() . DIRECTORY_SEPARATOR . $view;
        } elseif (($currentViewFile = $this->getViewFile()) !== false) {
            $file = dirname($currentViewFile) . DIRECTORY_SEPARATOR . $view;
        } else {
            throw new InvalidCallException("Unable to resolve view file for view '$view': no active view context.");
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $this->defaultExtension;
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }

    /**
     * 渲染视图文件
     *
     * 如果[[theme]]已启用(不是 null)，它将尝试渲染视图文件的主题版本，只要它是可用的。
     *
     * 本方法将调用[[FileHelper::localize()]]来定位视图文件
     *
     * 如果[[renderers|renderer]]已启用(不是 null)，本方法将用它来渲染该视图文件，否则，它将简单引入视图文件作为普通 PHP 文件，并捕获它的输出作为字符串返回。
     *
     * @param string $viewFile 要渲染的视图文件，文件路径或路径别名
     * @param array $params 提取到视图文件的可用参数(名值对)
     * @param object $context 视图要使用来渲染此视图的上下文，如果是 null ，已有的[[context]]将被使用
     * @return string 渲染结果
     * @throws InvalidParamException 如果视图文件不存在
     */
    public function renderFile($viewFile, $params = [], $context = null)
    {
        $viewFile = Yii::getAlias($viewFile);

        if ($this->theme !== null) {
            $viewFile = $this->theme->applyTo($viewFile);
        }
        if (is_file($viewFile)) {
            $viewFile = FileHelper::localize($viewFile);
        } else {
            throw new InvalidParamException("The view file does not exist: $viewFile");
        }

        $oldContext = $this->context;
        if ($context !== null) {
            $this->context = $context;
        }
        $output = '';
        $this->_viewFiles[] = $viewFile;

        if ($this->beforeRender()) {
            Yii::trace("Rendering view file: $viewFile", __METHOD__);
            $ext = pathinfo($viewFile, PATHINFO_EXTENSION);
            if (isset($this->renderers[$ext])) {
                if (is_array($this->renderers[$ext]) || is_string($this->renderers[$ext])) {
                    $this->renderers[$ext] = Yii::createObject($this->renderers[$ext]);
                }
                /** @var ViewRenderer $renderer */
                $renderer = $this->renderers[$ext];
                $output = $renderer->render($this, $viewFile, $params);
            } else {
                $output = $this->renderPhpFile($viewFile, $params);
            }
            $this->afterRender($output);
        }

        array_pop($this->_viewFiles);
        $this->context = $oldContext;

        return $output;
    }

    /**
     * @return string|boolean 当前被渲染的视图文件，如果没有正被渲染的视图文件就返回 false
     */
    public function getViewFile()
    {
        return end($this->_viewFiles);
    }

    /**
     * 本方法在[[renderFile()]]渲染视图文件前调用
     * 默认实现将触发[[EVENT_BEFORE_RENDER]]事件
     * 如果要覆写该方法，确保首先调用了父类实现
     * @return boolean 是否继续渲染视图文件
     */
    public function beforeRender()
    {
        $event = new ViewEvent;
        $this->trigger(self::EVENT_BEFORE_RENDER, $event);

        return $event->isValid;
    }

    /**
     * 本方法在[[renderFile()]]渲染视图文件后调用
     * 默认实现将触发[[EVENT_AFTER_RENDER]]事件
     * 如果要覆写该方法，确保首先调用了父类实现
     * @param string $output 视图文件的渲染结果，此参数的更新将被[[renderFile()]]传递回来并返回
     */
    public function afterRender(&$output)
    {
        if ($this->hasEventHandlers(self::EVENT_AFTER_RENDER)) {
            $event = new ViewEvent;
            $event->output = $output;
            $this->trigger(self::EVENT_AFTER_RENDER, $event);
            $output = $event->output;
        }
    }

    /**
     * Renders a view file as a PHP script.
     *
     * 此方法将视图文件视为 PHP 脚本并引入该文件，它提取给定参数并令它们在视图文件中可用。
     * 此方法捕获引入视图文件的输出并作为字符串返回。
     *
     * 此方法主要由视图渲染器或[[renderFile()]]调用
     *
     * @param string $_file_ 视图文件
     * @param array $_params_ 提取到视图文件并可被视图使用的参数(名值对) that will be extracted and made available in the view file.
     * @return string the rendering result
     */
    public function renderPhpFile($_file_, $_params_ = [])
    {
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        require($_file_);

        return ob_get_clean();
    }

    /**
     * 渲染由给定 PHP 表达式返回的动态内容
     * 此方法主要在一些内容部分（称为*动态内容*）不应该被缓存时和内容缓存（片段缓存和页面缓存）一起使用。
     * 动态内容必须是由一些 PHP 表达式返回。
     * @param string $statements 生成动态内容的 PHP 表达式
     * @return string 动态内容占位符，如果没有当前活动的内容缓存就返回动态内容
     */
    public function renderDynamic($statements)
    {
        if (!empty($this->cacheStack)) {
            $n = count($this->dynamicPlaceholders);
            $placeholder = "<![CDATA[YII-DYNAMIC-$n]]>";
            $this->addDynamicPlaceholder($placeholder, $statements);

            return $placeholder;
        } else {
            return $this->evaluateDynamicContent($statements);
        }
    }

    /**
     * 为动态内容添加占位符
     * 此方法是在内部使用
     * @param string $placeholder 占位符名
     * @param string $statements 为生成动态内容的 PHP 表达式
     */
    public function addDynamicPlaceholder($placeholder, $statements)
    {
        foreach ($this->cacheStack as $cache) {
            $cache->dynamicPlaceholders[$placeholder] = $statements;
        }
        $this->dynamicPlaceholders[$placeholder] = $statements;
    }

    /**
     * 对给定 PHP 表达式求值
     * 此方法主要用于内部实现动态内容功能
     * @param string $statements 要计算的 PHP 表达式
     * @return mixed  PHP 表达式的返回值
     */
    public function evaluateDynamicContent($statements)
    {
        return eval($statements);
    }

    /**
     * 开始记录块
     * 本方法是开始[[Block]]的快捷方式
     * @param string $id 块 ID
     * @param boolean $renderInPlace 是否一步到位渲染块内容
     * 缺省为 false ，即捕获的块不显示出来
     * @return Block  "块"小部件实例
     */
    public function beginBlock($id, $renderInPlace = false)
    {
        return Block::begin([
            'id' => $id,
            'renderInPlace' => $renderInPlace,
            'view' => $this,
        ]);
    }

    /**
     * 结束记录块
     */
    public function endBlock()
    {
        Block::end();
    }

    /**
     * 开始渲染被指定视图装饰的内容
     * 此方法可用于实现嵌套布局，如，一个布局可以嵌入另一个如下
     * 指定为'@app/views/layouts/base.php'的布局文件：
     *
     * ~~~
     * <?php $this->beginContent('@app/views/layouts/base.php'); ?>
     * ...布局内容写这里...
     * <?php $this->endContent(); ?>
     * ~~~
     *
     * @param string $viewFile 视图文件，用于装饰被此小部件包裹的内容，
     * 此参数可以指定为视图文件路径或路径别名。
     * @param array $params 要提取的名值对参数并在装饰视图可用
     * @return ContentDecorator "内容装饰器"小部件实例
     * @see ContentDecorator
     */
    public function beginContent($viewFile, $params = [])
    {
        return ContentDecorator::begin([
            'viewFile' => $viewFile,
            'params' => $params,
            'view' => $this,
        ]);
    }

    /**
     * 结束内容渲染
     */
    public function endContent()
    {
        ContentDecorator::end();
    }

    /**
     * 开始片段缓存
     * 此方法如果可用就现实已缓存内容，如果不可用，它就开始缓存并
     * 期望调用[[endCache()]]来结束缓存并保存内容到缓存。
     * 片段缓存的典型用法如下：
     *
     * ~~~
     * if ($this->beginCache($id)) {
     *     // ...生成内容在这里
     *     $this->endCache();
     * }
     * ~~~
     *
     * @param string $id 标识要缓存片段的唯一 ID
     * @param array $properties 为[[FragmentCache]]初始化属性值
     * @return boolean 是否为缓存生成内容，如果现有缓存版本可用就返回 false
     */
    public function beginCache($id, $properties = [])
    {
        $properties['id'] = $id;
        $properties['view'] = $this;
        /** @var FragmentCache $cache */
        $cache = FragmentCache::begin($properties);
        if ($cache->getCachedContent() !== false) {
            $this->endCache();

            return false;
        } else {
            return true;
        }
    }

    /**
     * 结束片段缓存
     */
    public function endCache()
    {
        FragmentCache::end();
    }

    /**
     * 标记页面的开始
     */
    public function beginPage()
    {
        ob_start();
        ob_implicit_flush(false);

        $this->trigger(self::EVENT_BEGIN_PAGE);
    }

    /**
     * 标记页面的结束
     */
    public function endPage()
    {
        $this->trigger(self::EVENT_END_PAGE);
        ob_end_flush();
    }
}
