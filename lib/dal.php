<?php

namespace Lib {
	
    use stdClass;
    
	class Dal {
		
        /**
         * Constructor
         */
        public function __construct($obj = null) {
        
            if (is_numeric($obj)) {
                $this->_getById($obj);
            } else if ($obj instanceof stdClass) {
                $this->copyFromDbRow($obj);
            }
        
        }
        
		/**
		 * Syncs the current object to the database
		 */
		public function sync() {
			
			$retVal = 0;
			
			if (property_exists($this, '_dbTable') && property_exists($this, '_dbMap')) {
				
				$dbParams = array();
				
				// Determine if a primary key was set
				$primaryKey = property_exists($this, '_dbPrimaryKey') ? $this->_dbPrimaryKey : false;
				$primaryKeyValue = 0;
				if ($primaryKey) {
					$primaryKeyValue = $this->$primaryKey;
				}
				
				// If the primary key value is non-zero, do an UPDATE
				$method = $primaryKeyValue !== 0 ? 'UPDATE' : 'INSERT';
				$parameters = array();
				
				foreach ($this->_dbMap as $property => $column) {
					// Primary only gets dropped in for UPDATEs
					if (($primaryKey === $property && 'UPDATE' === $method) || $primaryKey !== $property) {
						$paramName = ':' . $property;
						
						// Serialize objects going in as JSON
						$value = $this->$property;
						if (is_object($value)) {
							$value = json_encode($value);
						}
						$params[$paramName] = $value;
						
						if ('INSERT' === $method) {
							$parameters[] = $paramName;
						} else if ($primaryKey != $property) {
							$parameters[] = '`' . $column . '` = ' . $paramName;
						}
					}
				}
				
				// Build and execute the query
				$query = $method;
				if ('INSERT' === $method) {
					$query .= ' INTO `' . $this->_dbTable . '` (`' . implode('`,`', $this->_dbMap) . '`) VALUES (' . implode(',', $parameters) . ')';
					$query = str_replace('`' . $this->_dbMap[$primaryKey] . '`,', '', $query);
				} else {
					$query .= ' `' . $this->_dbTable . '` SET ' . implode(',', $parameters) . ' WHERE `' . $this->_dbMap[$primaryKey] . '` = :' . $primaryKey;
				}
				
				$retVal = Db::Query($query, $params);
				
				// Save the ID for insert
				if ($retVal > 0 && 'INSERT' === $method) {
					$this->$primaryKey = $retVal;
				}
				
			}
			
			return $retVal > 0;
		
		}
		
		/**
		 * Creates an object from the passed database row
		 */
		public function copyFromDbRow($obj) {
			if (property_exists($this, '_dbMap') && is_object($obj)) {
				foreach($this->_dbMap as $property => $column) {
					if (property_exists($obj, $column) && property_exists($this, $property)) {
						$this->$property = $obj->$column;
					}
				}
			}
		}
		
		/**
		 * Static version of _getAll
		 */
		public static function getAll() {
			$class = get_called_class();
			$temp = new $class();
			return $temp->_getAll();
		}
		
		/**
		 * Static version of _getAll
		 */
		public static function getByFilter($p1, $value = null) {
			$class = get_called_class();
			$temp = new $class();
			return $temp->_getByFilter($p1, $value);
		}
		
		/**
		 * Returns all records in the table for the object type
		 */
		protected function _getAll() {
			$retVal = null;
			
			if (property_exists($this, '_dbTable') && property_exists($this, '_dbMap')) {
				$columns = array_values($this->_dbMap);
				$query = 'SELECT ' . implode(',', $columns) . ' FROM `' . $this->_dbTable . '`';
				$result = Db::Query($query);
				if ($result && $result->count > 0) {
					$retVal = [];
					$class = get_class($this);
					while ($row = Db::Fetch($result)) {
						$retVal[] = new $class($row);
					}
				}
			}
			return $retVal;
		}
		
        /**
         * Get's a record by primary key
         */
        protected function _getById($id) {
            $retVal = null;
            if (property_exists($this, '_dbPrimaryKey')) {
                $retVal = $this->_getByFilter([ $this->_dbPrimaryKey => $id ]);
                $retVal = $retVal ? $retVal[0] : null;
            }
            return $retVal;
        }
        
		/**
		 * Returns a set of objects with a filtered query
		 */
		protected function _getByFilter($p1, $value = null) {
			$retVal = null;
			
			if (isset($this->_dbTable) && property_exists($this, '_dbMap')) {
				
				$where = [];
				$params = [];
				
				if (is_array($p1)) {
					foreach ($p1 as $column => $value) {
						if (isset($this->_dbMap[$column])) {
							$where[] = '`' . $this->_dbMap[$column] . '` = :' . $column;
							$params[':' . $column] = $value;
						}
					}
				} else {
					if (isset($this->_dbMap[$p1]) && $value !== null) {
						$where[] = '`' . $this->_dbMap[$p1] . '` = :' . $p1;
						$params[':' . $p1] = $value;
					}
				}
				
				if (count($where) > 0) {
					$columns = array_values($this->_dbMap);
					$query = 'SELECT ' . implode(',', $columns) . ' FROM `' . $this->_dbTable . '` WHERE ' . implode('AND', $where);
					$result = Db::Query($query, $params);
					if ($result && $result->count > 0) {
						$retVal = [];
						$class = get_class($this);
						while ($row = Db::Fetch($result)) {
							$retVal[] = new $class($row);
						}
					}
				}
				
			}
			
			return $retVal;
		}
	
	}

}