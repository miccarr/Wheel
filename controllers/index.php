<?php
	/*
	//	Example controller for WHEEL
	*/
	class indexController extends Controllers{

		public function index(){
			echo "<b>Hello World !</b>";
			foreach($this->db->Photos->selectByVisibility(1) as $photo){
				echo "<li>";
				echo $photo->name." <i>(in ".$photo->albums_id->name.")</i>";
				echo "</li>";
			}
		}

	}