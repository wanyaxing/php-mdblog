<?php
    $page     = isset($_GET['page']) && Utility::is_int($_GET['page'])?$_GET['page']:1;
    $size     = MDBLOG_PAGE_SIZE;

    $tag      = isset($_GET['tag'])?$_GET['tag']:'';
    if (isset($_GET['tag']))
    {
        if (preg_match('/[* .\/\\\?]/',$_GET['tag']))
        {
            include __dir__ .  '/404.php';
            exit;
        }
        $dirList   = array_merge(
                        glob(MDBLOG_ROOT_PATH.'/post/*.'.$_GET['tag'],GLOB_ONLYDIR)
                        ,glob(MDBLOG_ROOT_PATH.'/post/*.'.$_GET['tag'].'.*',GLOB_ONLYDIR)
                    );
    }
    else
    {
        $dirList   = glob(MDBLOG_ROOT_PATH.'/post/*',GLOB_ONLYDIR);
    }
    $dirList = array_unique($dirList);
    rsort($dirList);

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
        <?php if (isset($_GET['tag'])): ?>
            <div class="info_tips" ><a href="./">首页</a> &gt; <span class="tag"><?= $_GET['tag'] ?></span> </div>
        <?php endif ?>
<?php endif ?>
        <?php foreach ($items as $mdInfo): ?>
            <div class="item_li item_amex">
                <div class="item_bg" id="item_<?= md5($mdInfo['link'])  ?>">
                    <div class="item_body" >
                        <a class="name" href="<?= $mdInfo['link'] ?>"><?= $mdInfo['fTitle'] ?></a>
                        <div class="description"><?= $mdInfo['description'] ?></div>
                        <div class="content markdown-body"></div>
                        <div class="item_footer">
                            <div class="tags"><?= $mdInfo['fTagsLocal'] ?></div>
                            <div class="time"><?= $mdInfo['fTimeLocal'] ?></div>
                        </div>
                        <div class="btn_close">X</div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
        <?php if (count($items)==0): ?>
            <a class="btn_loadmore" >你发现了一片荒漠，这儿啥都没有。。。</a>
        <?php endif ?>
        <?php if ($page * $size < count($dirList)): ?>
            <a class="btn_loadmore" href="<?= './?'.http_build_query(array_merge($_GET,array('page'=>$page+1))) ?>">加载更多</a>
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
