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

use Yii;

/**
 * Request（请求类）代表由[[Application]]处理的一个请求
 *
 * @property boolean $isConsoleRequest 此值表明当前请求是否由控制台发出
 * @property string $scriptFile 入口脚本文件路径(processed w/ realpath()).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class Request extends Component
{
    private $_scriptFile;
    private $_isConsoleRequest;

    /**
     * 解析当前请求到路由和相关参数
     * @return array 第一个元素是路由，第二个是相关参数
     */
    abstract public function resolve();

    /**
     * 返回一个值表明当前请求是否由命令行发出
     * @return boolean 该值指明当前请求是否由控制台发出
     */
    public function getIsConsoleRequest()
    {
        return $this->_isConsoleRequest !== null ? $this->_isConsoleRequest : PHP_SAPI === 'cli';
    }

    /**
     * 设置一个值来表明当前请求是否由命令行发出
     * @param boolean $value 一个表明当前请求是否由命令行发出的值
     */
    public function setIsConsoleRequest($value)
    {
        $this->_isConsoleRequest = $value;
    }

    /**
     * 返回入口脚本文件路径
     * @return string 入口脚本文件路径(processed w/ realpath())
     * @throws InvalidConfigException 如果入口脚本文件路径不能自动确定
     */
    public function getScriptFile()
    {
        if ($this->_scriptFile === null) {
            if (isset($_SERVER['SCRIPT_FILENAME'])) {
                $this->setScriptFile($_SERVER['SCRIPT_FILENAME']);
            } else {
                throw new InvalidConfigException('Unable to determine the entry script file path.');
            }
        }

        return $this->_scriptFile;
    }

    /**
     * 设置入口脚本文件路径
     * 入口脚本文件路径通常基于`SCRIPT_FILENAME` SERVER 变量确定，然而，有些服务器配置，这并不正确或可行。
     * 本 setter 方法提供来便于手工指定入口脚本文件路径。
     * @param string $value 入口脚本文件路径，既可是文件路径又可是路径别名
     * @throws InvalidConfigException 如果提供的入口脚本文件路径无效
     */
    public function setScriptFile($value)
    {
        $scriptFile = realpath(Yii::getAlias($value));
        if ($scriptFile !== false && is_file($scriptFile)) {
            $this->_scriptFile = $scriptFile;
        } else {
            throw new InvalidConfigException('Unable to determine the entry script file path.');
        }
    }
}
