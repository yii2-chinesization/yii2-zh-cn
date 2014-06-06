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
 * BaseStringHelper 为[[StringHelper]]提供了具体实现
 *
 * 不要使用 BaseStringHelper ，而是使用[[StringHelper]]。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Alex Makarov <sam@rmcreative.ru>
 * @since 2.0
 */
class BaseStringHelper
{
    /**
     * 返回给定字符串的字节数
     * 本方法以使用`mb_strlen()`来保证字符串被视为一个字节数组。
     * @param string $string 要测量长度的字符串
     * @return integer 给定字符串的字节数
     */
    public static function byteLength($string)
    {
        return mb_strlen($string, '8bit');
    }

    /**
     * 返回字符串里指定了起始位置和长度参数的那部分
     * 本方法以使用`mb_substr()`来保证字符串被视为一个字节数组。
     * @param string $string 输入字符串，必须是一个字符或多个字符
     * @param integer $start 起始位置
     * @param integer $length 截取的长度
     * @return string 截取的字符串部分，如果失败返回 FALSE 或空字符串
     * @see http://www.php.net/manual/en/function.substr.php
     */
    public static function byteSubstr($string, $start, $length)
    {
        return mb_substr($string, $start, $length, '8bit');
    }

    /**
     * 返回一个路径的尾部名称部分
     * 本方法类似于 PHP 函数`basename()`，除了本方法把 \ 和 / 都当做目录分隔符，独立于操作系统。
     * 本方法主要创建来工作于 PHP 命名空间，当和真正的文件路径工作时，PHP 函数`basename()`已经很好了。
     * 注意：本方法并不知道实际的文件系统或类似".."的路径部分。
     *
     * @param string $path 路径字符串
     * @param string $suffix 如果名称部分以后缀结尾，那么此后缀也会被切掉。
     * @return string 给定路径的尾部名称部分
     * @see http://www.php.net/manual/en/function.basename.php
     */
    public static function basename($path, $suffix = '')
    {
        if (($len = mb_strlen($suffix)) > 0 && mb_substr($path, -$len) == $suffix) {
            $path = mb_substr($path, 0, -$len);
        }
        $path = rtrim(str_replace('\\', '/', $path), '/\\');
        if (($pos = mb_strrpos($path, '/')) !== false) {
            return mb_substr($path, $pos + 1);
        }

        return $path;
    }

    /**
     * 返回父目录的路径
     * 本方法类似于`dirname()`，除了它将 \ and / 视为目录分隔符，以独立于操作系统。
     *
     * @param string $path 路径字符串
     * @return string 父目录的路径
     * @see http://www.php.net/manual/en/function.basename.php
     */
    public static function dirname($path)
    {
        $pos = mb_strrpos(str_replace('\\', '/', $path), '/');
        if ($pos !== false) {
            return mb_substr($path, 0, $pos);
        } else {
            return '';
        }
    }
    
    /**
     * 截取字符串到指定的字符数
     *
     * @param string $string 要截取的字符串
     * @param integer $length 截取长度，从原始字符串中截取多少字符到截取后的字符串
     * @param string $suffix 要追加到已截取字符串末尾的字符串
     * @param string $encoding 要使用的字符集缺省为应用当前使用的字符集
     * @return string 截取后的字符串
     */
    public static function truncate($string, $length, $suffix = '...', $encoding = null)
    {
        if (mb_strlen($string, $encoding ?: \Yii::$app->charset) > $length) {
            return trim(mb_substr($string, 0, $length, $encoding ?: \Yii::$app->charset)) . $suffix;
        } else {
            return $string;
        }
    }
    
    /**
     * 截取字符串到指定单词数
     *
     * @param string $string 要截取的字符串
     * @param integer $count 从原始字符串截取多少单词到截取后的字符串
     * @param string $suffix 要追加到已截取字符串末尾的字符串
     * @return string 截取后的字符串
     */
    public static function truncateWords($string, $count, $suffix = '...')
    {
        $words = preg_split('/(\s+)/u', trim($string), null, PREG_SPLIT_DELIM_CAPTURE);
        if (count($words) / 2 > $count) {
            return implode('', array_slice($words, 0, ($count * 2) - 1)) . $suffix;
        } else {
            return $string;
        }
    }
}
