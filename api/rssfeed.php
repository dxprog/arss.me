<?php

namespace Api {

	class RssFeed {
		
		/**
		 * Feed title
		 */
		public $title;
		
		/**
		 * Feed description
		 */
		public $description;
		
		/**
		 * Feed link
		 */
		public $link;
		
		/**
		 * 
		
		/**
		 * Published date
		 */
		public $pubDate;
		
		/**
		 * List of items
		 */
		public $items;
		
		/**
		 * Constructor
		 * @param $p mixed If a url is provided, loads the RSS feed. If an RssFeed object, creates a copy
		 */
		public function __construct($p = null) {
		
			if (is_string($p)) {
				$this->loadRssFeed($p);
			}
		
		}
		
		/**
		 * Loads an RSS feed
		 */
		public function loadRssFeed($url) {
			
			$xml = simplexml_load_file($url, null, LIBXML_NOCDATA);
			if ($xml) {
			
				$this->title = (string) $xml->channel->title;
				$this->description = (string) $xml->channel->description;
				$this->link = (string) $xml->channel->link;
				$this->pubDate = (string) $xml->channel->pubDate;
				$this->items = [];
				
				foreach ($xml->channel->item as $item) {
					$this->items[] = new RssItem($item);
				}
			
			}
			
		}
	
	}
	
}