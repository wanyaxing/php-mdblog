<?php

if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])===false || $_SERVER['REQUEST_METHOD'] != 'POST') {
    echo '4,0,4';
    exit;
}

$fTime = isset($_POST['f_time']) && !empty($_POST['f_time'])?$_POST['f_time']:null;

if (isset($fTime)) {
    echo Utility::getSiteView(),',',Utility::getMdViewOfFtime($fTime);
} else {
    echo Utility::getSiteView();
}
