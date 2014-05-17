<?php
	/*	WHEEL > Core > Error
	//  --------------------
	//
	//	Error handler
	*/


	class wheel_ErrorHandler{
		private $_errors = array();

		public function __call($type, $msg){
			global $_;
			$this->_errors[] = array('type'=>$type, 'msg'=>$msg[0]);
			if($type=='fatal'){
				if($_['config']['core']['debug']){
					echo "<h1>Fatal Error</h1><h2>".$msg[0]."</h2>";
					$this->showErrors();
					die('Fatal error');
				}
				if(!empty($msg[1]))
					header('Location: '.$_['helper']->url(array_merge(
							$_['config']['routes']['fallback']['controller'],
							array('error' => $msg[1])
						)));
				else
					header('Location: '.$_['helper']->url($_['config']['routes']['fallback']['controller']));
			}
		}
		public function showErrors(){
			global $_;
			if(isset($_GET['debug'])){
				echo '<table id="wheel_errors" style="opacity:.5;"><tr><th colspan="2">Debug</th></tr>';
				foreach ($this->_errors as $e) {
					echo "<tr><td><b>".$e['type']."</b></td><td>";
					echo $e['msg']."</td></tr>";
				}
				echo '<tr><td colspan="2">';

				foreach($_ as $key => $content){
					if(($key != 'error' AND $key != 'db' AND $key != 'mustache') OR $_GET['debug']=='true'){
						echo "<h2>$key</h2><pre>";
						var_dump($_[$key]);
						echo "</pre>";
					}
				}
				echo '</td></tr></table>';
			}
		}
	}

	$_['error'] = new wheel_ErrorHandler();