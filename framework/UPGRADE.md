 Yii 框架 v2 升级操作指南
===========================================
> 中文版翻译日期：20140504

!!!重要!!!

以下升级操作指南是与时俱进的。即，如果你要从版本 A 升级到版本 C ，而版本 B 在 A 和 C 之间，你需要为 A 和 B 遵循以下操作指南。

从 Yii 2.0 Beta 升级
-------------------------

* 如果你曾使用 `clearAll()` 或 `yii\rbac\DbManager`的`clearAllAssignments()`，你应该用`removeAll()` 和 `removeAllAssignments()`分别替换它们。

* 如果你曾创建过 RBAC 规则类，你应添加`$user`作为它们的`execute()` 方法的第一个参数：`execute($user, $item, $params)`。`$user` 参数表示当前正被连接验证的用户 ID ，以前是传递`$params['user']`。

* 如果你用可见性`protected`覆写`yii\grid\DataColumn::getDataCellValue()` ，你必须更改可见性为`public` 作为已改变的父类方法的可见性。
