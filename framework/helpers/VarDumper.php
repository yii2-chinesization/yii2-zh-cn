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

/**
 * VarDumper 目的是取代古怪的 PHP 函数 var_dump 和 print_r
 * 它能准确识别在复杂对象结构中递归引用的对象，它也有递归深度控制以避免一些特殊变量无限递归显示
 *
 * VarDumper 可以如下使用：
 *
 * ~~~
 * VarDumper::dump($var);
 * ~~~
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class VarDumper extends BaseVarDumper
{
}
