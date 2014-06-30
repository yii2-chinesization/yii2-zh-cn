<?php
/**
 * 翻译日期：20140502
 */

/**
 * Yii 引导文件
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

require(__DIR__ . '/BaseYii.php');

/**
 * Yii 是服务于框架公共功能的辅助类
 *
 * 它继承自真正实现功能的[[\yii\BaseYii]]
 * 通过编写你自己的 Yii 类，你可以定制[[\yii\BaseYii]]的一些功能
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Yii extends \yii\BaseYii
{
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = include(__DIR__ . '/classes.php');
Yii::$container = new yii\di\Container;
