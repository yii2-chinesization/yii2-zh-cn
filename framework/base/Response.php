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

/**
 * Response（响应类）代表[[Application]]对[[Request]]的响应。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Response extends Component
{
    /**
     * @var integer 退出状态，退出状态可以是 0 至 254 之间
     * 状态 0 指程序终止成功
     */
    public $exitStatus = 0;

    /**
     * 发送响应到客户端
     */
    public function send()
    {
    }

    /**
     * 清除所有现存输出缓冲器
     */
    public function clearOutputBuffers()
    {
        // the following manual level counting is to deal with zlib.output_compression set to On
        for ($level = ob_get_level(); $level > 0; --$level) {
            if (!@ob_end_clean()) {
                ob_clean();
            }
        }
    }
}
