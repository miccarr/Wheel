<?php
	/*	WHEEL > Core > Models
	//  ---------------------
	//
	//	Define the default models methods and properties.
	*/

	class Models{
		private $_tableName;
		private $_primaryKey;

		public function __construct($tableName = null){
			if(empty($this->_tableName)){
				$this->_tableName = $tableName;
			}
		}

		private function _makeResult(&$rawResult){
			while($row = $rawResult->fetch_array()){
				if(empty($row->id)){
					$output[] = $row;
				}else{
					$output[$row->id] = $row;
				}
			}

			if(method_exists($this,'afterSelect')){
				$output = $this->afterSelect($output);
			}

			return new wheel_DatabaseResult($this, $output);
		}

		public function primaryKey(){
			if(empty($this->_primaryKey))
				$this->_primaryKey = $_['db']->sql("SHOW KEYS FROM tablename WHERE Key_name = 'PRIMARY'");
			return $this->_primaryKey;
		}

		public function tableName(){
			return $this->_tableName;
		}

		// The famous select function, with options : conditions, fields, order, list
		public function select($options=array()){
			global $_;

			// Format the conditions option
			if(is_array($options['conditions'])){
				foreach($options['conditions'] as $key => $value){
					if( is_numeric($key) )					// If complete string sql condition
						$conditions[] = $value;
					else{									// If associative condition
						if(!is_numeric($value))						// If not int value, quote
							$value = "'".$value."'";
						if(strpos('`', $key) == false)		
							$conditions[] = "`".$key."` = ".$value; // Add ` quotes for fields
						else
							$conditions[] = $key." = ".$value;
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
					if(strpos('`', $field) == false){
						$fields[] = "`".$field."`";
					}else{
						$fields[] = $field;
					}
				}
				$fields = implode(', ', $fields);
			}else{
				$fields = $options['fields'];
			}

			if(is_array($options['order'])){
				foreach($options['order'] as $field => $order){
					if( is_numeric($field) ){
						$orders[] = $order;
					}elseif(strpos('`', $field) == false){
						$orders[] = "`".$field."` ".$order;
					}else{
						$orders[] = $field." ".$order;
					}
				}


			$table = $this->_tableName;

			// Creation of the SQL Query
			$SQL = "SELECT $fields ";
			$SQL = "FROM `$table` ";
			if(!empty($conditions))
				$SQL.= "WHERE ".$conditions." ";
			if(!empty($options['order']))
				$SQL.= "WHERE ".$conditions." ";
			if(!empty($options['limit'])){
				$SQL.= "LIMIT ";
				if(!empty($options['offset']))
					$SQL.= $options['offset'].', ';
				$SQL.= $options['limit'];
			}

			return $this->_makeResult( $_['db']->sql($SQL) );
		}

		public function selectFirst($options=array()){
			$options = array_merge($options, array('limit' => 1));
			return $this->select($options)[0];
		}


		// Special methods
		public function __call($methodName, $args){
			// SelectByField()
			if(startWith($methodName, 'selectBy')){
				$options = array_merge($args[1], array('conditions'=> substr($methodName, 8)." = ".$args[0]));
				return $this->select($options);
			}

			// SelectFirstByField()
			elseif(startWith($methodName, 'selectFirstBy')){
				$options = array_merge($args[1], array('conditions'=> substr($methodName, 13)." = ".$args[0]));
				return $this->selectFirst($options);
			}
		}
	}