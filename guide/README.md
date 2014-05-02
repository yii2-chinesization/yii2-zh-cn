Yii 2.0 权威指南
===============================

> ### 翻译说明
> #### 概念解释

> - 翻译指粗翻和持续更新翻译工作，更新文档前的翻译状态。
> - 校对指文档的校对和更新工作，更新文档前的校对状态。
> - 同一文档的翻译人员和校对人员最好不同，以便核查勘误。
> - 无论翻译还是校对，都请标明所参考的英文原文的版本日期，请注意放原文日期，目的是方便后续更新文档。

> #### 翻译状态分为以下几种：
> 可以认领：
>
> - 【待翻译】任何翻译人员都可认领
> - 【需更新】指官方文档已更新，需要翻译人员或校对进行更新，及翻译。
>
> 不能认领：
>
> - 【翻译中】表示该文档已被某位翻译人员认领，正在翻译中
> - 【已完成】官方文档无更新，已完成翻译和校对的中文文档，表示该文档翻译结束。
> 
> 翻译中：
>
> - 【未校对】粗翻完成后更改
> - 【已更新】标明新增了内容，后面往往跟其他标签如：
>     * 【已更新|未校对】更新且汉化完成
>     * 【已更新|翻译中】更新大量内容，尚未完全汉化
> - 【已校对】校对完毕更新为该状态，文档需要更新时更改标签为【需更新】
>
> **************************

本教程的发布遵循[ Yii 文档使用许可](http://www.yiiframework.com/doc/terms/).

版权所有

2014 (c) Yii Software LLC.

介绍
------------

* 【已校对-20140430】[关于 Yii](intro-yii.md) - Yii 是什么，擅长做什么？
* 【需更新-20140502】[从 Yii 1.1 升级](intro-upgrade-from-v1.md)


入门
---------------

* **TBD** [概述](start-overview.md) - 从哪开始？
* 【已校对-20140501】【官方更新中】[从基础 App 开始](start-basic.md) - 适用于开发者个人开发单层应用
* 【已校对-20140501】【官方更新中】[从高级 App 开始](start-advanced.md) - 适用于开发团队开发企业应用
* 【已校对-20140501】【官方更新中】[从新建 App 开始](start-scratch.md) - 学习按步骤从头建立一个 Yii 应用的深入细节

基本概念
--------------

* [组件](basic-components.md)
* 【未校对-20140501】[对象属性](basic-properties.md)
* 【未校对-20140501】[事件](basic-events.md)
* 【未校对-20140501】[行为](basic-behaviors.md)
* 【需更新-20140502】【官方已完成】[对象配置](basic-configs.md)
* 【未校对-20140501】【官方更新中】[类自动加载](basic-autoloading.md)
* 【未校对-20140501】【官方更新中】[别名](basic-alias.md)
* **TBD** [扩展](basic-extensions.md)
* 【翻译中-20140501】[服务定位器](basic-service-locator.md)
* 【翻译中-20140501】[依赖注入容器](basic-di-container.md)


基本结构
---------------

* 【未校对-20140501】【官方更新中】[MVC 概述](structure-mvc.md)
* 【】【官方更新中】[入口脚本](structure-entry-scripts.md)
* **TBD** [应用](structure-applications.md)
* 【未校对-20140501】【官方更新中】[控制器和动作](structure-controllers.md)
* 【未校对-20140501】【官方更新中】[视图](structure-views.md)
* 【未校对-20140501】【官方更新中】[模型](structure-models.md)
* **TBD** [小部件](structure-widgets.md)
* **TBD** [模块](structure-modules.md)


请求处理
-----------------

* **TBD** [请求生命周期](runtime-lifecycle.md)
* **TBD** [引导](runtime-bootstrapping.md)
* **TBD** [路由](runtime-routing.md)
* **TBD** [请求](runtime-requests.md)
* **TBD** [响应](runtime-responses.md)
* **TBD** [Sessions（会话）和 Cookies](runtime-sessions-cookies.md)
* 【未校对-20140501】【官方更新中】[URL 解析和生成](runtime-url-handling.md)
* **TBD** [过滤器](runtime-filtering.md)


数据库使用
---------------------

* 【未校对-20140501】【官方更新中】[数据访问对象（DAO）](db-dao.md) - 数据库连接、基本查询、事务和模式操作
* 【未校对-20140501】【官方更新中】[查询生成器（Query Builder）](db-query-builder.md) - 使用简单抽象层查询数据库
* 【未校对-20140501】【官方更新中】[活动记录（Active Record）](db-active-record.md) - 活动记录对象关系映射（ORM），检索和操作记录、定义关联关系
* 【未校对-20140501】【官方更新中】[数据库迁移](db-migrations.md)
* **TBD** [Sphinx](db-sphinx.md)
* **TBD** [Redis](db-redis.md)
* **TBD** [MongoDB](db-mongodb.md)
* **TBD** [ElasticSearch](db-elastic-search.md)


收集数据
-----------------

* 【未校对-20140501】【官方更新中】[创建表单](input-forms.md)
* 【翻译中-20140501】[输入验证](input-validation.md)
* **TBD** [文件上传](input-file-uploading.md)
* **TBD** [输入多模型](input-multiple-models.md)


显示数据
---------------

* **TBD** [格式化输出数据](output-formatting.md)
* **TBD** [分页](output-pagination.md)
* **TBD** [排序](output-sorting.md)
* 【未校对-20140501】【官方更新中】[数据来源](output-data-providers.md)
* 【翻译中-20140501】[数据小部件](output-data-widgets.md)
* 【未校对-20140501】【官方更新中】[资源管理](output-assets.md)


安全
--------

* 【未校对-20140501】【官方更新中】[认证](security-authentication.md)
* 【翻译中-20140501】[授权](security-authorization.md)
* 【未校对-20140501】【官方更新中】[密码](security-passwords.md)
* **TBD** [验证客户](security-auth-clients.md)
* **TBD** [最佳实践](security-best-practices.md)


缓存
-------

* 【未校对-20140501】【官方更新中】[概述](caching-overview.md)
* **TBD** [数据缓存](caching-data.md)
* **TBD** [片段和页面缓存](caching-fragment.md)
* **TBD** [HTTP 缓存](caching-http.md)


RESTful 风格的 Web 服务
----------------------

* 【翻译中-20140501】[快速入门](rest-quick-start.md)
* **TBD** [资源](rest-resources.md)
* **TBD** [路由](rest-routing.md)
* **TBD** [格式化数据](rest-data-formatting.md)
* **TBD** [认证](rest-authentication.md)
* **TBD** [速率限制](rest-rate-limiting.md)
* **TBD** [版本控制](rest-versioning.md)
* **TBD** [缓存](rest-caching.md)
* **TBD** [错误处理](rest-error-handling.md)
* **TBD** [测试](rest-testing.md)


开发工具
-----------------

* 【未校对-20140501】【官方更新中】[调试工具栏和调试器](tool-debugger.md)
* 【翻译中-20140501】[使用 Gii 生成代码](tool-gii.md)
* **TBD** [生成 API 文档](tool-api-doc.md)


测试
-------

* [概述](test-overview.md)
* **TBD** [单元测试](test-unit.md)
* **TBD** [功能测试](test-functional.md)
* **TBD** [验收测试](test-acceptance.md)
* [定制器](test-fixtures.md)


扩展 Yii
-------------

* [创建扩展](extend-creating-extensions.md)
* [定制核心代码](extend-customizing-core.md)
* [使用第三方库](extend-using-libs.md)
* **TBD** [在第三方系统使用 Yii](extend-embedding-in-others.md)
* **TBD** [Yii 1.1和2.0共用](extend-using-v1-v2.md)
* [使用包管理器 Composer](extend-using-composer.md)


高级专题
--------------

* [配置 Web 服务器](tutorial-configuring-servers.md)
* [控制台命令](tutorial-console.md)
* [错误处理](tutorial-handling-errors.md)
* [国际化](tutorial-i18n.md)
* [日志](tutorial-logging.md)
* **TBD** [邮件收发](tutorial-mailing.md)
* [性能优化](tutorial-performance-tuning.md)
* [模板引擎](tutorial-template-engines.md)
* [主题](tutorial-theming.md)


小部件
-------

* 网格视图（GridView）：链接到 demo 页
* 列表视图（ListView）：链接到 demo 页
* 详情视图（DetailView）：链接到 demo 页
* 活动表单（ActiveForm）：链接到 demo 页
* Pjax：链接到 demo 页
* 菜单（Menu）：链接到 demo 页
* LinkPager：链接到 demo 页
* LinkSorter：链接到 demo 页
* [Bootstrap 小部件](bootstrap-widgets.md)
* **TBD** [Jquery UI 小部件](jui-widgets.md)



助手类
-------

* [概述](helper-overview.md)
* **TBD** [ArrayHelper](helper-array.md)
* **TBD** [Html](helper-html.md)
* **TBD** [Url](helper-url.md)
* **TBD** [安全助手类](helper-security.md)

