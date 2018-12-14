<?php
    $dirListInfo = Utility::getDirListInfo(1, MDBLOG_PAGE_SIZE, null);

    $title = MDBLOG_TITLE;

print('<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
    <channel>
        <title><![CDATA['.MDBLOG_TITLE.']]></title>
        <link><![CDATA['.MDBLOG_ROOT_URL . '/'.']]></link>
        <description><![CDATA['.MDBLOG_HOME_DESCRIPTION.']]>
        </description>
        <pubDate>'.(count($dirListInfo['currentList'])>0?date(DATE_RSS, Utility::strtotime($dirListInfo['currentList'][0]['fTimeModified'])):date(DATE_RSS)).'</pubDate>
        <ttl>60</ttl>');

foreach ($dirListInfo['currentList'] as $dirInfo) {
    $mdInfo = Utility::getMdInfoOfDirInfo($dirInfo);
    print("\n".'            <item>');
    printf("\n".'                <%s><![CDATA[%s]]>'."\n".'                </%s>', 'title', $mdInfo['fTitle'], 'title');
    printf("\n".'                <%s><![CDATA[%s]]>'."\n".'                </%s>', 'link', $mdInfo['url'], 'link');
    printf("\n".'                <%s><![CDATA[%s]]>'."\n".'                </%s>', 'description', implode('</p><p>', $mdInfo['descriptions']), 'description');
    printf("\n".'                <%s><![CDATA[%s]]>'."\n".'                </%s>', 'pubDate', date(DATE_RSS, Utility::strtotime($mdInfo['fTimeModified'])), 'pubDate');
    print("\n".'            </item>');
}

print('
    </channel>
</rss>');
