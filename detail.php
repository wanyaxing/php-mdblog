<?php

    include(MDBLOG_ROOT_PATH.'/'.'lib/Parsedown.php');

    $title = 'Not Found';
    $content = '';

    $detailKey = urldecode(preg_replace('/\.html$/','',$requestActions[0]));
    if (is_numeric($detailKey))
    {
        foreach (glob(__dir__.'/post/'.$detailKey.'/*.md') as $_file) {
            $mdFile = $_file;
            break;
        }
    }
    else
    {
        $mdFile = __dir__.'/post/'.$detailKey.'.md';
    }
    if (!isset($mdFile) || !file_exists($mdFile))
    {
        include __dir__.'/404.php';
        exit;
    }


    $content = file_get_contents($mdFile);


    $Parsedown = new Parsedown();

    $html = $Parsedown->text($content); # prints: <p>Hello <em>Parsedown</em>!</p>
    if (defined('MDBLOG_CDN_HOST'))
    {
        // 转化相对文件路径为绝对的CND路径
        $GLOBALS['detailKey'] = $detailKey;
        $html = preg_replace_callback('/(<img src=")(\..*?)(")/',function($matches){
            $imgFilePath = realpath(__dir__ . '/post/' . $GLOBALS['detailKey'] .'/' . $matches[2]);
            $imgFileRelativePath = str_replace(MDBLOG_ROOT_PATH,'',$imgFilePath);
            $imgFileUrl = MDBLOG_CDN_URL . $imgFileRelativePath;
            return $matches[1] . $imgFileUrl . $matches[3] ;
        },$html);
    }

    if (Utility::isAjax())
    {
        echo $html;
        return false;
    }

    $item = Utility::getInfoOfFile($mdFile);

    $title = $item['fTitle'];
?>
<?php include './header.php'; ?>
<body>
    <?php include './top.php'; ?>
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
        <?php include './side.php'; ?>
    </div>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/lib//jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/default.js?v=0720.5"></script>
<?php include './footer.php'; ?>
</body>
</html>
