Yii 2.0 is finally coming, after more than three years of intensive
development with almost [10,000
commits](https://github.com/yiisoft/yii2/commits/master) by over [300
authors](https://github.com/yiisoft/yii2/graphs/contributors)! Thank you
for your support and patience!

As you may have already known, Yii 2.0 is a complete rewrite over the
previous version 1.1. We made this choice in order to build a
state-of-the-art PHP framework by keeping the original simplicity and
extensibility of Yii while adopting the latest technologies and features
to make it even better. And today we are very glad to announce that we
have achieved our goal.

Below are some useful links about Yii and Yii 2.0:

-   [Yii project site](http://www.yiiframework.com)
-   [Yii 2.0 GitHub Project](https://github.com/yiisoft/yii2): you may
    star and/or watch it to keep track of Yii development activities.
-   [Yii Facebook group](https://www.facebook.com/groups/yiitalk/)
-   [Yii Twitter feeds](https://twitter.com/yiiframework)
-   [Yii LinkedIn
    group](https://www.linkedin.com/groups/yii-framework-1483367)

In the following we will summarize some of the highlights of this long
awaited release. You may check out the [Getting
Started](http://www.yiiframework.com/doc-2.0/guide-index.html#getting-started)
section if you want to rush to try it out first.

Highlights
----------

### Adopting Standards and Latest Technologies

Yii 2.0 adopts PHP namespaces and traits, [PSR
standards](http://www.php-fig.org/psr/),
[Composer](https://getcomposer.org/), [Bower](http://bower.io/) and
[NPM](https://www.npmjs.org/). All these make the framework more
refreshing and interoperable with other libraries.

### Solid Foundation Classes

Like in 1.1, Yii 2.0 supports [object
properties](http://www.yiiframework.com/doc-2.0/guide-concept-properties.html)
defined via getters and setters,
[configurations](http://www.yiiframework.com/doc-2.0/guide-concept-configurations.html),
[events](http://www.yiiframework.com/doc-2.0/guide-concept-events.html)
and
[behaviors](http://www.yiiframework.com/doc-2.0/guide-concept-behaviors.html).
The new implementation is more efficient and expressive. For example,
you can write the following code to respond to an event:

    $response = new yii\web\Response;
    $response->on('beforeSend', function ($event) {
        // respond to the "beforeSend" event here
    });

Yii 2.0 implements the [dependency injection
container](http://www.yiiframework.com/doc-2.0/guide-concept-di-container.html)
and [service
locator](http://www.yiiframework.com/doc-2.0/guide-concept-service-locator.html).
It makes the applications built with Yii more customizable and testable.

### Development Tools

Yii 2.0 comes with several development tools to make the life of
developers easier.

The [Yii
debugger](http://www.yiiframework.com/doc-2.0/guide-tool-debugger.html)
allows you to examine the runtime internals of your application. It can
also be used to do performance profiling to find out the performance
bottlenecks in your application.

Like 1.1, Yii 2.0 also provides Gii, a [code generation
tool](http://www.yiiframework.com/doc-2.0/guide-tool-gii.html), that can
cut down a large portion of your development time. Gii is very
extensible, allowing you to customize or create different code
generators. Gii provides both Web and console interfaces to fit for
different user preferences.

The API documentation of Yii 1.1 has received a lot of positive
feedback. Many people expressed the wish to create a similar
documentation for their applications. Yii 2.0 realizes this with a
[documentation
generator](https://github.com/yiisoft/yii2/tree/master/extensions/apidoc).
The generator supports Markdown syntax which allows you to write
documentation in a more succinct and expressive fashion.

### Security

Yii 2.0 helps you to write more secure code. It has built-in support to
prevent SQL injections, XSS attacks, CSRF attacks, cookie tampering,
etc. Security experts [Tom Worster](https://github.com/tom--) and
[Anthony Ferrara](https://github.com/ircmaxell) even helped us review
and rewrite some of the security-related code.

### Databases

Working with databases has never been easier. Yii 2.0 supports [DB
migration](http://www.yiiframework.com/doc-2.0/guide-db-migrations.html),
[database access objects
(DAO)](http://www.yiiframework.com/doc-2.0/guide-db-dao.html), [query
builder](http://www.yiiframework.com/doc-2.0/guide-db-query-builder.html)
and [Active
Record](http://www.yiiframework.com/doc-2.0/guide-db-active-record.html).
Compared with 1.1, Yii 2.0 improves the performance of Active Record and
unifies the syntax for querying data via query builder and Active
Record. The following code shows how you can query customer data using
either query builder or Active Record. As you can see, both approaches
use chained method calls which are similar to SQL syntax.

    use yii\db\Query;
    use app\models\Customer;
     
    $customers = (new Query)->from('customer')
        ->where(['status' => Customer::STATUS_ACTIVE])
        ->orderBy('id')
        ->all();
     
    $customers = Customer::find()
        ->where(['status' => Customer::STATUS_ACTIVE])
        ->orderBy('id')
        ->asArray();
        ->all();

The following code shows how you can perform relational queries using
Active Record:

    namespace app\models;
     
    use app\models\Order;
    use yii\db\ActiveRecord;
     
    class Customer extends ActiveRecord
    {
        public static function tableName()
        {
            return 'customer';
        }
     
        // defines a one-to-many relation with Order model
        public function getOrders()
        {
            return $this->hasMany(Order::className(), ['customer_id' => 'id']);
        }
    }
     
    // returns the customer whose id is 100
    $customer = Customer::findOne(100);
    // returns the orders for the customer
    $orders = $customer->orders;

And the following code shows how you can update a Customer record.
Behind the scene, parameter binding is used to prevent SQL injection
attacks, and only modified columns are saved to DB.

    $customer = Customer::findOne(100);
    $customer->address = '123 Anderson St';
    $customer->save();  // executes SQL: UPDATE `customer` SET `address`='123 Anderson St' WHERE `id`=100

Yii 2.0 supports the widest range of databases. Besides the traditional
relational databases, Yii 2.0 adds the support for Cubrid,
ElasticSearch, Sphinx. It also supports NoSQL databases, including Redis
and MongoDB. More importantly, the same query builder and Active Record
APIs can be used for all these databases, which makes it an easy task
for you to switch among different databases. And when using Active
Record, you can even relate data from different databases (e.g. between
MySQL and Redis).

For applications with big databases and high performance requirement,
Yii 2.0 also provides built-in support for [database replication and
read-write
splitting](http://www.yiiframework.com/doc-2.0/guide-db-dao.html#replication-and-read-write-splitting).

### RESTful APIs

With a few lines of code, Yii 2.0 lets you to quickly build a set of
fully functional [RESTful
APIs](http://www.yiiframework.com/doc-2.0/guide-rest-quick-start.html)
that comply to the latest protocols. The following example shows how you
can create a RESTful API serving user data.

First, create a controller class `app\controllers\UserController` and
specify `app\models\User` as the type of model being served:

    namespace app\controllers;
     
    use yii\rest\ActiveController;
     
    class UserController extends ActiveController
    {
        public $modelClass = 'app\models\User';
    }

Then, modify the configuration about the `urlManager` component in your
application configuration to serve user data in pretty URLs:

    'urlManager' => [
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false,
        'rules' => [
            ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
        ],
    ]

That's all you need to do! The API you just created supports:

-   `GET /users`: list all users page by page;
-   `HEAD /users`: show the overview information of user listing;
-   `POST /users`: create a new user;
-   `GET /users/123`: return the details of the user 123;
-   `HEAD /users/123`: show the overview information of user 123;
-   `PATCH /users/123` and `PUT /users/123`: update the user 123;
-   `DELETE /users/123`: delete the user 123;
-   `OPTIONS /users`: show the supported verbs regarding endpoint
    `/users`;
-   `OPTIONS /users/123`: show the supported verbs regarding endpoint
    `/users/123`.

You may access your API with the `curl` command like the following,

    $ curl -i -H "Accept:application/json" "http://localhost/users"

    HTTP/1.1 200 OK
    Date: Sun, 02 Mar 2014 05:31:43 GMT
    Server: Apache/2.2.26 (Unix) DAV/2 PHP/5.4.20 mod_ssl/2.2.26 OpenSSL/0.9.8y
    X-Powered-By: PHP/5.4.20
    X-Pagination-Total-Count: 1000
    X-Pagination-Page-Count: 50
    X-Pagination-Current-Page: 1
    X-Pagination-Per-Page: 20
    Link: <http://localhost/users?page=1>; rel=self, 
          <http://localhost/users?page=2>; rel=next, 
          <http://localhost/users?page=50>; rel=last
    Transfer-Encoding: chunked
    Content-Type: application/json; charset=UTF-8

    [
        {
            "id": 1,
            ...
        },
        {
            "id": 2,
            ...
        },
        ...
    ]

### Caching

Like 1.1, Yii 2.0 supports a whole range of caching options, from server
side caching, such as [fragment
caching](http://www.yiiframework.com/doc-2.0/guide-caching-fragment.html),
[query
caching](http://www.yiiframework.com/doc-2.0/guide-caching-data.html#query-caching)
to client side [HTTP
caching](http://www.yiiframework.com/doc-2.0/guide-caching-http.html).
They are supported on a variety of caching drivers, including APC,
Memcache, files, databases, etc.

### Forms

In 1.1, you can quickly create HTML forms that support both client side
and server side validation. In Yii 2.0, it is even easier [working with
forms](http://www.yiiframework.com/doc-2.0/guide-input-forms.html). The
following example shows how you can create a login form.

First create a `LoginForm` model to represent the data being collected.
In this class, you will list the rules that should be used to validate
the user input. The validation rules will later be used to automatically
generate the needed client-side JavaScript validation logic.

    use yii\base\Model;
     
    class LoginForm extends Model
    {
        public $username;
        public $password;
     
        /**
         * @return array the validation rules.
         */
        public function rules()
        {
            return [
                // username and password are both required
                [['username', 'password'], 'required'],
                // password is validated by validatePassword()
                ['password', 'validatePassword'],
            ];
        }
     
        /**
         * Validates the password.
         * This method serves as the inline validation for password.
         */
        public function validatePassword()
        {
            $user = User::findByUsername($this->username);
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Incorrect username or password.');
            }
        }
    }

Then create the view code for the login form:

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
     
    <?php $form = ActiveForm::begin() ?>
        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= Html::submitButton('Login') ?>
    <? ActiveForm::end() ?>

### Authentication and Authorization

Like 1.1, Yii 2.0 provides built-in support for user authentication and
authorization. It supports features such as login, logout, cookie-based
and token-based
[authentication](http://www.yiiframework.com/doc-2.0/guide-security-authentication.html),
[access control
filter](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#access-control-filter)
and [role-based access control
(RBAC)](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#role-based-access-control-rbac).

Yii 2.0 also provides the ability of the [authentication via external
credentials providers](https://github.com/yiisoft/yii2-authclient). It
supports OpenID, OAuth1 and OAuth2 protocols.

### Widgets

Yii 2.0 comes with a rich set of user interface elements, called
[widgets](http://www.yiiframework.com/doc-2.0/guide-structure-widgets.html),
to help you quickly build interactive user interfaces. It has built-in
support for [Bootstrap](http://getbootstrap.com/) widgets and [jQuery
UI](http://jqueryui.com/) widgets. It also provides commonly used
widgets such as pagers, grid view, list view, detail, all of which make
Web application development a truly speedy and enjoyable process. For
example, with the following lines of code, you can create a fully
functional jQuery UI date picker in Russian:

    use yii\jui\DatePicker;
     
    echo DatePicker::widget([
        'name' => 'date',
        'language' => 'ru',
        'dateFormat' => 'yyyy-MM-dd',
    ]);

### Helpers

Yii 2.0 provides many useful [helper
classes](https://github.com/yiisoft/yii2/tree/master/framework/helpers)
to simplify some common tasks. For example, the `Html` helper includes a
set of methods to create different HTML tags, and the `Url` helper lets
you more easily creates various URLs, like shown below:

    use yii\helpers\Html;
    use yii\helpers\Url;
     
    // creates a checkbox list of countries
    echo Html::checkboxList('country', 'USA', $countries);
     
    // generates a URL like "/index?r=site/index&src=ref1#name"
    echo Url::to(['site/index', 'src' => 'ref1', '#' => 'name']);

### Internationalization

Yii has strong support for internationalization, as it is being used all
over the world. It supports [message
translation](http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html#message-translation)
as well as [view
translation](http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html#views).
It also supports locale-based [plural forms and data
formatting](http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html#advanced-placeholder-formatting),
which complies to the [ICU
standard](http://icu-project.org/apiref/icu4c/classMessageFormat.html).
For example,

    // message translation with date formatting
    echo \Yii::t('app', 'Today is {0, date}', time());
     
    // message translation with plural forms
    echo \Yii::t('app', 'There {n, plural, =0{are no cats} =1{is one cat} other{are # cats}}!', ['n' => 0]);

### Template Engines

Yii 2.0 uses PHP as its default template language. It also supports
[Twig](http://twig.sensiolabs.org/) and [Smarty](http://www.smarty.net/)
through its [template engine
extensions](http://www.yiiframework.com/doc-2.0/guide-tutorial-template-engines.html).
And it is also possible for you to create extensions to support other
template engines.

### Testing

Yii 2.0 strengthens the testing support by integrating
[Codeception](http://codeception.com/) and
[Faker](https://github.com/fzaninotto/Faker). It also comes with a
fixture framework which coupled with DB migrations, allows you to manage
your fixture data more flexible.

### Application Templates

To further cut down your development time, Yii is released with two
application templates, each being a fully functional Web application.
The [basic application
template](http://www.yiiframework.com/doc-2.0/guide-start-installation.html#installing-via-composer)
can be used as a starting point for developing small and simple Web
sites, such as company portals, personal sites. The [advanced
application
template](http://www.yiiframework.com/doc-2.0/guide-tutorial-advanced-app.html)
is more suitable for building large enterprise applications that involve
multiple tiers and a big developer team.

### Extensions

While Yii 2.0 already provides many powerful features, one thing that
makes Yii even more powerful is its extension architecture. Extensions
are redistributable software packages specifically designed to be used
in Yii applications and provide ready-to-use features. Many built-in
features of Yii are provided in terms of extensions, such as
[mailing](http://www.yiiframework.com/doc-2.0/guide-tutorial-mailing.html),
[Bootstrap](https://github.com/yiisoft/yii2-bootstrap). Yii also boasts
a big user-contributed [extension
library](http://www.yiiframework.com/extensions/) consisting of almost
1700 extensions, as the time of this writing. We also find there are
more than 1300 Yii-related packages on
[packagist.org](https://packagist.org/search/?q=yii).

Getting Started
---------------

To get started with Yii 2.0, simply run the following commands:

    # install the composer-asset-plugin globally. This needs to be run only once.
    php composer.phar global require "fxp/composer-asset-plugin:1.0.0-beta3"

    # install the basic application template
    php composer.phar create-project yiisoft/yii2-app-basic basic 2.0.0

The above commands assume you already have
[Composer](https://getcomposer.org/). If not, please follow the
[Composer installation
instructions](http://getcomposer.org/doc/00-intro.md#installation-nix)
to install it.

Note that you may be prompted to enter your GitHub username and password
during the installation process. This is normal. Just enter them and
continue.

With the above commands, you have a ready-to-use Web application that
may be accessed through the URL `http://localhost/basic/web/index.php`.

Upgrading
---------

If you are upgrading from previous Yii 2.0 development releases (e.g.
2.0.0-beta, 2.0.0-rc), please follow the [upgrade
instructions](https://github.com/yiisoft/yii2/blob/master/framework/UPGRADE.md).

If you are upgrading from Yii 1.1, we have to warn you that it will not
be smooth, mainly because Yii 2.0 is a complete rewrite with many syntax
changes. However, most of your Yii knowledge still apply in 2.0. Please
read the [upgrade
instructions](http://www.yiiframework.com/doc-2.0/guide-intro-upgrade-from-v1.html)
to learn the major changes introduced in 2.0.

Documentation
-------------

Yii 2.0 has a [definitive
guide](http://www.yiiframework.com/doc-2.0/guide-README.html) as well as
a [class reference](http://www.yiiframework.com/doc-2.0/index.html). The
definitive guide is also being translated into [many
languages](https://github.com/yiisoft/yii2/tree/master/docs).

There are also a few books about Yii 2.0 [just
published](https://www.packtpub.com/web-development/web-application-development-yii-2-and-php)
or being written by famous writers such as [Larry
Ullman](http://www.larryullman.com/). Larry even spends his time helping
us polish the definitive guide. And Alexander Makarov is coordinating a
community-contributed [cookbook about Yii
2.0](https://github.com/samdark/yii2-cookbook), following his
well-received cookbook about Yii 1.1.

Credits
-------

We hereby thank [everyone who has contributed to
Yii](https://github.com/yiisoft/yii2/graphs/contributors). Your support
and contributions are invaluable!
