模型验证参考
==========================

既然模型即代表数据又定义了数据依附的业务规则，那么理解数据验证就是使用 Yii 的钥匙。要学习模型验证基础知识，请参考[模型, 验证部分](model.md#Validation)

本指南描述了 Yii 所有的验证器及其参数。

标准的 Yii 验证器（validators）
-----------------------

标准的 Yii 验证器定义在许多类中，主要在 `yii\validators` 命名空间。但你不必为 Yii 标准验证器指定完整命名空间，因为 Yii 能从已定义的路径别名识别它们。

这里是绑定到 Yii 框架的所有验证器列表，包括它们最有用的属性。每个属性的缺省值是标示在小括号内。注意这里并没有介绍所有验证器的完整属性列表。

### `boolean`: [[yii\validators\BooleanValidator|BooleanValidator]]（布尔值验证器）

核对属性赋值是否为布尔值。

- `trueValue`, 此值表示真. _(1)_
- `falseValue`, 此值表示假. _(0)_
- `strict`, 除了比对值是否还比对数据类型并输出 `trueValue`/`falseValue`. _(false)_

### `captcha`: [[yii\captcha\CaptchaValidator|CaptchaValidator]]（验证码验证器）

验证属性赋值等同于显示在验证码框的验证码，应和[[yii\captcha\CaptchaAction]]一起使用。

- `caseSensitive`, 比对是否区分大小写. _(false)_
- `captchaAction`, 渲染验证码图片的控制器动作路径. _('site/captcha')_

### `compare`: [[yii\validators\CompareValidator|CompareValidator]]（对比验证器）

比对指定的属性赋值和其他值并验证它们是否相等。

- `compareAttribute`, 拟对比的属性名. _(currentAttributeName&#95;repeat)_
- `compareValue`, 拟对比的常量值.
- `operator`, 对比操作符. _('==')_

### `date`: [[yii\validators\DateValidator|DateValidator]]（日期验证器）

验证属性是否以适当格式代表日期、时间和日期时间。

- `format`, 要验证值的日期格式应遵循[PHP date_create_from_format](http://www.php.net/manual/en/datetime.createfromformat.php). _('Y-m-d')_
- `timestampAttribute`, 接收解析结果的属性名.

### `default`: [[yii\validators\DefaultValueValidator|DefaultValueValidator]]（缺省值验证器）

验证属性是否设置为指定缺省值。

- `value`, 拟分配的缺省值.

### `double`: [[yii\validators\NumberValidator|NumberValidator]]（精度验证器）

Validates that the attribute value is a number, integer or decimal.
验证属性赋值是数值、整型还是浮点型。

- `max`, 数值上限（含）. _(null)_
- `min`, 数值下限（含）. _(null)_

### `email`: [[yii\validators\EmailValidator|EmailValidator]]（电邮验证器）

Validates that the attribute value is a valid email address. By default, this validator checks if the attribute value is a syntactical valid email address, but the validator can be configured to check the address's domain for the address's existence.
验证属性赋值是否有效的电子邮箱地址。默认该验证器验证属性值是否

- `allowName`, whether to allow the name in the email address (e.g. `John Smith <john.smith@example.com>`). _(false)_.
- `checkMX`, whether to check the MX record for the email address. _(false)_
- `checkPort`, whether to check port 25 for the email address. _(false)_
- `enableIDN`, whether the validation process should take into account IDN (internationalized domain names). _(false)_

### `exist`: [[yii\validators\ExistValidator|ExistValidator]]

Validates that the attribute value exists in a table.

- `targetClass`, the ActiveRecord class name or alias of the class that should be used to look for the attribute value being
  validated. _(ActiveRecord class of the attribute being validated)_
- `targetAttribute`, the ActiveRecord attribute name that should be used to look for the attribute value being validated.
  _(name of the attribute being validated)_

### `file`: [[yii\validators\FileValidator|FileValidator]]

Verifies if an attribute is receiving a valid uploaded file.

- `types`, an array of file name extensions that are allowed to be uploaded. _(any)_
- `minSize`, the minimum number of bytes required for the uploaded file.
- `maxSize`, the maximum number of bytes allowed for the uploaded file.
- `maxFiles`, the maximum number of files that the given attribute can hold. _(1)_

### `filter`: [[yii\validators\FilterValidator|FilterValidator]]

Converts the attribute value by sending it through a filter.

- `filter`, a PHP callback that defines a filter.

Typically a callback is either the name of PHP function:

```php
['password', 'filter', 'filter' => 'trim'],
```

Or an anonymous function:

```php
['text', 'filter', 'filter' => function ($value) {
    // here we are removing all swear words from text
    return $newValue;
}],
```

### `in`: [[yii\validators\RangeValidator|RangeValidator]]

Validates that the attribute value is among a list of values.

- `range`, a list of valid values that the attribute value should be among (inclusive).
- `strict`, whether the comparison should be strict (both the type and value must be the same). _(false)_
- `not`, whether to invert the validation logic. _(false)_

### `inline`: [[yii\validators\InlineValidator|InlineValidator]]

Uses a custom function to validate the attribute. You need to define a public method in your
model class that will evaluate the validity of the attribute. For example, if an attribute
needs to be divisible by 10, in the rules you would define: `['attributeName', 'isDivisibleByTen']`.

Then, your own method could look like this:

```php
public function isDivisibleByTen($attribute) {
    if (($this->$attribute % 10) != 0) {
         $this->addError($attribute, 'cannot divide value by 10');
    }
}
```

### `integer`: [[yii\validators\NumberValidator|NumberValidator]]

Validates that the attribute value is an integer.

- `max`, the upper limit of the number (inclusive). _(null)_
- `min`, the lower limit of the number (inclusive). _(null)_

### `match`: [[yii\validators\RegularExpressionValidator|RegularExpressionValidator]]

Validates that the attribute value matches the specified pattern defined by a regular expression.

- `pattern`, the regular expression to be matched.
- `not`, whether to invert the validation logic. _(false)_

### `number`: [[yii\validators\NumberValidator|NumberValidator]]

Validates that the attribute value is a number.

- `max`, the upper limit of the number (inclusive). _(null)_
- `min`, the lower limit of the number (inclusive). _(null)_

### `required`: [[yii\validators\RequiredValidator|RequiredValidator]]

Validates that the specified attribute does not have a null or empty value.

- `requiredValue`, the desired value that the attribute must have. _(any)_
- `strict`, whether the comparison between the attribute value and
  [[yii\validators\RequiredValidator::requiredValue|requiredValue]] must match both value and type. _(false)_

### `safe`: [[yii\validators\SafeValidator|SafeValidator]]

Serves as a dummy validator whose main purpose is to mark the attributes to be safe for massive assignment.

### `string`: [[yii\validators\StringValidator|StringValidator]]

Validates that the attribute value is of certain length.

- `length`, specifies the length limit of the value to be validated (inclusive). Can be `exactly X`, `[min X]`, `[min X, max Y]`.
- `max`, the upper length limit (inclusive). If not set, it means no maximum length limit.
- `min`, the lower length limit (inclusive). If not set, it means no minimum length limit.
- `encoding`, the encoding of the string value to be validated. _([[yii\base\Application::charset]])_

### `unique`: [[yii\validators\UniqueValidator|UniqueValidator]]

Validates that the attribute value is unique in the corresponding database table.

- `targetClass`, the ActiveRecord class name or alias of the class that should be used to look for the attribute value being
  validated. _(ActiveRecord class of the attribute being validated)_
- `targetAttribute`, the ActiveRecord attribute name that should be used to look for the attribute value being validated.
  _(name of the attribute being validated)_

### `url`: [[yii\validators\UrlValidator|UrlValidator]]

Validates that the attribute value is a valid http or https URL.

- `validSchemes`, an array of URI schemes that should be considered valid. _['http', 'https']_
- `defaultScheme`, the default URI scheme. If the input doesn't contain the scheme part, the default scheme will be
  prepended to it. _(null)_
- `enableIDN`, whether the validation process should take into account IDN (internationalized domain names). _(false)_

Validating values out of model context
--------------------------------------

Sometimes you need to validate a value that is not bound to any model, such as a standalone email address. The `Validator` class has a
`validateValue` method that can help you in these scenarios. Not all validator classes have implemented this method, but the ones that have implemented `validateValue` can be used without a model. For example, to validate an email stored in a string, you can do the following:

```php
$email = 'test@example.com';
$validator = new yii\validators\EmailValidator();
if ($validator->validate($email, $error)) {
    echo 'Email is valid.';
} else {
    echo $error;
}
```

TBD: refer to http://www.yiiframework.com/wiki/56/ for the format
