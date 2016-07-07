<?php

class Frag extends Message {

	public function __construct(\MatchBot $matchBot) {
		parent::__construct($matchBot);

		$fragger = static::getUserRegex('fragger_') . static::getTeamRegex('fragger_');
		$killed = static::getUserRegex('killed_') . static::getTeamRegex('killed_');

		$this->regex = '/"' . $fragger . '" ' . self::$position . ' killed "' . $killed . '" ' . self::$position . ' with "(?<weapon>[a-zA-Z0-9_]+)"(?<headshot>.*)/';
	}

	protected function process($data) {
		if ($data['fragger_steam_id'] != "BOT") {
			if ($data['fragger_team'] == $data['killed_team']) {
				$this->matchBot->onTeamkill($data['fragger_steam_id'], $data['fragger_team']);
			} else {
				$this->matchBot->onFrag($data['fragger_steam_id'], $data['fragger_team']);
			}
		}
		if ($data['killed_steam_id'] != "BOT") {
			$this->matchBot->onDeath($data['killed_steam_id'], $data['killed_team']);
		}
	}	
}
