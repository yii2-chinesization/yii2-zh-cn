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
use DateTime;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;

/**
 * Formatter（格式器类）提供了一组普遍使用的数据格式化方法
 *
 * 格式器提供的格式化方法都命名为`asXyz()`形式。
 * 某些它们的行为可通过格式器的属性配置，如，配置[[dateFormat]]，就可以控制 one may control how [[asDate()]]如何格式化值到日期字符串。
 *
 * 格式器默认[[\yii\base\Application]]配置为一个应用组件，可通过`Yii::$app->formatter` 访问该实例。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Formatter extends Component
{
    /**
     * @var string 用于格式化时间和日期的时区，可以是任何值传递给[date_default_timezone_set()](http://www.php.net/manual/en/function.date-default-timezone-set.php)
     * 如`UTC`, `Europe/Berlin` 或`America/Chicago`
     * 请参考[php manual](http://www.php.net/manual/en/timezones.php) 了解可用的时区
     * 如果此属性未设置，将使用[[\yii\base\Application::timeZone]]
     */
    public $timeZone;
    /**
     * @var string 默认用于格式化 PHP date() 方法输出的日期格式字符串
     */
    public $dateFormat = 'Y-m-d';
    /**
     * @var string 默认用于格式化 PHP date() 方法输出的时间格式字符串
     */
    public $timeFormat = 'H:i:s';
    /**
     * @var string 默认用于格式化 PHP date() 方法输出的日期时间格式字符串
     */
    public $datetimeFormat = 'Y-m-d H:i:s';
    /**
     * @var string 当格式化 null 时要显示的文本，默认为'<span class="not-set">(not set)</span>'.
     */
    public $nullDisplay;
    /**
     * @var array 当格式化布尔值时要显示的文本，第一个元素对应 false ，第二个元素对应 ture，缺省为`['No', 'Yes']` 。
     */
    public $booleanFormat;
    /**
     * @var string 当格式化数字时字符显示为浮点型，如未设置将使用"."
     */
    public $decimalSeparator;
    /**
     * @var string 当格式化数字时字符以千位分隔符显示，如未设置将使用","
     */
    public $thousandSeparator;
    /**
     * @var array 用于格式化大小(bytes)的格式，指定了三个元素："base", decimals"和"decimalSeparator" 。
     * 它们分别对应千字节计算的基数(1000或1024 bytes/kilobyte,缺省为1024)，
     * 小数点后的位数(缺省为2)和字符显示为小数点。
     */
    public $sizeFormat = [
        'base' => 1024,
        'decimals' => 2,
        'decimalSeparator' => null,
    ];

    /**
     * 初始化该组件
     */
    public function init()
    {
        if ($this->timeZone === null) {
            $this->timeZone = Yii::$app->timeZone;
        }

        if (empty($this->booleanFormat)) {
            $this->booleanFormat = [Yii::t('yii', 'No'), Yii::t('yii', 'Yes')];
        }
        if ($this->nullDisplay === null) {
            $this->nullDisplay = '<span class="not-set">' . Yii::t('yii', '(not set)') . '</span>';
        }
    }

    /**
     * 基于给定的格式类型格式化传入值
     * 此方法将调用此类中适用的一个"as"方法来完成格式化
     * 对于"xyz"类型，将使用"asXyz"方法。例如，如果格式是"html"，就使用[[asHtml()]]。格式名不区分大小写。
     * @param mixed $value 要格式化的值
     * @param string|array $format 值的格式，如"html", "text"。要指定此格式化方法的其他参数，请使用数组
     * 数组的第一个元素指定格式名，而剩下的元素将用作此格式化方法的参数，
     * 如`['date', 'Y-m-d']`格式将引起`asDate($value, 'Y-m-d')`的调用。
     * @return string 格式化结果
     * @throws InvalidParamException 如果是该类不支持的类型
     */
    public function format($value, $format)
    {
        if (is_array($format)) {
            if (!isset($format[0])) {
                throw new InvalidParamException('The $format array must contain at least one element.');
            }
            $f = $format[0];
            $format[0] = $value;
            $params = $format;
            $format = $f;
        } else {
            $params = [$value];
        }
        $method = 'as' . $format;
        if ($this->hasMethod($method)) {
            return call_user_func_array([$this, $method], $params);
        } else {
            throw new InvalidParamException("Unknown type: $format");
        }
    }

    /**
     * 格式化值不带任何格式
     * 这个方法只简单返回没有任何格式的参数
     * @param mixed $value 要格式化的值
     * @return string 已格式化的结果
     */
    public function asRaw($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return $value;
    }

    /**
     * 格式化值成为 HTML 编码的纯文本
     * @param mixed $value 要格式化的值
     * @return string 格式化结果
     */
    public function asText($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return Html::encode($value);
    }

    /**
     * 格式化值为 HTML 编码的纯文本且换行符变成分隔符
     * @param mixed $value 要格式化的值
     * @return string 格式化结果
     */
    public function asNtext($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return nl2br(Html::encode($value));
    }

    /**
     * 格式化值为 HTML 编码的文本段落
     * 每个文本段落以`<p>`标签封闭，一个或多个连续空行就分隔为两个段落
     * @param mixed $value 要格式化的值
     * @return string 格式化结果
     */
    public function asParagraphs($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return str_replace('<p></p>', '', '<p>' . preg_replace('/[\r\n]{2,}/', "</p>\n<p>", Html::encode($value)) . '</p>');
    }

    /**
     * 格式化值为 HTML 文本
     * 传入值用[[HtmlPurifier]]净化以避免 XSS 攻击，如果不想对值进行任何净化请使用[[asRaw()]]。
     * @param mixed $value 要格式化的值
     * @param array|null $config HTMLPurifier 类的配置
     * @return string 格式化结果
     */
    public function asHtml($value, $config = null)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return HtmlPurifier::process($value, $config);
    }

    /**
     * 格式化值成一个 mailto 链接
     * @param mixed $value 要格式化的值
     * @return string 格式化结果
     */
    public function asEmail($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return Html::mailto(Html::encode($value), $value);
    }

    /**
     * 格式化值为图像标签
     * @param mixed $value 要格式化的值
     * @return string 格式化结果
     */
    public function asImage($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return Html::img($value);
    }

    /**
     * 格式化值成为超链接
     * @param mixed $value 要格式化的值
     * @return string 格式化结果
     */
    public function asUrl($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $url = $value;
        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            $url = 'http://' . $url;
        }

        return Html::a(Html::encode($value), $url);
    }

    /**
     * 格式化值成为布尔值
     * @param mixed $value 要格式化的值
     * @return string 格式化结果
     * @see booleanFormat
     */
    public function asBoolean($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return $value ? $this->booleanFormat[1] : $this->booleanFormat[0];
    }

    /**
     * 格式化值成为日期
     * @param integer|string|DateTime $value 要格式化的值
     * 支持以下类型的值：
     *
     * - 代表 UNIX 时间戳的整型
     * - 可用`strtotime()`解析为 UNIX 时间戳的字符串
     * - PHP DateTime 对象
     *
     * @param string $format 用于把值转为日期字符串的格式，如为 null ，将使用[[dateFormat]]。
     * 这个格式字符串必须是能被 PHP `date()`函数识别的类型。
     * @return string 格式化结果
     * @see dateFormat
     */
    public function asDate($value, $format = null)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $value = $this->normalizeDatetimeValue($value);

        return $this->formatTimestamp($value, $format === null ? $this->dateFormat : $format);
    }

    /**
     * 格式化值为事件
     * @param integer|string|DateTime $value 要格式化的值，支持以下类型的值：
     *
     * - 代表 UNIX 时间戳的整型
     * - 可用`strtotime()`解析为 UNIX 时间戳的字符串
     * - PHP DateTime 对象
     *
     * @param string $format 用于转变值到时间字符串的格式，如果是 null ，使用[[timeFormat]]。
     * 格式字符串必须是能被PHP `date()` 函数识别的类型
     * @return string 格式化的结果
     * @see timeFormat
     */
    public function asTime($value, $format = null)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $value = $this->normalizeDatetimeValue($value);

        return $this->formatTimestamp($value, $format === null ? $this->timeFormat : $format);
    }

    /**
     * 格式化值成为日期时间
     * @param integer|string|DateTime $value 要格式化的值，支持以下类型的值：
     *
     * - 代表 UNIX 时间戳的整型
     * - 可用`strtotime()`解析为 UNIX 时间戳的字符串
     * - PHP DateTime 对象
     *
     * @param string $format 用于将值转变为日期时间字符串的格式，如果是 null ，将使用[[datetimeFormat]]
     * 格式字符串必须是能被 PHP `date()` 函数识别的类型
     * @return string 格式化的结果
     * @see datetimeFormat
     */
    public function asDatetime($value, $format = null)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $value = $this->normalizeDatetimeValue($value);

        return $this->formatTimestamp($value, $format === null ? $this->datetimeFormat : $format);
    }

    /**
     * 标准化给定的日期时间值以便各种日期/时间格式化方法都能使用
     *
     * @param mixed $value 要标准化的日期时间值
     * @return integer 标准化的日期时间值
     */
    protected function normalizeDatetimeValue($value)
    {
        if (is_string($value)) {
            if (is_numeric($value) || $value === '') {
                $value = (double)$value;
            } else {
                try {
                    $date = new DateTime($value);
                } catch (\Exception $e) {
                    return false;
                }
                $value = (double)$date->format('U');
            }
            return $value;
        } elseif ($value instanceof DateTime || $value instanceof \DateTimeInterface) {
            return (double)$value->format('U');
        } else {
            return (double)$value;
        }
    }

    /**
     * @param integer $value 标准化的日期时间值
     * @param string $format 用于转换值到日期字符串的格式
     * @return string 格式化的结果
     */
    protected function formatTimestamp($value, $format)
    {
        $date = new DateTime(null, new \DateTimeZone($this->timeZone));
        $date->setTimestamp($value);

        return $date->format($format);
    }

    /**
     * 格式化值成为整型
     * @param mixed $value 要格式化的值
     * @return string 格式化的结果
     */
    public function asInteger($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        if (is_string($value) && preg_match('/^(-?\d+)/', $value, $matches)) {
            return $matches[1];
        } else {
            $value = (int) $value;

            return "$value";
        }
    }

    /**
     * 格式化值为双精度数字
     * 属性[[decimalSeparator]]将用来表示小数点
     * @param mixed $value 要格式化的值
     * @param integer $decimals 小数点后的位数
     * @return string 格式化的结果
     * @see decimalSeparator
     */
    public function asDouble($value, $decimals = 2)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        if ($this->decimalSeparator === null) {
            return sprintf("%.{$decimals}f", $value);
        } else {
            return str_replace('.', $this->decimalSeparator, sprintf("%.{$decimals}f", $value));
        }
    }

    /**
     * 格式化值为带小数和千位分隔符的数字
     * 这个方法会调用 PHP number_format() 函数来进行格式化
     * @param mixed $value 要格式化的值
     * @param integer $decimals 小数点后的位数
     * @return string 格式化的结果
     * @see decimalSeparator
     * @see thousandSeparator
     */
    public function asNumber($value, $decimals = 0)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $ds = isset($this->decimalSeparator) ? $this->decimalSeparator : '.';
        $ts = isset($this->thousandSeparator) ? $this->thousandSeparator : ',';

        return number_format($value, $decimals, $ds, $ts);
    }

    /**
     * 格式化字节值变为人类可读形式的大小
     * @param integer $value 要格式化的字节值
     * @param boolean $verbose 是否使用全名(如 bytes, kilobytes, ...).
     * 默认为 false 即使用缩写(如 B, KB, ...).
     * @return string 格式化结果
     * @see sizeFormat
     */
    public function asSize($value, $verbose = false)
    {
        $position = 0;

        do {
            if ($value < $this->sizeFormat['base']) {
                break;
            }

            $value = $value / $this->sizeFormat['base'];
            $position++;
        } while ($position < 6);

        $value = round($value, $this->sizeFormat['decimals']);
        $formattedValue = isset($this->sizeFormat['decimalSeparator']) ? str_replace('.', $this->sizeFormat['decimalSeparator'], $value) : $value;
        $params = ['n' => $formattedValue];

        switch ($position) {
            case 0:
                return $verbose ? Yii::t('yii', '{n, plural, =1{# byte} other{# bytes}}', $params) : Yii::t('yii', '{n} B', $params);
            case 1:
                return $verbose ? Yii::t('yii', '{n, plural, =1{# kilobyte} other{# kilobytes}}', $params) : Yii::t('yii', '{n} KB', $params);
            case 2:
                return $verbose ? Yii::t('yii', '{n, plural, =1{# megabyte} other{# megabytes}}', $params) : Yii::t('yii', '{n} MB', $params);
            case 3:
                return $verbose ? Yii::t('yii', '{n, plural, =1{# gigabyte} other{# gigabytes}}', $params) : Yii::t('yii', '{n} GB', $params);
            case 4:
                return $verbose ? Yii::t('yii', '{n, plural, =1{# terabyte} other{# terabytes}}', $params) : Yii::t('yii', '{n} TB', $params);
            default:
                return $verbose ? Yii::t('yii', '{n, plural, =1{# petabyte} other{# petabytes}}', $params) : Yii::t('yii', '{n} PB', $params);
        }
    }

    /**
     * 格式化值成为人类可读的某个日期和当前日期的时间间隔
     *
     * @param integer|string|DateTime|\DateInterval $value 要格式化的值，支持以下格式：
     *
     * - 代表 UNIX 时间戳的整型
     * - 可通过`strtotime()`解析为 UNIX 时间戳或可传递到 DateInterval 构造函数的字符串
     * - PHP DateTime 对象
     * - PHP DateInterval 对象(正的时间间隔表示过去，负的时间间隔表示将来)
     *
     * @param integer|string|DateTime|\DateInterval $referenceTime 如果指定的值用来取代现在
     * @return string 格式化的结果
     */
    public function asRelativeTime($value, $referenceTime = null)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        if ($value instanceof \DateInterval) {
            $interval = $value;
        } else {
            $timestamp = $this->normalizeDatetimeValue($value);

            if ($timestamp === false) {
                // $value is not a valid date/time value, so we try
                // to create a DateInterval with it
                try {
                    $interval = new \DateInterval($value);
                } catch (\Exception $e) {
                    // invalid date/time and invalid interval
                    return $this->nullDisplay;
                }
            } else {
                $timezone = new \DateTimeZone($this->timeZone);

                if ($referenceTime === null) {
                    $dateNow = new DateTime('now', $timezone);
                } else {
                    $referenceTime = $this->normalizeDatetimeValue($referenceTime);
                    $dateNow = new DateTime(null, $timezone);
                    $dateNow->setTimestamp($referenceTime);
                }

                $dateThen = new DateTime(null, $timezone);
                $dateThen->setTimestamp($timestamp);

                $interval = $dateThen->diff($dateNow);
            }
        }

        if ($interval->invert) {
            if ($interval->y >= 1) {
                return Yii::t('yii', 'in {delta, plural, =1{a year} other{# years}}', ['delta' => $interval->y]);
            }
            if ($interval->m >= 1) {
                return Yii::t('yii', 'in {delta, plural, =1{a month} other{# months}}', ['delta' => $interval->m]);
            }
            if ($interval->d >= 1) {
                return Yii::t('yii', 'in {delta, plural, =1{a day} other{# days}}', ['delta' => $interval->d]);
            }
            if ($interval->h >= 1) {
                return Yii::t('yii', 'in {delta, plural, =1{an hour} other{# hours}}', ['delta' => $interval->h]);
            }
            if ($interval->i >= 1) {
                return Yii::t('yii', 'in {delta, plural, =1{a minute} other{# minutes}}', ['delta' => $interval->i]);
            }

            return Yii::t('yii', 'in {delta, plural, =1{a second} other{# seconds}}', ['delta' => $interval->s]);
        } else {
            if ($interval->y >= 1) {
                return Yii::t('yii', '{delta, plural, =1{a year} other{# years}} ago', ['delta' => $interval->y]);
            }
            if ($interval->m >= 1) {
                return Yii::t('yii', '{delta, plural, =1{a month} other{# months}} ago', ['delta' => $interval->m]);
            }
            if ($interval->d >= 1) {
                return Yii::t('yii', '{delta, plural, =1{a day} other{# days}} ago', ['delta' => $interval->d]);
            }
            if ($interval->h >= 1) {
                return Yii::t('yii', '{delta, plural, =1{an hour} other{# hours}} ago', ['delta' => $interval->h]);
            }
            if ($interval->i >= 1) {
                return Yii::t('yii', '{delta, plural, =1{a minute} other{# minutes}} ago', ['delta' => $interval->i]);
            }

            return Yii::t('yii', '{delta, plural, =1{a second} other{# seconds}} ago', ['delta' => $interval->s]);
        }
    }
}
