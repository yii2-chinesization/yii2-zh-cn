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

/**
 * ArrayAccessTrait 实现了[[\IteratorAggregate]], [[\ArrayAccess]]和[[\Countable]]。
 *
 * 注意 ArrayAccessTrait 要求使用它的类包括一个数组形式的名为`data`的属性，
 * 此数据将被 ArrayAccessTrait 暴露以支持如数组般访问类对象
 *
 * @property array $data
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
trait ArrayAccessTrait
{
    /**
     * 返回一个迭代器来遍历数据
     * 此方法被 SPL 接口`IteratorAggregate`所要求，它在你使用`foreach`遍历集合时将隐式调用
     * @return \ArrayIterator 为遍历集合的 cookies 而新创建的数组迭代器对象
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * 返回数据项的数量
     * 此方法被 Countable 接口所要求
     * @return integer 数据元素的数目
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * 此方法被 ArrayAccess 接口所要求
     * @param mixed $offset 开始检查的偏移量
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * 此方法被 ArrayAccess 接口所要求
     * @param integer $offset 检索元素的偏移量
     * @return mixed 偏移量上的元素，如果在偏移量没有找到元素就返回 null
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * 此方法被 ArrayAccess 接口所要求
     * @param integer $offset 设置元素的偏移量
     * @param mixed $item 元素值
     */
    public function offsetSet($offset, $item)
    {
        $this->data[$offset] = $item;
    }

    /**
     * 此方法被 ArrayAccess 接口所要求
     * @param mixed $offset 清除（unset）元素的偏移量
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
