<?php

namespace Api {

	use stdClass;

	class Feed extends \Lib\Dal {
		
		/**
		 * Database table mapping
		 */
		protected $_dbTable = 'feeds';
		protected $_dbMap = [
			'id' => 'feed_id',
			'url' => 'feed_url',
			'name' => 'feed_name',
			'updated' => 'feed_updated'
		];
		protected $_dbPrimaryKey = 'id';
		
		/**
		 * Feed ID
		 */
		public $id = 0;
		
		/**
		 * Feed URL
		 */
		public $url;
		
		/**
		 * Feed name
		 */
		public $name;
		
		/**
		 * Unix time stamp of when this feed was last checked
		 */
		public $updated;
	
	}

}