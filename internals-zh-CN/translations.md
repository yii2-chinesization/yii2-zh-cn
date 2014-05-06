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

Put documentation translations under `docs/<original>-<language>` where `<original>` is the original documentation name
such as `guide` or `internals` and `<language>` is the language code of the language docs are translated to. For the
Russian guide translation it is `docs/guide-ru`.

After initial work is done you can get what's changed since last translation of the file using a special command from
`build` directory:

```
build translation ../docs/guide" "../docs/guide-ru" --title="Russian guide translation report" > report-guide-ru.html
```