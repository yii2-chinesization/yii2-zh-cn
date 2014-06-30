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
use yii\helpers\ArrayHelper;
use yii\web\Link;
use yii\web\Linkable;

/**
 * ArrayableTrait 提供了[[Arrayable]]接口的公共实现
 *
 * ArrayableTrait 通过遵守[[fields()]]和[[extraFields()]]所声明的字段定义来实现[[toArray()]]
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
trait ArrayableTrait
{
    /**
     * 当没有特定字段被指定时返回默认由[[toArray()]]返回的字段列表
     *
     * 一个字段是[[toArray()]]返回的数组中的指定元素
     *
     * 本方法应返回字段名或字段定义的数组，如果是前者，字段名将视为对象属性名，其值用作字段值；
     * 如果是后者，数组键作为字段名而数组值作为对应的字段定义，
     * 可以是对象属性名或返回对应字段值的 PHP 回调函数，回调函数的标志是：
     *
     * ```php
     * function ($field, $model) {
     *     // 返回字段值
     * }
     * ```
     *
     * 如，以下代码声明了四个字段：
     *
     * - `email`：字段名等于属性名`email`；
     * - `firstName` 和 `lastName`：字段名是`firstName` 和 `lastName`，
     *   而它们的值从`first_name`和`last_name`属性获取；
     * - `fullName`：字段名是`fullName`，它的值通过连接`first_name`和`last_name`获得。
     *
     * ```php
     * return [
     *     'email',
     *     'firstName' => 'first_name',
     *     'lastName' => 'last_name',
     *     'fullName' => function () {
     *         return $this->first_name . ' ' . $this->last_name;
     *     },
     * ];
     * ```
     *
     * 在此方法中，你也想基于一些上下文信息返回不同的字段列表，
     * 如，根据当前应用的用户权限，你想返回一套可见性不同的字段或过滤掉一些字段。
     *
     * 此方法的默认实现是返回对象的公共成员变量
     *
     * @return array 字段名或字段定义的列表
     * @see toArray()
     */
    public function fields()
    {
        $fields = array_keys(Yii::getObjectVars($this));

        return array_combine($fields, $fields);
    }

    /**
     * 返回由[[toArray()]]返回的可进一步扩展的字段列表
     *
     * 本方法类似于[[fields()]]，除了本方法返回的字段列表默认不是由[[toArray()]]所返回，
     * 只有当要扩展的字段名在调用[[toArray()]]时显式指定，它们的值才会输出。
     *
     * 默认实现返回了空数组
     *
     * 你可以覆写本方法以基于某些上下文信息（如应用当前用户）来返回可扩展的字段列表。
     *
     * @return array 可扩展的字段名或字段定义的列表，请参阅[[fields()]]以了解更多返回值的格式
     * @see toArray()
     * @see fields()
     */
    public function extraFields()
    {
        return [];
    }

    /**
     * 将模型转换为数组
     *
     * 此方法首先通过调用[[resolveFields()]]来区分哪个字段要包括到输出数组，
     * 然后它用这些字段把模型转变为数组，如果`$recursive` 为 true ，所有嵌入的对象也被转变成数组。
     *
     * 如果模型实现了[[Linkable]]接口，输出的数组也会有`_link`元素来代表接口所指定的链接列表
     *
     * @param array $fields 被请求的字段，如为空，在[[fields()]]指定的所有字段将被返回
     * @param array $expand 为输出而被请求的附加字段，只有声明在[[extraFields()]]的字段才会被注意
     * @param boolean $recursive 是否递归返回嵌入对象的数组表示
     * @return array 对象的数组表示
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = [];
        foreach ($this->resolveFields($fields, $expand) as $field => $definition) {
            $data[$field] = is_string($definition) ? $this->$definition : call_user_func($definition, $field, $this);
        }

        if ($this instanceof Linkable) {
            $data['_links'] = Link::serialize($this->getLinks());
        }

        return $recursive ? ArrayHelper::toArray($data) : $data;
    }

    /**
     * 确定[[toArray()]]返回哪些字段
     * 本方法将核对被请求字段和声明在[[fields()]]和[[extraFields()]]的字段以确定哪些字段被返回
     * @param array $fields 被请求输出的字段
     * @param array $expand 被请求输出的附加字段
     * @return array 要输出的字段列表，数组键是字段名，
     * 而数组值是相应的对象属性名或返回字段值的 PHP 回调函数
     */
    protected function resolveFields(array $fields, array $expand)
    {
        $result = [];

        foreach ($this->fields() as $field => $definition) {
            if (is_integer($field)) {
                $field = $definition;
            }
            if (empty($fields) || in_array($field, $fields, true)) {
                $result[$field] = $definition;
            }
        }

        if (empty($expand)) {
            return $result;
        }

        foreach ($this->extraFields() as $field => $definition) {
            if (is_integer($field)) {
                $field = $definition;
            }
            if (in_array($field, $expand, true)) {
                $result[$field] = $definition;
            }
        }

        return $result;
    }
}
