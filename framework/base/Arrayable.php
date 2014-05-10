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
 * Arrayable 是某些类要实现的接口，这些类的实例想实现自定义的表现形式。
 *
 * 例如，如果一个类通过调用[[toArray()]]实现了 Arrayable 接口，此类的实例可以转变为一个数组(包括所有嵌入的对象)，然后可以更容易进一步转变到其他格式，如 JSON, XML 。
 *
 * 方法[[fields()]]和[[extraFields()]]允许实现类自定义它们的哪些数据怎样格式化并放入[[toArray()]]的结果里。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
interface Arrayable
{
    /**
     * 返回字段（field）列表，当没有指定特定字段时，它们默认由[[toArray()]]返回
     *
     * 一个字段是由[[toArray()]]返回的数组中的指定元素
     *
     * 本方法将返回字段名或定义字段的数组，如果是前者，field 名将视为对象属性名，
     * 属性值将用作字段值，如果是后者，数组键是字段名而数组值是相应的字段定义，
     * 即可以是对象属性名也可以是返回对应字段值的 PHP 回调函数，回调函数的标识是：
     *
     * ```php
     * function ($field, $model) {
     *     // 返回字段值
     * }
     * ```
     *
     * 如，以下代码声明了四个 fields ：
     *
     * - `email`：字段名等同于属性名`email`；
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
     * @return array 字段名或字段定义的列表
     * @see toArray()
     */
    public function fields();
    /**
     * 返回其他字段列表，由[[toArray()]]返回但未列入[[fields()]]
     *
     * 此方法类似于[[fields()]]，除了此方法声明的字段列表不是默认由[[toArray()]]返回，
     * 只有当此列表的字段是显式请求时，它才被包括到[[toArray()]]的结果中。
     *
     * @return array 可扩展的字段名或字段定义的列表，请参阅[[fields()]]了解返回值的格式
     * @see toArray()
     * @see fields()
     */
    public function extraFields();
    /**
     * 把对象转换成数组
     *
     * @param array $fields 输出数组要包含的字段，未指定在[[fields()]]的字段将被忽略，
     * 如果此参数为空，所有指定在[[fields()]]的字段将被返回。
     * @param array $expand 输出数组要包含的附加字段，未指定在[[extraFields()]]的字段将被忽略，
     * 如果此参数为空，*没有附加字段*被返回。
     * @param boolean $recursive 是否递归返回嵌套对象的数组表示
     * @return array 对象的数组表示
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true);
}
