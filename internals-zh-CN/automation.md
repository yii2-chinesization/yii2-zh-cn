自动化操作
==========

进行 Yii 相关工作时，有一些任务可以自动完成：

- 生成位于框架根目录下的 classmap `classes.php` 文件。
  运行 `./build/build classmap` 命令就能自动生成。

- 生成类文件中的 `@property` annotations（属性注释），它主要用于描述通过 getters 和 setters 引入的对象属性。
  运行 `./build/build php-doc/property` 更新他们。

- 修正代码风格以及一些在 phpdoc 注释中的其他小问题。运行 `./build/build php-doc/fix` 执行该命令。在你提交它们之前检查一下这些改动，因为这个命令并不完美，这里可能会出现你不想要的修改。你可以用 `git add -p` 检查相关改动。
