<?php
	/*	WHEEL > Core > Error
	//  --------------------
	//
	//	Error handler
	*/


	class wheel_ErrorHandler{
		private $_errors = array();

		public function __call($type, $msg){
			$this->_errors[] = array('type'=>$type, 'msg'=>$msg[0]);
			if($type=='fatal'){
				$this->showErrors();
				die();
			}
		}
		public function showErrors(){
			global $_;
			if(isset($_GET['debug']))
				echo '<table id="errors" style="opacity:.5;"><tr><th colspan="2">Debug</th></tr>';
			else
				echo '<table id="errors" style="display: none;"><tr><th colspan="2">Debug</th></tr>';
			foreach ($this->_errors as $e) {
				echo "<tr><td><b>".$e['type']."</b></td><td>";
				echo $e['msg']."</td></tr>";
			}
			echo '<tr><td colspan="2"><pre>';
			var_dump($_);
			echo '</pre>';
			echo '</td></tr></table>';
		}
	}

	$_['error'] = new wheel_ErrorHandler();