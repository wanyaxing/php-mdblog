<?php

    define('MDBLOG_TITLE','php-mdblog');
    define('MDBLOG_HOME_DESCRIPTION','A blog which write with markdown which supported by php.');

    // 列表页，每页数量
    define('MDBLOG_PAGE_SIZE',10);

    //七牛CDN镜像域名，
    //简单的说，该域名下的所有资源自动读取源站资源进行分发，
    //https://developer.qiniu.com/kodo/kb/1376/seven-cattle-image-storage-instruction-manuals
    // define('MDBLOG_CDN_HOST','blog.example.com');


    if (!isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST']=='127.0.0.1' || $_SERVER['HTTP_HOST']=='localhost' || strpos($_SERVER['HTTP_HOST'],'.local')!=false || preg_match('/^\d+\.\d+\.\d+\.\d+$/',$_SERVER['HTTP_HOST']))
    {//1是开发模式  2是正式环境
        define('MDBLOG_DEPLOY_STATUS', 1 );
    }
    else
    {
        define('MDBLOG_DEPLOY_STATUS', 2 );
    }

