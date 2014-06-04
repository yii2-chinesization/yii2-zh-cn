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
 * Security（安全助手类） 提供了一组方法来处理常见的有关安全的任务
 *
 * 特别是，安全类支持以下功能：
 *
 * - 加密和解密：[[encrypt()]]和[[decrypt()]]
 * - 数据纂改预防：[[hashData()]]和[[validateData()]]
 * - 密码验证：[[generatePasswordHash()]]和[[validatePassword()]]
 *
 * 此外，安全类提供[[getSecretKey()]]来支持生成指定的密钥，
 * 这些密钥一旦生成，将被存储在文件中并可在以后的请求中使用。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Tom Worster <fsb@thefsb.org>
 * @since 2.0
 */
class Security extends BaseSecurity
{
}
