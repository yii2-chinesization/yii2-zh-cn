<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\web;

use Yii;
use yii\base\InvalidRouteException;

/**
 * Application （应用）类是所有 WEB 应用类的基类
 *
 * @property string $homeUrl 主页的 URL。
 * @property Session $session Session 组件，为只读属性.
 * @property User $user 用户组件，只读属性。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Application extends \yii\base\Application
{
    /**
     * @var string 此应用的默认路由. 缺省是 'site'.
     */
    public $defaultRoute = 'site';
    /**
     * @var array 这个配置选项指定了一个用于处理（译者注：其实就是拦截）所有有用户请求的 Controller 动作。
     * 这个主要是用在当整个应用处于维护状态，进而需要用一个单独的动作，处理所有发来的请求的状况下。
     * 该配置项是一个数组，他的第一个元素指定该动作的路由，其余数组元素（都系键值对）指定提供给
     * 这个动作的所有参数（parameters）。就像这样，噹噹噹噹(〜￣△￣)〜
     *
     * ~~~
     * [
     *     'offline/notice',
     *     'param1' => 'value1',
     *     'param2' => 'value2',
     * ]
     * ~~~
     *
     * 缺省是 null，指 catch-all 选项未被使用。
     */
    public $catchAll;
    /**
     * @var Controller 当前处于活跃状态的 Controller 的实例
     */
    public $controller;

    /**
     * @inheritdoc
     */
    protected function bootstrap()
    {
        $request = $this->getRequest();
        Yii::setAlias('@webroot', dirname($request->getScriptFile()));
        Yii::setAlias('@web', $request->getBaseUrl());

        parent::bootstrap();
    }

    /**
     * 处理特定请求。
     * @param Request $request 是需被处理的那个请求
     * @return Response 作为结果返回的响应
     * @throws NotFoundHttpException 若该请求路由无效则抛异常
     */
    public function handleRequest($request)
    {
        if (empty($this->catchAll)) {
            list ($route, $params) = $request->resolve();
        } else {
            $route = $this->catchAll[0];
            $params = array_splice($this->catchAll, 1);
        }
        try {
            Yii::trace("Route requested: '$route'", __METHOD__);
            $this->requestedRoute = $route;
            $result = $this->runAction($route, $params);
            if ($result instanceof Response) {
                return $result;
            } else {
                $response = $this->getResponse();
                if ($result !== null) {
                    $response->data = $result;
                }

                return $response;
            }
        } catch (InvalidRouteException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private $_homeUrl;

    /**
     * @return string 返回 homepage URL
     */
    public function getHomeUrl()
    {
        if ($this->_homeUrl === null) {
            if ($this->getUrlManager()->showScriptName) {
                return $this->getRequest()->getScriptUrl();
            } else {
                return $this->getRequest()->getBaseUrl() . '/';
            }
        } else {
            return $this->_homeUrl;
        }
    }

    /**
     * @param string $value 是 homepage URL
     */
    public function setHomeUrl($value)
    {
        $this->_homeUrl = $value;
    }

    /**
     * 返回 session 组件。
     * @return Session session 组件
     */
    public function getSession()
    {
        return $this->get('session');
    }

    /**
     * 返回用户组件
     * @return User user 组件
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'request' => ['class' => 'yii\web\Request'],
            'response' => ['class' => 'yii\web\Response'],
            'session' => ['class' => 'yii\web\Session'],
            'user' => ['class' => 'yii\web\User'],
        ]);
    }

    /**
     * 把 errorHandler 组件注册为一个 PHP Error Handler。
     */
    protected function registerErrorHandler(&$config)
    {
        if (!isset($config['components']['errorHandler']['class'])) {
            $config['components']['errorHandler']['class'] = 'yii\\web\\ErrorHandler';
        }
        parent::registerErrorHandler($config);
    }
}
