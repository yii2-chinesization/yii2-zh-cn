<?php
/**
 * 翻译日期：20140513
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\helpers;

use Yii;
use yii\base\InvalidParamException;

/**
 * BaseUrl 为[[Url]]提供具体实现
 *
 * 不要使用 BaseUrl ，而是使用[[Url]]代替
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @since 2.0
 */
class BaseUrl
{
    /**
     * 为给定路由创建一个 URL
     *
     * 本方法将使用[[\yii\web\UrlManager]]创建 URL
     *
     * 你可以指定路由为一个字符串，如`site/index`，如果你想为要创建的 URL 指定额外的查询参数，
     * 你也可以使用一个数组，数组格式是：
     *
     * ```php
     * // 生成： /index.php?r=site/index&param1=value1&param2=value2
     * ['site/index', 'param1' => 'value1', 'param2' => 'value2']
     * ```
     *
     * 如果你想创建一个带锚点的 URL ，可以使用带 `#` 参数的数组格式
     * For example,
     *
     * ```php
     * // 生成： /index.php?r=site/index&param1=value1#name
     * ['site/index', 'param1' => 'value1', '#' => 'name']
     * ```
     *
     * 路由可以是绝对的或相对的，绝对路由前带斜杠(如`/site/index`)，
     * 而相对路由前面没有斜杠(如`site/index` or `index`)。相对路由可由以下规则转变成绝对路由：
     *
     * - 如果路由是空字符串，将使用当前的[[\yii\web\Controller::route|route]]；
     * - 如果路由根本不包含任何斜杠(如`index`)，
     *   就认为它是当前控制器的动作并在它前面加上[[\yii\web\Controller::uniqueId]]；
     * - 如果路由前没有斜杠(如`site/index`),
     *   就认为它是相对于当前模块的路由并在它前面加上模块的[[\yii\base\Module::uniqueId|uniqueId]]。
     *
     * 下面是使用本方法的一些例子：
     *
     * ```php
     * // /index?r=site/index
     * echo Url::toRoute('site/index');
     *
     * // /index?r=site/index&src=ref1#name
     * echo Url::toRoute(['site/index', 'src' => 'ref1', '#' => 'name']);
     *
     * // http://www.example.com/index.php?r=site/index
     * echo Url::toRoute('site/index', true);
     *
     * // https://www.example.com/index.php?r=site/index
     * echo Url::toRoute('site/index', 'https');
     * ```
     *
     * @param string|array $route 使用一个字符串代表一个路由(如`index`, `site/index`)，
     * 或一个数组代表一个带查询参数的路由(如`['site/index', 'param1' => 'value1']`)。
     * @param boolean|string $scheme 在生成的 URL 使用的 URI 命名结构：
     *
     * - `false` (缺省): 生成一个相对 URL
     * - `true`: 生成一个命名结构和当前请求相同的绝对 URL
     * - string: 生成具有指定命名结构的绝对 URL(`http`或`https`)
     *
     * @return string 生成后的 URL
     * @throws InvalidParamException 给定的相对路由没有对应的活动控制器
     */
    public static function toRoute($route, $scheme = false)
    {
        $route = (array)$route;
        $route[0] = static::normalizeRoute($route[0]);

        if ($scheme) {
            return Yii::$app->getUrlManager()->createAbsoluteUrl($route, is_string($scheme) ? $scheme : null);
        } else {
            return Yii::$app->getUrlManager()->createUrl($route);
        }
    }

    /**
     * 标准化路由，使得它适合于 UrlManager ，绝对路由保留原状而相对路由转变为绝对路由
     *
     * 相对路由是前面没有斜杠的路由，如"view", "post/view" 。
     *
     * - 如果路由是空字符串，使用当前[[\yii\web\Controller::route|route]]；
     * - 如果路由完全不包含任何斜杠，它被认为是当前控制器的一个动作 ID
     *   并在其之前加上[[\yii\web\Controller::uniqueId]]；
     * - 如果路由前没有斜杠，它被认为是相对当前模块的路由，并在其前面加上模块的唯一 ID 。
     *
     * @param string $route 路由，绝对路由或相对路由均可
     * @return string 为适合 UrlManager 而标准化了的路由
     * @throws InvalidParamException 给定相对路由没有对应的活动控制器
     */
    protected static function normalizeRoute($route)
    {
        $route = (string) $route;
        if (strncmp($route, '/', 1) === 0) {
            // absolute route
            return ltrim($route, '/');
        }

        // relative route
        if (Yii::$app->controller === null) {
            throw new InvalidParamException("Unable to resolve the relative route: $route. No active controller is available.");
        }

        if (strpos($route, '/') === false) {
            // empty or an action ID
            return $route === '' ? Yii::$app->controller->getRoute() : Yii::$app->controller->getUniqueId() . '/' . $route;
        } else {
            // relative to module
            return ltrim(Yii::$app->controller->module->getUniqueId() . '/' . $route, '/');
        }
    }

    /**
     * 基于给定参数创建 URL
     *
     * 本方法非常类似于[[toRoute()]]，唯一的区别是本方法需要路由只能指定为数组。
     * 如果给的是字符串，它将被视为一个 URL ，且如果它没有以斜杠开头给其附上根 URL 前缀。
     * 尤其是当`$url`是
     *
     * - 数组：[[toRoute()]]将被调用来生成 URL ，如：
     *   `['site/index']`, `['post/index', 'page' => 2]`. 请参考[[toRoute()]]了解如何指定路由。
     * - 以`@`开头的字符串：它被视为一个别名，对应的别名字符串从属于以下规则。
     * - 空字符串：当前被请求的 URL 将被返回；
     * - 没有前置斜杠的字符串：它将加上前缀[[\yii\web\Request::baseUrl]]。
     * - 有前置斜杠的字符串：它将返回自身
     *
     * 注意在`$scheme`已指定的情况下(字符串或 true)，将返回带主机信息的绝对 URL
     *
     * 以下是使用本方法的一些例子：
     *
     * ```php
     * // /index?r=site/index
     * echo Url::to(['site/index']);
     *
     * // /index?r=site/index&src=ref1#name
     * echo Url::to(['site/index', 'src' => 'ref1', '#' => 'name']);
     *
     * // 当前被请求的 URL
     * echo Url::to();
     *
     * // /images/logo.gif
     * echo Url::to('images/logo.gif');
     *
     * // http://www.example.com/index.php?r=site/index
     * echo Url::to(['site/index'], true);
     *
     * // https://www.example.com/index.php?r=site/index
     * echo Url::to(['site/index'], 'https');
     * ```
     *
     *
     * @param array|string $url 用来生成一个有效 URL 的参数
     * @param boolean|string $scheme  在已生成的 URL 使用的 URI 命名结构：
     *
     * - `false` (缺省): 生成一个相对 URL.
     * - `true`: 生成一个命名结构和当前请求相同的绝对 URL
     * - string: 以指定命名结构生成一个绝对 URL (`http`或`https`)
     *
     * @return string 已生成的 URL
     * @throws InvalidParamException 给定的相对路径没有当前活动控制器
     */
    public static function to($url = '', $scheme = false)
    {
        if (is_array($url)) {
            return static::toRoute($url, $scheme);
        }

        $url = (string) Yii::getAlias($url);

        if ($url === '') {
            $url = Yii::$app->getRequest()->getUrl();
        } elseif ($url[0] !== '/' && $url[0] !== '#' && $url[0] !== '.' && strpos($url, '://') === false) {
            $url = Yii::$app->getRequest()->getBaseUrl() . '/' . $url;
        }

        if ($scheme) {
            if (strpos($url, '://') === false) {
                $url = Yii::$app->getRequest()->getHostInfo() . '/' . ltrim($url, '/');
            }
            if (is_string($scheme) && ($pos = strpos($url, '://')) !== false) {
                $url = $scheme . substr($url, $pos);
            }
        }

        return $url;
    }

    /**
     * 返回当前请求的根 URL
     * @param boolean|string $scheme 在返回的根 URL 使用的 URI 命名结构：
     *
     * - `false` (缺省): 返回没有主机信息的根 URL
     * - `true`: 返回命名结构和当前请求相同的绝对根 URL
     * - string: 返回指定命名结构的绝对根 URL(`http`或`https`)
     * @return string
     */
    public static function base($scheme = false)
    {
        $url = Yii::$app->getRequest()->getBaseUrl();
        if ($scheme) {
            $url = Yii::$app->getRequest()->getHostInfo() . $url;
            if (is_string($scheme) && ($pos = strpos($url, '://')) !== false) {
                $url = $scheme . substr($url, $pos);
            }
        }
        return $url;
    }

    /**
     * 记住指定 URL 以便之后可由[[previous()]]取回
     *
     * @param string|array $url 要记住的 URL ，请参考[[to()]]了解接受的格式
     * 如果此参数未指定，将使用当前请求 URL
     * @param string $name 关联到要记住的 URL 的名称，此参数之后可由[[previous()]]使用，
     * 如果未设置，将使用[[\yii\web\User::returnUrlParam]]
     * @see previous()
     */
    public static function remember($url = '', $name = null)
    {
        $url = static::to($url);

        if ($name === null) {
            Yii::$app->getUser()->setReturnUrl($url);
        } else {
            Yii::$app->getSession()->set($name, $url);
        }
    }

    /**
     * 返回之前[[remember()|remembered]]的 URL
     *
     * @param string $name 关联到之前已记住的 URL 的名称
     * 如果未设置，将使用[[\yii\web\User::returnUrlParam]]
     * @return string 之前被记住的 URL ，如果没有给定名的 URL 被记住就返回 Null
     * @see remember()
     */
    public static function previous($name = null)
    {
        if ($name === null) {
            return Yii::$app->getUser()->getReturnUrl();
        } else {
            return Yii::$app->getSession()->get($name);
        }
    }

    /**
     * 返回当前被请求页的标准 URL
     * 标准的 URL 用当前控制器的[[\yii\web\Controller::route]]和[[\yii\web\Controller::actionParams]]
     * 组成，你可以使用以下代码到布局视图以添加一个有关标准 URL 的链接标签：
     *
     * ```php
     * $this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
     * ```
     *
     * @return string 当前被请求页面的标准 URL
     */
    public static function canonical()
    {
        $params = Yii::$app->controller->actionParams;
        $params[0] = Yii::$app->controller->getRoute();

        return Yii::$app->getUrlManager()->createAbsoluteUrl($params);
    }

    /**
     * 返回主页 URL.
     *
     * @param boolean|string $scheme 为返回 URL 所使用的 URI 命名结构：
     *
     * - `false` (缺省): 返回相对 URL
     * - `true`: 返回命名结构和当前请求相同的绝对 URL
     * - string: 返回指定命名结构的绝对 URL (`http`或`https`)
     *
     * @return string home URL
     */
    public static function home($scheme = false)
    {
        $url = Yii::$app->getHomeUrl();

        if ($scheme) {
            $url = Yii::$app->getRequest()->getHostInfo() . $url;
            if (is_string($scheme) && ($pos = strpos($url, '://')) !== false) {
                $url = $scheme . substr($url, $pos);
            }
        }

        return $url;
    }
}
