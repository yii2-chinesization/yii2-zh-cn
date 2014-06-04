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

use Yii;
use yii\base\InvalidParamException;

/**
 * BaseMarkdown 为[[Markdown]]提供具体实现
 *
 * 不要使用 BaseMarkdown ，而是使用[[Markdown]]代替。
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class BaseMarkdown
{
    /**
     * @var array  markdown 风格名和对应解析器类配置的映射
     */
    public static $flavors = [
        'original' => [
            'class' => 'cebe\markdown\Markdown',
            'html5' => true,
        ],
        'gfm' => [
            'class' => 'cebe\markdown\GithubMarkdown',
            'html5' => true,
        ],
        'gfm-comment' => [
            'class' => 'cebe\markdown\Markdown',
            'html5' => true,
            'enableNewlines' => true,
        ],
    ];
    /**
     * @var string 当没有显式指定时使用的 markdown 风格，缺省为`original`
     * @see $flavors
     */
    public static $defaultFlavor = 'original';

    /**
     * 把 markdown 转变为 HTML
     *
     * @param string $markdown 要解析的 markdown 文本
     * @param string $flavor 要使用的 markdown 风格，可用值参阅[[$flavors]]
     * @return string 解析后的 HTML 输出
     * @throws \yii\base\InvalidParamException 当给定未定义的风格参数时
     */
    public static function process($markdown, $flavor = 'original')
    {
        $parser = static::getParser($flavor);

        return $parser->parse($markdown);
    }

    /**
     * 把 markdown 转变为 HTML 但只解析行内元素
     *
     * 这对解析小注释或描述行是有用的
     *
     * @param string $markdown 要解析的 markdown 文本
     * @param string $flavor 要使用的 markdown 风格，可用值见[[$flavors]]
     * @return string 解析后的 HTML 输出
     * @throws \yii\base\InvalidParamException 当给定未定义的风格参数时
     */
    public static function processParagraph($markdown, $flavor = 'original')
    {
        $parser = static::getParser($flavor);

        return $parser->parseParagraph($markdown);
    }

    /**
     * @param string $flavor
     * @return \cebe\markdown\Parser
     * @throws \yii\base\InvalidParamException 当给定未定义的风格参数时
     */
    protected static function getParser($flavor)
    {
        /** @var \cebe\markdown\Markdown $parser */
        if (!isset(static::$flavors[$flavor])) {
            throw new InvalidParamException("Markdown flavor '$flavor' is not defined.'");
        } elseif (!is_object($config = static::$flavors[$flavor])) {
            $parser = Yii::createObject($config);
            if (is_array($config)) {
                foreach ($config as $name => $value) {
                    $parser->{$name} = $value;
                }
            }
            static::$flavors[$flavor] = $parser;
        }

        return static::$flavors[$flavor];
    }
}
