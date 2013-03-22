<?php

namespace Api {
	
	use SimpleXMLElement;
	
	class RssItem {
	
		/**
		 * Item title
		 */
		public $title;
		
		/**
		 * Item description
		 */
		public $description;
		
		/**
		 * Item content (where available)
		 */
		public $content;
		
		/**
		 * Item link
		 */
		public $link;
		
		/**
		 * Item guid
		 */
		public $guid;
		
		/**
		 * Published date
		 */
		public $pubDate;
		
		/** 
		 * A unix time representation of the pubdate
		 */
		public $unixDate;
		
		/**
		 * Constructor
		 * @param $item mixed Creates an item from another item (copy) or an SimpleXMLElement object
		 */
		public function __construct($item = null) {
			
			if ($item instanceof SimpleXMLElement) {
				$this->loadFromXml($item);
			} else if ($item instanceof RssItem) {
			
			}
			
		}
		
		/**
		 * Creates an item object from a SimpleXMLElement object
		 */
		public function loadFromXml($xml) {
		
			$retVal = false;
			
			if ($xml instanceof SimpleXMLElement) {
			
				$this->title = (string) $xml->title;
				$this->description = (string) $xml->description;
				$this->link = (string) $xml->link;
				$this->guid = (string) $xml->guid;
				$this->pubDate = (string) $xml->pubDate;
				$this->unixDate = strtotime($this->pubDate);
				
				$content = $xml->children('http://purl.org/rss/1.0/modules/content/');
				if ($content) {
					$this->content = (string) $content->encoded;
				} else {
					$this->content = $this->description;
				}
				
				$retVal = true;
			
			}
			
			return $retVal;
		
		}
	
	}

}