<?php

class Disconnected extends Message {

	public function __construct(\MatchBot $matchBot) {
		parent::__construct($matchBot);

		$user = static::getUserRegex() . static::getTeamRegex();
		$this->regex = '/"' . $user . '" disconnected/';
	}

	protected function process($data) {
		if ($data['steam_id'] != "BOT") {
			$this->matchBot->onUserDisconnected($data['steam_id'], $data['team']);
		}
	}	
}
