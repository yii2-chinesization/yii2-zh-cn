定制器(Fixtures)
========

定制器是测试的重要组成部分。 他们的主要目的是建立环境在一个 固定/已知 状态中，让你的测试是可重复的，并以预期的方式运行。 Yii提供一个定制器的框架，允许你精确定义你的定制器并轻松地使用它们。

>译者注：这里已经知道了 fixture 的功能主要是存放构造/模拟出不变的（固定的）准备测试的数据，在参考了几本关于TDD相关的出版图书中，有将fixture译成定制器、特定状态、固件、夹具，以下也将使用定制器这个词。

在Yii定制器框架中一个关键的概念是所谓的 *定制器对像* 。 一个定制器对像代表测试环境的一个特定的方面。它是一个 [[yii\test\Fixture]] 类或其子类的实例。例如，你可以使用 `UserFixture` 对像表示用户数据库表包括的固定的一组数据。在运行测试之前加载定制器对像并当完成时卸载它们。

一个定制器可能依赖于其他定制器, 通过 [[yii\test\Fixture::depends]] 属性指定。
当一个定制器加载，这个定制器所依赖的定制器会在这个定制器加载前自动加载；当一定制器卸载，这个定制器所依赖的定制器会在这个定制器卸载后卸载。

定义一个定制器
------------------

定义一个定制器，通过继承 [[yii\test\Fixture]] 或 [[yii\test\ActiveFixture]] 来创建一个新类。前者是一个通用的定制器，而后者
针对数据库和ActiveRecord进行了具体的设计的增强。

下面的代码定义了一个关于 `User` ActiveRecord 和相应的user表的定制器。

```php
<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\User';
}
```

> 提示: 每一个 `ActiveFixture` 的测试目的是准备数据库表的相关数据。你可以通过设置指定 [[yii\test\ActiveFixture::tableName]] 属性或 [[yii\test\ActiveFixture::modelClass]]
> 属性。如果是后者，表名称将取自 `modelClass` 指定的 `ActiveRecord` 类。

为 `ActiveFixture` 定制器提供数据通常是在 `FixturePath/data/TableName.php` 文件中,
其中 `FixturePath` 代表定制器类所在目录， `TableName`
是与使用的定制器相关联的表名称。在下面的例子中，这个文件应该是
`@app/tests/fixtures/data/user.php`。 数据文件应该返回一个数据行数组插入到user表。例如，

```php
<?php
return [
    'user1' => [
        'username' => 'lmayert',
        'email' => 'strosin.vernice@jerde.com',
        'auth_key' => 'K3nF70it7tzNsHddEiq0BZ0i-OU8S3xV',
        'password' => '$2y$13$WSyE5hHsG1rWN2jV8LRHzubilrCLI5Ev/iK0r3jRuwQEs2ldRu.a2',
    ],
    'user2' => [
        'username' => 'napoleon69',
        'email' => 'aileen.barton@heaneyschumm.com',
        'auth_key' => 'dZlXsVnIDgIzFgX4EduAqkEPuphhOh9q',
        'password' => '$2y$13$kkgpvJ8lnjKo8RuoR30ay.RjDf15bMcHIF7Vz1zz/6viYG5xJExU6',
    ],
];
```

在你的测试中，你可以为一个指定一个别名，您可以通过别名引用的一行。在上面的例子中，两行别名为`user1` and `user2`。

另外，你不需要在数据行中指定自动增长列，Yii在定制器被加载时，会自动填写实际值。

> 提示: 你可以通过设置 [[yii\test\ActiveFixture::dataFile]] 属性自定义数据文件的位置。
> 你也可以覆盖 [[yii\test\ActiveFixture::getData()]] 方法提供的数据。

正如我们前面所述，定制器可能依赖于其他装置。例如, `UserProfileFixture` 依赖于 `UserFixture`
因为user profile表包含一个外键关联到user表。
这个依赖关系是通过 [[yii\test\Fixture::depends]] 属性指定，如下所示，

```php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class UserProfileFixture extends ActiveFixture
{
    public $modelClass = 'app\models\UserProfile';
    public $depends = ['app\tests\fixtures\UserFixture'];
}
```

在上文中，我们已经展示了如何定义一个数据库定制器。要定义一个与数据库不相关的定制器（例如，对某些文件或目录相关的定制器），你可以继承
[[yii\test\Fixture]] 类并覆盖 [[yii\test\Fixture::load()|load()]] 和 [[yii\test\Fixture::unload()|unload()]] 方法。


使用定制器
--------------

如果你使用 [CodeCeption](http://codeception.com/) 来测试你的代码，你应该考虑使用
 `yii2-codeception` 扩展，它内置支持对定制器的加载和访问。
如果你使用的是其他测试框架，你可以在你的测试用例中使用 [[yii\test\FixtureTrait]] 来达到同样的目的。

下面我们将介绍如何使用 `yii2-codeception` 写一个 `UserProfile` 单元测试类。

在你的继承自 [[yii\codeception\DbTestCase]] 或 [[yii\codeception\TestCase]] 的单元测试类中，在 [[yii\test\FixtureTrait::fixtures()|fixtures()]] 方法中定义指定的定制器，例如，

```php
namespace app\tests\unit\models;

use yii\codeception\DbTestCase;
use app\tests\fixtures\UserProfileFixture;

class UserProfileTest extends DbTestCase
{
    public function fixtures()
    {
        return [
            'profiles' => UserProfileFixture::className(),
        ];
    }

    // ...测试方法...
}
```

在 `fixtures()` 方法中列出的定制器，在运行每一个测试方法之前会自动加载
并在每一个测方法完成后卸载。正如我们前面所述，当定制器加载时，它的所有依赖定制器会首先加载。在上面的例子中，因为 `UserProfileFixture` 依赖于 `UserFixture`。当测试类运行任何测试方法时，两个定制器会按照 `UserFixture` `UserProfileFixture` 循序加载。

当在`fixtures()`指定定制器时，你可以使用类名或配置数据指定一个定制器。配置数据将让你自定义定制器类的属性。

你也可以指定一个定制器别名，在上面的例子中 `UserProfileFixture` 的别名是 `profiles`。
在测试方法中，你可以使用别名访问定制器对象。例如，`$this->profiles` 将返回 `UserProfileFixture` 对象。

因为 `UserProfileFixture` 继承自 `ActiveFixture`，所以你可以进一步使用下面的语法来访问定制器提供的数据：

```php
// returns the data row aliased as 'user1'
$row = $this->profiles['user1'];
// returns the UserProfile model corresponding to the data row aliased as 'user1'
$profile = $this->profiles('user1');
// traverse every data row in the fixture
foreach ($this->profiles as $row) ...
```

> 信息: `$this->profiles` 仍然是 `UserProfileFixture` 类型。上面的访问是通过PHP魔术方法实现的。


定义和使用全局定制器
----------------------------------

上面所描述的定制器，主要用于单独的测试用例。在大多数情况下，你还需要一些全局的适用于所有或多数测试用例的定制器。一个例子是 [[yii\test\InitDbFixture]] 做了哪两件事：

* 通过执行位于 `@app/tests/fixtures/initdb.php` 脚本，运行一些常见的初始化任务；
* 加载其他DB定制器之前，禁用数据库完整性检查，并在定制器卸载后，重新启用。

使用全局定制器与使用非全局定制器类似。唯一的区别是，你需要在 [[yii\codeception\TestCase::globalFixtures()]] 中声明定制器。 当一个测试用例加载定制器时，它将首先加载全局定制器然后加载非全局的。

默认情况下，[[yii\codeception\DbTestCase]] 已经在 `globalFixtures()` 方法中声明了 `InitDbFixture`。这意味着如果你想要在每个测试这前做一些初始化工作只需要使用 `@app/tests/fixtures/initdb.php`。否则，可能会简单地专注于开发各个测试案例和相应的定制器。


组织定制器类和数据文件
-----------------------------------------

默认情况下，定制器类查找相应的数据文件在当前定制器类所在目录的 `data` 子目录。在简单的项目中，你可以遵循这个惯例。
对于大项目，你经常需要为同一类定制器切换不同的数据文件进行不同的测试。因此，我们建议你使用一个分层的方式，数据文件与你的类的命名空间对应。例如，
```
# 在tests\unit\fixtures目录下

data\
    components\
        fixture_data_file1.php
        fixture_data_file2.php
        ...
        fixture_data_fileN.php
    models\
        fixture_data_file1.php
        fixture_data_file2.php
        ...
        fixture_data_fileN.php
# 等等
```

这种方式你就会避免定制器的数据文件在测试之间相互冲突。

> 注意：出于例子的目的，在上面例子中，定制器数据文件的名字被命名相同的。在真实场景中，你应该根据该定制器类所继承的定制器父类的功能来确定他们的名字。
> 例如，如果你是继承自 [[yii\test\ActiveFixture]] 的数据库定制器，你应该使用数据库表名做为定制器数据文件名；
> 如果你是继承自 [[yii\mongodb\ActiveFixture]] 的MongoDB定制器，你应该使用集合名称作为文件名。

类似的层次结构，可以用来组织定制器类文件。而不是使用 `data` 作为根目录，你可以要使用 `fixtures` 做为根目录，以避免与数据文件的冲突。

小结
-------

在上文中，我们已经描述了如何定义和使用定制器。下面我们总结一下运行与数据库相关的单元测试典型的工作流程：

1. 使用 `yii migrate` 工具升级你的测试数据库到最新版本；
2. 运行一个测试用例：
   - 加载定制器：清理相关的数据库表，并填充定制器数据；
   - 进行实际测试；
   - 卸载定制器。
3. 重复步骤2，直到所有测试完成。

