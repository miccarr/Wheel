<?php
	/*
	//	index.php : Example controller for WHEEL
	*/

	class indexController extends Controllers{

		public function index(){
			$photos = $this->database->Photos->selectByVisibility(true);
			$this->render( array('photos' => $photos, 'title' => "List of photos" ) );
		}

		public function show($options){
			$photo = $this->database->Photos($options['id']);
			$this->render( array('photo' => $photo, 'id'=>$options['id'], 'title' => "Photo no. ".$options['id']) );
		}

		// Way to shortcut the show action
		public function __call($id, $other){
			$this->_view = 'indexController/show';
			return $this->show( array_merge(array('id'=>$id), $other) );
		}
	}