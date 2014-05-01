自动加载
===========

> 注意：该章节还在开发中。

所有类、接口和特征都在使用时自动加载，不必使用 `include` 或 `require` 。 Composer 加载的包和 Yii 扩展也同样如此。

Yii 的自动加载器按照[PSR-4 规范](https://github.com/php-fig/fig-standards/blob/master/proposed/psr-4-autoloader/psr-4-autoloader.md)运行。那就是说命名空间、类、接口和特征必须对应到文件系统路径和相应的文件名，除了根命名空间的路径定义为一个别名。

例如，如果标准别名 `@app` 指向 `/var/www/example.com/` ，然后 `\app\models\User` 就从 `/var/www/example.com/models/User.php` 加载。

自定义别名可以使用以下代码添加：

```php
Yii::setAlias('@shared', realpath('~/src/shared'));
```

其他自动加载器可使用 PHP 标准 `spl_autoload_register` 注册。
