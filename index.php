<?php
ini_set('display_errors', 1);            //错误信息
ini_set('display_startup_errors', 1);    //php启动错误信息
error_reporting(-1);                    //打印出所有的 错误信息
date_default_timezone_set('Asia/Shanghai');//设定时区

include(__dir__.'/includes/config.php');
include(MDBLOG_ROOT_PATH.'/includes/lib/Utility.php');

// 将操作分成数组
$requestActions = explode('/', trim($relativePath, '/'));

// 对操作数组进行分析
switch ($requestActions[0]) {
    case '':
    case 'index.php':
        $page     = isset($_GET['page']) && Utility::is_int($_GET['page'])?$_GET['page']:1;
        $tag      = isset($_GET['tag']) ? $_GET['tag']:null;
        $includeFile = MDBLOG_ROOT_PATH.'/includes/list.php';
        break;
    case 'sitemap.txt':
        header("Content-Type: text/plain");
        $includeFile = MDBLOG_ROOT_PATH.'/includes/sitemap.txt.php';
        break;
    case 'feed.xml':
        header('Content-Type:application/xml');
        $includeFile = MDBLOG_ROOT_PATH.'/includes/feed.xml.php';
        break;
    case 'pv':
        // pv 直接操作，并结束进程。
        include MDBLOG_ROOT_PATH.'/includes/pv.php';
        exit;
        break;
    default:
        $fTime = urldecode(preg_replace('/\.html$/', '', $requestActions[0]));
        $includeFile = MDBLOG_ROOT_PATH.'/includes/detail.php';
        break;
}


define('MDBLOG_IS_AJAX', isset($_GET['is_ajax']));


if (!defined('MDBLOG_CACHE_DIR')) {
    // 如果支持压缩，则压缩输出。
    if (extension_loaded('zlib') && strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start('ob_gzhandler');
    }
    include $includeFile;
} else {
    $cacheKey = MDBLOG_IS_AJAX . '_';

    if (isset($fTime)) {
        $cacheKey .= $fTime;
    } else {
        if (isset($page)) {
            $cacheKey .= $requestActions[0] . '_' .$tag . '_' .$page;
        } else {
            $cacheKey .= $requestActions[0];
        }
        $filemtime = Utility::getMtimeOfPost();
    }

    $cacheKey = preg_replace('/[\.\/\\\?#\-\:\s\*]/', '_', $cacheKey);

    $cacheFile = MDBLOG_CACHE_DIR . '/' . $cacheKey . '.cache' ;

    // 默认是需要重载的
    $isNeedReload = true;

    if (file_exists($cacheFile)) {
        // 有缓存意味着可以使用缓存
        if (isset($fTime)) {
            $filemtime = Utility::getMtimeOfFtime($fTime);
            // 如果缓存时间比文件时间新，就不需要重载
            if (filemtime($cacheFile) > $filemtime) {
                $isNeedReload = false;
            }
        } else {
            // 如果有更新的文件，则需要重载。
            $isNeedReload = Utility::isLastPostNewer(filemtime($cacheFile));
        }

        // // 使用缓存时，启用缓存锁功能，可用于防止缓存并发，小型站点可以不用开启，几乎用不到。
        // // 就算重载也不是人人都能重载，只允许第一个发现需要重载的人去重载。
        // // 因为会有缓存锁的IO 操作，所以如果没有并发情况，那么就不用开启这个功能，所以注释掉。
        // if ($isNeedReload)
        // {
        //     $cacheLockFile = MDBLOG_CACHE_DIR . '/' . $cacheKey . '_lock.cache' ;
        //     if (file_exists($cacheLockFile))
        //     {
        //         $isNeedReload = false;
        //         //如果存在缓存锁，则意味着有其他用户正在生成缓存，当前用户仍使用旧缓存，防止并发。
        //         // 如果缓存锁存在超过30秒，则删掉该缓存锁，让下一个用户重新生成缓存。
        //         if (time() - filemtime($cacheFile) > 30)
        //         {
        //             unlink($cacheLockFile);
        //         }
        //     }
        //     else
        //     {
        //         file_put_contents($cacheLockFile, time());
        //     }
        // }
    }

    if ($isNeedReload) {
        ob_start();
        try {
            include $includeFile;
            $main_content = ob_get_clean();
        } catch (Exception $e) {
            $main_content = $e->getMessage();
        }
        file_put_contents($cacheFile, $main_content);
        //重新生成缓存后，移除缓存锁
        // if (isset($cacheLockFile) && file_exists($cacheLockFile))
        // {
        //     unlink($cacheLockFile);
        // }
    }

    $cachemtime = filemtime($cacheFile);

    // 根据缓存文件的修改时间作为 key 值，判断是否可以范围304状态
    $etag = $cachemtime;
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {//这里用Last-Modified的header标识来进行客户端的缓存控制。
        header('Etag:'.$etag, true, 304);
        exit;
    }
    $eLastModified = gmdate('D, d M Y H:i:s GMT', $cachemtime);
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $eLastModified) {//这里用etag的header标识来进行客户端的缓存控制。
        header('Last-Modified:'.$eLastModified, true, 304);
        exit;
    }

    // 输出200状态的数据，并设定头信息。
    header('Cache-Control:no-cache');
    header('Last-Modified:'.$eLastModified);
    header('Keep-Alive:timeout=5, max=5');//设定过期时间，禁止只读缓存
    // header('Expires:'.gmdate('D, d M Y H:i:s \G\M\T', $cachemtime+3600));
    // header('Expires:'.gmdate('D, d M Y H:i:s \G\M\T', $cachemtime+5));
    header('Etag:'.$etag);

    // 如果支持压缩，则压缩输出。
    if (extension_loaded('zlib') && strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start('ob_gzhandler');
    }
    // 如果缓存文件可用，直接输出缓存文件。
    if (!isset($main_content)) {
        include $cacheFile;
    } else {
        echo $main_content;
    }
}
