<?php
	/*	WHEEL > Core > Database
	//  -----------------------
	//
	//	Creates a magic connection to the database.
	*/

	// Main class
	class wheel_DatabaseConnect {
		private $handler;

		public function __construct($configName){
			$this->connect($configName);
		}

		// Try to connect to the database
		public function connect($configName = null){
			global $_;

			if(isset($_['config']['databases'][$configName])){
				$config = new wheel_DatabaseConfig($_['config']['databases'][$configName]);

				try{
					$this->handler = new new mysqli($config->host, $config->user, $config->password, $config->dbname);
				}catch(Exception $e){
					$_["error"]->error("WHEEL : Unable to connect to the database ('".$configName." : ".$config->user."@".$config->host."').<br />\n".$e);
				}
			}else{
				$_["error"]->error("WHEEL : Tryed to use undefined database configuration ('$configName').");
			}
		}

		// Executes the querry
		public function sql($query){
			return $this->handler->query($query);
		}

		// Load model or creates a generic model if not exists
		public function __get($table){
			if(class_exists($table)){
				$this->table = new $table($table);
			}elseif(is_file('./models/'.$table.'.php')){
				require_once('./models/'.$table.'.php');
				$this->table = new $table($table);
			}elseif($this->handler->query("SELECT 1 FROM '$table' LIMIT 1")){
				$this->$table = new Models($table);
				return $this->$table;
			}else{
				$_["error"]->error("WHEEL : This table don't exist ('$table').");
			}
		}

		// Shortcut of selectById()
		public function __call($table, $id){
			try{
				return $this->$table->selectById($id);
			}catch(Exception $e){
				$_["error"]->error("WHEEL : This table don't exist ('$table').");
			}
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
					$_["error"]->info("WHEEL : Unknown variable in database config ('$key').");
				}
			}
		}
	}

	// The generic result class
	class wheel_DatabaseResult{
		private $_rawDataArray;
		private $_externals;
		private $_model;
		private $_tableName;
		private $_primaryKey;
		private $_identifier;

		public function __construct(&$model, $array){
			foreach($array as $varName => $value){			// Put the array result in private property
				$varName = strtolower($varName);				// All to lowercase to simplify
				$this->_rawDataArray[$varName] = $value;	
			}
			$this->_tableName = $model->tableName();			// Save the table name;
			$this->_primaryKey = $this->_model->primaryKey();
			$this->_identifier = $id = $this->_rawDataArray[ $this->_primaryKey ];
		}

		// Get any var from the raw data array.
		public function __get($varName){
			global $_;
			if( strcontains($varName, '_') ) {		// If it's an external reference to another table
				$table, $field = explode('_', $varName, 2);		// Get the tablename and the field
				if(empty($this->_externals[$table])){
					$varName = strtolower($varName);			// All to lowercase to simplify
					$value = $this->_rawDataArray[$varName];
					$field = "selectBy".$field;
					$this->_externals[$table] = &$_['db']->$table->$field($value);
				}
				return &$this->_externals[$table];
			}else{
				return &$this->_rawDataArray[$varName];
			}
		}

		public function __set($varName, $value){
			global $_;

			$table = $this->_tableName;
			$primKey = $this->_primaryKey;
			$id = $this->_identifier;
			$varName = strtolower($varName);			// All to lowercase to simplify

			$value = mysqli_escape_string($value);		// Avoid SQL injections

			$_['db']->sql("UPDATE $table SET `$varName` = '$value' WHERE `$primKey` = $id LIMIT 1");

			$this->_rawDataArray[$varName] = $value;

			if( strcontains($varName, '_') ) {		// If it's an external reference to another table
				$table, $field = explode('_', $varName, 2);
				$this->_externals[$table] = null;
			}
		}

		// Delete this save on the database and unset object
		public function delete(){
			$table = $this->_tableName;
			$primKey = $this->_primaryKey;
			$id = $this->_identifier;

			$_['db']->sql("DELETE FROM $table WHERE `$primKey` = $id LIMIT 1");
			unset($this);
		}

		// Gives access to the raw data array
		public function array(){
			return &$this->_rawDataArray;
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
	}


	// Load default config
	if(!empty($_['config']['databases']['defaultConfig'])){
		$_['db'] = new wheel_DatabaseConnect($_['config']['databases']['defaultConfig']);
		$_['DB'] = &$_['db'];
		$_['database'] = &$_['db'];
	}else{
		$_["error"]->info("WHEEL : No default database config, no autoLoad .");
	}