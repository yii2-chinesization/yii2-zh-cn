调试工具和调试器
==========================

Yii2 包括了一个方便的工具栏和内置的调试器来更快的开发和调试你的应用。工具栏显示当前打开页面的信息，而调试器用来分析之前收集的数据（如确认变量值）。

这些工具允许你开箱即用：

- 通过工具栏可快速获取框架版本、PHP 版本、响应状态、当前控制器和动作、性能数据等
- 浏览应用和 PHP 配置
- 查看请求数据、请求和响应头、会话数据和环境变量
- 查看、检索和过滤日志
- 查看所有分析结果
- 查看该页面执行的数据库查询
- 查看应用发送的邮件

每个请求周期这些信息都是可用的，也允许你再次访问过往的请求信息。

安装和配置
--------------------------

要启用这些功能，添加以下代码到你的配置文件以启用调试模块：

```php
'bootstrap' => ['debug'],
'modules' => [
    'debug' => 'yii\debug\Module',
]
```

调试模块默认浏览本地站点才运行。如要在远程（工作）服务器使用调试模块，添加 `allowedIPs` 参数到你的 IP 白名单配置文件：

```php
'bootstrap' => ['debug'],
'modules' => [
    'debug' => [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['1.2.3.4', '127.0.0.1', '::1']
    ]
]
```

如果使用 `enableStrictParsing` URL 管理器选项，添加以下代码到你的 `rules` 中：

```php
'urlManager' => [
    'enableStrictParsing' => true,
    'rules' => [
        // ...
        'debug/<controller>/<action>' => 'debug/<controller>/<action>',
    ],
],
```

### 日志分析补充配置

日志和分析是简单但强大的工具，可以帮助你理解框架和应用两者的执行流程。这些工具对开发和生产环境都很有用。

在生产环境，应该只记录特别重要的信息，如[日志](logging.md)所描述的。生产环境持续记录所有信息对性能损耗严重。

在开发环境中，越多日志越好，记录执行跟踪尤其有用。

为了看到追溯信息，这些信息能帮助你理解框架内部发生什么，需要在配置文件设置追溯级别：

```php
return [
    // ...
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0, // <-- 这里
```

 Yii 运行在调试模式的缺省追溯级别自动设置为 3 ，由你的 `index.php` 文件的以下部分决定：

```php
defined('YII_DEBUG') or define('YII_DEBUG', true);
```

> Note: Make sure to disable debug mode in production environments since it may have a significant and adverse performance effect. Further, the debug mode may expose sensitive information to end users.

创建自己的面板
------------------------

工具栏和调试器都是高度可配置和定制的，因此，你可以创建自己的面板来收集和显示你所需要的特定数据。以下描述了创建一个简单定制面板的过程：

- 收集一个请求周期被渲染的视图
- 在工具栏显示被渲染视图的数量
- 允许你检查调试器中的视图名称

以上定制过程的前提是使用基础应用模板。

首先需要实现 `panels/ViewsPanel.php` 这个 `Panel`  类：

```php
<?php
namespace app\panels;

use yii\base\Event;
use yii\base\View;
use yii\base\ViewEvent;
use yii\debug\Panel;


class ViewsPanel extends Panel
{
    private $_viewFiles = [];

    public function init()
    {
        parent::init();
        Event::on(View::className(), View::EVENT_BEFORE_RENDER, function (ViewEvent $event) {
            $this->_viewFiles[] = $event->sender->getViewFile();
        });
    }


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Views';
    }

    /**
     * @inheritdoc
     */
    public function getSummary()
    {
        $url = $this->getUrl();
        $count = count($this->data);
        return "<div class=\"yii-debug-toolbar-block\"><a href=\"$url\">Views <span class=\"label\">$count</span></a></div>";
    }

    /**
     * @inheritdoc
     */
    public function getDetail()
    {
        return '<ol><li>' . implode('<li>', $this->data) . '</ol>';
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        return $this->_viewFiles;
    }
}
```

以上代码工作流是：

1. `init` 在任何控制器动作运行前执行。该方法是附加这种事件处理器的最佳位置：在控制器动作执行期间收集数据的处理器。
2. `save` 在控制器动作执行后调用。该方法返回的数据将存储在数据文件，如果该方法没有返回任何东西，面板将不渲染。
3. 数据文件的数据可用 `$this->data` 加载。对工具栏来说， `$this->data` 只代表最新数据，而对于调试器，该属性可以设置来读取任何之前的数据文件。
4. 工具栏从 `getSummary` 获取内容，在这里显示被渲染视图文件的数量。调试器用 `getDetail` 实现相同功能。

现在是时候告诉调试器使用新面板了。在 `config/web.php` ，修改调试配置如下：

```php
if (YII_ENV_DEV) {
    // 配置调整为 'dev' 开发环境
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'panels' => [
            'views' => ['class' => 'app\panels\ViewsPanel'],
        ],
    ];

// ...
```

这就是新面板，现在我们无须编写太多代码就有了另一个有用的面板。
