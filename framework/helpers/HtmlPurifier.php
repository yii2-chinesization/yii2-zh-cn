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
 * HtmlPurifier 提供了从任何有害代码清理 HTML 的能力
 *
 * 基本用法如下：
 *
 * ```php
 * echo HtmlPurifier::process($html);
 * ```
 *
 * 如果你想配置它：
 *
 * ```php
 * echo HtmlPurifier::process($html, [
 *     'Attr.EnableID' => true,
 * ]);
 * ```
 *
 * 详情请参阅 [HTMLPurifier 文档](http://htmlpurifier.org/).
 *
 * 注意你要添加 `ezyang/htmlpurifier` 到你的 composer.json `require` 部分并运行`composer install`后再使用它。
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @since 2.0
 */
class HtmlPurifier extends BaseHtmlPurifier
{
}
