<?php 

class Redirect extends Exception {

	public $path;

	public function __construct($path = '/', $message = '') {
		parent::__construct($message);
		$this->path = $path;
	}
}
