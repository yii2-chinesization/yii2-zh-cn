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
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;

/**
 * BaseSecurity 为[[Security]]提供具体实现
 *
 * 不要使用 BaseSecurity ，而是使用[[Security]]
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Tom Worster <fsb@thefsb.org>
 * @since 2.0
 */
class BaseSecurity
{
    /**
     * 使用 AES ，块大小是128位(16字节)
     */
    const CRYPT_BLOCK_SIZE = 16;

    /**
     * 使用 AES-192 ，密钥长度是192位(24字节)
     */
    const CRYPT_KEY_SIZE = 24;

    /**
     * 使用 SHA-256.
     */
    const DERIVATION_HASH = 'sha256';

    /**
     * 使用 1000 次迭代
     */
    const DERIVATION_ITERATIONS = 1000;

    /**
     * 加密数据
     * @param string $data 要加密的数据
     * @param string $password 加密密码
     * @return string 已加密的数据
     * @throws Exception 如果 PHP Mcrypt 未加载或初始化失败
     * @see decrypt()
     */
    public static function encrypt($data, $password)
    {
        $module = static::openCryptModule();
        $data = static::addPadding($data);
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($module), MCRYPT_RAND);
        $key = static::deriveKey($password, $iv);
        mcrypt_generic_init($module, $key, $iv);
        $encrypted = $iv . mcrypt_generic($module, $data);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        return $encrypted;
    }

    /**
     * 解密数据
     * @param string $data 要解密的数据
     * @param string $password 解密密码
     * @return string 已解密数据
     * @throws Exception 如果 PHP Mcrypt 未加载或初始化失败
     * @see encrypt()
     */
    public static function decrypt($data, $password)
    {
        if ($data === null) {
            return null;
        }
        $module = static::openCryptModule();
        $ivSize = mcrypt_enc_get_iv_size($module);
        $iv = StringHelper::byteSubstr($data, 0, $ivSize);
        $key = static::deriveKey($password, $iv);
        mcrypt_generic_init($module, $key, $iv);
        $decrypted = mdecrypt_generic($module, StringHelper::byteSubstr($data, $ivSize, StringHelper::byteLength($data)));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        return static::stripPadding($decrypted);
    }

    /**
     * 添加填充到给定的数据(PKCS #7).
     * @param string $data 要填充的数据
     * @return string 填充后的数据
     */
    protected static function addPadding($data)
    {
        $pad = self::CRYPT_BLOCK_SIZE - (StringHelper::byteLength($data) % self::CRYPT_BLOCK_SIZE);

        return $data . str_repeat(chr($pad), $pad);
    }

    /**
     * 从给定数据剥除填充
     * @param string $data 要整理的数据
     * @return string 整理后的数据
     */
    protected static function stripPadding($data)
    {
        $end = StringHelper::byteSubstr($data, -1, null);
        $last = ord($end);
        $n = StringHelper::byteLength($data) - $last;
        if (StringHelper::byteSubstr($data, $n, null) == str_repeat($end, $last)) {
            return StringHelper::byteSubstr($data, 0, $n);
        }

        return false;
    }

    /**
     * 从给定的密码导出密钥(PBKDF2).
     * @param string $password 源密码
     * @param string $salt 随机的 salt
     * @return string 导出的密钥
     */
    protected static function deriveKey($password, $salt)
    {
        if (function_exists('hash_pbkdf2')) {
            return hash_pbkdf2(self::DERIVATION_HASH, $password, $salt, self::DERIVATION_ITERATIONS, self::CRYPT_KEY_SIZE, true);
        }
        $hmac = hash_hmac(self::DERIVATION_HASH, $salt . pack('N', 1), $password, true);
        $xorsum  = $hmac;
        for ($i = 1; $i < self::DERIVATION_ITERATIONS; $i++) {
            $hmac = hash_hmac(self::DERIVATION_HASH, $hmac, $password, true);
            $xorsum ^= $hmac;
        }

        return substr($xorsum, 0, self::CRYPT_KEY_SIZE);
    }

    /**
     * 用密钥哈希值作数据前缀，这样的话如果之后数据被纂改了就可以检测出来。
     * @param string $data 要保护的数据
     * @param string $key 用于生成哈希的密钥
     * @param string $algorithm 哈希算法(如"md5", "sha1", "sha256"等)，
     * 调用 PHP "hash_algos()"函数以了解你的系统所支持的哈希算法
     * @return string 以密钥哈希为前缀的数据
     * @see validateData()
     * @see getSecretKey()
     */
    public static function hashData($data, $key, $algorithm = 'sha256')
    {
        return hash_hmac($algorithm, $data, $key) . $data;
    }

    /**
     * 如果给定数据被纂改了就验证
     * @param string $data 要验证的数据，数据必须是之前由[[hashData()]]生成的数据
     * @param string $key 之前用于[[hashData()]]数据生成哈希的密钥
     * @param string $algorithm 哈希算法(如"md5", "sha1", "sha256"等)，调用 PHP "hash_algos()"
     * 函数了解你的系统所支持的哈希算法，当为数据生成哈希时，这个算法必须和传递到[[hashData()]]的值相同
     * @return string 用哈希剥出的真实数据，如果数据被纂改就返回 false
     * @see hashData()
     */
    public static function validateData($data, $key, $algorithm = 'sha256')
    {
        $hashSize = StringHelper::byteLength(hash_hmac($algorithm, 'test', $key));
        $n = StringHelper::byteLength($data);
        if ($n >= $hashSize) {
            $hash = StringHelper::byteSubstr($data, 0, $hashSize);
            $data2 = StringHelper::byteSubstr($data, $hashSize, $n - $hashSize);

            return $hash === hash_hmac($algorithm, $data2, $key) ? $data2 : false;
        } else {
            return false;
        }
    }

    /**
     * 返回关联到指定名的一个密钥
     * 如果密钥不存在，一个随机密钥将被生成并保存在应用的运行时目录下的"keys.json"文件，
     * 以便在之后的请求中返回相同的密钥。
     * @param string $name 要关联到密钥的名称
     * @param integer $length 如果密钥不存在应生成的密钥的长度
     * @return string 关联到指定名的密钥
     */
    public static function getSecretKey($name, $length = 32)
    {
        static $keys;
        $keyFile = Yii::$app->getRuntimePath() . '/keys.json';
        if ($keys === null) {
            $keys = [];
            if (is_file($keyFile)) {
                $keys = json_decode(file_get_contents($keyFile), true);
            }
        }
        if (!isset($keys[$name])) {
            $keys[$name] = static::generateRandomKey($length);
            file_put_contents($keyFile, json_encode($keys));
        }

        return $keys[$name];
    }

    /**
     * 生成随机密钥，密钥可包括大小写拉丁字母、数字、下划线、破折号和点。
     * @param integer $length 应生成的密钥长度
     * @return string 生成后的随机密钥
     */
    public static function generateRandomKey($length = 32)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $key = strtr(base64_encode(openssl_random_pseudo_bytes($length, $strong)), '+/=', '_-.');
            if ($strong) {
                return substr($key, 0, $length);
            }
        }
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-.';

        return substr(str_shuffle(str_repeat($chars, 5)), 0, $length);
    }

    /**
     * 打开 mcrypt 模块
     * @return resource mcrypt 模块句柄
     * @throws InvalidConfigException 如果 mcrypt 扩展没有安装
     * @throws Exception 如果 mcrypt 初始化失败
     */
    protected static function openCryptModule()
    {
        if (!extension_loaded('mcrypt')) {
            throw new InvalidConfigException('The mcrypt PHP extension is not installed.');
        }
        // AES uses a 128-bit block size
        $module = @mcrypt_module_open('rijndael-128', '', 'cbc', '');
        if ($module === false) {
            throw new Exception('Failed to initialize the mcrypt module.');
        }

        return $module;
    }

    /**
     * 从密码和一个随机 salt 生成一个安全的哈希
     *
     * 生成的哈希可以存储在数据库(如 MySQL 的`CHAR(64) CHARACTER SET latin1`)，
     * 然后当密码需要验证时，哈希可以取回来并传递给[[validatePassword()]]，如：
     *
     * ~~~
     * // 生成哈希(通常在用户注册或密码修改时完成)
     * $hash = Security::generatePasswordHash($password);
     * // ...保存 $hash 到数据库...
     *
     * // 在登录、验证时如果输入密码正确使用数据库取回的 $hash
     * if (Security::validatePassword($password, $hash) {
     *     // 密码是好的
     * } else {
     *     // 密码是坏的
     * }
     * ~~~
     *
     * @param string $password 要哈希加密的密码
     * @param integer $cost 用于 Blowfish 哈希算法的 cost 参数
     * cost 越高，该算法就用更长时间来生成哈希和靠哈希验证密码。高的 cost 能降低暴力攻击。
     * 对暴力攻击最好的保护是设置 cost 为生产服务器所容忍的最高值。
     * 计算哈希的时间每增加一倍就递增一 $cost 。
     * 因此，如果哈希花一秒钟计算的时候 $cost 是14，那么计算时间以2^($cost - 14)秒来变化。
     * @throws Exception 坏的密码参数或 cost 参数
     * @return string 密码哈希字符串， ASCII 且不高于64位
     * @see validatePassword()
     */
    public static function generatePasswordHash($password, $cost = 13)
    {
        $salt = static::generateSalt($cost);
        $hash = crypt($password, $salt);

        if (!is_string($hash) || strlen($hash) < 32) {
            throw new Exception('Unknown error occurred while generating hash.');
        }

        return $hash;
    }

    /**
     * 验证哈希密码
     * @param string $password 要验证的密码
     * @param string $hash 要验证密码的哈希
     * @return boolean 密码是否正确
     * @throws InvalidParamException 坏密码或哈希参数或如果Blowfish 哈希的crypt() 不可用
     * @see generatePasswordHash()
     */
    public static function validatePassword($password, $hash)
    {
        if (!is_string($password) || $password === '') {
            throw new InvalidParamException('Password must be a string and cannot be empty.');
        }

        if (!preg_match('/^\$2[axy]\$(\d\d)\$[\.\/0-9A-Za-z]{22}/', $hash, $matches) || $matches[1] < 4 || $matches[1] > 30) {
            throw new InvalidParamException('Hash is invalid.');
        }

        $test = crypt($password, $hash);
        $n = strlen($test);
        if ($n < 32 || $n !== strlen($hash)) {
            return false;
        }

        // Use a for-loop to compare two strings to prevent timing attacks. See:
        // http://codereview.stackexchange.com/questions/13512
        $check = 0;
        for ($i = 0; $i < $n; ++$i) {
            $check |= (ord($test[$i]) ^ ord($hash[$i]));
        }

        return $check === 0;
    }

    /**
     * 生成一个可以用来生成密码哈希的 salt
     *
     * PHP [crypt()](http://php.net/manual/en/function.crypt.php) 内置函数要求，
     * 对于 Blowfish 哈希算法，一个 salt 字符串的指定格式是：
     * "$2a$", "$2x$" 或 "$2y$", 一个两位 cost 参数，"$" 以及22位由 “./0-9A-Za-z”字符组成的字符串
     *
     * @param integer $cost cost 参数
     * @return string 随机 salt 值
     * @throws InvalidParamException 如果 cost 参数不在 4 至 31 之间
     */
    protected static function generateSalt($cost = 13)
    {
        $cost = (int) $cost;
        if ($cost < 4 || $cost > 31) {
            throw new InvalidParamException('Cost must be between 4 and 31.');
        }

        // Get 20 * 8bits of random entropy
        if (function_exists('openssl_random_pseudo_bytes')) {
            // https://github.com/yiisoft/yii2/pull/2422
            $rand = openssl_random_pseudo_bytes(20);
        } else {
            $rand = '';
            for ($i = 0; $i < 20; ++$i) {
                $rand .= chr(mt_rand(0, 255));
            }
        }

        // Add the microtime for a little more entropy.
        $rand .= microtime(true);
        // Mix the bits cryptographically into a 20-byte binary string.
        $rand = sha1($rand, true);
        // Form the prefix that specifies Blowfish algorithm and cost parameter.
        $salt = sprintf("$2y$%02d$", $cost);
        // Append the random salt data in the required base64 format.
        $salt .= str_replace('+', '.', substr(base64_encode($rand), 0, 22));

        return $salt;
    }
}
