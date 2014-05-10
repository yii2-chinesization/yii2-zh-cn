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

/**
 * InlineAction 表示动作以控制器方法来定义
 *
 * 控制器方法名可通过创建该动作的[[controller]]所设置的[[actionMethod]]使用
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InlineAction extends Action
{
    /**
     * @var string 此内联动作关联的控制器方法
     */
    public $actionMethod;

    /**
     * @param string $id 动作 ID
     * @param Controller $controller 拥有该动作的控制器
     * @param string $actionMethod 此内联动作关联的控制器方法
     * @param array $config 用于初始化对象属性的名值对数组
     */
    public function __construct($id, $controller, $actionMethod, $config = [])
    {
        $this->actionMethod = $actionMethod;
        parent::__construct($id, $controller, $config);
    }

    /**
     * 以指定参数运行此动作
     * 本方法主要由控制器调用
     * @param array $params 动作参数
     * @return mixed 动作执行结果
     */
    public function runWithParams($params)
    {
        $args = $this->controller->bindActionParams($this, $params);
        Yii::trace('Running action: ' . get_class($this->controller) . '::' . $this->actionMethod . '()', __METHOD__);
        if (Yii::$app->requestedParams === null) {
            Yii::$app->requestedParams = $args;
        }

        return call_user_func_array([$this->controller, $this->actionMethod], $args);
    }
}
