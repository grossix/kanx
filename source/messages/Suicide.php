<?php

class Suicide extends Message {

	public function __construct(\MatchBot $matchBot) {
		parent::__construct($matchBot);

		$this->regex = '/"' . static::getUserRegex() . static::getTeamRegex() . '" ' . self::$position . ' committed suicide/';
	}

	protected function process($data) {
		if ($data['steam_id'] != "BOT") {
			$this->matchBot->onDeath($data['steam_id'], $data['team']);
		}
	}	
}
