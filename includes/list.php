<?php
$size     = MDBLOG_PAGE_SIZE;

$dirListInfo = Utility::getDirListInfo($page, $size, $tag);

if (!is_null($tag)) {
    $title = $tag.' 的搜索结果';
}
?>
<?php if (!MDBLOG_IS_AJAX): ?>
<?php include __dir__ . '/header.php'; ?>
<body>
<?php include __dir__ . '/top.php'; ?>
    <div id="home_body">
    <main id="blog_list">
<?php if (!is_null($tag)): ?>
        <nav class="nav_tips" ><a href="./">首页</a> &gt; <span class="tag"><?= $_GET['tag'] ?></span> </nav>
<?php endif ?>
<?php endif ?>
<?php foreach ($dirListInfo['currentList'] as $dirInfo): ?>
<?php $mdInfo = Utility::getMdInfoOfDirInfo($dirInfo); ?>
<?php Utility::printMdInfo($mdInfo)?>
<?php endforeach ?>
<?php if (count($dirListInfo['currentList'])==0): ?>
        <a class="btn_loadmore" >你发现了一片荒漠，这儿啥都没有。。。</a>
<?php endif ?>
<?php if ($page * $size < $dirListInfo['countTotal']) {
    $params = array_merge($_GET, array('page'=>$page+1));
    if (isset($params['is_ajax'])) {
        unset($params['is_ajax']);
    }
    printf('<a class="btn_loadmore" href="./?%s">点我加载更多</a>', http_build_query($params));
} 
?>
<?php if (!MDBLOG_IS_AJAX): ?>
    </main>
<?php include __dir__ . '/side.php'; ?>
    </div>
<?php include __dir__ . '/footer.php'; ?>
</body>
</html>
<?php endif ?>
