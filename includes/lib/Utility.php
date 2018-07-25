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
                if (preg_match('/(\d{4})(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)//'))
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

    public static function getDescription($text,$ignoreStrings=array())
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
                    if (strpos($line,$ignoreStr)!==false)
                    {
                        $isIgnore = true;
                        break;
                    }
                }
                if (!$isIgnore)
                {
                    $description[] = preg_replace('/\[(.*?)\]\((.*?)\)/','$1',$line)."\n";
                    if (count($description)>=3)
                    {
                        break;
                    }
                }
            }
        }
        return '<p>'.implode('</p><p>',$description).'</p>';
    }

    public static function getInfoOfDir($dir)
    {
        $mdFile = null;
        foreach (glob($dir.'/*.md') as $_file) {
            $mdFile = $_file;
            break;
        }
        return Utility::getInfoOfFile($mdFile);
    }
    public static function getInfoOfFile($file)
    {
        if (file_exists($file))
        {
            $dirName = preg_replace('/^.*[\/\\\\](.*?)$/','$1',pathinfo($file,PATHINFO_DIRNAME));
            $dirInfo = explode('.',$dirName);
            $fTime = null;
            $fTags = array();
            $fTagsLocal = array();
            foreach ($dirInfo as $value) {
                if (is_null($fTime) && static::strtotime($value))
                {
                    $fTime = $value;
                }
                else if (!empty($value))
                {
                    $fTags[] = $value;
                    $fTagsLocal[] = sprintf('<a href="./?tag=%s">%s</a>',urlencode($value),$value);
                }
            }
            $fTitle              = pathinfo($file,PATHINFO_FILENAME);
            $item['fTitle']      = $fTitle;
            $item['fTags']       = $fTags;
            $item['fTagsLocal']  = implode('',$fTagsLocal);
            $item['fTime']       = $fTime;
            $item['dirName']     = $dirName;
            $item['fTimeLocal']  = static::timetostr($fTime);
            $item['link']        = './' . urlencode($fTime) . '.html';
            $item['description'] = Utility::getDescription(file_get_contents($file),$fTitle);
            return $item;
        }
        return null;
    }

    /*当前请求是否ajax请求*/
    public static function isAjax()
    {
        $headers = getallheaders();
        return isset($headers['X-Requested-With']) && $headers['X-Requested-With']=='XMLHttpRequest';
    }

}
