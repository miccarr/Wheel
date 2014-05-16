<?php
	/*	WHEEL > Core > Models
	//  ---------------------
	//
	//	Define the default models methods and properties.
	*/

	class Models{
		private $_tableName;
		private $_primaryKey;
		private $_cache;

		public function __construct($tableName = null){
			if(empty($this->_tableName)){
				$this->_tableName = $tableName;
			}
		}

		private function _makeResult($rawResult){
			global $_;

			$output = array();

			while( $row = $rawResult->fetch_array() ){
				if(empty($row->id))
					$output[] = $row;
				else
					$output[$row->id] = $row;
			}

			if(method_exists($this,'afterSelect')){
				$output = $this->afterSelect($output);
			}

			$rows = array();

			foreach ($output as $key => $data){
				$rows[$key] = new wheel_DatabaseResult($this, $data);
			}
			return $rows;
		}

		private function _cache($sqlQuery){
			global $_;

			// If not in the cache, makeResult from sqlQuery
			if(!isset($this->_cache[$sqlQuery]))
				$this->_cache[$sqlQuery] = $this->_makeResult( $_['db']->sql($sqlQuery) );

			return $this->_cache[$sqlQuery];
		}

		public function primaryKey(){
			global $_;
			$tableName = $this->_tableName;
			if(empty($this->_primaryKey))
				$this->_primaryKey = $_['db']->sql("SHOW KEYS FROM `$tableName` WHERE Key_name = 'PRIMARY'")->fetch_array()['Column_name'];
			return $this->_primaryKey;
		}

		public function tableName(){
			return $this->_tableName;
		}

		// The famous select function, with options : conditions, fields, order, list
		public function select( $options=array() ){
			global $_;
			// Format the conditions option
			if(is_array($options['conditions'])){
				foreach($options['conditions'] as $key => $value){
					if( is_numeric($key) )					// If complete string sql condition
						$conditions[] = $value;
					else{									// If associative condition
						if(!is_numeric($value))
							$value = "'".$value."'";
						if(strcontains($key, '`'))	
							$conditions[] = $key." = ".$value;
						else
							$conditions[] = "`".$key."` = ".$value; // Add ` quotes for fields
					}
				}
				$conditions = implode(' AND ', $conditions);
			}else{
				$conditions = $options['conditions'];
			}

			// Format the fields option
			if(empty($options['fields'])){
				$fields = '*';
			}elseif(is_array($options['fields'])){
				foreach($options['fields'] as $field){
					if(strcontains($key, '`')){
						$fields[] = "`".$field."`";
					}else{
						$fields[] = $field;
					}
				}
				$fields = implode(', ', $fields);
			}else{
				$fields = $options['fields'];
			}

			if(isset($options['order']) AND is_array($options['order'])){
				foreach($options['order'] as $field => $order){
					if( is_numeric($field) ){
						$orders[] = $order;
					}elseif(strpos('`', $field) == false){
						$orders[] = "`".$field."` ".$order;
					}else{
						$orders[] = $field." ".$order;
					}
				}
				$orders = implode(', ', $orders);
			}else{
				$orders = isset($options['order']) ? $options['order'] : null;
			}

			$table = $this->_tableName;

			// Creation of the SQL Query
			$SQL = "SELECT $fields ";
			$SQL.= "FROM `$table` ";
			if(!empty($conditions))
				$SQL.= "WHERE ".$conditions." ";
			if(!empty($orders))
				$SQL.= "ORDER BY ".$orders." ";
			if(!empty($options['limit'])){
				$SQL.= "LIMIT ";
				if(!empty($options['offset']))
					$SQL.= $options['offset'].', ';
				$SQL.= $options['limit'];
			}

			return $this->_cache($SQL);
		}

		public function selectFirst( $options=array() ){
			$options = array_merge($options, array('limit' => 1));
			$result = $this->select($options);
			$result = reset($result);	// Return first element
			return $result;
		}


		// Special methods
		public function __call($methodName, $args){
			// SelectByField()
			if(startWith($methodName, 'selectBy')){
				$options = isset($args[1]) ? $args[1] : array();
				$options = array_merge($options, array('conditions' => array( substr($methodName, 8) => $args[0])));
				return $this->select($options);
			}

			// SelectFirstByField()
			elseif(startWith($methodName, 'selectFirstBy')){
				$options = isset($args[1]) ? $args[1] : array();
				$options = array_merge($options, array('conditions' => array( substr($methodName, 13) => $args[0])));
				return $this->selectFirst($options);
			}
		}
	}