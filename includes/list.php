<?php
    $page     = is_numeric($requestActions[0])?$requestActions[0]:1;
    $size     = MDBLOG_PAGE_SIZE;

    $dirList   = array_reverse(glob(MDBLOG_ROOT_PATH.'/post/*',GLOB_ONLYDIR));

    $currentList =  array_slice($dirList, ($page-1) * $size,$size);

    $items = array();
    foreach ($currentList as $dir) {
        $items[] = Utility::getInfoOfDir($dir);
    }

    $title = MDBLOG_TITLE;
?>
<?php if (!Utility::isAjax()): ?>
<?php include __dir__ . '/header.php'; ?>
<body>
    <?php include __dir__ . '/top.php'; ?>
    <div id="home_body">
    <div id="blog_list">
<?php endif ?>
        <?php foreach ($items as $item): ?>
            <div class="item_li item_amex">
                <div class="item_bg" id="item_<?= md5($item['link'])  ?>">
                    <div class="item_body" >
                        <a class="name" href="<?= $item['link'] ?>"><?= $item['fTitle'] ?></a>
                        <div class="time"><?= $item['fTimeLocal'] ?></div>
                        <div class="description"><?= $item['description'] ?></div>
                        <div class="content markdown-body"></div>
                        <div class="btn_close">X</div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
        <?php if ($page * $size < count($dirList)): ?>
            <a class="btn_loadmore" href="<?= './'.($page+1) ?>">加载更多</a>
        <?php endif ?>
<?php if (!Utility::isAjax()): ?>
    </div>
    <?php include __dir__ . '/side.php'; ?>
    </div>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/lib/animejs/2.2.0/anime.min.js"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/lib/highlight/highlight.min.js"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/default.js?v=0724.1"></script>
<script type="text/javascript" src="<?=MDBLOG_CDN_URL?>/js/list.js?v=0724.5"></script>
<?php include __dir__ . '/footer.php'; ?>
</body>
</html>
<?php endif ?>
