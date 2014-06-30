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

/**
 * ActionFilter（动作过滤器）是所有动作过滤器的基类
 *
 * 动作过滤器通过响应被模块或控制器触发的`beforeAction` 和 `afterAction` 事件来参与动作执行流程
 *
 * 检查[[\yii\filters\AccessControl]]、[[\yii\filters\PageCache]]和[[\yii\filters\HttpCache]]的实现示例来了解怎样使用它
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ActionFilter extends Behavior
{
    /**
     * @var array 该过滤器要运用到的动作的 ID 列表，如果本属性未设置，过滤器就运用到所有动作，
     * 除了列入[[except]]的过滤器。如果动作 ID 在[[only]]和[[except]]都有，该过滤器将*不*适用这个动作。
     *
     * 注意如果过滤器被添加到模块，动作 ID 也要包括子模块 ID （如有）和控制器 ID
     *
     * @see except
     */
    public $only;
    /**
     * @var array 本过滤器不适用的动作 ID
     * @see only
     */
    public $except = [];


    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        $this->owner = $owner;
        $owner->on(Controller::EVENT_BEFORE_ACTION, [$this, 'beforeFilter']);
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        if ($this->owner) {
            $this->owner->off(Controller::EVENT_BEFORE_ACTION, [$this, 'beforeFilter']);
            $this->owner->off(Controller::EVENT_AFTER_ACTION, [$this, 'afterFilter']);
            $this->owner = null;
        }
    }

    /**
     * @param ActionEvent $event
     */
    public function beforeFilter($event)
    {
        if (!$this->isActive($event->action)) {
            return;
        }

        $event->isValid = $this->beforeAction($event->action);
        if ($event->isValid) {
            // call afterFilter only if beforeFilter succeeds
            // beforeFilter and afterFilter should be properly nested
            $this->owner->on(Controller::EVENT_AFTER_ACTION, [$this, 'afterFilter'], null, false);
        } else {
            $event->handled = true;
        }
    }

    /**
     * @param ActionEvent $event
     */
    public function afterFilter($event)
    {
        $event->result = $this->afterAction($event->action, $event->result);
        $this->owner->off(Controller::EVENT_AFTER_ACTION, [$this, 'afterFilter']);
    }

    /**
     * 此方法在动作被执行前的瞬间被调用(所有可能的过滤器执行后)
     * 你可以覆写本方法为动作执行前做最后一分钟的准备
     * @param Action $action 要执行的动作
     * @return boolean 动作是否继续执行
     */
    public function beforeAction($action)
    {
        return true;
    }

    /**
     * 此方法在动作被执行后的瞬间被调用，你可以覆写它来未动作做一些收尾工作
     * @param Action $action 刚执行完的动作
     * @param mixed $result 动作执行结果
     * @return mixed 处理后的动作结果
     */
    public function afterAction($action, $result)
    {
        return $result;
    }

    /**
     * 返回一个值来表明此过滤器是否对给定动作激活
     * @param Action $action 正被过滤的动作
     * @return boolean 此过滤器是否对给定动作是活动的
     */
    protected function isActive($action)
    {
        if ($this->owner instanceof Module) {
            // convert action uniqueId into an ID relative to the module
            $mid = $this->owner->getUniqueId();
            $id = $action->getUniqueId();
            if ($mid !== '' && strpos($id, $mid) === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }
        return !in_array($id, $this->except, true) && (empty($this->only) || in_array($id, $this->only, true));
    }
}
