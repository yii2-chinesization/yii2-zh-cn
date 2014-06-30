<?php
/**
 * 翻译日期：20140507
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;

/**
 * Action（动作类）是所有控制器动作类的基类
 *
 * 动作类提供了一个方式来分离复杂控制器，即将更小的动作类放到单独的类文件
 *
 * 动作类的衍生类必须实现`run()`方法，此方法当动作被请求时将被控制器调用。
 * `run()`方法可以根据参数名把用户输入值自动填充到参数。
 * 例如，如果`run()`方法声明如下：
 *
 * ~~~
 * public function run($id, $type = 'book') { ... }
 * ~~~
 *
 * 提供给该动作的参数值是：`['id' => 1]`，那么`run()`方法将作为`run(1)`自动调用
 *
 * @property string $uniqueId 此动作在整个应用范围内的唯一 ID ，本属性是只读属性。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Action extends Component
{
    /**
     * @var string 动作 ID
     */
    public $id;
    /**
     * @var Controller 拥有此动作的控制器
     */
    public $controller;

    /**
     * Constructor（构造函数）
     *
     * @param string $id 动作 ID
     * @param Controller $controller 拥有此动作的控制器
     * @param array $config 用于初始化对象属性的名值对
     */
    public function __construct($id, $controller, $config = [])
    {
        $this->id = $id;
        $this->controller = $controller;
        parent::__construct($config);
    }

    /**
     * 返回此动作在整个应用内的唯一 ID
     *
     * @return string 返回此动作在整个应用内的唯一 ID
     */
    public function getUniqueId()
    {
        return $this->controller->getUniqueId() . '/' . $this->id;
    }

    /**
     * 使用指定参数运行此动作
     * 此方法主要由控制器调用
     *
     * @param array $params 要绑定到动作run()方法的参数
     * @return mixed 动作结果
     * @throws InvalidConfigException 如果动作类没有run()方法
     */
    public function runWithParams($params)
    {
        if (!method_exists($this, 'run')) {
            throw new InvalidConfigException(get_class($this) . ' must define a "run()" method.');
        }
        $args = $this->controller->bindActionParams($this, $params);
        Yii::trace('Running action: ' . get_class($this) . '::run()', __METHOD__);
        if (Yii::$app->requestedParams === null) {
            Yii::$app->requestedParams = $args;
        }
        if ($this->beforeRun()) {
            $result = call_user_func_array([$this, 'run'], $args);
            $this->afterRun();

            return $result;
        } else {
            return null;
        }
    }

    /**
     * 此方法在run()执行前被调用，你可以覆写此方法来为动作运行做些准备工作。
     * 如果此方法返回 false ，它将取消动作。
     *
     * @return boolean 是否运行动作
     */
    protected function beforeRun()
    {
        return true;
    }

    /**
     * 此方法在run()执行后被调用，你可以覆写此方法来为动作运行做些收尾工作。
     */
    protected function afterRun()
    {
    }
}
