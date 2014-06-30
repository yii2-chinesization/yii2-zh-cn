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

/**
 * Theme（主题）代表应用的主题
 *
 * 当[[View]]渲染视图文件时，它将检查[[View::theme|active theme]]以查看是否存在视图文件的主题版本。
 * 如果有，就用主题版本渲染。
 *
 * 主题是由以一对一取代非主题视图文件为目的的视图文件组成的目录。
 *
 * 主题使用[[pathMap]]来实现视图文件替换：
 *
 * 1. 首先，它在[[pathMap]]查找数组键，那是给定视图文件路径的子字符串；
 * 2. 如果存在这样的键，相应的值就被用来替换视图文件路径的相应部分（即数组值取代数组键）；
 * 3. 然后它将检查更新的视图文件是否存在，如果存在，那个文件就用来取得原始的视图文件。
 * 4. 如果步骤 2 或步骤 3 失败了，将使用原始的视图文件。
 *
 * 例如，如果[[pathMap]]是`['@app/views' => '@app/themes/basic']`,
 * 那边该视图文件`@app/views/site/index.php`的主题版本就是`@app/themes/basic/site/index.php`。
 *
 * 映射一个路径到多个路径是可能的，如：
 *
 * ~~~
 * 'pathMap' => [
 *     '@app/views' => [
 *         '@app/themes/christmas',
 *         '@app/themes/basic',
 *     ],
 * ]
 * ~~~
 *
 * 这种情况下，主题版本可以是`@app/themes/christmas/site/index.php`或`@app/themes/basic/site/index.php`
 * 如果两个文件都存在，前者对后者有优先权。
 *
 * 要使用视图，你应如下配置"view"应用组件的[[View::theme|theme]]属性：
 *
 * ~~~
 * 'view' => [
 *     'theme' => [
 *         'basePath' => '@app/themes/basic',
 *         'baseUrl' => '@web/themes/basic',
 *     ],
 * ],
 * ~~~
 *
 * 以上配置指定了一个位于 Web 文件夹内的"themes/basic"目录下的主题，在 Web 文件夹包括了应用的入口脚本。
 * 如果你的主题设计来处理模块，你可以如上描述的配置[[pathMap]]属性。
 *
 * @property string $basePath 此主题的根路径，此主题的所有资源都位于该目录下。
 * @property string $baseUrl 此主题的根 URL (没有末尾的斜线)，此主题的所有资源被认为位于此根 URL，这是只读属性。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Theme extends Component
{
    /**
     * @var array 视图目录和它们对应的主题版本的映射
     * 如未设置，它将被初始化为[[Application::basePath]]和[[basePath]]的映射。
     * 当视图尝试应用该主题时，本属性由[[applyTo()]]使用。
     * 当指定了目录时，路径别名可使用
     */
    public $pathMap;

    /**
     * 初始化主题
     * @throws InvalidConfigException 如[[basePath]] 未设置
     */
    public function init()
    {
        parent::init();

        if (empty($this->pathMap)) {
            if (($basePath = $this->getBasePath()) === null) {
                throw new InvalidConfigException('The "basePath" property must be set.');
            }
            $this->pathMap = [Yii::$app->getBasePath() => [$basePath]];
        }
    }

    private $_baseUrl;

    /**
     * @return string 主题的根 URL (无末尾斜线)，此主题的所有资源被认为位于本根 URL 下。
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * @param $url string 主题的根 URL 或路径别名，此主题的所有资源被认为位于本根 URL 下。
     */
    public function setBaseUrl($url)
    {
        $this->_baseUrl = rtrim(Yii::getAlias($url), '/');
    }

    private $_basePath;

    /**
     * @return string 此主题的根路径，此主题的所有资源位于该目录
     * @see pathMap
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * @param string $path 此主题的根路径或路径别名，此主题的所有资源都位于该目录下
     * @see pathMap
     */
    public function setBasePath($path)
    {
        $this->_basePath = Yii::getAlias($path);
    }

    /**
     * 如果可能就将一根文件转换为一个主题文件
     * 如果没有对应的主题文件，原始文件将被返回。
     * @param string $path 要被主题文件替换的文件
     * @return string 主题文件，如果主题版本不可用就返回原始文件
     */
    public function applyTo($path)
    {
        $path = FileHelper::normalizePath($path);
        foreach ($this->pathMap as $from => $tos) {
            $from = FileHelper::normalizePath(Yii::getAlias($from)) . DIRECTORY_SEPARATOR;
            if (strpos($path, $from) === 0) {
                $n = strlen($from);
                foreach ((array) $tos as $to) {
                    $to = FileHelper::normalizePath(Yii::getAlias($to)) . DIRECTORY_SEPARATOR;
                    $file = $to . substr($path, $n);
                    if (is_file($file)) {
                        return $file;
                    }
                }
            }
        }

        return $path;
    }

    /**
     * 使用[[baseUrl]]把相对 URL 转换为绝对 URL
     * @param string $url 要转换的相对 URL
     * @return string 绝对 URL
     * @throws InvalidConfigException 如[[baseUrl]]未设置
     */
    public function getUrl($url)
    {
        if (($baseUrl = $this->getBaseUrl()) !== null) {
            return $baseUrl . '/' . ltrim($url, '/');
        } else {
            throw new InvalidConfigException('The "baseUrl" property must be set.');
        }
    }

    /**
     * 使用[[basePath]]把相对文件路径转换为绝对路径
     * @param string $path 要转换的相对文件路径
     * @return string 绝对文件路径
     * @throws InvalidConfigException 如[[baseUrl]]未设置
     */
    public function getPath($path)
    {
        if (($basePath = $this->getBasePath()) !== null) {
            return $basePath . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        } else {
            throw new InvalidConfigException('The "basePath" property must be set.');
        }
    }
}
