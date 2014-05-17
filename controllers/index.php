<?php
	/*
	//	index.php : Example controller for WHEEL
	*/

	class indexController extends Controllers{

		public function index(){
			$photos = $this->database->Photos->selectByVisibility(true);
			$this->render( array('photos' => $photos) );
		}

		public function show($options){
			$photo = $this->database->Photos($options['id']);
			$this->render( array('photo' => $photo, 'id'=>$options['id']) );
		}
	}