<?php

class Connected extends Message {

	public function __construct(\MatchBot $matchBot) {
		parent::__construct($matchBot);

		$this->regex = '/"' . static::getUserRegex() . '<>" connected, address/';
	}

	protected function process($data) {
		if ($data['steam_id'] != "BOT") {
			$this->matchBot->onUserConnected($data['steam_id']);
		}
	}	
}
