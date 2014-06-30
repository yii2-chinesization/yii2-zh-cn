翻译工作流
====================

Yii 被翻译成多种语言并被国际化应用与开发者委以重任。我们极力欢迎贡献者对文档和框架信息这两个主要部分进行翻译。

框架信息
------------------

框架包含两种信息：一种是不该被翻译的针对开发者的异常信息，另一种是对普通访客可见的信息例如表单验证提示。

开始翻译信息：

1. 检查 `framework/messages/config.php` 并确保你的语言被列在 `languages` 中。没有的话就添加进去（确保列表按字母顺序排列）。 语言的格式应该遵循 [IETF 语言标签规格](http://en.wikipedia.org/wiki/IETF_language_tag)，例如：
   `ru`， `zh-CN`。
2. 进入 `framework` 运行命令 `yii message/extract messages/config.php`。
3. 在 `framework/messages/你的语言/yii.php` 中翻译。确保文件以 UTF-8 编码保存。
4. [发起 pull request](git-workflow.md).

为保证你的的翻译是最新的你可以重新执行 `yii message/extract messages/config.php`。它将在不改变现有译文的条件下增量重新获取信息。

该翻译文件中的每个数组元素代表一段信息（key）的译文（value）。 如果数组 value 为空，信息将被视作未翻译。 不再需要翻译的信息将会由一对“@@”标记闭合。信息文本可以被用作复数形式。在 [权威指南的国际化部分](.
./guide-zh-CN/i18n.md) 查看详情。

文档翻译
-------------

把文档翻译放在 `docs/<original>-<language>` ，其中 `<original>` 指文档的原文件夹名
比如 `guide` 或是 `internals` ，`<language>` 是所用翻译语言的语言代码。
比如 `简体中文-中国` 的代码就是 `docs/guide-zh-CN`，`繁体-呆湾` 就是 `docs/guide-zh-TW`。

初始化的工作结束后，你可以通过在 `build` 文件夹里，用一条特殊命令，来获取自上次翻译以来，文档又发生了那些改变：

```
php build translation "../docs/guide" "../docs/guide-zh-CN" "Chinese guide translation report" > report_guide_zh_CN.html
```

若出现有关 Composer 的报错，请在 Yii 源代码根目录里执行一遍 `composer install`。