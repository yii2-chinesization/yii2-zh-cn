
[Source](http://www.yiiframework.com/news/77/yii-2-0-beta-is-released/)  
使用 [MarkdownRules](http://markdownrules.com/)工具转换，可能出现错误，请多多包涵。

# Yii 2.0 Beta 发布啦！

[APR 13, 2014]:2014-04-13

We are very pleased to announce the Beta release of Yii Framework version 2. You can [download it from yiiframework.com][1].

This Beta release includes hundreds of new features, changes and bug fixes that have been made since the [alpha release][2]. We will review the most important ones in the following. But first, we would like to answer some commonly asked questions regarding Beta.

## Commonly Asked Questions

  * **What does Beta mean?** Beta means feature and design freeze. After Beta and before GA (General Availability), we will mainly focus on fixing bugs and finishing documentation. We will no longer introduce major new features or significant design changes. There may still be changes that will break BC (Backward Compatibility), but we will try to minimize them and will record clearly the BC-breaking changes.

  * **When will GA be released?** We do not have an exact date for that yet. Since our focus next is mainly on bug fixes and documentation, we expect it will not take long to reach GA.

  * **Can I use Beta for my projects?** Do not use Beta if your project is on a tight schedule and you are not familiar with Yii 2.0 yet. Otherwise, you may consider using Yii 2 Beta, provided that you are comfortable with occasional BC-breaking changes. We have heard there are already many projects built on 2.0 master and are working well. Also keep in mind that the minimum PHP version required is 5.4.

  * **Are there any documentation for 2.0?** Yes, we have [The Definitive Guide][3] and [the API documentation][4]. And we are still adding more contents to the former.

  * **How can I upgrade my applications written in 1.1 to 2.0?** Please refer to [Upgrading from Yii 1.1][5]. Note that since 2.0 is a complete rewrite of 1.1, the upgrade will not be trivial. However, if you are familiar with 1.1, you will find many similarities in 2.0, which should help you to adopt 2.0 more quickly.

  * **How can I upgrade from 2.0 alpha?** If you are updating alpha version via Composer you need to remove everything except `.gitignore` from vendor directory and re-run composer. This is a one-time thing that will not be required for any future releases. Please check the [CHANGELOG][6] file in the release to find out more details about the BC-breaking changes.

  * **How can I follow the 2.0 development?** All development activities of Yii 2.0 can be found on GitHub: . You may watch or star this project to receive development updates. You may also follow our twitter updates at .

## Major Changes since 2.0 Alpha

You may find a complete list of changes in the [CHANGELOG][7]. Below we are summarizing the most important new features and changes.

### Structure

Yii 2 now follows the [PSR-4 standard][8] for its class autoloading. This results in three improvements:

  * Simpler framework directory structure.
  * Simpler extensions directory structure.
  * We've dropped PEAR-style class naming resulting in simpler and faster autoloading.

Controller classes are now required to be namespaced and must be located under `Module::controllerNamespace`, unless you use controller mapping via `Module::controllerMap`.

We have added back the support for grouping controllers by subdirectories, which is also supported in 1.1.

### Usability

Usability is one of the highest priorities for the Yii team. That's why we're spending lots of time choosing good names for everything, making code work with IDEs better and doing developer day-to-day job more pleasant.

We've adopted PSR-1 and PSR-2 and got out of the box support from various IDEs, [code style checkers][9] and [automatic formatters][10].

### Performance

The most notable change is that session is now started until it is actually used. This allows applications to not waste resources in starting sessions unnecessarily.

If you are using MarkDown in your project, you may find the MarkDown formatting speed is significantly improved. This is because Carsten Brandt (cebe) built a new MarkDown library from scratch after analyzing all existing solutions. The new library is much fast and is easier to be extended. It also supports GitHub flavored format and many other features.

### Security

Yii now uses _masked_ CSRF tokens to prevent [BREACH][11] type of exploits.

RBAC biz rules were refactored, which results in a more flexible yet safer solution. We have eliminated the use of `eval()` for biz rules.

### RESTful API framework

A long wanted feature in Yii is the built-in support for RESTful API development. This finally came into reality with the Beta release. Due to the limit of this article, we will not expand the details here. You may refer to [The Definitive Guide][12] for details. Below we mainly summarize the supported features as of now:

  * Quick prototyping with support for common APIs for ActiveRecord;
  * Response format negotiation (supporting JSON and XML by default);
  * Customizable object serialization with support for selectable output fields;
  * Proper formatting of collection data and validation errors;
  * Efficient routing with proper HTTP verb check;
  * Support for `OPTIONS` and `HEAD` verbs;
  * Authentication;
  * Authorization;
  * Support for HATEOAS;
  * HTTP Caching;
  * Rate limiting.

### Dependency Injection and Service Locator

Many users were asking why Yii does not provide a Dependency Injection (DI) Container. The fact is that Yii has long been providing a similar facility known as Service Locator - the Yii application instance. Now we have formally extracted out the service locator as a reusable component `yii\di\ServiceLocator`. Like before, the Yii application and also modules are both service locators. You may obtain a service (aka. application component in 1.1 terminology) using the expression `Yii::$app-&gt;get('something')`.

Besides Service Locator, we also implemented a DI Container `yii\di\Container` to help you develop code in a less coupled way. Our internal profiling shows this DI container is one of the fastest among most notable PHP DI implementations. You may use `Yii::$container-&gt;set()` to configure default settings of classes. The old `Yii::$objectConfig` is dropped in favor of this new implementation.

### Testing

Yii got integration with the [Codeception testing framework][13]. It allows you to test an application as a whole simulating user actions and verifying if resulting output is correct. In contrast with PhpUnit's selenium support it doesn't require a browser so it's easier to install for CI server and runs much faster.

Yii also added more support for building test fixtures, which is often a tedious and time consuming task when building tests. In particular, a [fixture framework][14] is developed to unify the fixture definition and management. We created the [faker extension][15] by integrating the "faker" library to help you create some realistically-looking faked fixture data.

Both the "basic" and "advanced" application templates now come with tests, including unit tests, functionality tests and acceptance tests. This will give a good start for Test-Driven development.

### Model Validation

There are many useful enhancements to the model validation feature.

The `UniqueValidator` and `ExistValidator` now support validating multiple columns. Below are some examples about the `unique` validation rule declaration:

```php
// a1 needs to be unique
['a1', 'unique']
 
// a1 needs to be unique, but column a2 will be used to check the uniqueness of the a1 value
['a1', 'unique', 'targetAttribute' => 'a2']
 
// a1 and a2 need to be unique together, and they both will receive an error message
[['a1', 'a2'], 'unique', 'targetAttribute' => ['a1', 'a2']]
 
// a1 and a2 need to unique together, only a1 will receive the error message
['a1', 'unique', 'targetAttribute' => ['a1', 'a2']]
 
// a1 needs to be unique by checking the uniqueness of both a2 and a3 (using a1 value)
['a1', 'unique', 'targetAttribute' => ['a2', 'a1' => 'a3']]
```
Validations can be done conditionally (aka. conditional validation). This is supported by the addition of two properties `when` and `whenClient` to each validator. The following example shows how to require the "state" input only when the country is selected as "USA":

```php
['state', 'required',
    'when' => function ($model) {
        return $model->country == Country::USA;
    },
    'whenClient' =>  "function (attribute, value) {
        return $('#country').value == 'USA';
    }",
]
```
Sometimes, you may want to do some ad-hoc data validation without the trouble of writing new model classes. You can accomplish this with the help of the new `yiiase\DynamicModel`. For example,

```php
public function actionSearch($name, $email)
{
    $model = DynamicModel::validateData(compact('name', 'email'), [
        [['name', 'email'], 'string', 'max' => 128],
        ['email', 'email'],
    ]);
    if ($model->hasErrors()) {
        // validation fails
    } else {
        // validation succeeds
    }
}
```
### Database and Active Record

Database-related features are one of the strongest sides of Yii. They were quite interesting when alpha was released and now beta brought more improvements and features. Among support for SQL databases we have ActiveRecord implementations for [elasticsearch][16], [redis][17] and [Sphinx search][18] already. The Beta version now brings support for the [mongodb][19] document storage.

#### Nested Transaction Support

Yii now supports nested transactions. As a result, you can safely start a transaction without worrying if there is already an existing transaction enclosing it.

#### Join Queries

We added `ActiveQuery::joinWith()` to support creating JOIN SQL statements using the AR relations you have already declared. This is especially useful when you want to filter or sort by columns from foreign tables. For example,

```php
// find all orders and sort the orders by the customer id and the order id. also eager loading "customer"
$orders = Order::find()->joinWith('customer')->orderBy('customer.id, order.id')->all();
 
// find all orders that contain books, and eager loading "books"
$orders = Order::find()->innerJoinWith('books')->all();
```
This feature is especially useful when displaying relational columns in a GridView. It became very easy making them sortable and filterable using `joinWith()`.

#### Data Typecasting

ActiveRecord will now convert data retrieved from the database to proper types. For example, if you have an integer column `type`, after the corresponding ActiveRecord instance is populated, you will find the `type` attribute gets an integer value, rather than a string value.

#### Searching

To facilitate building search functionality, we have added the `Query::filterWhere()` method which will automatically remove empty filter values. For example, if you have a search form with `name` and `email` filter fields. You may use the following code to build the search query. Without this method, you would have to check if the user has entered anything in a filter field, and if not you will not put it in the query condition. `filterWhere()` will only add non-empty fields to the condition.

```php
$query = User::find()->filterWhere([
    'name' => Yii::$app->request->get('name'),
    'email' => Yii::$app->request->get('email'),
]);
```
#### Batch Query

To support big data query, we have added the batch query feature which brings back data in batches instead of all at once. This allows you to keep the server memory usage under a limit. For example,

```php
use yii\db\Query;
 
$query = (new Query())
    ->from('user')
    ->orderBy('id');
 
foreach ($query->batch() as $users) {
    // $users is an array of 100 or fewer rows from the user table
}
 
// or if you want to iterate the row one by one
foreach ($query->each() as $user) {
    // $user represents one row of data from the user table
}
```
You may use batch with ActiveRecord too. For example,

```php
// fetch 10 customers at a time
foreach (Customer::find()->batch(10) as $customers) {
    // $customers is an array of 10 or fewer Customer objects
}
// fetch 10 customers at a time and iterate them one by one
foreach (Customer::find()->each(10) as $customer) {
    // $customer is a Customer object
}
// batch query with eager loading
foreach (Customer::find()->with('orders')->each() as $customer) {
}
```
#### Support for Sub-queries

The query builder has been improved to support sub-queries. You may build a sub-query as a normal `Query` object and then use it in appropriate places in another query. For example,

```php
$subQuery = (new Query())->select('id')->from('user')->where('status=1');
$query->select('*')->from(['u' => $subQuery]);
```
#### Inverse Relations

Relations can often be defined in pairs. For example, `Customer` may have a relation named `orders` while `Order` may have a relation named `customer`. In the following example, we may find that the `customer` of an order is not the same customer object that finds those orders, and accessing `customer-&gt;orders` will trigger one SQL execution while accessing the `customer` of an order will trigger another SQL execution:

```php
// SELECT * FROM customer WHERE id=1
$customer = Customer::findOne(1);
// echoes "not equal"
// SELECT * FROM order WHERE customer_id=1
// SELECT * FROM customer WHERE id=1
if ($customer->orders[0]->customer === $customer) {
    echo 'equal';
} else {
    echo 'not equal';
}
```

To avoid the redundant execution of the last SQL statement, we could declare the inverse relation for the `customer` and the `orders` relations by calling the `inverseOf()` method, like the following:

```php
class Customer extends ActiveRecord
{
    // ...
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['customer_id' => 'id'])->inverseOf('customer');
    }
}
```
Now if we execute the same query as shown above, we would get:

```php
// SELECT * FROM customer WHERE id=1
$customer = Customer::findOne(1);
// echoes "equal"
// SELECT * FROM order WHERE customer_id=1
if ($customer->orders[0]->customer === $customer) {
    echo 'equal';
} else {
    echo 'not equal';
}
```
#### More Consistent Relational Query APIs

In 2.0 alpha, we have introduced ActiveRecord support for both relational (e.g. MySQL) and NoSQL databases (e.g. redis, elasticsearch, MongoDB). In Beta, we refactored the relevant code to make the interfaces more consistent. In particular, we dropped `ActiveRelation` and made `ActiveQuery` the sole entry point for building ActiveRecord relational queries and declaring relations. We also added `ActiveRecord::findOne()` and `findAll()` to support quick query by primary keys or column values. Previously, the same functionality was assumed by `ActiveRecord::find()` which sometimes caused confusion due to inconsistent return types.

### Advanced Ajax Support

We have decided to use the excellent [Pjax][20] library and created the `yii\widgets\Pjax` widget. This is a generic widget that can enable ajax support for anything it encloses. For example, you can enclose a `GridView` with `Pjax` to enable ajax-based grid pagination and sorting:

```php
use yii\widgets\Pjax;
use yii\grid\GridView;

Pjax::begin();
echo GridView::widget([ /*...*/ ]);
Pjax::end();
```
### Request and response

Besides many internal bug fixes and improvements request and response got some significant changes. Most notably working with request data now looks like the following:

```php
// take a GET parameter from the request, defaults to 1 if not given
$page = Yii::$app->request->get('page', 1);
// take a POST parameter from the request, defaults to null if not given
$name = Yii::$app->request->post('name');
```
Another fundamental change is that response is actually sent at the very end of application lifecycle allowing you to modify headers and content as you like and where you prefer.

The request class is now also able to parse different body types for example JSON requests.

### Filters

The whole action filtering mechanism has been revamped. You can now enable action filtering at controller level as well as application and module levels. This allows you to filter action flow hierarchically. For example, you can install a filter in a module so that all actions within the module are subject to this filter; and you can further install another filter in some of the controllers in the module so that only actions in those controllers will be filtered.

We have reorganized our code and created a whole set of filters under the `yii ilters` namespace. For example, you can use `yii ilters\HttpBasicAtuh` filter to enable authentication based on HTTP Basic Auth by declaring it in a controller or module:

```php
public function behaviors()
{
    return [
        'basicAuth' => [
            'class' => \yii\filters\auth\HttpBasicAuth::className(),
            'exclude'=> ['error'],   // do not apply it to the "error" action
        ],
    ];
}
```
### Bootstrap Components

We introduce the important "bootstrap" step in the application life cycle. Extensions can register bootstrap classes by declaring them in the `composer.json` file. A normal component can also be registered as a bootstrap component as long as it is declared in `Application::$bootstrap`.

A bootstrap component will be instantiated before the application starts to process a request. This gives the component the opportunity to register handlers to important events and participate in the application life cycle.

### URL Handling

Since developers are dealing with URLs a lot we've extracted most of URL-related methods into a `Url` helper class resulting in a nicer API.

```php
 use yii\helpers\Url;
 
// currently active route
// example: /index.php?r=management/default/users
echo Url::to('');
 
// same controller, different action
// example: /index.php?r=management/default/page&id=contact
echo Url::toRoute(['page', 'id' => 'contact']);
 
 
// same module, different controller and action
// example: /index.php?r=management/post/index
echo Url::toRoute('post/index');
 
// absolute route no matter what controller is making this call
// example: /index.php?r=site/index
echo Url::toRoute('/site/index');
 
// url for the case sensitive action `actionHiTech` of the current controller
// example: /index.php?r=management/default/hi-tech
echo Url::toRoute('hi-tech');
 
// url for action the case sensitive controller, `DateTimeController::actionFastForward`
// example: /index.php?r=date-time/fast-forward&id=105
echo Url::toRoute(['/date-time/fast-forward', 'id' => 105]);
 
// get URL from alias
Yii::setAlias('@google', 'http://google.com/');
echo Url::to('@google/?q=yii');
 
// get canonical URL for the curent page
// example: /index.php?r=management/default/users
echo Url::canonical();
 
// get home URL
// example: /index.php?r=site/index
echo Url::home();
 
Url::remember(); // save URL to be used later
Url::previous(); // get previously saved URL
```
There are improvements in URL rules as well. You can use the new yii\web\GroupUrlRule to group rules defining their common parts once instead of repeating them:
```php
new GroupUrlRule([
    'prefix' => 'admin',
    'rules' => [
        'login' => 'user/login',
        'logout' => 'user/logout',
        'dashboard' => 'default/dashboard',
    ],
]);
 
// the above rule is equivalent to the following three rules:
[
    'admin/login' => 'admin/user/login',
    'admin/logout' => 'admin/user/logout',
    'admin/dashboard' => 'admin/default/dashboard',
]
```

### Role-Based Access Control (RBAC)

We have revamped the RBAC implementation by following more closely to the original NIST RBAC model. In particular, we have dropped the concept of _operation_ and _task_, and replaced them with _permission_ which is the term used in NIST RBAC.

And as aforementioned, we also redesigned the biz rule feature by managing it separately.

### Translations

First of all we'd like to thank all the community members who have participated in translating framework messages. The core messages are now available in 26 languages, which is a very impressive number.

Message translation now supports language fallback. For example, if your application is using `fr-CA` as the language while you only have `fr` translations, Yii will first look for `fr-CA` translations; if not found, it will try `fr`.

A new option is added to every Gii generator, which allows you to choose if you want to generate code with message translated via `Yii::t()`.

The message extraction tool now supports writing strings into `.po` files as well as databases.

### Extensions and Tools

We have built a documentation generator extension named `yii2-apidoc` that can be used to help you generate nice looking API documentation as well as MarkDown-based tutorials. The generator can be easily customized and extended to fit for your specific needs. It is also used to generate official documentation and API docs, as you can see at .

The Yii Debugger was polished with many minor enhancements. It is also now equipped with a mail panel as well as DB query and mail summary column in its summary page.

Besides the new translation supported mentioned above, the Yii code generator tool Gii can now be used to create a new extension. You may also notice the code preview window is enhanced so that you can quickly refresh and navigate among different files. It is also copy-paste friendly and supports keyboard shortcuts. Give it a try!

## Thank you!

The Yii 2.0 Beta release is a major milestone that has involved tremendous efforts from all parties. We don't think it would be possible without [all the valuable contribution][21] from our excellent community. Thank you all for making this release possible.

   [1]: http://www.yiiframework.com/download/
   [2]: http://www.yiiframework.com/news/76/yii-2-0-alpha-is-released/
   [3]: http://www.yiiframework.com/doc-2.0/guide-index.html
   [4]: http://www.yiiframework.com/doc-2.0/
   [5]: http://www.yiiframework.com/doc-2.0/guide-upgrade-from-v1.html
   [6]: https://github.com/yiisoft/yii2/blob/2.0.0-beta/CHANGELOG.md
   [7]: https://github.com/yiisoft/yii2/blob/2.0.0-beta/framework/CHANGELOG.md
   [8]: https://github.com/php-fig/fig-standards/blob/master/proposed/psr-4-autoloader/psr-4-autoloader.md
   [9]: https://github.com/squizlabs/PHP_CodeSniffer
   [10]: https://github.com/fabpot/PHP-CS-Fixer
   [11]: http://breachattack.com/
   [12]: http://www.yiiframework.com/doc-2.0/guide-rest.html
   [13]: https://github.com/yiisoft/yii2-codeception
   [14]: http://www.yiiframework.com/doc-2.0/guide-test-fixture.html
   [15]: https://github.com/yiisoft/yii2-faker
   [16]: http://www.elasticsearch.org/
   [17]: http://redis.io
   [18]: http://sphinxsearch.com/docs/
   [19]: https://www.mongodb.org/
   [20]: https://github.com/yiisoft/jquery-pjax
   [21]: https://github.com/yiisoft/yii2/graphs/contributors
  