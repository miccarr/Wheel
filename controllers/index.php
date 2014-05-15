<?php
	/*
	//	Example controller for WHEEL
	*/
	class indexController extends Controllers{

		public function index(){
			if(isset($_GET['id'])){
				$photo = $this->database->Photos($_GET['id']);
				echo "<h1><u>Name :</u> ".$photo->name.'</h1>';
				echo "<u>Album :</u> ".$photo->albums_id->name;
				echo '<hr /><a href="?">Back</a>';
			}else{
				echo "<b>Hello World !</b>";
				foreach($this->database->Photos->selectByVisibility(1) as $photo){
					echo '<li><a href="?id='.$photo->id.'">';
					echo $photo->name." <i>(in ".$photo->albums_id->name.")</i>";
					echo "</a></li>";
				}
			}
		}

	}