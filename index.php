<?php
ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息

error_reporting(-1);                    //打印出所有的 错误信息

date_default_timezone_set('Asia/Shanghai');//设定时区


    include(__dir__.'/includes/config.php');
    include(__dir__.'/includes/lib/Utility.php');

    // 博客所在根目录
    define('MDBLOG_ROOT_PATH',__dir__);
    // 博客所在目录路径（即当从根目录到当前目录路径）
    define('MDBLOG_ROOT_URI',preg_replace ("/\/[^\/]*$/", '', $_SERVER['PHP_SELF']));

    // 博客所在URL根路径（比如xxx.com/blog)
    if (!defined('MDBLOG_CDN_HOST') || MDBLOG_DEPLOY_STATUS==1)
    {// 相对路径
        define('MDBLOG_CDN_URL','.' );
    }
    else
    {
        define('MDBLOG_CDN_URL',(strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')  === false ? 'http' : 'https').'://'.MDBLOG_CDN_HOST.MDBLOG_ROOT_URI   );
    }


    // 当前请求的子路径（不含域名）
    $requestPath = preg_replace ("/(\/*[\?#].*$|[\?#].*$|\/*$|\.\.+)/", '', $_SERVER['REQUEST_URI']);
    // 从当前请求子路径中移除博客所在URL路径，则获得当前请求中相对博客的操作 如 /blog/2 得 /2通常是翻页
    $relativePath = str_replace(MDBLOG_ROOT_URI,'',$requestPath);
    // 将操作分成数组
    $requestActions = explode('/',trim($relativePath,'/'));

    // 对操作数组进行分析
    if ($requestActions[0]=='' || $requestActions[0]=='index.php')
    {
        include MDBLOG_ROOT_PATH.'/includes/list.php';
    }
    else if (preg_match('/^.*\.html$/',$requestActions[0]))
    {
        include MDBLOG_ROOT_PATH.'/includes/detail.php';
    }
    else if ($requestActions[0]=='sitemap.txt')
    {
        include MDBLOG_ROOT_PATH.'/includes/sitemap.txt.php';
    }
    else
    {
        include MDBLOG_ROOT_PATH.'/includes/404.php';
    }

