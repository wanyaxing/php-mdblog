<?php

    include_once(MDBLOG_ROOT_PATH.'/includes/lib/Parsedown.php');

    $mdInfo = Utility::getMdInfoOfFtime($fTime);

    if (is_null($mdInfo))
    {
        Utility::exit404();
    }

    $html   = Utility::getHtmlOfMdInfo($mdInfo);

    $html .= sprintf('<p class="auth_info" title="转载注明来源即可">原文来自%s：<a href="%s">%s</a></p>',MDBLOG_TITLE,$mdInfo['url'],$mdInfo['url']);

    if (MDBLOG_IS_AJAX)
    {
        echo $html;
        return false;
    }

    $title       = $mdInfo['fTitle'];
    $description = implode(' ',$mdInfo['descriptions']);
?>
<?php include __dir__ . '/header.php'; ?>
<body>
    <?php include __dir__ . '/top.php'; ?>
    <div id="home_body">
        <main id="blog_detail">
            <?php Utility::printMdInfo($mdInfo,$html)?>
        </main>
        <?php include __dir__ . '/side.php'; ?>
    </div>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/lib/highlight/highlight.min.js"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/default.min.js?v=0724.4"></script>
<?php include __dir__ . '/footer.php'; ?>
</body>
</html>
