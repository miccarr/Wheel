<?php
	/*
	//	Example controller for WHEEL
	*/
	class indexController extends Controllers{

		public function index(){
			echo "<b>Hello World !</b>";
			foreach($this->database->Photos->selectByVisibility(1) as $photo){
				echo '<li><a href="index/show/'.$photo->id.'">';
				echo $photo->name." <i>(in ".$photo->albums_id->name.")</i>";
				echo "</a></li>";
			}
		}

		public function show($options){
			if($photo = $this->database->Photos($options['id'])){
				echo "<h1><u>Name :</u> ".$photo->name.'</h1>';
				echo "<u>Album :</u> ".$photo->albums_id->name;
			}else{
				echo "ID Not found";
			}
			echo '<hr /><a href="../..">Back</a>';
		}

	}