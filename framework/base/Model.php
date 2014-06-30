<?php
/**
 * 翻译日期：20140509
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;
use ArrayAccess;
use ArrayObject;
use ArrayIterator;
use ReflectionClass;
use IteratorAggregate;
use yii\helpers\Inflector;
use yii\validators\RequiredValidator;
use yii\validators\Validator;

/**
 * Model（模型）是数据模型的基类
 *
 * 模型实现了以下常用功能：
 *
 * - 属性声明：默认所有类的公共成员看作模型属性
 * - 属性标签：为显示目的可把每个属性关联到一个标签
 * - 属性批量赋值
 * - 基于场景的验证
 *
 * 模型在执行数据验证时也会引发以下事件：
 *
 * - [[EVENT_BEFORE_VALIDATE]]：事件在[[validate()]]开始时引发
 * - [[EVENT_AFTER_VALIDATE]]: 事件在[[validate()]]结束时引起
 *
 * 你可以直接使用模型来存储模型数据，也可以自定义扩展它。
 *
 * @property \yii\validators\Validator[] $activeValidators 当前[[scenario]]可用的验证器，是只读属性。
 * @property array $attributes 属性值(name => value).
 * @property array $errors 所有属性的错误数组，如果没有错误返回空数组。
 * 这是两维数组，更多细节描述见[[getErrors()]]。也是只读属性。
 * @property array $firstErrors 第一个错误，数组键是属性名，数组值是相应的错误消息。
 * 如果没有错误返回空数组，这是只读属性。
 * @property ArrayIterator $iterator 用于遍历列表各项的迭代器，是只读属性。
 * @property string $scenario 此模型所在的场景，缺省为[[SCENARIO_DEFAULT]]。
 * @property ArrayObject|\yii\validators\Validator[] $validators 声明在此模型的所有验证器，只读属性。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Model extends Component implements IteratorAggregate, ArrayAccess, Arrayable
{
    use ArrayableTrait;

    /**
     * 默认场景名
     */
    const SCENARIO_DEFAULT = 'default';
    /**
     * @event ModelEvent 在[[validate()]]开始时引发的事件，
     * 你可以设置[[ModelEvent::isValid]]为 false 来停止验证。
     */
    const EVENT_BEFORE_VALIDATE = 'beforeValidate';
    /**
     * @event Event 在[[validate()]]结束时引发的事件
     */
    const EVENT_AFTER_VALIDATE = 'afterValidate';

    /**
     * @var array 验证错误(attribute name => array of errors)
     */
    private $_errors;
    /**
     * @var ArrayObject 验证器列表
     */
    private $_validators;
    /**
     * @var string 当前场景
     */
    private $_scenario = self::SCENARIO_DEFAULT;

    /**
     * 返回属性验证规则
     *
     * 验证规则被[[validate()]]用来核对属性值是否有效，子类可以覆写此方法来声明不同的验证规则。
     *
     * 每个规则是以下格式的数组：
     *
     * ~~~
     * [
     *     ['attribute1', 'attribute2'], //attributes list 要验证的属性数组
     *     'validator type', //要使用的验证器
     *     'on' => ['scenario1', 'scenario2'], //在哪些场景验证以上属性
     *     ...other parameters... //其他参数
     * ]
     * ~~~
     *
     * 其中
     *
     *  - 属性列表：必填项，指定要验证的属性数组，只有一个属性可以传递字符串；
     *  - 验证器类型：必填项，指定要使用的验证器，
     *    可以是内置验证器名、模型类的方法名、匿名函数或验证器类名；
     *  - on：可选项，指定验证规则应用时所处的[[scenario|scenarios]]数组，
     *    如果此项未设置，规则应用到所有场景。
     *  - 可指定其他名值对来初始化相应的验证器属性，请参考单个的验证器类 API 来了解可用的属性。
     *
     * 验证器可以是继承自[[Validator]]的类对象，也可以是有如下标识的模型类方法（称为*内联验证器*）：
     *
     * ~~~
     * // $params 引用在规则中指定的验证器参数
     * function validatorName($attribute, $params)
     * ~~~
     *
     * 以上，`$attribute` 引用当前被验证的属性名而`$params` 包括验证器配置选项的数组，
     * 如`string`验证器的`max`配置项。当前验证属性值可以`$this->[$attribute]`访问。
     *
     * Yii 也提供了一组内置验证器——[[Validator::builtInValidators|built-in validators]]，
     * 它们每一个都有一个别名，在指定验证规则时可以使用。
     *
     * 下面是一些例子：
     *
     * ~~~
     * [
     *     // 内置"required"验证器
     *     [['username', 'password'], 'required'],
     *     // 内置"string"验证器，可自定义"min" 和 "max" 属性
     *     ['username', 'string', 'min' => 3, 'max' => 12],
     *     // 内置"compare"验证器，只用于"register"场景
     *     ['password', 'compare', 'compareAttribute' => 'password2', 'on' => 'register'],
     *     // 在模型类以"authenticate()"方法定义的内联验证器
     *     ['password', 'authenticate', 'on' => 'login'],
     *     // "DateRangeValidator"类验证器
     *     ['dateRange', 'DateRangeValidator'],
     * ];
     * ~~~
     *
     * 注意为了继承父类所定义的规则，子类必须用类似`array_merge()`的方法来合并父类和子类的规则。
     *
     * @return array validation rules
     * @see scenarios()
     */
    public function rules()
    {
        return [];
    }

    /**
     * 返回场景列表和相应场景活动的属性
     * 活动属性是指在当前场景受验证支配（经过验证）的属性
     * 返回的数组应是以下格式：
     *
     * ~~~
     * [
     *     'scenario1' => ['attribute11', 'attribute12', ...],
     *     'scenario2' => ['attribute21', 'attribute22', ...],
     *     ...
     * ]
     * ~~~
     *
     * 默认，活动属性被认为是安全的并可以批量赋值，如果一个属性*不能*批量赋值（因此认为是不安全的），
     * 请为该属性加上感叹号前缀"!"(如'!rank')。
     *
     * 本方法的默认实现是返回在[[rules()]]中声明的所有场景，
     * 一个名为[[SCENARIO_DEFAULT]]的特殊场景包括了[[rules()]]中的所有属性。
     * 每个场景将关联到一些属性，这些属性正被应用到此场景的验证规则所验证。
     *
     * @return array 场景列表和对应的活动属性
     */
    public function scenarios()
    {
        $scenarios = [self::SCENARIO_DEFAULT => []];
        foreach ($this->getValidators() as $validator) {
            foreach ($validator->on as $scenario) {
                $scenarios[$scenario] = [];
            }
            foreach ($validator->except as $scenario) {
                $scenarios[$scenario] = [];
            }
        }
        $names = array_keys($scenarios);

        foreach ($this->getValidators() as $validator) {
            if (empty($validator->on) && empty($validator->except)) {
                foreach ($names as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            } elseif (empty($validator->on)) {
                foreach ($names as $name) {
                    if (!in_array($name, $validator->except, true)) {
                        foreach ($validator->attributes as $attribute) {
                            $scenarios[$name][$attribute] = true;
                        }
                    }
                }
            } else {
                foreach ($validator->on as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
        }

        foreach ($scenarios as $scenario => $attributes) {
            if (empty($attributes) && $scenario !== self::SCENARIO_DEFAULT) {
                unset($scenarios[$scenario]);
            } else {
                $scenarios[$scenario] = array_keys($attributes);
            }
        }

        return $scenarios;
    }

    /**
     * 返回本模型类使用的表单名
     *
     * 表单名主要被[[\yii\widgets\ActiveForm]]用于决定怎样为模型属性命名输入字段。
     * 如果表单名是"A" 而属性名是"b"，那么相应的输入框名称就是"A[b]"。
     * 如果表单名是空字符串，那么输入框名称就是"b" 。
     *
     * 默认本方法返回模型类名(没有命名空间部分)作为表单名，你可以在模型用于不同表单时覆写本方法。
     *
     * @return string 此模型类的表单名
     */
    public function formName()
    {
        $reflector = new ReflectionClass($this);

        return $reflector->getShortName();
    }

    /**
     * 返回属性名列表
     * 此方法默认返回该类的所有非静态公共属性，你可以覆写此方法以改变默认行为。
     * @return array 属性名列表
     */
    public function attributes()
    {
        $class = new ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }

    /**
     * 返回属性标签
     *
     * 属性标签主要用于显示目的，如，给定属性`firstName`，
     * 我们可以声明一个标签`First Name` 来更人性化地显示给终端用户。
     *
     * 属性标签默认使用[[generateAttributeLabel()]]生成，本方法允许你显式指定属性标签。
     *
     * 注意，为了继承父类定义的标签，子类必须用类似`array_merge()`的方法来合并父类和子类的标签。
     *
     * @return array 属性标签(name => label)
     * @see generateAttributeLabel()
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * 执行数据验证
     *
     * 本方法执行适用于当前[[scenario]]的验证规则。
     * 以下标准用于确定规则当前是否适用：
     *
     * - 规则必须关联到和当前场景相关的属性；
     * - 规则对当前场景必须是有效的。
     *
     * 本方法将在真正的验证之前或之后分别调用[[beforeValidate()]]和[[afterValidate()]]方法，
     * 如果[[beforeValidate()]]返回 false ，验证将取消，也不会调用[[afterValidate()]]。
     *
     * 验证中查出的错误可用[[getErrors()]]、[[getFirstErrors()]]和[[getFirstError()]]检索。
     *
     * @param array $attributeNames 要验证的属性名列表
     * 如果此参数为空，意味着列于适用验证规则的所有属性都要验证。
     * @param boolean $clearErrors 是否在执行验证前调用[[clearErrors()]]
     * @return boolean 是否没有任何错误地成功验证
     * @throws InvalidParamException 如果当前场景是未知的
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        $scenarios = $this->scenarios();
        $scenario = $this->getScenario();
        if (!isset($scenarios[$scenario])) {
            throw new InvalidParamException("Unknown scenario: $scenario");
        }

        if ($clearErrors) {
            $this->clearErrors();
        }
        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }
        if ($this->beforeValidate()) {
            foreach ($this->getActiveValidators() as $validator) {
                $validator->validateAttributes($this, $attributeNames);
            }
            $this->afterValidate();

            return !$this->hasErrors();
        }

        return false;
    }

    /**
     * 此方法在验证开始前调用
     * 默认实现是引发一个`beforeValidate`事件，你可以覆写该方法以在验证前做初步检查，
     * 确保父类实现被调用以便事件能被唤起。
     * @return boolean 验证是否要执行，缺省为 true
     * 如果返回 false ，验证将停止而此模型被认为是无效的。
     */
    public function beforeValidate()
    {
        $event = new ModelEvent;
        $this->trigger(self::EVENT_BEFORE_VALIDATE, $event);

        return $event->isValid;
    }

    /**
     * 本方法在验证结束时调用
     * 默认实现是引发一个`afterValidate`事件，你可以覆写该方法以在验证后做些收尾工作，
     * 确保父类实现被调用以便事件可以被唤起。
     */
    public function afterValidate()
    {
        $this->trigger(self::EVENT_AFTER_VALIDATE);
    }

    /**
     * 返回声明在[[rules()]]里的所有验证器
     *
     * 此方法不同于[[getActiveValidators()]]，后者只返回当前[[scenario]]适用的验证器。
     *
     * 因为此方法返回一个 ArrayObject （数组对象）对象，
     * 你可以通过插入或移除验证器来操作它（模型行为很有用）。
     * 如：
     *
     * ~~~
     * $model->validators[] = $newValidator;
     * ~~~
     *
     * @return ArrayObject|\yii\validators\Validator[] 模型中声明的所有验证器
     */
    public function getValidators()
    {
        if ($this->_validators === null) {
            $this->_validators = $this->createValidators();
        }

        return $this->_validators;
    }

    /**
     * 返回当前[[scenario]]适用的验证器
     * @param string $attribute 要返回其可用验证器的属性名称
     * 如果是 null ，此模型所有属性的验证器都要返回
     * @return \yii\validators\Validator[] 适用于当前[[scenario]]的验证器
     */
    public function getActiveValidators($attribute = null)
    {
        $validators = [];
        $scenario = $this->getScenario();
        foreach ($this->getValidators() as $validator) {
            if ($validator->isActive($scenario) && ($attribute === null || in_array($attribute, $validator->attributes, true))) {
                $validators[] = $validator;
            }
        }

        return $validators;
    }

    /**
     * 基于指定在[[rules()]]的验证规则创建验证器对象
     * 不同于[[getValidators()]]，本方法每次被调用，都返回一个新的验证器列表。
     * @return ArrayObject 验证器
     * @throws InvalidConfigException 如果任何验证规则配置均无效
     */
    public function createValidators()
    {
        $validators = new ArrayObject;
        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $validator = Validator::createValidator($rule[1], $this, (array) $rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }

        return $validators;
    }

    /**
     * 返回一个值来指明传入属性是否必填
     * 这由属性和当前[[scenario]]的[[\yii\validators\RequiredValidator|required]]验证规则
     * 关联与否的核对结果来决定。
     * @param string $attribute 属性名
     * @return boolean 属性是否必填
     */
    public function isAttributeRequired($attribute)
    {
        foreach ($this->getActiveValidators($attribute) as $validator) {
            if ($validator instanceof RequiredValidator) {
                return true;
            }
        }

        return false;
    }

    /**
     * 返回一个值来表明此属性是否对批量赋值是安全的
     * @param string $attribute 属性名
     * @return boolean 对批量赋值此属性是否安全
     * @see safeAttributes()
     */
    public function isAttributeSafe($attribute)
    {
        return in_array($attribute, $this->safeAttributes(), true);
    }

    /**
     * 返回一个值来指明传入属性在当前场景是否活动
     * @param string $attribute 属性名
     * @return boolean 属性是否在当前场景是活动的
     * @see activeAttributes()
     */
    public function isAttributeActive($attribute)
    {
        return in_array($attribute, $this->activeAttributes(), true);
    }

    /**
     * 返回指定属性的文本标签
     * @param string $attribute 属性名
     * @return string 属性标签
     * @see generateAttributeLabel()
     * @see attributeLabels()
     */
    public function getAttributeLabel($attribute)
    {
        $labels = $this->attributeLabels();

        return isset($labels[$attribute]) ? $labels[$attribute] : $this->generateAttributeLabel($attribute);
    }

    /**
     * 返回一个值表明是否有任何验证错误
     * @param string|null $attribute 属性名， null 的话就核对所有属性
     * @return boolean 是否有任何错误
     */
    public function hasErrors($attribute = null)
    {
        return $attribute === null ? !empty($this->_errors) : isset($this->_errors[$attribute]);
    }

    /**
     * 返回所有属性或单个属性的错误
     * @param string $attribute 属性名，传入 null 来检索所有属性的错误
     * @property array 所有属性的错误数组，如果没错误返回空数组
     * 此结果是二维数组，细节描述见[[getErrors()]]
     * @return array 所有属性或指定属性的错误，没有错误返回空数组
     * 注意当返回所有属性的错误时，结果是二维数组，如下所示：
     *
     * ~~~
     * [
     *     'username' => [
     *         'Username is required.',
     *         'Username must contain only word characters.',
     *     ],
     *     'email' => [
     *         'Email address is invalid.',
     *     ]
     * ]
     * ~~~
     *
     * @see getFirstErrors()
     * @see getFirstError()
     */
    public function getErrors($attribute = null)
    {
        if ($attribute === null) {
            return $this->_errors === null ? [] : $this->_errors;
        } else {
            return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
        }
    }

    /**
     * 返回模型每一个属性的第一个错误
     * @return array 第一个错误集，数组键是属性名，数组值是对应的错误消息，如果没有错误返回空数组。
     * @see getErrors()
     * @see getFirstError()
     */
    public function getFirstErrors()
    {
        if (empty($this->_errors)) {
            return [];
        } else {
            $errors = [];
            foreach ($this->_errors as $name => $es) {
                if (!empty($es)) {
                    $errors[$name] = reset($es);
                }
            }

            return $errors;
        }
    }

    /**
     * 返回指定属性的第一个错误
     * @param string $attribute 属性名
     * @return string 错误消息，没有错误返回 Null
     * @see getErrors()
     * @see getFirstErrors()
     */
    public function getFirstError($attribute)
    {
        return isset($this->_errors[$attribute]) ? reset($this->_errors[$attribute]) : null;
    }

    /**
     * 添加一个新错误到指定属性
     * @param string $attribute 属性名
     * @param string $error 新错误消息
     */
    public function addError($attribute, $error = '')
    {
        $this->_errors[$attribute][] = $error;
    }

    /**
     * 为所有属性或单个属性移除所有错误
     * @param string $attribute 属性名，用 null 来为所有属性移除错误
     */
    public function clearErrors($attribute = null)
    {
        if ($attribute === null) {
            $this->_errors = [];
        } else {
            unset($this->_errors[$attribute]);
        }
    }

    /**
     * 基于给定属性名生成一个用户友好的属性标签
     * 这是用空格替换下划线、破折号和点并把每个单词首字母变成大写来完成的。
     * 如，'department_name'或'DepartmentName'将生成'Department Name'.
     * @param string $name 列名
     * @return string 属性标签
     */
    public function generateAttributeLabel($name)
    {
        return Inflector::camel2words($name, true);
    }

    /**
     * 返回属性值
     * @param array $names 其值必须返回的属性列表
     * 默认为 null ，即列于[[attributes()]]的所有属性都返回。
     * 如果是数组，只有数组中的属性返回。
     * @param array $except 其值*无须*返回的属性列表
     * @return array 属性值(name => value).
     */
    public function getAttributes($names = null, $except = [])
    {
        $values = [];
        if ($names === null) {
            $names = $this->attributes();
        }
        foreach ($names as $name) {
            $values[$name] = $this->$name;
        }
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }

    /**
     * 批量设置属性值
     * @param array $values 要赋给模型的属性值(name => value)
     * @param boolean $safeOnly 是否只为安全属性赋值
     * 安全属性是指关联到当前[[scenario]]验证规则的那些属性
     * @see safeAttributes()
     * @see attributes()
     */
    public function setAttributes($values, $safeOnly = true)
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }

    /**
     * 此方法在非安全属性被批量赋值时调用
     * 默认实现在 YII_DEBUG 开启后将记录一个警告消息日志，它不做其他事情。
     * @param string $name 不安全的属性名
     * @param mixed $value 属性值
     */
    public function onUnsafeAttribute($name, $value)
    {
        if (YII_DEBUG) {
            Yii::trace("Failed to set unsafe attribute '$name' in '" . get_class($this) . "'.", __METHOD__);
        }
    }

    /**
     * 返回被使用的此模型所处的场景
     *
     * 场景影响验证如何执行和哪些属性能批量赋值
     *
     * @return string 此模型所在的场景，默认为[[SCENARIO_DEFAULT]]
     */
    public function getScenario()
    {
        return $this->_scenario;
    }

    /**
     * 为模型设置场景
     * 注意本方法不检查场景是否存在，[[validate()]]方法才执行场景检查
     * @param string $value 此模型所在的场景
     */
    public function setScenario($value)
    {
        $this->_scenario = $value;
    }

    /**
     * 返回当前场景能批量赋值的安全属性名
     * @return string[] 安全属性名
     */
    public function safeAttributes()
    {
        $scenario = $this->getScenario();
        $scenarios = $this->scenarios();
        if (!isset($scenarios[$scenario])) {
            return [];
        }
        $attributes = [];
        foreach ($scenarios[$scenario] as $attribute) {
            if ($attribute[0] !== '!') {
                $attributes[] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * 返回当前场景经验证的属性名
     * @return string[] 安全属性名
     */
    public function activeAttributes()
    {
        $scenario = $this->getScenario();
        $scenarios = $this->scenarios();
        if (!isset($scenarios[$scenario])) {
            return [];
        }
        $attributes = $scenarios[$scenario];
        foreach ($attributes as $i => $attribute) {
            if ($attribute[0] === '!') {
                $attributes[$i] = substr($attribute, 1);
            }
        }

        return $attributes;
    }

    /**
     * 从终端用户获取数据填充模型
     * 要加载的数据是`$data[formName]`，其中`formName`指[[formName()]]的值。
     * 如果[[formName()]]是空，整个`$data`数组将用来填充此模型。
     * 被填充的数据要经过[[setAttributes()]]的安全校对。
     * @param array $data 数据数组，通常是`$_POST`或`$_GET`，但也能是终端用户提供的任何有效数组
     * @param string $formName 用于加载数据到模型的表单名，如未设置，将使用[[formName()]]
     * @return boolean 模型是否被成功填入一些数据
     */
    public function load($data, $formName = null)
    {
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope == '' && !empty($data)) {
            $this->setAttributes($data);

            return true;
        } elseif (isset($data[$scope])) {
            $this->setAttributes($data[$scope]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * 用终端用户的数据填充一组模型
     * 此方法主要用来收集表格数据输入，要加载到每个模型的数据是`$data[formName][index]`，
     * 其中`formName`引用[[formName()]]的值，而`index` 是在`$models`数组中的模型索引。
     * 如果[[formName()]]为空，`$data[index]`将用来填充每个模型。
     * 被填充到每个模型的数据要经过[[setAttributes()]]的安全校对。
     * @param array $models 要填充的模型，注意所有模型都具有相同的类
     * @param array $data 数据数组，通常是`$_POST`或`$_GET`,但也可以是终端用户提供的任何有效数组
     * @return boolean 模型是否被成功填入一些数据
     */
    public static function loadMultiple($models, $data)
    {
        /** @var Model $model */
        $model = reset($models);
        if ($model === false) {
            return false;
        }
        $success = false;
        $scope = $model->formName();
        foreach ($models as $i => $model) {
            if ($scope == '') {
                if (isset($data[$i])) {
                    $model->setAttributes($data[$i]);
                    $success = true;
                }
            } elseif (isset($data[$scope][$i])) {
                $model->setAttributes($data[$scope][$i]);
                $success = true;
            }
        }

        return $success;
    }

    /**
     * 验证多模型
     * 此方法将验证每个模型，正被验证的模型可以是相同或不同类型。
     * @param array $models 要验证的模型
     * @param array $attributeNames 要验证的属性名，如果此参数为空，即验证列于适用验证规则的所有属性
     * @return boolean 是否所有模型都是有效的，如果一个或多个模型有验证错误就返回 false
     */
    public static function validateMultiple($models, $attributeNames = null)
    {
        $valid = true;
        /** @var Model $model */
        foreach ($models as $model) {
            $valid = $model->validate($attributeNames) && $valid;
        }

        return $valid;
    }

    /**
     * 返回字段列表，这些字段在没有特定字段被指定时默认由[[toArray()]]返回
     *
     * 一个字段就是[[toArray()]]返回的数组中的指定元素
     *
     * 本方法应返回字段名或字段定义的数组，如果是前者，字段名将视为对象属性名，属性值将用作字段值，
     * 如果是后者，数组键是字段名而数组值是相应的字段定义，
     * 字段定义可以是对象属性名或返回对应字段值的 PHP 回调函数。 回调函数的标识是：
     *
     * ```php
     * function ($field, $model) {
     *     // 返回字段值
     * }
     * ```
     *
     * 如，以下代码声明了四个字段：
     *
     * - `email`: 字段名和属性名`email`相同；
     * - `firstName`和`lastName`: 字段名是`firstName`和`lastName`，
     *    而它们的值从`first_name`和`last_name`属性获取；
     * - `fullName`: 字段名是`fullName`，它的值通过连接`first_name`和`last_name`获取。
     *
     * ```php
     * return [
     *     'email',
     *     'firstName' => 'first_name',
     *     'lastName' => 'last_name',
     *     'fullName' => function () {
     *         return $this->first_name . ' ' . $this->last_name;
     *     },
     * ];
     * ```
     *
     * 在这个方法，有时想要基于不同的上下文信息返回不同的字段列表。
     * 如，根据[[scenario]]或应用当前用户的权限，可以返回一组可见性不同的字段或过滤一些字段。
     *
     * 此方法的默认实现将返回以相同属性名为索引的[[attributes()]]
     *
     * @return array 字段名或字段定义的列表
     * @see toArray()
     */
    public function fields()
    {
        $fields = $this->attributes();

        return array_combine($fields, $fields);
    }

    /**
     * 确定[[toArray()]]返回哪些字段
     * 本方法将核对被请求的字段和声明在[[fields()]]和[[extraFields()]]的字段以确定哪些字段被返回。
     * @param array $fields 被请求输出的字段
     * @param array $expand 被请求输出的附加字段
     * @return array 要输出的字段列表，数组键是字段名，
     * 而数组值是对应的对象属性名或返回字段值的 PHP 回调函数。
     */
    protected function resolveFields(array $fields, array $expand)
    {
        $result = [];

        foreach ($this->fields() as $field => $definition) {
            if (is_integer($field)) {
                $field = $definition;
            }
            if (empty($fields) || in_array($field, $fields, true)) {
                $result[$field] = $definition;
            }
        }

        if (empty($expand)) {
            return $result;
        }

        foreach ($this->extraFields() as $field => $definition) {
            if (is_integer($field)) {
                $field = $definition;
            }
            if (in_array($field, $expand, true)) {
                $result[$field] = $definition;
            }
        }

        return $result;
    }

    /**
     * 返回遍历模型属性的迭代器
     * 此方法被IteratorAggregate接口所要求
     * @return ArrayIterator 遍历列表各项的迭代器
     */
    public function getIterator()
    {
        $attributes = $this->getAttributes();
        return new ArrayIterator($attributes);
    }

    /**
     * 返回在指定偏移量是否有元素
     * 此方法被 SPL 接口`ArrayAccess`所要求，它在你使用一些判断如`isset($model[$offset])`时会隐式调用
     * @param mixed $offset 要核对的偏移量
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->$offset !== null;
    }

    /**
     * 返回指定偏移量上的元素
     * 此方法被 SPL 接口`ArrayAccess`所要求，当你使用一些表达式如`$value = $model[$offset];`时会隐式调用
     * @param mixed $offset 要检索元素的偏移量
     * @return mixed 偏移量上的元素，如果在传入偏移量上没找到元素就返回 null
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * 设置指定偏移量的元素
     * 此方法被 SPL 接口`ArrayAccess`所要求，当你使用一些表达式如`$model[$offset] = $item;`时会隐式调用它
     * @param integer $offset 要设置元素的偏移量
     * @param mixed $item 元素值
     */
    public function offsetSet($offset, $item)
    {
        $this->$offset = $item;
    }

    /**
     * 设置指定偏移量的元素为 null
     * 此方法被 SPL 接口`ArrayAccess`所要求，当你使用一些表达式如`unset($model[$offset])`时它会隐式调用
     * @param mixed $offset 要释放元素的偏移量
     */
    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }
}
