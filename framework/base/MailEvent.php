<?php
/**
 * 翻译日期：20140507
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

/**
 * MailEvent（电邮事件）代表用于电邮事件的事件参数
 *
 * 通过设置[[isValid]]属性可以控制是否继续发送邮件
 *
 * @author Mark Jebri <mark.github@yandex.ru>
 * @since 2.0
 */
class MailEvent extends Event
{

    /**
     * @var \yii\mail\MessageInterface 被发送的邮件
     */
    public $message;
    /**
     * @var boolean 如果邮件发送成功
     */
    public $isSuccessful;
    /**
     * @var boolean 是否继续发送邮件，[[\yii\mail\BaseMailer::EVENT_BEFORE_SEND]]的事件处理器
     * 可以设置本属性以决定是否继续发送邮件
     */
    public $isValid = true;
}
