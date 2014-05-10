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
 * BootstrapInterface（引导接口）是想要参与应用引导过程的那些类必须实现的接口
 *
 * 主要方法[[bootstrap()]]将在应用实例的`init()` 方法开始时被调用 。
 *
 * Bootstrap 类可以用两个方法注册
 *
 * 第一个方法主要是被扩展使用，并由 composer 安装流程所管理。
 * 你要做的主要是将你的扩展的引导类列入`composer.json`文件，如下所示：
 *
 * ```json
 * {
 *     // ...
 *     "extra": {
 *         "bootstrap": "path\\to\\MyBootstrapClass"
 *     }
 * }
 * ```
 *
 * 如果该扩展已安装，引导信息将保存在[[Application::extensions]]。
 *
 * 第二个方法是某些应用代码使用的，这些应用代码必须在引导过程注册并运行，这通过配置[[Application::bootstrap]]属性来完成：
 *
 * ```php
 * return [
 *     // ...
 *     'bootstrap' => [
 *         "path\\to\\MyBootstrapClass1",
 *         [
 *             'class' => "path\\to\\MyBootstrapClass2",
 *             'prop1' => 'value1',
 *             'prop2' => 'value2',
 *         ],
 *     ],
 * ];
 * ```
 *
 * 如你所见，你可以注册一个引导类，无论是类名或配置文件的形式均可。
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
interface BootstrapInterface
{
    /**
     * 在应用引导阶段要调用的引导方法
     * @param Application $app 当前运行的应用实例
     */
    public function bootstrap($app);
}
