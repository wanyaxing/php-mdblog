<?php

    include(MDBLOG_ROOT_PATH.'/includes/lib/Parsedown.php');

    $title = 'Not Found';
    $content = '';

    $fTime = urldecode(preg_replace('/\.html$/','',$requestActions[0]));

    $mdInfo = Utility::getMdInfoOfFtime($fTime);

    if (is_null($mdInfo))
    {
        Utility::exit404();
    }

    $html   = Utility::getHtmlOfMdInfo($mdInfo);

    $html .= sprintf('<p class="auth_info" title="转载注明来源即可">原文来自%s：<a href="%s">%s</a></p>',MDBLOG_TITLE,$mdInfo['url'],$mdInfo['url']);

    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Pragma: no-cache"); // Date in the past

    if (Utility::isAjax())
    {
        echo $html;
        return false;
    }

    $title = $mdInfo['fTitle'];
?>
<?php include __dir__ . '/header.php'; ?>
<body>
    <?php include __dir__ . '/top.php'; ?>
    <div id="home_body">
        <article id="blog_detail">
            <?php Utility::printMdInfo($mdInfo,$html)?>
        </article>
        <?php include __dir__ . '/side.php'; ?>
    </div>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/lib/highlight/highlight.min.js"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/default.js?v=0724.2"></script>
<?php include __dir__ . '/footer.php'; ?>
</body>
</html>
