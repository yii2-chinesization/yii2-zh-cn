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

/**
 * BaseVarDumper 为[[VarDumper]]提供具体实现
 *
 * 不要使用 BaseVarDumper ，而是使用 [[VarDumper]]
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BaseVarDumper
{
    private static $_objects;
    private static $_output;
    private static $_depth;

    /**
     * 显示变量
     * 本方法实现了 var_dump 和 print_r 类似的功能，但在处理复杂对象如 Yii 控制器时更强大。
     * @param mixed $var 要打印的变量
     * @param integer $depth  dumper 要进入的变量的最大深度，缺省为 10
     * @param boolean $highlight 结果是否语法高亮
     */
    public static function dump($var, $depth = 10, $highlight = false)
    {
        echo static::dumpAsString($var, $depth, $highlight);
    }

    /**
     * 以字符串形式打印变量
     * 本方法实现了 var_dump 和 print_r 类似的功能，但在处理复杂对象如 Yii 控制器时更强大。
     * @param mixed $var 要打印的变量
     * @param integer $depth dumper 要进入的变量的最大深度，缺省为 10
     * @param boolean $highlight 结果是否语法高亮
     * @return string 变量的字符串表现形式
     */
    public static function dumpAsString($var, $depth = 10, $highlight = false)
    {
        self::$_output = '';
        self::$_objects = [];
        self::$_depth = $depth;
        self::dumpInternal($var, 0);
        if ($highlight) {
            $result = highlight_string("<?php\n" . self::$_output, true);
            self::$_output = preg_replace('/&lt;\\?php<br \\/>/', '', $result, 1);
        }

        return self::$_output;
    }

    /**
     * @param mixed $var 要打印的变量
     * @param integer $level 深度层次
     */
    private static function dumpInternal($var, $level)
    {
        switch (gettype($var)) {
            case 'boolean':
                self::$_output .= $var ? 'true' : 'false';
                break;
            case 'integer':
                self::$_output .= "$var";
                break;
            case 'double':
                self::$_output .= "$var";
                break;
            case 'string':
                self::$_output .= "'" . addslashes($var) . "'";
                break;
            case 'resource':
                self::$_output .= '{resource}';
                break;
            case 'NULL':
                self::$_output .= "null";
                break;
            case 'unknown type':
                self::$_output .= '{unknown}';
                break;
            case 'array':
                if (self::$_depth <= $level) {
                    self::$_output .= '[...]';
                } elseif (empty($var)) {
                    self::$_output .= '[]';
                } else {
                    $keys = array_keys($var);
                    $spaces = str_repeat(' ', $level * 4);
                    self::$_output .= '[';
                    foreach ($keys as $key) {
                        self::$_output .= "\n" . $spaces . '    ';
                        self::dumpInternal($key, 0);
                        self::$_output .= ' => ';
                        self::dumpInternal($var[$key], $level + 1);
                    }
                    self::$_output .= "\n" . $spaces . ']';
                }
                break;
            case 'object':
                if (($id = array_search($var, self::$_objects, true)) !== false) {
                    self::$_output .= get_class($var) . '#' . ($id + 1) . '(...)';
                } elseif (self::$_depth <= $level) {
                    self::$_output .= get_class($var) . '(...)';
                } else {
                    $id = array_push(self::$_objects, $var);
                    $className = get_class($var);
                    $spaces = str_repeat(' ', $level * 4);
                    self::$_output .= "$className#$id\n" . $spaces . '(';
                    foreach ((array) $var as $key => $value) {
                        $keyDisplay = strtr(trim($key), ["\0" => ':']);
                        self::$_output .= "\n" . $spaces . "    [$keyDisplay] => ";
                        self::dumpInternal($value, $level + 1);
                    }
                    self::$_output .= "\n" . $spaces . ')';
                }
                break;
        }
    }
}
