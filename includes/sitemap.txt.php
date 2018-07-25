<?php
    header("Content-Type: text/plain");
    echo MDBLOG_ROOT_URL;
    echo "\n";

    $dirList   = glob(__dir__.'/../post/*',GLOB_ONLYDIR);
    rsort($dirList);
    foreach ($dirList as $dir) {
        $dirInfo = Utility::getDirInfoOfName($dir);
        echo MDBLOG_ROOT_URL;
        echo urlencode($dirInfo['fTime']);
        echo ".html\n";
    }
