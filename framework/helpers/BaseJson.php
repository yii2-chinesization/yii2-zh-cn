<?php
/**
 * 翻译日期：20140513
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\helpers;

use yii\base\InvalidParamException;
use yii\base\Arrayable;
use yii\web\JsExpression;

/**
 * BaseJson 为[[Json]]提供具体实现
 *
 * 不要使用 BaseJson ，而是使用[[Json]]代替。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BaseJson
{
    /**
     * 将给定值编码为 JSON 字符串
     * 本方法通过支持 JavaScript 表达式来增强了`json_encode()`函数，特别是本方法将不编码[[JsExpression]]对象的形式所代表的 JavaScript 表达式。
     * @param mixed $value 要编码的数据
     * @param integer $options 编码选项，详情参阅
     * <http://www.php.net/manual/en/function.json-encode.php>.
     * @return string 编码结果
     */
    public static function encode($value, $options = 0)
    {
        $expressions = [];
        $value = static::processData($value, $expressions, uniqid());
        $json = json_encode($value, $options);

        return empty($expressions) ? $json : strtr($json, $expressions);
    }

    /**
     * 把给定 JSON 字符串解码为 PHP 数据结构
     * @param string $json 要解码的 JSON 字符串
     * @param boolean $asArray 是否返以关联数组形式的对象
     * @return mixed  PHP 数据
     * @throws InvalidParamException 如果解码发生任何错误
     */
    public static function decode($json, $asArray = true)
    {
        if (is_array($json)) {
            throw new InvalidParamException('Invalid JSON data.');
        }
        $decode = json_decode((string) $json, $asArray);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                break;
            case JSON_ERROR_DEPTH:
                throw new InvalidParamException('The maximum stack depth has been exceeded.');
            case JSON_ERROR_CTRL_CHAR:
                throw new InvalidParamException('Control character error, possibly incorrectly encoded.');
            case JSON_ERROR_SYNTAX:
                throw new InvalidParamException('Syntax error.');
            case JSON_ERROR_STATE_MISMATCH:
                throw new InvalidParamException('Invalid or malformed JSON.');
            case JSON_ERROR_UTF8:
                throw new InvalidParamException('Malformed UTF-8 characters, possibly incorrectly encoded.');
            default:
                throw new InvalidParamException('Unknown JSON decoding error.');
        }

        return $decode;
    }

    /**
     * 在发送数据到`json_encode()`前预处理数据
     * @param mixed $data 要处理的数据
     * @param array $expressions  JavaScript 表达式集合
     * @param string $expPrefix 内部用来处理 JS 表达式的前缀
     * @return mixed 处理后的数据
     */
    protected static function processData($data, &$expressions, $expPrefix)
    {
        if (is_object($data)) {
            if ($data instanceof JsExpression) {
                $token = "!{[$expPrefix=" . count($expressions) . ']}!';
                $expressions['"' . $token . '"'] = $data->expression;

                return $token;
            } elseif ($data instanceof \JsonSerializable) {
                $data = $data->jsonSerialize();
            } elseif ($data instanceof Arrayable) {
                $data = $data->toArray();
            } else {
                $result = [];
                foreach ($data as $name => $value) {
                    $result[$name] = $value;
                }
                $data = $result;
            }

            if ($data === []) {
                return new \stdClass();
            }
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $data[$key] = static::processData($value, $expressions, $expPrefix);
                }
            }
        }

        return $data;
    }
}
