<?php
    echo MDBLOG_ROOT_URL , '/' , "\n";

    $dirListInfo = Utility::getDirListInfo(1,9999,null);

    foreach ($dirListInfo['currentList'] as $dirInfo) {
        echo MDBLOG_ROOT_URL, '/', urlencode($dirInfo['fTime']), ".html\n";
    }
