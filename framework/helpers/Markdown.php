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
 * Markdown 通过了转换 markdown 到 HTML 的能力
 *
 * 基本用法如下：
 *
 * ```php
 * $myHtml = Markdown::process($myText); // 使用原始的 markdown 风格
 * $myHtml = Markdown::process($myText, 'gfm'); // 使用 github 风格的 markdown
 * ```
 *
 * 你可以使用[[$flavors]]属性来配置多种风格
 *
 * 详情请参阅[Markdown 库文档](https://github.com/cebe/markdown#readme).
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class Markdown extends BaseMarkdown
{
}
