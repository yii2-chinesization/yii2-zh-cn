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

The above command will create a new
file named `m101129_185401_create_news_table.php`. This file will be created within the `@app/migrations` directory. Initially, the migration file will be generated with the following code:
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

The base class [\yii\db\Migration] exposes a database connection via `db`
property. You can use it for manipulating data and schema of a database.

The column types used in this example are abstract types that will be replaced
by Yii with the corresponding types depended on your database management system.
You can use them to write database independent migrations.
For example `pk` will be replaced by `int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY`
for MySQL and `integer PRIMARY KEY AUTOINCREMENT NOT NULL` for sqlite.
See documentation of [[yii\db\QueryBuilder::getColumnType()]] for more details and a list
of available types. You may also use the constants defined in [[yii\db\Schema]] to
define column types.


Transactional Migrations
------------------------

While performing complex DB migrations, we usually want to make sure that each
migration succeed or fail as a whole so that the database maintains the
consistency and integrity. In order to achieve this goal, we can exploit
DB transactions. We could use special methods `safeUp` and `safeDown` for these purposes.

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

When your code uses more then one query it is recommended to use `safeUp` and `safeDown`.

> Note: Not all DBMS support transactions. And some DB queries cannot be put
> into a transaction. In this case, you will have to implement `up()` and
> `down()`, instead. And for MySQL, some SQL statements may cause
> [implicit commit](http://dev.mysql.com/doc/refman/5.1/en/implicit-commit.html).


Applying Migrations
-------------------

To apply all available new migrations (i.e., make the local database up-to-date),
run the following command:

```
yii migrate
```

The command will show the list of all new migrations. If you confirm to apply
the migrations, it will run the `up()` method in every new migration class, one
after another, in the order of the timestamp value in the class name.

After applying a migration, the migration tool will keep a record in a database
table named `migration`. This allows the tool to identify which migrations
have been applied and which are not. If the `migration` table does not exist,
the tool will automatically create it in the database specified by the `db`
application component.

Sometimes, we may only want to apply one or a few new migrations. We can use the
following command:

```
yii migrate/up 3
```

This command will apply the 3 new migrations. Changing the value 3 will allow
us to change the number of migrations to be applied.

We can also migrate the database to a specific version with the following command:

```
yii migrate/to 101129_185401
```

That is, we use the timestamp part of a migration name to specify the version
that we want to migrate the database to. If there are multiple migrations between
the last applied migration and the specified migration, all these migrations
will be applied. If the specified migration has been applied before, then all
migrations applied after it will be reverted (to be described in the next section).


Reverting Migrations
--------------------

To revert the last one or several applied migrations, we can use the following
command:

```
yii migrate/down [step]
```

where the optional `step` parameter specifies how many migrations to be reverted
back. It defaults to 1, meaning reverting back the last applied migration.

As we described before, not all migrations can be reverted. Trying to revert
such migrations will throw an exception and stop the whole reverting process.


Redoing Migrations
------------------

Redoing migrations means first reverting and then applying the specified migrations.
This can be done with the following command:

```
yii migrate/redo [step]
```

where the optional `step` parameter specifies how many migrations to be redone.
It defaults to 1, meaning redoing the last migration.


Showing Migration Information
-----------------------------

Besides applying and reverting migrations, the migration tool can also display
the migration history and the new migrations to be applied.

```
yii migrate/history [limit]
yii migrate/new [limit]
```

where the optional parameter `limit` specifies the number of migrations to be
displayed. If `limit` is not specified, all available migrations will be displayed.

The first command shows the migrations that have been applied, while the second
command shows the migrations that have not been applied.


Modifying Migration History
---------------------------

Sometimes, we may want to modify the migration history to a specific migration
version without actually applying or reverting the relevant migrations. This
often happens when developing a new migration. We can use the following command
to achieve this goal.

```
yii migrate/mark 101129_185401
```

This command is very similar to `yii migrate/to` command, except that it only
modifies the migration history table to the specified version without applying
or reverting the migrations.


Customizing Migration Command
-----------------------------

There are several ways to customize the migration command.

### Use Command Line Options

The migration command comes with four options that can be specified in command
line:

* `interactive`: boolean, specifies whether to perform migrations in an
  interactive mode. Defaults to true, meaning the user will be prompted when
  performing a specific migration. You may set this to false should the
  migrations be done in a background process.

* `migrationPath`: string, specifies the directory storing all migration class
  files. This must be specified in terms of a path alias, and the corresponding
  directory must exist. If not specified, it will use the `migrations`
  sub-directory under the application base path.

* `migrationTable`: string, specifies the name of the database table for storing
  migration history information. It defaults to `migration`. The table
  structure is `version varchar(255) primary key, apply_time integer`.

* `connectionID`: string, specifies the ID of the database application component.
  Defaults to 'db'.

* `templateFile`: string, specifies the path of the file to be served as the code
  template for generating the migration classes. This must be specified in terms
  of a path alias (e.g. `application.migrations.template`). If not set, an
  internal template will be used. Inside the template, the token `{ClassName}`
  will be replaced with the actual migration class name.

To specify these options, execute the migrate command using the following format

```
yii migrate/up --option1=value1 --option2=value2 ...
```

For example, if we want to migrate for a `forum` module whose migration files
are located within the module's `migrations` directory, we can use the following
command:

```
yii migrate/up --migrationPath=@app/modules/forum/migrations
```


### Configure Command Globally

While command line options allow us to configure the migration command
on-the-fly, sometimes we may want to configure the command once for all.
For example, we may want to use a different table to store the migration history,
or we may want to use a customized migration template. We can do so by modifying
the console application's configuration file like the following,

```php
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationTable' => 'my_custom_migrate_table',
    ],
]
```

Now if we run the `migrate` command, the above configurations will take effect
without requiring us to enter the command line options every time. Other command options
can be also configured this way.
