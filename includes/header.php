<!DOCTYPE html>
<html lang="zh-CN">
<head>
<title><?= isset($title)?$title.' - '.MDBLOG_TITLE:MDBLOG_TITLE ?></title>
<meta charset="utf-8">
<meta itemprop="description" content="<?= htmlspecialchars(isset($description)?$description:MDBLOG_HOME_DESCRIPTION) ?>">
<meta name="viewport" content="width=480,user-scalable=no, viewport-fit=cover">
<meta name="apple-touch-fullscreen" content="yes">
<meta http-equiv="Cache-Control" content="no-siteapp">
<meta name="format-detection" content="telephone=no">
<meta name="format-detection" content="email=no">
<meta http-equiv="X-UA-COMPATIBLE" content="IE=edge,chrome=1">
<link rel="alternate" type="application/rss+xml" title="<?= MDBLOG_TITLE ?>" href="./feed.xml" />
<link rel="stylesheet" type="text/css" href="<?=MDBLOG_CDN_URL?>/css/github-markdown.css">
<link rel="stylesheet" href="<?=MDBLOG_CDN_URL?>/js/lib/highlight/github.min.css">
<link rel="stylesheet" href="<?=MDBLOG_CDN_URL?>/css/default.min.css?v=0730.1">
</head>
