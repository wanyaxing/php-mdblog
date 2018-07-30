<?php

    define('MDBLOG_TITLE','php-mdblog');
    define('MDBLOG_HOME_DESCRIPTION','A blog which write with markdown which supported by php.');

    // 列表页，每页数量
    define('MDBLOG_PAGE_SIZE',10);

    //七牛CDN镜像域名，
    //简单的说，该域名下的所有资源自动读取源站资源进行分发，
    //https://developer.qiniu.com/kodo/kb/1376/seven-cattle-image-storage-instruction-manuals
    // define('MDBLOG_CDN_HOST','blog.example.com');

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

