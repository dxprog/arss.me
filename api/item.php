<?php

namespace Api {

	use stdClass;
	
	class Item extends \Lib\Dal {
	
		/**
		 * Database mapping
		 */
		protected $_dbTable = 'items';
		protected $_dbMap = [
			'id' => 'item_id',
			'feedId' => 'feed_id',
			'image' => 'item_image',
			'title' => 'item_title',
            'link' => 'item_link',
			'content' => 'item_content',
			'date' => 'item_date',
			'guid' => 'item_guid'
		];
		protected $_dbPrimaryKey = 'id';
	
		/**
		 * Item ID
		 */
		public $id = 0;
	
		/**
		 * ID of feed that the item belongs to
		 */
		public $feedId;
		
		/**
		 * Image associated with the item
		 */
		public $image;
		
		/**
		 * Item title
		 */
		public $title;
		
        /**
         * Link to full item content
         */
        public $link;
        
		/**
		 * Body content
		 */
		public $content;
		
		/**
		 * Unix time stamp of item date
		 */
		public $date;
		
		/**
		 * Unique identifier
		 */
		public $guid;
		
		/**
		 * Constructor
		 */
		public function __construct($p = null) {
			
			if ($p instanceof stdClass) {
				$this->copyFromDbRow($p);
			}
			
		}
	
	}

}