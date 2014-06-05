国际化
====================

国际化(I18N)指软件应用设计成无须改动引擎即可应用于不同语言和地区的过程。对于 web 应用，这点特别重要，因为潜在用户是全球范围的。

地区和语言
-------------------

在 Yii 应用中定义了两个语言属性：[[yii\base\Application::$sourceLanguage|source language]]和[[yii\base\Application::$language|target language]]。源语言是应用消息原始编写语言：


```php
echo \Yii::t('app', 'I am a message!');
```

> **提示**：默认是英语，推荐不要更改。原因是人们翻译英语到其他语言比非英语翻译到其他语言更容易。

目标语言是当前使用的语言，在应用配置中如下定义：

```php
// ...
return [
    'id' => 'applicationID',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU' // ← 在这里！
```

然后就能容易地实时更改：

```php
\Yii::$app->language = 'zh-CN';
```

格式是 `ll-CC` ，其中 `ll` 是语言的两个或三个小写字母代码，根据[ISO-639](http://www.loc.gov/standards/iso639-2/)分配确定，而 `CC` 是国家代码，根据[ISO-3166](http://www.iso.org/iso/en/prods-services/iso3166ma/02iso-3166-code-lists/list-en1.html)分配确定。

如果没有 `ru-RU` 翻译文件，Yii 将在提示失败前尝试查找 `ru` 翻译文件。

> **注意**：你能更进一步地自定义指定语言的细节[as documented in ICU project](http://userguide.icu-project.org/locale#TOC-The-Locale-Concept).

消息翻译
-------------------

### 基础

Yii 基础消息翻译在基本的变换工作中无须使用其他 PHP 扩展。它要做的只是查找从源语言翻译到目标语言的消息翻译文件。消息以`\Yii::t` 方法的第二个参数来指定：

```php
echo \Yii::t('app', 'This is a string to translate!');
```

Yii 将尝试从定义在 `i18n` 组件配置中的消息源加载适当的翻译：

```php
'components' => [
    // ...
    'i18n' => [
        'translations' => [
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                //'basePath' => '@app/messages',
                //'sourceLanguage' => 'en',
                'fileMap' => [
                    'app' => 'app.php',
                    'app/error' => 'error.php',
                ],
            ],
        ],
    ],
],
```

以上 `app*` 指定了该消息源处理哪些类别的消息。这个例子中我们处理以 `app`开头的所有消息。你也可以指定缺省翻译，更多消息请参考[i18n 示例](i18n.md#examples).

`class` 定义使用哪个消息源。以下消息源是可用的：

- PhpMessageSource 使用 PHP 文件保存
- GettextMessageSource 使用 GNU Gettext MO 或 PO 文件保存
- DbMessageSource 使用数据库保存

`basePath` 定义当前使用消息源在哪里保存消息。该例中保存在应用的 `messages` 目录。使用数据库保存的情况要跳过这个选项。

`sourceLanguage` 定义 `\Yii::t` 第二个参数使用的语言。如未定义，将使用应用的代码语言。

`fileMap` 在`PhpMessageSource` 使用时定义指定在 `\Yii::t()` 第一个参数的消息类别如何映射到文件。该例中我们定义了两个类别 `app` 和 `app/error` 。

依靠`BasePath/messages/LanguageID/CategoryName.php` 这样约定好的翻译文件格式可省略配置 `fileMap` 。

#### 命名占位符

可以添加参数到翻译消息，翻译后这些参数将被对应的值替换。格式是使用大括号包围参数名，如下所示：

```php
$username = 'Alexander';
echo \Yii::t('app', 'Hello, {username}!', [
    'username' => $username,
]);
```

注意给参数赋值没有大括号。

#### 位置占位符

```php
$sum = 42;
echo \Yii::t('app', 'Balance: {0}', $sum);
```

> **提示**：要努力保持消息字符串有意义和避免使用太多位置参数。记住翻译者只有源字符串，所以每个占位符替换什么必须是清晰明确的。

### 高级占位符格式


要使用高级功能需要安装和启用 [intl](http://www.php.net/manual/en/intro.intl.php) PHP 扩展。安装并启用这个扩展后就能够对占位符使用扩展语法。可以是默认设置的缩写形式`{placeholderName, argumentType}` ，也可以是允许指定格式风格的完整形式 `{placeholderName, argumentType, argumentStyle}` 。

完整参考请看[available at ICU website](http://icu-project.org/apiref/icu4c/classMessageFormat.html)。但它有点晦涩，我们在下面提供自己的参考。

#### 数字

```php
$sum = 42;
echo \Yii::t('app', 'Balance: {0, number}', $sum);
```

你可以指定内置格式风格 (`integer`, `currency`, `percent`)的其中一个：

```php
$sum = 42;
echo \Yii::t('app', 'Balance: {0, number, currency}', $sum);
```

或指定自定义格式：

```php
$sum = 42;
echo \Yii::t('app', 'Balance: {0, number, ,000,000000}', $sum);
```

[格式参考](http://icu-project.org/apiref/icu4c/classicu_1_1DecimalFormat.html).

#### 日期

```php
echo \Yii::t('app', 'Today is {0, date}', time());
```

内置格式有 (`short`, `medium`, `long`, `full`):

```php
echo \Yii::t('app', 'Today is {0, date, short}', time());
```

自定义格式：

```php
echo \Yii::t('app', 'Today is {0, date, YYYY-MM-dd}', time());
```

[格式参考](http://icu-project.org/apiref/icu4c/classicu_1_1SimpleDateFormat.html).

#### 时间

```php
echo \Yii::t('app', 'It is {0, time}', time());
```

内置格式 (`short`, `medium`, `long`, `full`):

```php
echo \Yii::t('app', 'It is {0, time, short}', time());
```

自定义格式：

```php
echo \Yii::t('app', 'It is {0, date, HH:mm}', time());
```

[格式参考](http://icu-project.org/apiref/icu4c/classicu_1_1SimpleDateFormat.html).


#### 拼出

```php
echo \Yii::t('app', '{n,number} is spelled as {n, spellout}', ['n' => 42]);
```

#### 序数

```php
echo \Yii::t('app', 'You are {n, ordinal} visitor here!', ['n' => 42]);
```

将输出 "You are 42nd visitor here!".

#### 期间


```php
echo \Yii::t('app', 'You are here for {n, duration} already!', ['n' => 47]);
```

将输出 "You are here for 47 sec. already!".

#### 复数

不同语言的复数表现形式不同。有些规则非常复杂，所以非常方便，已提供的功能不需要指定转化规则。相反它只需要在指定的位置输入转化好的单词即可。

```php
echo \Yii::t('app', 'There {n, plural, =0{are no cats} =1{is one cat} other{are # cats}}!', ['n' => 0]);
```

将输出 "There are no cats!".


在以上的复数规则参数， `=0` 指恰好等于零， `=1` 指恰好是一个，而 `other` 是此外的所有数字。`#` 将用 `n` 参数值替换。但不是所有语言都像英语这么简单，如俄语的例子：

```
Здесь {n, plural, =0{котов нет} =1{есть один кот} one{# кот} few{# кота} many{# котов} other{# кота}}!
```

以上要提出的是 `=1` 精确匹配 `n = 1` 但 `one` 匹配 `21` 或 `101` 。

注意如果使用两次占位符，一次用来表示复数而另一处要用来表示数字，否则将会输出 "Inconsistent types declared for an argument: U_ARGUMENT_TYPE_MISMATCH" 错误：

```
Total {count, number} {count, plural, one{item} other{items}}.
```

了解你的语言要用哪个转化形式可以参考[rules reference at unicode.org](http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html).

#### 选集

可以基于关键词挑选短语，这种例子的格式指定了怎样映射关键词到短信并提供了默认短语。

```php
echo \Yii::t('app', '{name} is {gender} and {gender, select, female{she} male{he} other{it}} loves Yii!', [
    'name' => 'Snoopy',
    'gender' => 'dog',
]);
```

将输出 "Snoopy is dog and it loves Yii!".

在表达式中 `female` 和 `male` 都是可选值，而 `other` 处理那些不匹配前两者的值。大括号内的字符串是子表达式所以可以是一个字符串也可以是字符串和占位符。

### 指定默认翻译

可以指定默认翻译作为回调函数用于某些不需要匹配其他翻译的类别。该翻译要用 `*` 标记。要做到这一点需要添加以下代码到配置文件( `yii2-basic` 应用的话是 `web.php`)：

```php
//配置 i18n 组件

'i18n' => [
    'translations' => [
        '*' => [
            'class' => 'yii\i18n\PhpMessageSource'
        ],
    ],
],
```

现在无须逐个配置就能使用类别，这和 Yii 1.1 行为是类似的。类别的消息将从默认翻译根路径（ `basePath` ）即`@app/messages` 下的文件加载：

```php
echo Yii::t('not_specified_category', 'message from unspecified category');
```

消息将从 `@app/messages/<LanguageCode>/not_specified_category.php` 加载。

### 翻译模块消息

要翻译模块消息和避免给所有消息使用一个翻译文件，可以这样操作：

```php
<?php

namespace app\modules\users;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\users\controllers';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/users/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => '@app/modules/users/messages',
            'fileMap' => [
                'modules/users/validation' => 'validation.php',
                'modules/users/form' => 'form.php',
                ...
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/users/' . $category, $message, $params, $language);
    }

}
```

上例中使用通配符匹配和过滤每个必须文件的每个类别。要省略 `fileMap` 设置，只要简单地遵循类别和同名文件的映射约定并直接使用 `Module::t('validation', 'your custom validation message')` 或 `Module::t('form', 'some form label')` 即可。

### 翻译小部件消息

对小部件也使用相同规则，如：

```php
<?php

namespace app\widgets\menu;

use yii\base\Widget;
use Yii;

class Menu extends Widget
{

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['widgets/menu/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => '@app/widgets/menu/messages',
            'fileMap' => [
                'widgets/menu/messages' => 'messages.php',
            ],
        ];
    }

    public function run()
    {
        echo $this->render('index');
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('widgets/menu/' . $category, $message, $params, $language);
    }

}
```

要省略 `fileMap` 设置，只要简单地遵循类别和同名文件的映射约定并直接使用 `Menu::t('messages', 'new messages {messages}', ['{messages}' => 10])` 即可。


> **注意**：小部件也可使用 i18n 视图，规则和它们应用在控制器上是一样的。


### 翻译框架消息

有时你想要为你的应用校正默认的框架消息翻译文件，可以如下配置`i18n` 组件：

```php
'components' => [
    'i18n' => [
        'translations' => [
            'yii' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '/path/to/my/message/files'
            ],
        ],
    ],
],
```

现在你可以在 `/path/to/my/message/files` 放入已调整过的翻译文件了。

视图
-----

可以在视图使用 i18n 来支持不同语言。例如，给视图 `views/site/index.php` 创建俄语版本，就要在当前控制器/小部件的视图路径下创建 `ru-RU` 文件夹并放入俄语版本的视图文件 `views/site/ru-RU/index.php`。

> **注意**：如果指定的语言为 `en-US` 且没有对应的视图， Yii 将在使用原始视图文件前查找 `en` 下的视图文件。

i18n 格式器
--------------

i18n 格式器组件是格式器的本地化版本，支持基于当前时区的日期、时间和数字的格式化。要使用格式器须配置格式器应用组件如下：

```php
return [
    // ...
    'components' => [
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
        ],
    ],
];
```

配置组件后就能用 `Yii::$app->formatter` 连接格式器了。

注意要使用 i18n 格式器先要安装和启用[intl](http://www.php.net/manual/en/intro.intl.php) PHP 扩展。

要了解格式器方法请参考它的 API 文档：[[yii\i18n\Formatter]]。