<?php

    include(__dir__.'/../includes/config.php');
    include(MDBLOG_ROOT_PATH.'/includes/lib/Utility.php');

    $isAuthed = false;

    $filePath = MDBLOG_ROOT_PATH . urldecode($relativePath);

    if (defined('MDBLOG_CDN_MINSIZE') && filesize($filePath) <= MDBLOG_CDN_MINSIZE)
    {
        $isAuthed = true;
    }

    if (!$isAuthed && defined('PHP_AUTH_USER') && defined('PHP_AUTH_PW') && (!isset($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER']!=PHP_AUTH_USER || $_SERVER['PHP_AUTH_PW']!=PHP_AUTH_PW) )
    {
        $isAuthed = false;
    }

    if (!$isAuthed)
    {
        header('WWW-Authenticate: Basic realm="文件区数据需要用户认证后才可查看。"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'HTTP/1.0 401 Unauthorized';
        exit;
    }

    Utility::download($filePath);


