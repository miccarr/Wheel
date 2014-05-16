<?php
	/*
	//	index.php : Example controller for WHEEL
	*/

	class indexController extends Controllers{

		public function index(){
			echo "<h1>Hello World !</h1>";
			foreach($this->database->Photos->selectByVisibility(true) as $photo){
				echo "<li>";
				echo $this->helper->link(
						'/index/show/'.$photo->id,
						$photo->name." <i>(in ".$photo->albums_id->name.")</i>", 'Image n° '.$photo->id
					);
				echo "</li>";
			}
		}

		public function show($options){
			if($photo = $this->database->Photos($options['id'])){
				echo "<h1><u>Name :</u> ".$photo->name.'</h1>';
				echo "<u>Album N# ".$photo->albums_id." :</u> ".$photo->albums_id->name;
				echo "<hr />".$this->helper->img($photo->url);
			}else
				echo "<h1>There is not photo with the id '".$options['id']."' !<h1>";

			echo '<hr />'.$this->helper->link('/', e('<- Back'), 'Return to the list');
		}
	}