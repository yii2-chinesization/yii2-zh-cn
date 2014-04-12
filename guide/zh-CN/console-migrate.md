数据库迁移——数据库的版本控制工具
==================

和源码相同，数据库结构也像数据库驱动的应用那样逐步形成，慢慢成熟，可持续维护。例如，开发阶段，可能添加新表；或在应用上线后，发现需要另一个索引。可持续追踪数据库结构的变化是非常重要的（这称为 *迁移* ），正如源码的变化用版本控制来追踪一样。如果源码和数据库不同步，bugs (错误)就会产生，或整个应用终止运行。因为这样，Yii 提供了数据库迁移工具来保持追踪数据库迁移的历史，应用新的迁移版本，或恢复之前的迁移版本。

以下步骤展示了一个开发团队在开发阶段如何使用数据库迁移：

1. Tim 建立了新的迁移版本（如建立新表、更改一列的定义等）。
2. Tim 提交新的迁移版本到代码控制系统（如 Git、Mercurial）。
3. Doug 从代码控制系统升级他的版本库，接收到新的数据库迁移版本。
4. Doug 应用该迁移版本到他的本地开发数据库，从而同步他的数据库以反映 Tim 所做的改变。

Yii 用 `yii migrate` 命令行工具来支持数据库迁移。这个工具支持：

* 建立新迁移版本
* 应用、回退或重做迁移
* 显示迁移历史和新的迁移

建立迁移
-------------------

建立新的迁移请运行以下命令：

```
yii migrate/create <name>
```

必须的 `name` 参数指定了迁移的简要描述。例如，如果迁移建立名为 *news* 的新表，使用以下命令：

```
yii migrate/create create_news_table
```

你很快将看到，`name` 参数用作迁移版本中 PHP 类名的一部分。因此，这个参数应该只包括字母、数字或下划线。

以上命令将建立一个名为 `m101129_185401_create_news_table.php` 的新文件。该文件将创建在`@app/migrations` 目录内。刚生成的迁移文件就是下面的代码：

```php
class m101129_185401_create_news_table extends \yii\db\Migration
{
    public function up()
    {
    }

    public function down()
    {
        echo "m101129_185401_create_news_table cannot be reverted.\n";
        return false;
    }
}
```

注意类名和文件名相同，都遵循 `m<timestamp>_<name>` 模式，其中：

* `<timestamp>` 指迁移创建时的 UTC 时间戳 (格式是 `yymmdd_hhmmss`)，
* `<name>` 从命令中的 `name` 参数获取。

这个类中。 `up()` 方法应包括实际实现数据库迁移的代码。换言之， `up()` 方法执行了实际改变数据库的代码。`down()` 方法包括回退前版本的代码。

有时，用 `down()` 撤销数据库迁移是不可能的。例如，如果迁移删除表的某些行或整个表，那些数据将不能在 `down()` 方法里恢复。这种情况，该迁移称为不可逆迁移，即数据库不能回退到前一状态。当迁移是不可逆的，在以上生成代码的 `down()` 方法将返回 `false` 来表明这个迁移版本不能回退。

下面举例说明迁移如何建立新表：

```php

use yii\db\Schema;

class m101129_185401_create_news_table extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable('news', [
            'id' => 'pk',
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'content' => Schema::TYPE_TEXT,
        ]);
    }

    public function down()
    {
        $this->dropTable('news');
    }

}
```

基类[\yii\db\Migration] 通过 `db` 属性建立一个数据库连接。可以使用它来操作数据和数据库的模式。

上例中使用的列类型是抽象类型，将被 Yii 用相应的数据库管理系统的类型取代。可以使用它们来编写独立于数据库的迁移。如 `pk` 在 MySQL 中将替换为 `int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY` ，而在 sqlite 中则替换为 `integer PRIMARY KEY AUTOINCREMENT NOT NULL` 。更多细节和可用的类型列表请参考[[yii\db\QueryBuilder::getColumnType()]]。也可以使用定义在[[yii\db\Schema]]中的常量来定义列类型。

事务性的迁移（整体迁移或回滚）
------------------------

执行复杂的 DB 迁移时，通常想确定每个完整迁移全体是成功了还是失败了，以便数据库保持一致和完整。为实现该目标，可以利用数据库事务来处理，使用专用的 `safeUp` 和 `safeDown` 方法来达到这些目的。

```php

use yii\db\Schema;

class m101129_185401_create_news_table extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable('news', [
            'id' => 'pk',
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'content' => Schema::TYPE_TEXT,
        ]);

        $this->createTable('user', [
            'id' => 'pk',
            'login' => Schema::TYPE_STRING . ' NOT NULL',
            'password' => Schema::TYPE_STRING . ' NOT NULL',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('news');
        $this->dropTable('user');
    }

}
```

当代码使用多于一条查询时推荐使用 `safeUp` 和 `safeDown` 。

> 注意：不是所有的 DBMS 都支持事务，并且有些 DB 查询不能用事务表示。这种情况，必须用 `up()`
> 和`down()` 方法替代实现。对于 MySQL，有些 SQL 语句会引发[隐式提交]
> (http://dev.mysql.com/doc/refman/5.1/en/implicit-commit.html)。


应用迁移
-------------------

要应用所有可用的新迁移（如，升级本地数据库），运行以下命令：

```
yii migrate
```

该命令将显示所有新迁移列表。如果你确认应用这些迁移，它将会按类名的时间戳一个接一个地运行每个新迁移类的 `up()` 方法。

应用迁移成功后，迁移工具将在名为 `migration` 的数据库表保持迁移记录。这就允许该工具区分应用和未应用的迁移。如果 `migration` 表不存在，迁移工具将通过 `db` 组件自动在数据库中创建。

有时，我们只想应用一个或少量的新迁移，可以使用以下命令：

```
yii migrate/up 3
```

这个命令将应用3个新的迁移，改变这个值就改变拟应用的迁移数量。

也可以迁移数据库到特定版本，命令如下：

```
yii migrate/to 101129_185401
```

那就是，使用迁移数据表名的时间戳部分来指定需要迁移数据库的版本。如果在最后应用的迁移和指定迁移间有多个迁移，所有这些迁移将被应用。如果指定的迁移已经被应用，那么所有在其后应用的迁移将回退（指南下一节将描述）。


迁移回退（恢复、回滚）
--------------------

要恢复上一个或多个已应用的迁移，可以使用以下命令：

```
yii migrate/down [step]
```

其中可选项 `step` 参数指定多少迁移将被恢复。缺省为 1 ，即回退上一个被应用的迁移。

如前所述，并不是所有的迁移都能恢复。尝试回退这些不能恢复的迁移将抛出一个异常并终止整个回退流程。


重做迁移
------------------

重做迁移就是首先回退然后应用指定的迁移，用以下命令完成：

```
yii migrate/redo [step]
```

其中可选项 `step` 参数指定了重做多少迁移。默认为 1 ，即重做上一个迁移。

显示迁移信息
-----------------------------

除了应用和回退迁移，迁移工具还能显示迁移历史和拟应用的新迁移：

```
yii migrate/history [limit]
yii migrate/new [limit]
```

其中可选项 `limit` 参数指定了要显示的迁移数量。如果 `limit` 未指定，将显示所有可用的迁移。

第一条命令显示被应用的所有迁移，而第二条命令显示没有被应用的所有新迁移。


修改迁移历史
---------------------------

有时，想修改迁移历史到特定的迁移版本，而不要真的应用或回退相关的迁移。当开发新迁移时经常发生这种需求。使用以下命令实现该目标：

```
yii migrate/mark 101129_185401
```

该命令和 `yii migrate/to` 命令非常相似，除了只修改迁移历史表来指定版本，而不应用或恢复该迁移。


自定义迁移命令
-----------------------------

自定义迁移命令有几种方法。

### 使用命令行选项

迁移命令有五（？原文是四）个可指定的命令行选项：

* `interactive` ：布尔值，指定交互模式中是否执行迁移。默认为 true ，即执行特定迁移时将给用户弹出提示。可设置为 false 使迁移在后台执行。

* `migrationPath` ：字符串，指定存储所有迁移类文件的目录。必须以路径别名的形式提供，且相应的目录必须存在。如未指定该选项，将使用应用根路径下的 `migrations` 子目录。

* `migrationTable`：字符串，指定存储迁移历史信息的数据表名。默认为 `migration` ，表结构是 `version varchar(255) primary key, apply_time integer` 。

* `connectionID`：字符串，指定数据库连接应用组件的 ID ，默认为 'db'。

* `templateFile`：字符串，指定用作迁移类生成模板的文件路径。必须以路径别名形式指定（如 `application.migrations.template`）。如未设置，将使用内部模板。模板内的占位符 `{ClassName}` 将用实际的迁移类名替换。

要指定这些选项，执行以下格式的迁移命令：

```
yii migrate/up --option1=value1 --option2=value2 ...
```

例如，如果想迁移 `forum` 模块，其迁移文件放在模块内部的 `migrations` 目录，可以使用以下命令：

```
yii migrate/up --migrationPath=@app/modules/forum/migrations
```


### 全局配置命令

虽然命令行选项允许我们在运行时实时配置迁移命令，但有时也想一劳永逸地配置命令。例如，要用其他表来存储迁移历史，或想使用自定义迁移模板。可以如下修改控制台应用的配置文件实现：

```php
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationTable' => 'my_custom_migrate_table',
    ],
]
```

现在只要运行 `migrate` 命令，以上配置就会生效，无须我们每次输入命令行选项。其他命令选项也可以如此配置。