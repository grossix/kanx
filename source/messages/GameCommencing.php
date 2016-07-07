<?php

class GameCommencing extends Message {
	protected $regex = '/World triggered "Game_Commencing"/';

	protected function process($data) {
		$this->matchBot->onGameCommencing();
	}	
}
