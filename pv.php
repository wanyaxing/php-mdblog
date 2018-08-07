<?php

    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===false)
    {
        echo '4,0,4';
        exit;
    }

    include(__dir__.'/includes/config.php');
    include(MDBLOG_ROOT_PATH.'/includes/lib/Utility.php');

    $fTime = isset($_GET['f_time']) && !empty($_GET['f_time'])?$_GET['f_time']:null;

    if (isset($fTime))
    {
        echo Utility::getSiteView(),',',Utility::getMdViewOfFtime($fTime);
    }
    else
    {
        echo Utility::getSiteView();
    }
