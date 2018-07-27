<?php
    header("Content-Type: text/plain");
    echo MDBLOG_ROOT_URL;
    echo "\n";

    $dirListInfo = Utility::getDirListInfo(1,9999,null);

    foreach ($dirListInfo['currentList'] as $dirInfo) {
        echo MDBLOG_ROOT_URL;
        echo '/';
        echo urlencode($dirInfo['fTime']);
        echo ".html\n";
    }
