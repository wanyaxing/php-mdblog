<?php

    include(__dir__.'/../includes/config.php');
    include(MDBLOG_ROOT_PATH.'/includes/lib/Utility.php');

    $requestActions = explode('/',trim($relativePath,'/'));
    $requestActions = array_filter($requestActions);

    $downloadDir = array_shift($requestActions);

    if (defined('PHP_AUTH_USER') && defined('PHP_AUTH_PW'))
    {
        $PHP_AUTH_USER = array_shift($requestActions);
        $PHP_AUTH_PW = array_shift($requestActions);
        if (PHP_AUTH_USER != $PHP_AUTH_USER || PHP_AUTH_PW != $PHP_AUTH_PW)
        {
            header('HTTP/1.0 401 Unauthorized');
            echo 'HTTP/1.0 401 Unauthorized';
            exit;
        }
    }


    $firstDir = array_shift($requestActions);

    if ($firstDir!='post' && $firstDir!='css' && $firstDir!='js')
    {
        Utility::exit404('Not Found');
    }

    $filePath = MDBLOG_ROOT_PATH . '/' .$firstDir .'/'. implode('/',$requestActions);

    Utility::download($filePath);

