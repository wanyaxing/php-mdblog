<?php
    $page     = 1;
    $size     = MDBLOG_PAGE_SIZE;

    $dirList   = glob(MDBLOG_ROOT_PATH.'/post/*',GLOB_ONLYDIR);

    rsort($dirList);

    $currentList =  array_slice($dirList, ($page-1) * $size,$size);

    $items = array();
    foreach ($currentList as $dir) {
        $items[] = Utility::getInfoOfDir($dir);
    }

    $title = MDBLOG_TITLE;

header('Content-Type:application/xml');
print('<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
    <channel>
        <title><![CDATA['.MDBLOG_TITLE.']]></title>
        <link><![CDATA['.MDBLOG_ROOT_URL.']]></link>
        <description><![CDATA['.MDBLOG_HOME_DESCRIPTION.']]>
        </description>
        <pubDate>'.(count($items)>0?date(DATE_RSS,Utility::strtotime($items[0]['fTime'])):date(DATE_RSS)).'</pubDate>
        <ttl>60</ttl>');

foreach ($items as $item) {
    print("\n".'            <item>');
    printf("\n".'                <%s><![CDATA[%s]]>'."\n".'                </%s>','title',$item['fTitle'],'title');
    printf("\n".'                <%s><![CDATA[%s]]>'."\n".'                </%s>','link',$item['url'],'link');
    printf("\n".'                <%s><![CDATA[%s]]>'."\n".'                </%s>','description',$item['description'],'description');
    printf("\n".'                <%s><![CDATA[%s]]>'."\n".'                </%s>','pubDate',date(DATE_RSS,Utility::strtotime($item['fTime'])),'pubDate');
    print("\n".'            </item>');
}

print('
    </channel>
</rss>');
