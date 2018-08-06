<?php
class Utility{

    /**
     * 目标值是否数字（或数字组成的字符串）
     * @param  int|string  $_v [description]
     * @return boolean      [description]
     */
    public static function is_int($_v)
    {
        return is_int($_v) || (strval(intval($_v))===strval($_v));
    }

    /**
     * 将时间转化成时间戳
     * @param  [string | int | DateTime] $p_time [description]
     * @return [int]         时间戳
     */
    public static function strtotime($p_time=null)
    {
        if ($p_time===null || $p_time=='time()')
        {
            return time();
        }
        else
        {
            $time = null;
            if (static::is_int($p_time) && strlen($p_time)==13 )
            {
                $p_time = substr($p_time,0,10);
            }
            $p_time = preg_replace_callback('/^(\d{4})[^\d]*?(\d\d)[^\d]*?(\d\d)[^\d]*?(\d\d)[^\d]*?(\d\d)[^\d]*?(\d\d)$/',function($matches){
                if (
                       $matches[1]>=1970 && $matches[1]<=2999
                    && $matches[2]>=1 && $matches[2]<=12
                    && $matches[3]>=1 && $matches[3]<=31
                    && $matches[4]>=0 && $matches[4]<=24
                    && $matches[5]>=0 && $matches[5]<=60
                    && $matches[6]>=0 && $matches[6]<=60
                )
                {
                    return $matches[1] . '-' . $matches[2] . '-' . $matches[3]  . ' ' . $matches[4] . ':' . $matches[5]. ':' . $matches[6] ;
                }
                return $matches[0];
            },$p_time);
            $p_time = preg_replace_callback('/^(\d{4})[^\d]*?(\d\d)[^\d]*?(\d\d)$/',function($matches){
                if (
                       $matches[1]>=1970 && $matches[1]<=2999
                    && $matches[2]>=1 && $matches[2]<=12
                    && $matches[3]>=1 && $matches[3]<=31
                )
                {
                    return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                }
                return $matches[0];
            },$p_time);
            $strtotime = strtotime($p_time);
            if (static::is_int($p_time) )
            {
                if ($p_time>=19700101 && $p_time<=20991231 && strtotime(date('Y-m-d H:i:s',$strtotime)) == $strtotime)
                {//1970 - 2099
                    $time = $strtotime;
                }
                else
                {
                    $time = intval($p_time);
                }
                // if (strtotime(date('Y-m-d H:i:s',$p_time))==$p_time)
                // {
                // }
            }
            else if (strtotime(date('Y-m-d H:i:s',$strtotime)) == $strtotime )
            {
                $time = $strtotime;
            }
            else if (is_subclass_of($p_time,'DateTime'))
            {
                $time = $p_time->getTimestamp();
            }
            else
            {
                return null;
            }
            return $time;
        }
    }

    /**
     * 将时间转化成字符串
     * @param  [string | int | DateTime] $p_time [description]
     * @param  string $p_format [description]
     * @return [string]           时间字符串
     */
    public static function timetostr($p_time=null,$p_format='Y-m-d H:i:s')
    {
        return date($p_format,static::strtotime($p_time));
    }

    public static function getDescriptions($text,$ignoreStrings=array())
    {
        if (isset($ignoreStrings) && !is_array($ignoreStrings))
        {
            $ignoreStrings = array($ignoreStrings);
        }
        $text = str_replace(array("\r\n", "\r"), "\n", $text);
        $text = trim($text, "\n");
        $lines = explode("\n", $text);
        $description = [];
        foreach ($lines as $line) {
            if ($line != '')
            {
                $isIgnore = false;
                foreach ($ignoreStrings as $ignoreStr) {
                    if (!empty($line) && !empty($ignoreStr) && strpos($line,$ignoreStr)!==false)
                    {
                        $isIgnore = true;
                        break;
                    }
                }
                if (!$isIgnore)
                {
                    $description[] = preg_replace('/\[(.*?)\]\((.*?)\)/','$1',$line);
                    if (count($description)>=3)
                    {
                        break;
                    }
                }
            }
        }
        return $description;
    }

    // 遍历获取所有文章基础信息
    public static function getDirListInfo($page=1,$size=10,$tag=null)
    {
        if (isset($tag))
        {
            if (preg_match('/[\.\/\\\?#\-\:\s\*]/',$tag))
            {
                Utility::exit404();
            }
            $dirList   = array_merge(
                glob(MDBLOG_ROOT_PATH.'/post/*.'.$tag,GLOB_ONLYDIR)
                ,glob(MDBLOG_ROOT_PATH.'/post/*.'.$tag.'.*',GLOB_ONLYDIR)
            );
        }
        else
        {
            $dirList   = glob(MDBLOG_ROOT_PATH.'/post/*',GLOB_ONLYDIR);
        }

        $dirList = array_unique($dirList);

        $dirInfoList = array();
        $timeNow = time();
        foreach ($dirList as $dir) {
            $dirInfo = Utility::getDirInfoOfPath($dir);
            if ($dirInfo['fTimeCreated']<=$timeNow)
            {//只有创建时间在当前时间的才算，未来的还没到时候。
                $dirInfoList[] = $dirInfo;
            }
        }

        usort($dirInfoList,function($a,$b){
            return $a['fTimeModified']<=$b['fTimeModified']?1:-1;
        });

        $currentList =  array_slice($dirInfoList, ($page-1) * $size,$size);

        return array(
                    'currentList'=>$currentList,
                    'countTotal'=>count($dirList),
                ) ;
    }


    // 获取指定路径的基础信息
    public static function getDirInfoOfPath($path)
    {
        if (file_exists($path))
        {
            $dir = preg_replace('/^(.+)\/(.*?)\.md$/','$1',$path);
        }
        else
        {
            $dir = $path;
        }
        $dirName = preg_replace('/^.*[\/\\\\](.*?)$/','$1',$dir);
        $dirInfo = explode('.',$dirName);
        $fTime = null;
        $fTimeCreated = null;
        $fTimeModified = null;
        $fTags = array();
        $fTagsLocal = array();
        foreach ($dirInfo as $value) {
            $time = static::strtotime($value);
            if ($time)
            {
                if (is_null($fTime))
                {
                    $fTimeCreated   = $time;
                    $fTime          = $value;
                }
                else
                {
                    // 智能判断创建时间和修改时间
                    if ($fTimeCreated>$time)
                    {
                        $fTimeModified = $fTimeCreated;
                        $fTimeCreated  = $time;
                        $fTime         = $value;
                    }
                    else
                    {
                        $fTimeModified = $time;
                    }
                }
                continue;
            }
            if (!empty($value))
            {
                $fTags[] = $value;
            }
        }
        return array(
                    'dir'         => $dir,
                    'dirName'       => $dirName,
                    'fTags'         => $fTags,
                    'fTime'         => $fTime,
                    'fTimeCreated'  => $fTimeCreated,
                    'fTimeModified' => $fTimeModified?$fTimeModified:$fTimeCreated,
                );
    }

    public static function getMdInfoOfFtime($fTime)
    {
        if (Utility::strtotime($fTime))
        {
            foreach (glob(MDBLOG_ROOT_PATH.'/post/*'.$fTime.'*',GLOB_ONLYDIR) as $_dir) {
                $mdDir = $_dir;
                break;
            }
        }

        if (isset($mdDir))
        {
            $dirInfo = Utility::getDirInfoOfPath($mdDir);
            if ($dirInfo['fTime'] == $fTime)
            {
                return Utility::getMdInfoOfDirInfo($dirInfo);
            }
        }

        return null;

    }

    public static function getMtimeOfFtime($fTime)
    {
        if (Utility::strtotime($fTime))
        {
            foreach (glob(MDBLOG_ROOT_PATH.'/post/*'.$fTime.'*/*.md') as $_file) {
                return filemtime($_file);
            }
        }

        return 0;

    }

    public static function getMtimeOfPost()
    {
        $mtime = 0;
        foreach (glob(MDBLOG_ROOT_PATH.'/post/*/*.md') as $_file) {
            $_mtime = filemtime($_file);
            if ($mtime < $_mtime)
            {
                $mtime = $_mtime;
            }
        }
        return $mtime;
    }

    // 根据基础信息获取文章信息
    public static function getMdInfoOfDirInfo($dirInfo)
    {
        $mdFile = null;
        foreach (glob($dirInfo['dir'].'/*.md') as $_file) {
            $mdFile = $_file;
            break;
        }
        if (isset($mdFile))
        {
            $fTitle              = preg_replace('/^(.+)\/(.*?)\.md$/','$2',$mdFile);
            $dirInfo['fTitle']      = $fTitle;
            $dirInfo['mdFile']      = $mdFile;
            $fTagsLocal = array();
            foreach ($dirInfo['fTags'] as $tag) {
                $fTagsLocal[] = sprintf('<a href="./?tag=%s">%s</a>',urlencode($tag),$tag);
            }
            $dirInfo['fTagsLocal']  = implode('',$fTagsLocal);
            $dirInfo['link']        = './' . urlencode($dirInfo['fTime']) . '.html';
            $dirInfo['url']         = MDBLOG_ROOT_URL . '/' . urlencode($dirInfo['fTime']) . '.html';
            $dirInfo['descriptions'] = Utility::getDescriptions(file_get_contents($mdFile),$fTitle);
            return $dirInfo;
        }
        return null;
    }

    public static function getHtmlOfMdInfo($mdInfo)
    {
        $content = file_get_contents($mdInfo['mdFile']);
        $Parsedown = new Parsedown();
        $html = $Parsedown->text($content); # prints: <p>Hello <em>Parsedown</em>!</p>
        // 转化相对当前文件路径为可访问的URL路径
        $GLOBALS['dirName'] = $mdInfo['dirName'];
        $html = preg_replace_callback('/(<img src=")(\..*?)(")/',function($matches){
            $imgFilePath = realpath(MDBLOG_ROOT_PATH . '/post/' . $GLOBALS['dirName'] .'/' . $matches[2]);
            $imgFileRelativePath = str_replace(MDBLOG_ROOT_PATH,'',$imgFilePath);
            if (defined('MDBLOG_CDN_FORMAT'))
            {
                $imgFileUrl = sprintf(MDBLOG_CDN_FORMAT,$imgFileRelativePath);
            }
            else
            {
                $imgFileUrl = MDBLOG_CDN_URL . $imgFileRelativePath;
            }
            return $matches[1] . $imgFileUrl . $matches[3] ;
        },$html);

        return $html;
    }

    public static function exit404($message=null)
    {
        include MDBLOG_ROOT_PATH .  '/includes/404.php';
        exit;
    }

    public static function printMdInfo($mdInfo,$html='')
    {
?>
            <article class="item_li" >
                <div class="item_bg" id="item_<?= md5($mdInfo['link'])  ?>">
                    <div class="item_body" >
                        <h1><a class="name" href="<?= $mdInfo['link'] ?>"><?= $mdInfo['fTitle'] ?></a></h1>
                        <?php if (empty($html)): ?>
                        <div class="description"><?= '<p>'.implode('</p><p>',$mdInfo['descriptions']).'</p>' ?></div>
                        <?php endif ?>
                        <div class="content markdown-body"><?= $html ?></div>
                        <div class="item_footer">
                            <div class="tags">
                                <?php foreach ($mdInfo['fTags'] as $tag): ?>
                                    <a href="./?tag=<?= urlencode($tag) ?>"><?= $tag ?></a>
                                <?php endforeach ?>
                            </div>
                            <div class="time">
                                <time class="time_created" pubdate="<?= date(DATE_ATOM,$mdInfo['fTimeCreated']) ?>">发表于：<?= Utility::timetostr($mdInfo['fTimeCreated']) ?></time>
                                <?php if ($mdInfo['fTimeModified'] != $mdInfo['fTimeCreated']): ?>
                                    <time class="time_modified" datetime="<?= date(DATE_ATOM,$mdInfo['fTimeModified']) ?>">编辑于：<?= Utility::timetostr($mdInfo['fTimeModified']) ?></time>
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="btn_close">X</div>
                    </div>
                </div>
            </article>
<?php

    }


    /*当前请求是否ajax请求*/
    public static function isAjax()
    {
        $headers = getallheaders();
        return isset($headers['X-Requested-With']) && $headers['X-Requested-With']=='XMLHttpRequest';
    }

    public static function download($filePath)
    {
        if (!file_exists($filePath))
        {
            header( 'HTTP/1.1 404 Not Found' );
            exit;
        }

        $filename =  preg_replace('/^(.+)\/(.*?)$/','$2',$filePath);

        $ext = preg_replace('/(.*)\.([^\.]+)$/','$2',$filename);

        switch ($ext) {
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: application/x-javascript');
                break;
            case 'md':
                header('Content-Type: text/markdown');
                break;
            case 'jpg':
            case 'png':
            case 'gif':
                header('Content-Type: image/'.$ext);
                break;
            default:
                header('Content-Type: application/octet-stream');
                break;
        }


        set_time_limit(300);  // 避免下载超时
        ob_end_clean();  // 避免大文件导致超过 memory_limit 限制
        readfile($filePath);
    }

}
