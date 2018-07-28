<?php
    $page     = isset($_GET['page']) && Utility::is_int($_GET['page'])?$_GET['page']:1;
    $size     = MDBLOG_PAGE_SIZE;

    $dirListInfo = Utility::getDirListInfo($page,$size,isset($_GET['tag'])?$_GET['tag']:null);

    if (isset($_GET['tag']))
    {
        $title = $_GET['tag'].' 的搜索结果';
    }
?>
<?php if (!Utility::isAjax()): ?>
<?php include __dir__ . '/header.php'; ?>
<body>
    <?php include __dir__ . '/top.php'; ?>
    <div id="home_body">
    <main id="blog_list">
        <?php if (isset($_GET['tag'])): ?>
            <nav class="info_tips" ><a href="./">首页</a> &gt; <span class="tag"><?= $_GET['tag'] ?></span> </nav>
        <?php endif ?>
<?php endif ?>
        <?php foreach ($dirListInfo['currentList'] as $dirInfo): ?>
            <?php $mdInfo = Utility::getMdInfoOfDirInfo($dirInfo); ?>
            <?php Utility::printMdInfo($mdInfo)?>
        <?php endforeach ?>
        <?php if (count($dirListInfo['currentList'])==0): ?>
            <a class="btn_loadmore" >你发现了一片荒漠，这儿啥都没有。。。</a>
        <?php endif ?>
        <?php if ($page * $size < $dirListInfo['countTotal']): ?>
            <a class="btn_loadmore" href="<?= './?'.http_build_query(array_merge($_GET,array('page'=>$page+1))) ?>">点我加载更多</a>
        <?php endif ?>
<?php if (!Utility::isAjax()): ?>
    </main>
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
