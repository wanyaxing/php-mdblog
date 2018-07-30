<?php
ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息

error_reporting(-1);                    //打印出所有的 错误信息

date_default_timezone_set('Asia/Shanghai');//设定时区


    include(__dir__.'/includes/config.php');
    include(__dir__.'/includes/lib/Utility.php');

    // 博客所在根目录
    define('MDBLOG_ROOT_PATH',__dir__);

    // 当前请求的子路径（不含域名和参数）
    $requestPath = preg_replace ("/(\/*[\?#].*$|[\?#].*$|\/*$|\.\.+)/", '', $_SERVER['REQUEST_URI']);
    $requestPath = preg_replace('/\/+/','/',$requestPath);

    // 博客所在根路径（注：不是网站根目录）
    $mdblogRootUri = preg_replace ("/\/[^\/]*$/", '', $_SERVER['PHP_SELF']);
    while($mdblogRootUri!='')
    {
        if (strpos($requestPath,$mdblogRootUri)===0)
        {
            break;
        }
        $mdblogRootUri = preg_replace('/^\/[^\/]+/','',$mdblogRootUri);
    }
    define('MDBLOG_ROOT_URI',$mdblogRootUri);



    // 博客根路径绝对地址（用于静态化网址）
    define('MDBLOG_ROOT_URL',(strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')  === false ? 'http' : 'https').'://'.$_SERVER['HTTP_HOST'].MDBLOG_ROOT_URI   );

    // 博客所在URL根路径（比如xxx.com/blog)
    if (!defined('MDBLOG_CDN_HOST') || MDBLOG_DEPLOY_STATUS==1)
    {// 相对路径
        define('MDBLOG_CDN_URL','.' );
    }
    else
    {
        define('MDBLOG_CDN_URL',(strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')  === false ? 'http' : 'https').'://'.MDBLOG_CDN_HOST.MDBLOG_ROOT_URI   );
    }


    // 从当前请求子路径中移除博客所在URL路径，则获得当前请求中相对博客的操作 如 /blog/2 得 /2通常是翻页
    $relativePath = substr($requestPath,strlen(MDBLOG_ROOT_URI));
    // 将操作分成数组
    $requestActions = explode('/',trim($relativePath,'/'));

    // 对操作数组进行分析
    switch ($requestActions[0]) {
        case '':
        case 'index.php':
            $includeFile = MDBLOG_ROOT_PATH.'/includes/list.php';
            break;
        case 'sitemap.txt':
            $includeFile = MDBLOG_ROOT_PATH.'/includes/sitemap.txt.php';
            break;
        case 'feed.xml':
            $includeFile = MDBLOG_ROOT_PATH.'/includes/feed.xml.php';
            break;
        default:
            $fTime = urldecode(preg_replace('/\.html$/','',$requestActions[0]));
            $includeFile = MDBLOG_ROOT_PATH.'/includes/detail.php';
            break;
    }


    define('MDBLOG_IS_AJAX',Utility::isAjax());

    if (!defined('MDBLOG_CACHE_DIR'))
    {
        include $includeFile;
    }
    else
    {
        $cacheKey = 'cache' . preg_replace('/[\.\/\?#-]/','_',$_SERVER['REQUEST_URI']);

        if (isset($fTime))
        {
            $filemtime = Utility::getMtimeOfFtime($fTime);
        }
        else
        {
            $filemtime = Utility::getMtimeOfPost();
        }

        $cacheFile = MDBLOG_CACHE_DIR . '/' . $cacheKey ;

        if (file_exists($cacheFile) && filemtime($cacheFile) > $filemtime )
        {
            $main_content = file_get_contents($cacheFile);
        }
        else
        {
            ob_start();
            try {
                include $includeFile;
                $main_content = ob_get_clean();
            } catch (Exception $e) {
                $main_content = $e->getMessage();
            }
            file_put_contents($cacheFile, $main_content);
        }

        $cachemtime = filemtime($cacheFile);

        $etag = $cachemtime;
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag)//这里用Last-Modified的header标识来进行客户端的缓存控制。
        {
            header('Etag:'.$etag,true,304);
            exit;
        }
        $eLastModified = gmdate('D, d M Y H:i:s GMT', $cachemtime);
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $eLastModified)//这里用etag的header标识来进行客户端的缓存控制。
        {
            header('Last-Modified:'.$eLastModified,true,304);
            exit;
        }

        header('Cache-Control:public');
        header('Last-Modified:'.$eLastModified);
        header('Keep-Alive:timeout=5, max=5');//设定过期时间，禁止只读缓存
        // header('Expires:'.gmdate('D, d M Y H:i:s \G\M\T', $_time+3600));
        header('Expires:'.gmdate('D, d M Y H:i:s GMT', $cachemtime+5));
        header('Etag:'.$etag);

        echo $main_content;
    }

