<?php
	/*	WHEEL > Core > Database
	//  -----------------------
	//
	//	Creates a magic connection to the database.
	*/

	// Main class
	class wheel_DatabaseConnect {
		private $_handler = null;
		public $cache;
		public $configName;

		public function __construct($configName = null){
			global $_;
			if(!empty($configName))
				$this->configName = $configName;

			if(empty($this->configName))
				$this->configName = $_['config']['databases']['defaultConfig'];
		}

		// Try to connect to the database
		public function connect($configName = null){
			global $_;

			if(!empty($configName))
				$this->configName = $configName;
			
			if($this->_handler == null){
				if(isset($_['config']['databases'][$this->configName])){
					$config = new wheel_DatabaseConfig($_['config']['databases'][$this->configName]);

					@$this->_handler = new mysqli($config->host, $config->user, $config->pass, $config->dbname);
				}else{
					$_["error"]->error("WHEEL > DB : Tryed to use undefined database configuration ('$this->configName').");
				}

				if(!empty($this->_handler->connect_error)){
					$error = $this->_handler->connect_error;
					unset($this->_handler);
					$this->error = $error;
					$_["error"]->fatal("WHEEL > DB : Unable to connect to the database ($error).");
				}elseif(!empty($config->encoding)){
					$this->_handler->set_charset($config->encoding);
					$_["error"]->log("WHEEL > DB : Connected to the database ('$this->configName').");
				}
			}
		}

		// Executes the querry
		public function sql($query){
			global $_;

			//Connect if not yet done
			$this->connect();

			$_["error"]->log("SQL Querry : ".$query);

			$result = $this->_handler->query($query);
			if($result===false)
				$_["error"]->fatal("SQL ERROR : ".$this->_handler->error);

			return $result;
		}

		// Load model or creates a generic model if not exists
		public function __get($table){
			global $_;

			if(class_exists($table)){
				$this->table = new $table($table);
			}elseif(is_file('../models/'.$table.'.php')){
				require_once('../models/'.$table.'.php');
				$this->table = new $table($table);
			}else{
				$this->$table = new Models($table);
			}
			return $this->$table;
		}

		// Shortcut of selectFirstById()
		public function __call($table, $id){
			return $this->$table->selectFirstById($id[0]);
		}
		
	}

	// Get the config and makes a cute object with each property declared, otherwise defines a default value.
	class wheel_DatabaseConfig {
		public $host = 'localhost';
		public $user = '';
		public $pass = '';
		public $dbname = '';

		public function __construct($config){
			foreach($config as $key => $val){
				if(isset($key)){
					$this->$key = $val;
				}else{
					$_["error"]->info("WHEEL > DB : Unknown variable in database config ('$key').");
				}
			}
		}
	}

	// The generic result class
	class wheel_DatabaseResult{
		private $_rawDataArray;
		private $_externals = array();
		private $_model;
		private $_identifier;

		public function __construct(&$model, $array){
			$array = array_map('strtolower', $array);		// All to lowercase to be insensitive
			$this->_rawDataArray = $array;		// Put in the private rowDataArray

			$this->_model = $model;								// Save a link to model
			$this->_identifier = $this->_rawDataArray[ $this->_model->primaryKey() ];
		}

		// Get any var from the raw data array.
		public function __get($varName){
			global $_;

			$varName = strtolower($varName);			// All to lowercase to simplify

			if( strcontains($varName, '_') ) {				// If it's an external reference to another table
				list($table, $field) = explode('_', $varName, 2);		// Get the tablename and the field
				if(empty($this->_externals[$table])){
					$value = $this->_rawDataArray[$varName];
					$field = "selectFirstBy".$field;
					$this->_externals[$varName] = $_['db']->$table->$field($value);
				}
				if(!empty($this->_externals[$varName]))
					return $this->_externals[$varName];
			}

			if(isset($this->_rawDataArray[$varName])){
				if(is_serialized($this->_rawDataArray[$varName]) AND $unser = unserialize($this->_rawDataArray[$varName])){
					return $unser;		// Unserialize
				}else{
					return $this->_rawDataArray[$varName];
				}
			}

			// If nothing returned
			$_["error"]->error("WHEEL > DB : Field '$varName' not found.");
			return false;
		}

		public function __set($varName, $value){
			global $_;

			$table = $this->_model->tableName();
			$primKey = $this->_model->primaryKey();
			$id = $this->_identifier;
			$varName = strtolower($varName);			// All to lowercase to simplify

			if(is_numeric($value)){
				$_['db']->sql("UPDATE $table SET `$varName` = $value WHERE `$primKey` = $id LIMIT 1");
			}elseif(is_array($value)){
				$value = serialize($value);
				$_['db']->sql("UPDATE $table SET `$varName` = '$value' WHERE `$primKey` = $id LIMIT 1");
			}else{
				$value = mysqli_escape_string($value);		// Avoid SQL injections
				$_['db']->sql("UPDATE $table SET `$varName` = '$value' WHERE `$primKey` = $id LIMIT 1");
			}

			$this->_rawDataArray[$varName] = $value;

			if( strcontains($varName, '_') ) {		// If it's an external reference to another table
				list($table, $field) = explode('_', $varName, 2);
				$this->_externals[$table] = null;
			}
		}

		// Delete this save on the database and unset object
		public function delete(){
			$table = $this->_model->tableName();
			$primKey = $this->_model->primaryKey();
			$id = $this->_identifier;

			$_['db']->sql("DELETE FROM $table WHERE `$primKey` = $id LIMIT 1");
			unset($this);
		}

		// Gives access to the raw data array
		public function rawData(){
			return $this->_rawDataArray;
		}

		// Get any var from the raw data array.
		public function get($varName){
			if(!startWith('_'))	// Check if not private
				return $this->$varName;
			else
				return null;
		}

		// Update the value in the database and in the raw data array.
		public function set($varName, $value){
			if(!startWith('_'))	// Check if not private
				$this->$varName = $value;
			else
				return false;
		}

		public function __call($methodName, $arg){
			// GetVariable();
			if(startWith($methodName, 'get')){
				$varName =  substr($methodName, 3);	// Remove the "get" to find varName
				return $this->get( $varName );
			}

			// SetVariable();
			elseif(startWith($methodName, 'set')){
				$varName =  substr($methodName, 3);	// Remove the "set" to find varName
				return $this->set( $varName, $arg[0]);
			}
		}

		public function __toString(){
			$primKey = $this->_model->primaryKey();
			return $this->$primKey;
		}
	}


	// Load default config
	if(!empty($_['config']['databases']['defaultConfig'])){
		$_['db'] = new wheel_DatabaseConnect($_['config']['databases']['defaultConfig']);
		$_['database'] = &$_['db'];
	}else{
		$_["error"]->info("WHEEL > DB : No default database config, no autoLoad .");
	}