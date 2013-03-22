<?php

require('lib/aal.php');

date_default_timezone_set('America/Chicago');

$feeds = Api\Feed::getAll();
// $feeds = Api\Feed::getByFilter([ 'id' => 9 ]);

foreach ($feeds as $feed) {

	$rss = new Api\RssFeed($feed->url);
	if ($rss && count($rss->items) > 0) {
    
        $feed->name = $rss->title;
        $feed->updated = time();
        $feed->sync();
    
		foreach ($rss->items as $item) {
			$dbItem = Api\Item::getByFilter([ 'guid' => $item->guid ]);
			$dbItem = $dbItem ? $dbItem[0] : new Api\Item();
			$dbItem->title = $item->title;
			$dbItem->content = $item->content;
			$dbItem->date = $item->unixDate;
			$dbItem->guid = $item->guid;
			$dbItem->feedId = $feed->id;
            $dbItem->link = $item->link;
			if ($dbItem->sync()) {
				
			}
		}
	}

}