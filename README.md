# php-mdblog
A blog which write with markdown which supported by php.

[demo](http://appjk.com/blog)

## 运行环境

* 默认使用 `Apache+PHP` 的运行环境，使用 `.htaccess` 进行网址转换。
* 如果使用 `Nginx` 或其他环境，需要重写网址到 `index.php` 进行处理。

## 如何安装

* 将整个项目下载并放置到网站所在文件夹中即可。
    * 如：将`php-mdblog`放置到 `webroot/` 目录下，其中 `webroot` 为网站根目录，则访问 `xxx.com/php-mdblog` 即可。

## 如何使用

* 在 `./post/` 目录下使用时间戳或时间格式的文本创建一个文件夹，如 `20160816091230`
* 在文件夹中创建 `标题.m`d 的文件，如 `hello world.md`
* 在这个 `.md` 文件中编写文章，建议使用 Markdown 语法，也可用原生 HTML 语法。
* 使用 FTP 工具上传到网站服务器中，
* 即可在 `xxx.com/php-mdblog` 中看到新出现的文章啦。
