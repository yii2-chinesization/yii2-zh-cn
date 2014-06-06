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
 * BaseHtmlPurifier 为[[HtmlPurifier]]提供具体实现
 *
 * 不要使用 BaseHtmlPurifier ，而是使用[[HtmlPurifier]]替代。
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @since 2.0
 */
class BaseHtmlPurifier
{
    /**
     * 通过 HTMLPurifier 传递标记使其安全地输出到终端用户
     *
     * @param string $content
     * @param array|null $config
     * @return string
     */
    public static function process($content, $config = null)
    {
        $configInstance = \HTMLPurifier_Config::create($config);
        $configInstance->autoFinalize = false;
        $purifier=\HTMLPurifier::instance($configInstance);
        $purifier->config->set('Cache.SerializerPath', \Yii::$app->getRuntimePath());

        return $purifier->purify($content);
    }
}
