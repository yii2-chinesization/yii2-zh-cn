<?php
/**
 * 翻译日期：20140510
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

use yii\validators\Validator;

/**
 * DynamicModel（动态模型）是基本用于支持临时数据验证的模型类
 *
 * 动态模型的典型用法如下：
 *
 * ```php
 * public function actionSearch($name, $email)
 * {
 *     $model = DynamicModel::validateData(compact('name', 'email'), [
 *         [['name', 'email'], 'string', 'max' => 128]],
 *         ['email', 'email'],
 *     ]);
 *     if ($model->hasErrors()) {
 *         // 验证失败
 *     } else {
 *         // 验证成功
 *     }
 * }
 * ```
 *
 * 以上示例说明如何在动态模型的帮助下验证`$name`和`$email`。[[validateData()]]方法创建了一个动态模型实例，使用给定数据（此例是`name`和`email`）来定义属性，然后调用[[Model::validate()]]方法。
 *
 * 你可以像普通模型里所做那样用[[hasErrors()]]核对验证结果。
 * 你也可以通过模型实例访问已定义的动态属性，如`$model->name`和`$model->email`。
 *
 * 另外你可以使用以下更"classic"（经典）的语法来执行临时数据验证：
 *
 * ```php
 * $model = new DynamicModel(compact('name', 'email'));
 * $model->addRule(['name', 'email'], 'string', ['max' => 128])
 *     ->addRule('email', 'email')
 *     ->validate();
 * ```
 *
 * 动态模型通过支持所谓的"动态属性"来实现以上临时数据验证功能，
 * 它基本上都允许通过它的构造函数或[[defineAttribute()]]来动态定义一个属性。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DynamicModel extends Model
{
    private $_attributes = [];

    /**
     * 构造函数
     * @param array $attributes 正要定义的动态属性(名值对或名)
     * @param array $config 要应用对此对象的配置数组
     */
    public function __construct(array $attributes = [], $config = [])
    {
        foreach ($attributes as $name => $value) {
            if (is_integer($name)) {
                $this->_attributes[$value] = null;
            } else {
                $this->_attributes[$name] = $value;
            }
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_attributes)) {
            $this->_attributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return isset($this->_attributes[$name]);
        } else {
            return parent::__isset($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            unset($this->_attributes[$name]);
        } else {
            parent::__unset($name);
        }
    }

    /**
     * 定义属性
     * @param string $name 属性名
     * @param mixed $value 属性值
     */
    public function defineAttribute($name, $value = null)
    {
        $this->_attributes[$name] = $value;
    }

    /**
     * 取消定义属性
     * @param string $name 属性名
     */
    public function undefineAttribute($name)
    {
        unset($this->_attributes[$name]);
    }

    /**
     * 添加一个验证规则到此模型
     * 你可以直接操作[[validators]]来添加或删除验证规则，此方法是快捷方式
     * @param string|array $attributes 要被该规则验证的属性
     * @param mixed $validator 此规则的验证器，可以是内置验证器名、模型类的方法名、匿名函数或验证类名。
     * @param array $options 要应用到此验证器的名值对选项
     * @return static 模型自身
     */
    public function addRule($attributes, $validator, $options = [])
    {
        $validators = $this->getValidators();
        $validators->append(Validator::createValidator($validator, $this, (array) $attributes, $options));

        return $this;
    }

    /**
     * 用指定验证规则验证给定数据
     * 此方法将创建一个动态模型实例，用要验证的数据填充它，并创建指定的验证规则，然后使用这些规则验证数据
     * @param array $data 要验证的数据（名值对）
     * @param array $rules 验证规则，请参考[[Model::rules()]]来了解此参数的格式
     * @return static 包含正被验证数据的模型实例
     * @throws InvalidConfigException 如果没有正确指定验证规则
     */
    public static function validateData(array $data, $rules = [])
    {
        /** @var DynamicModel $model */
        $model = new static($data);
        if (!empty($rules)) {
            $validators = $model->getValidators();
            foreach ($rules as $rule) {
                if ($rule instanceof Validator) {
                    $validators->append($rule);
                } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                    $validator = Validator::createValidator($rule[1], $model, (array) $rule[0], array_slice($rule, 2));
                    $validators->append($validator);
                } else {
                    throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
                }
            }
            $model->validate();
        }

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_keys($this->_attributes);
    }
}
