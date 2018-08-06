<?php

    define('MDBLOG_TITLE','php-mdblog');
    define('MDBLOG_HOME_DESCRIPTION','A blog which write with markdown which supported by php.');

    // 列表页，每页数量
    define('MDBLOG_PAGE_SIZE',10);

    //七牛CDN镜像域名，
    //简单的说，该域名下的所有资源自动读取源站资源进行分发，
    //https://developer.qiniu.com/kodo/kb/1376/seven-cattle-image-storage-instruction-manuals
    //注意，镜像空间的设定要直接指向当前博客的所在目录，而不是当前博客的域名。
    //即 blog.example.com 应该指向 http://www.example.com/blog/
    // define('MDBLOG_CDN_HOST','blog.example.com');
    // 镜像域名的 url 组织模板，如有些空间可以在 url 中添加参数来支持对图片进行二次处理，此处用来约定 url 的组织形式
    // %s 为资源文件相对于根目录的路径
    // define('MDBLOG_CDN_FORMAT','http://blog.example.com/%s-default_water');



    // 使用HTTP基本认证保护文件区
    // 通常和上面的镜像域名一起使用，
    // 即：只允许镜像空间访问，不允许其他人访问
    // 如 ./post/20160816091230.20180727170638.php.markdown/demo.gif
    // 可以使用 ./download/admin/123456/20160816091230.20180727170638.php.markdown/demo.gif 下载
    // define('PHP_AUTH_USER','admin');
    // define('PHP_AUTH_PW','123456');

    // 设定缓存文件夹（如不设定则禁用缓存）
    // 请实现创建好该文件夹并确认该文件夹开启了PHP读写权限
    // 请确认该文件夹禁止网站直接访问
    define('MDBLOG_CACHE_DIR',__dir__ . '/cache');

    if (!isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST']=='127.0.0.1' || $_SERVER['HTTP_HOST']=='localhost' || strpos($_SERVER['HTTP_HOST'],'.local')!=false || preg_match('/^\d+\.\d+\.\d+\.\d+$/',$_SERVER['HTTP_HOST']))
    {//1是开发模式  2是正式环境
        define('MDBLOG_DEPLOY_STATUS', 1 );
    }
    else
    {
        define('MDBLOG_DEPLOY_STATUS', 2 );
    }

    // 博客所在根目录
    define('MDBLOG_ROOT_PATH',realpath(__dir__ . '/../'));

    // 当前请求的子路径（不含域名和参数）
    $requestPath = preg_replace ("/(\/*[\?#].*$|[\?#].*$|\/*$|\.\.+)/", '', $_SERVER['REQUEST_URI']);
    $requestPath = preg_replace('/\/+/','/',$requestPath);

    // 博客所在根路径（注：不是网站根目录）
    $mdblogRootUri = MDBLOG_ROOT_PATH;
    while($mdblogRootUri!='')
    {
        if (strpos($requestPath,$mdblogRootUri)===0)
        {
            break;
        }
        $mdblogRootUri = preg_replace('/^\/[^\/]+/','',$mdblogRootUri);
    }
    define('MDBLOG_ROOT_URI',$mdblogRootUri);



    // 博客根路径绝对地址（用于静态化网址）
    define('MDBLOG_ROOT_URL',(strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')  === false ? 'http' : 'https').'://'.$_SERVER['HTTP_HOST'].MDBLOG_ROOT_URI   );

    // 博客所在URL根路径（比如xxx.com/blog)
    if (!defined('MDBLOG_CDN_HOST') || MDBLOG_DEPLOY_STATUS==1)
    {// 相对路径
        define('MDBLOG_CDN_URL','.' );
    }
    else
    {
        define('MDBLOG_CDN_URL',(strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')  === false ? 'http' : 'https').'://'.MDBLOG_CDN_HOST   );
    }

    // 从当前请求子路径中移除博客所在URL路径，则获得当前请求中相对博客的操作 如 /blog/2 得 /2通常是翻页
    $relativePath = substr($requestPath,strlen(MDBLOG_ROOT_URI));

