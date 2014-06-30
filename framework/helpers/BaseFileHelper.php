<?php
/**
 * 英文文档日期：20140513
 * 未翻译完成
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\helpers;

use Yii;
use yii\base\InvalidParamException;

/**
 * BaseFileHelper 为[[FileHelper]]提供具体实现
 *
 * 不要使用 BaseFileHelper ，而是使用[[FileHelper]]替代
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Alex Makarov <sam@rmcreative.ru>
 * @since 2.0
 */
class BaseFileHelper
{
    const PATTERN_NODIR = 1;
    const PATTERN_ENDSWITH = 4;
    const PATTERN_MUSTBEDIR = 8;
    const PATTERN_NEGATIVE = 16;

    /**
     * 标准化文件或目录路径
     * 标准化做了以下工作：
     *
     * - 将所有目录分隔符转为`DIRECTORY_SEPARATOR` (如"\a/b\c" becomes "/a/b/c")
     * - 移除末尾的目录分隔符(如"/a/b/c/" becomes "/a/b/c")
     * - 把多个连续斜杠减少为一个斜杠(如 "/a///b/c" becomes "/a/b/c")
     * - 基于它们的含义移除".."和"."(如"/a/./b/../c" becomes "/a/c")
     *
     * @param string $path 要标准化的文件/目录路径
     * @param string $ds 用于标准化结果的目录分隔符，缺省为`DIRECTORY_SEPARATOR`.
     * @return string 标准化后的文件/目录路径
     */
    public static function normalizePath($path, $ds = DIRECTORY_SEPARATOR)
    {
        $path = rtrim(strtr($path, ['/' => $ds, '\\' => $ds]), $ds);
        if (strpos($ds . $path, "{$ds}.") === false && strpos($path, "{$ds}{$ds}") === false) {
            return $path;
        }
        // the path may contain ".", ".." or double slashes, need to clean them up
        $parts = [];
        foreach (explode($ds, $path) as $part) {
            if ($part === '..' && !empty($parts) && end($parts) !== '..') {
                array_pop($parts);
            } elseif ($part === '.' || $part === '' && !empty($parts)) {
                continue;
            } else {
                $parts[] = $part;
            }
        }
        return implode($ds, $parts);
    }

    /**
     * 返回指定文件的本地化版本
     *
     * 搜索基于指定的语言编码。尤其是，同名文件将在以语言编码为名的子目录下查找。
     * 如，给定文件"path/to/view.php"和语言编码"zh-CN"，将查找本地化文件"path/to/zh-CN/view.php"。
     * 如果此文件不存在，将尝试回退到"zh"去查找，如"path/to/zh/view.php"。如果也没找到就返回原始文件。
     * 如果目标语言和源码语言相同，将返回原始文件。
     *
     * @param string $file 原始文件
     * @param string $language 文件要本地化的目标语言
     * 如果未设置，将使用[[\yii\base\Application::language]]的值。
     * @param string $sourceLanguage 原始文件的语言
     * 如果未设置，将使用[[\yii\base\Application::sourceLanguage]]的值。
     * @return string 匹配的本地化文件，如果本地化版本未找到就返回原始文件。
     * 如果目标语言和源码语言相同就返回原始文件。
     */
    public static function localize($file, $language = null, $sourceLanguage = null)
    {
        if ($language === null) {
            $language = Yii::$app->language;
        }
        if ($sourceLanguage === null) {
            $sourceLanguage = Yii::$app->sourceLanguage;
        }
        if ($language === $sourceLanguage) {
            return $file;
        }
        $desiredFile = dirname($file) . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . basename($file);
        if (is_file($desiredFile)) {
            return $desiredFile;
        } else {
            $language = substr($language, 0, 2);
            if ($language === $sourceLanguage) {
                return $file;
            }
            $desiredFile = dirname($file) . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . basename($file);

            return is_file($desiredFile) ? $desiredFile : $file;
        }
    }

    /**
     * 确定指定文件的 MIME 类型
     * 本方法首先将尝试基于[finfo_open](http://php.net/manual/en/function.finfo-open.php)
     * 确定 MIME 类型。如果不起作用，就退而使用[[getMimeTypeByExtension()]]。
     * @param string $file 文件名
     * @param string $magicFile 可选数据库魔术文件名，通常是`/path/to/magic.mime`。
     * 这将作为第二个参数传递到[finfo_open](http://php.net/manual/en/function.finfo-open.php).
     * @param boolean $checkExtension 是否使用文件扩展名来确定 MIME 类型以防`finfo_open()`不能确定
     * @return string MIME 类型(如`text/plain`)，如果 MIME 类型无法确定返回 null
     */
    public static function getMimeType($file, $magicFile = null, $checkExtension = true)
    {
        if (function_exists('finfo_open')) {
            $info = finfo_open(FILEINFO_MIME_TYPE, $magicFile);
            if ($info) {
                $result = finfo_file($info, $file);
                finfo_close($info);
                if ($result !== false) {
                    return $result;
                }
            }
        }

        return $checkExtension ? static::getMimeTypeByExtension($file) : null;
    }

    /**
     * Determines the MIME type based on the extension name of the specified file.
     * This method will use a local map between extension names and MIME types.
     * @param string $file the file name.
     * @param string $magicFile the path of the file that contains all available MIME type information.
     * If this is not set, the default file aliased by `@yii/util/mimeTypes.php` will be used.
     * @return string the MIME type. Null is returned if the MIME type cannot be determined.
     */
    public static function getMimeTypeByExtension($file, $magicFile = null)
    {
        static $mimeTypes = [];
        if ($magicFile === null) {
            $magicFile = __DIR__ . '/mimeTypes.php';
        }
        if (!isset($mimeTypes[$magicFile])) {
            $mimeTypes[$magicFile] = require($magicFile);
        }
        if (($ext = pathinfo($file, PATHINFO_EXTENSION)) !== '') {
            $ext = strtolower($ext);
            if (isset($mimeTypes[$magicFile][$ext])) {
                return $mimeTypes[$magicFile][$ext];
            }
        }

        return null;
    }

    /**
     * Copies a whole directory as another one.
     * The files and sub-directories will also be copied over.
     * @param string $src the source directory
     * @param string $dst the destination directory
     * @param array $options options for directory copy. Valid options are:
     *
     * - dirMode: integer, the permission to be set for newly copied directories. Defaults to 0775.
     * - fileMode:  integer, the permission to be set for newly copied files. Defaults to the current environment setting.
     * - filter: callback, a PHP callback that is called for each directory or file.
     *   The signature of the callback should be: `function ($path)`, where `$path` refers the full path to be filtered.
     *   The callback can return one of the following values:
     *
     *   * true: the directory or file will be copied (the "only" and "except" options will be ignored)
     *   * false: the directory or file will NOT be copied (the "only" and "except" options will be ignored)
     *   * null: the "only" and "except" options will determine whether the directory or file should be copied
     *
     * - only: array, list of patterns that the file paths should match if they want to be copied.
     *   A path matches a pattern if it contains the pattern string at its end.
     *   For example, '.php' matches all file paths ending with '.php'.
     *   Note, the '/' characters in a pattern matches both '/' and '\' in the paths.
     *   If a file path matches a pattern in both "only" and "except", it will NOT be copied.
     * - except: array, list of patterns that the files or directories should match if they want to be excluded from being copied.
     *   A path matches a pattern if it contains the pattern string at its end.
     *   Patterns ending with '/' apply to directory paths only, and patterns not ending with '/'
     *   apply to file paths only. For example, '/a/b' matches all file paths ending with '/a/b';
     *   and '.svn/' matches directory paths ending with '.svn'. Note, the '/' characters in a pattern matches
     *   both '/' and '\' in the paths.
     * - recursive: boolean, whether the files under the subdirectories should also be copied. Defaults to true.
     * - beforeCopy: callback, a PHP callback that is called before copying each sub-directory or file.
     *   If the callback returns false, the copy operation for the sub-directory or file will be cancelled.
     *   The signature of the callback should be: `function ($from, $to)`, where `$from` is the sub-directory or
     *   file to be copied from, while `$to` is the copy target.
     * - afterCopy: callback, a PHP callback that is called after each sub-directory or file is successfully copied.
     *   The signature of the callback should be: `function ($from, $to)`, where `$from` is the sub-directory or
     *   file copied from, while `$to` is the copy target.
     * @throws \yii\base\InvalidParamException if unable to open directory
     */
    public static function copyDirectory($src, $dst, $options = [])
    {
        if (!is_dir($dst)) {
            static::createDirectory($dst, isset($options['dirMode']) ? $options['dirMode'] : 0775, true);
        }

        $handle = opendir($src);
        if ($handle === false) {
            throw new InvalidParamException('Unable to open directory: ' . $src);
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $from = $src . DIRECTORY_SEPARATOR . $file;
            $to = $dst . DIRECTORY_SEPARATOR . $file;
            if (static::filterPath($from, $options)) {
                if (isset($options['beforeCopy']) && !call_user_func($options['beforeCopy'], $from, $to)) {
                    continue;
                }
                if (is_file($from)) {
                    copy($from, $to);
                    if (isset($options['fileMode'])) {
                        @chmod($to, $options['fileMode']);
                    }
                } else {
                    static::copyDirectory($from, $to, $options);
                }
                if (isset($options['afterCopy'])) {
                    call_user_func($options['afterCopy'], $from, $to);
                }
            }
        }
        closedir($handle);
    }

    /**
     * Removes a directory (and all its content) recursively.
     * @param string $dir the directory to be deleted recursively.
     */
    public static function removeDirectory($dir)
    {
        if (!is_dir($dir) || !($handle = opendir($dir))) {
            return;
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                unlink($path);
            } else {
                static::removeDirectory($path);
            }
        }
        closedir($handle);
        rmdir($dir);
    }

    /**
     * Returns the files found under the specified directory and subdirectories.
     * @param string $dir the directory under which the files will be looked for.
     * @param array $options options for file searching. Valid options are:
     *
     * - filter: callback, a PHP callback that is called for each directory or file.
     *   The signature of the callback should be: `function ($path)`, where `$path` refers the full path to be filtered.
     *   The callback can return one of the following values:
     *
     *   * true: the directory or file will be returned (the "only" and "except" options will be ignored)
     *   * false: the directory or file will NOT be returned (the "only" and "except" options will be ignored)
     *   * null: the "only" and "except" options will determine whether the directory or file should be returned
     *
     * - except: array, list of patterns excluding from the results matching file or directory paths.
     *   Patterns ending with '/' apply to directory paths only, and patterns not ending with '/'
     *   apply to file paths only. For example, '/a/b' matches all file paths ending with '/a/b';
     *   and '.svn/' matches directory paths ending with '.svn'.
     *   If the pattern does not contain a slash /, it is treated as a shell glob pattern and checked for a match against the pathname relative to $dir.
     *   Otherwise, the pattern is treated as a shell glob suitable for consumption by fnmatch(3) with the FNM_PATHNAME flag: wildcards in the pattern will not match a / in the pathname.
     *   For example, "views/*.php" matches "views/index.php" but not "views/controller/index.php".
     *   A leading slash matches the beginning of the pathname. For example, "/*.php" matches "index.php" but not "views/start/index.php".
     *   An optional prefix "!" which negates the pattern; any matching file excluded by a previous pattern will become included again.
     *   If a negated pattern matches, this will override lower precedence patterns sources. Put a backslash ("\") in front of the first "!"
     *   for patterns that begin with a literal "!", for example, "\!important!.txt".
     *   Note, the '/' characters in a pattern matches both '/' and '\' in the paths.
     * - only: array, list of patterns that the file paths should match if they are to be returned. Directory paths are not checked against them.
     *   Same pattern matching rules as in the "except" option are used.
     *   If a file path matches a pattern in both "only" and "except", it will NOT be returned.
     * - recursive: boolean, whether the files under the subdirectories should also be looked for. Defaults to true.
     * @return array files found under the directory. The file list is sorted.
     * @throws InvalidParamException if the dir is invalid.
     */
    public static function findFiles($dir, $options = [])
    {
        if (!is_dir($dir)) {
            throw new InvalidParamException('The dir argument must be a directory.');
        }
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        if (!isset($options['basePath'])) {
            $options['basePath'] = realpath($dir);
            // this should also be done only once
            if (isset($options['except'])) {
                foreach ($options['except'] as $key => $value) {
                    if (is_string($value)) {
                        $options['except'][$key] = self::parseExcludePattern($value);
                    }
                }
            }
            if (isset($options['only'])) {
                foreach ($options['only'] as $key => $value) {
                    if (is_string($value)) {
                        $options['only'][$key] = self::parseExcludePattern($value);
                    }
                }
            }
        }
        $list = [];
        $handle = opendir($dir);
        if ($handle === false) {
            throw new InvalidParamException('Unable to open directory: ' . $dir);
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (static::filterPath($path, $options)) {
                if (is_file($path)) {
                    $list[] = $path;
                } elseif (!isset($options['recursive']) || $options['recursive']) {
                    $list = array_merge($list, static::findFiles($path, $options));
                }
            }
        }
        closedir($handle);

        return $list;
    }

    /**
     * Checks if the given file path satisfies the filtering options.
     * @param string $path the path of the file or directory to be checked
     * @param array $options the filtering options. See [[findFiles()]] for explanations of
     * the supported options.
     * @return boolean whether the file or directory satisfies the filtering options.
     */
    public static function filterPath($path, $options)
    {
        if (isset($options['filter'])) {
            $result = call_user_func($options['filter'], $path);
            if (is_bool($result)) {
                return $result;
            }
        }

        if (empty($options['except']) && empty($options['only'])) {
            return true;
        }

        $path = str_replace('\\', '/', $path);

        if (!empty($options['except'])) {
            if (($except = self::lastExcludeMatchingFromList($options['basePath'], $path, $options['except'])) !== null) {
                return $except['flags'] & self::PATTERN_NEGATIVE;
            }
        }

        if (!is_dir($path) && !empty($options['only'])) {
            if (($except = self::lastExcludeMatchingFromList($options['basePath'], $path, $options['only'])) !== null) {
                // don't check PATTERN_NEGATIVE since those entries are not prefixed with !
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Creates a new directory.
     *
     * This method is similar to the PHP `mkdir()` function except that
     * it uses `chmod()` to set the permission of the created directory
     * in order to avoid the impact of the `umask` setting.
     *
     * @param string $path path of the directory to be created.
     * @param integer $mode the permission to be set for the created directory.
     * @param boolean $recursive whether to create parent directories if they do not exist.
     * @return boolean whether the directory is created successfully
     */
    public static function createDirectory($path, $mode = 0775, $recursive = true)
    {
        if (is_dir($path)) {
            return true;
        }
        $parentDir = dirname($path);
        if ($recursive && !is_dir($parentDir)) {
            static::createDirectory($parentDir, $mode, true);
        }
        $result = mkdir($path, $mode);
        chmod($path, $mode);

        return $result;
    }

    /**
     * Performs a simple comparison of file or directory names.
     *
     * Based on match_basename() from dir.c of git 1.8.5.3 sources.
     *
     * @param string $baseName file or directory name to compare with the pattern
     * @param string $pattern the pattern that $baseName will be compared against
     * @param integer|boolean $firstWildcard location of first wildcard character in the $pattern
     * @param integer $flags pattern flags
     * @return boolean wheter the name matches against pattern
     */
    private static function matchBasename($baseName, $pattern, $firstWildcard, $flags)
    {
        if ($firstWildcard === false) {
            if ($pattern === $baseName) {
                return true;
            }
        } elseif ($flags & self::PATTERN_ENDSWITH) {
            /* "*literal" matching against "fooliteral" */
            $n = StringHelper::byteLength($pattern);
            if (StringHelper::byteSubstr($pattern, 1, $n) === StringHelper::byteSubstr($baseName, -$n, $n)) {
                return true;
            }
        }

        return fnmatch($pattern, $baseName, 0);
    }

    /**
     * Compares a path part against a pattern with optional wildcards.
     *
     * Based on match_pathname() from dir.c of git 1.8.5.3 sources.
     *
     * @param string $path full path to compare
     * @param string $basePath base of path that will not be compared
     * @param string $pattern the pattern that path part will be compared against
     * @param integer|boolean $firstWildcard location of first wildcard character in the $pattern
     * @param integer $flags pattern flags
     * @return boolean wheter the path part matches against pattern
     */
    private static function matchPathname($path, $basePath, $pattern, $firstWildcard, $flags)
    {
        // match with FNM_PATHNAME; the pattern has base implicitly in front of it.
        if (isset($pattern[0]) && $pattern[0] == '/') {
            $pattern = StringHelper::byteSubstr($pattern, 1, StringHelper::byteLength($pattern));
            if ($firstWildcard !== false && $firstWildcard !== 0) {
                $firstWildcard--;
            }
        }

        $namelen = StringHelper::byteLength($path) - (empty($basePath) ? 0 : StringHelper::byteLength($basePath) + 1);
        $name = StringHelper::byteSubstr($path, -$namelen, $namelen);

        if ($firstWildcard !== 0) {
            if ($firstWildcard === false) {
                $firstWildcard = StringHelper::byteLength($pattern);
            }
            // if the non-wildcard part is longer than the remaining pathname, surely it cannot match.
            if ($firstWildcard > $namelen) {
                return false;
            }

            if (strncmp($pattern, $name, $firstWildcard)) {
                return false;
            }
            $pattern = StringHelper::byteSubstr($pattern, $firstWildcard, StringHelper::byteLength($pattern));
            $name = StringHelper::byteSubstr($name, $firstWildcard, $namelen);

            // If the whole pattern did not have a wildcard, then our prefix match is all we need; we do not need to call fnmatch at all.
            if (empty($pattern) && empty($name)) {
                return true;
            }
        }

        return fnmatch($pattern, $name, FNM_PATHNAME);
    }

    /**
     * Scan the given exclude list in reverse to see whether pathname
     * should be ignored.  The first match (i.e. the last on the list), if
     * any, determines the fate.  Returns the element which
     * matched, or null for undecided.
     *
     * Based on last_exclude_matching_from_list() from dir.c of git 1.8.5.3 sources.
     *
     * @param string $basePath
     * @param string $path
     * @param array $excludes list of patterns to match $path against
     * @return string null or one of $excludes item as an array with keys: 'pattern', 'flags'
     * @throws InvalidParamException if any of the exclude patterns is not a string or an array with keys: pattern, flags, firstWildcard.
     */
    private static function lastExcludeMatchingFromList($basePath, $path, $excludes)
    {
        foreach (array_reverse($excludes) as $exclude) {
            if (is_string($exclude)) {
                $exclude = self::parseExcludePattern($exclude);
            }
            if (!isset($exclude['pattern']) || !isset($exclude['flags']) || !isset($exclude['firstWildcard'])) {
                throw new InvalidParamException('If exclude/include pattern is an array it must contain the pattern, flags and firstWildcard keys.');
            }
            if ($exclude['flags'] & self::PATTERN_MUSTBEDIR && !is_dir($path)) {
                continue;
            }

            if ($exclude['flags'] & self::PATTERN_NODIR) {
                if (self::matchBasename(basename($path), $exclude['pattern'], $exclude['firstWildcard'], $exclude['flags'])) {
                    return $exclude;
                }
                continue;
            }

            if (self::matchPathname($path, $basePath, $exclude['pattern'], $exclude['firstWildcard'], $exclude['flags'])) {
                return $exclude;
            }
        }

        return null;
    }

    /**
     * Processes the pattern, stripping special characters like / and ! from the beginning and settings flags instead.
     * @param string $pattern
     * @return array with keys: (string) pattern, (int) flags, (int|boolean)firstWildcard
     * @throws InvalidParamException if the pattern is not a string.
     */
    private static function parseExcludePattern($pattern)
    {
        if (!is_string($pattern)) {
            throw new InvalidParamException('Exclude/include pattern must be a string.');
        }
        $result = [
            'pattern' => $pattern,
            'flags' => 0,
            'firstWildcard' => false,
        ];
        if (!isset($pattern[0])) {
            return $result;
        }

        if ($pattern[0] == '!') {
            $result['flags'] |= self::PATTERN_NEGATIVE;
            $pattern = StringHelper::byteSubstr($pattern, 1, StringHelper::byteLength($pattern));
        }
        $len = StringHelper::byteLength($pattern);
        if ($len && StringHelper::byteSubstr($pattern, -1, 1) == '/') {
            $pattern = StringHelper::byteSubstr($pattern, 0, -1);
            $len--;
            $result['flags'] |= self::PATTERN_MUSTBEDIR;
        }
        if (strpos($pattern, '/') === false) {
            $result['flags'] |= self::PATTERN_NODIR;
        }
        $result['firstWildcard'] = self::firstWildcardInPattern($pattern);
        if ($pattern[0] == '*' && self::firstWildcardInPattern(StringHelper::byteSubstr($pattern, 1, StringHelper::byteLength($pattern))) === false) {
            $result['flags'] |= self::PATTERN_ENDSWITH;
        }
        $result['pattern'] = $pattern;

        return $result;
    }

    /**
     * Searches for the first wildcard character in the pattern.
     * @param string $pattern the pattern to search in
     * @return integer|boolean position of first wildcard character or false if not found
     */
    private static function firstWildcardInPattern($pattern)
    {
        $wildcards = ['*', '?', '[', '\\'];
        $wildcardSearch = function ($r, $c) use ($pattern) {
            $p = strpos($pattern, $c);

            return $r===false ? $p : ($p===false ? $r : min($r, $p));
        };

        return array_reduce($wildcards, $wildcardSearch, false);
    }
}
