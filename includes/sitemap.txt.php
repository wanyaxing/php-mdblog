<?php
    $dirList   = glob(__dir__.'/../post/*',GLOB_ONLYDIR);

    $rootUrl   = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')  === false ? 'http' : 'https').'://'. $_SERVER['HTTP_HOST'] . preg_replace('/(^.*?\/)sitemap.txt.*/','$1',$_SERVER['REQUEST_URI']);

    header("Content-Type: text/plain");
    echo $rootUrl;
    echo "\n";
    foreach ($dirList as $dir) {
        $dirInfo = Utility::getDirInfoOfName($dir);
        echo $rootUrl;
        echo urlencode($dirInfo['fTime']);
        echo ".html\n";
    }
