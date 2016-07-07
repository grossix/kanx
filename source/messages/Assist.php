<?php

class Assist extends Message {

	public function __construct(\MatchBot $matchBot) {
		parent::__construct($matchBot);

		$user = static::getUserRegex() . static::getTeamRegex();

		$this->regex = '/"' . $user . '" assisted killing/';

	}

	protected function process($data) {
		if ($data['steam_id'] != "BOT") {
			$this->matchBot->onAssist($data['steam_id'], $data['team']);
		}
	}	
}
