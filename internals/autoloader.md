Yii2 的类加载
=================

Yii2 的类加载符合 PSR-4 规范。也就是说它可以处理绝大多数 PHP 库和框架。

为了自动加载一个类，你需要设置根别名。

PEAR 风格的库
--------------------

```php
\Yii::setAlias('@Twig', '@app/vendors/Twig');
```

请参考
----------

- BaseYii::autoload
