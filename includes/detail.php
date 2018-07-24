<?php

    include(MDBLOG_ROOT_PATH.'/includes/lib/Parsedown.php');

    $title = 'Not Found';
    $content = '';

    $detailKey = urldecode(preg_replace('/\.html$/','',$requestActions[0]));
    if (is_numeric($detailKey))
    {
        foreach (glob(MDBLOG_ROOT_PATH.'/post/'.$detailKey.'/*.md') as $_file) {
            $mdFile = $_file;
            break;
        }
    }
    else
    {
        $mdFile = MDBLOG_ROOT_PATH.'/post/'.$detailKey.'.md';
    }
    if (!isset($mdFile) || !file_exists($mdFile))
    {
        include __dir__ .  '/404.php';
        exit;
    }


    $content = file_get_contents($mdFile);


    $Parsedown = new Parsedown();

    $html = $Parsedown->text($content); # prints: <p>Hello <em>Parsedown</em>!</p>
    // 转化相对当前文件路径为可访问的URL路径
    $GLOBALS['detailKey'] = $detailKey;
    $html = preg_replace_callback('/(<img src=")(\..*?)(")/',function($matches){
        $imgFilePath = realpath(MDBLOG_ROOT_PATH . '/post/' . $GLOBALS['detailKey'] .'/' . $matches[2]);
        $imgFileRelativePath = str_replace(MDBLOG_ROOT_PATH,'',$imgFilePath);
        $imgFileUrl = MDBLOG_CDN_URL . $imgFileRelativePath;
        return $matches[1] . $imgFileUrl . $matches[3] ;
    },$html);

    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Pragma: no-cache"); // Date in the past

    if (Utility::isAjax())
    {
        echo $html;
        return false;
    }

    $item = Utility::getInfoOfFile($mdFile);

    $title = $item['fTitle'];
?>
<?php include __dir__ . '/header.php'; ?>
<body>
    <?php include __dir__ . '/top.php'; ?>
    <div id="home_body">
        <div id="blog_detail">
            <div class="item_li item_amex item_detail" >
                <div class="item_bg">
                    <div class="item_body">
                        <div class="name"><?= $item['fTitle'] ?></div>
                        <div class="time"><?= $item['fTimeLocal'] ?></div>
                        <!-- <div class="description"><?= $item['description'] ?></div> -->
                        <div class="content"><?= $html ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php include __dir__ . '/side.php'; ?>
    </div>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/default.js?v=0720.5"></script>
<?php include __dir__ . '/footer.php'; ?>
</body>
</html>
