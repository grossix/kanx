<?php

class SwitchTeam extends Message {

	public function __construct(\MatchBot $matchBot) {
		parent::__construct($matchBot);

		$this->regex = '/"' . static::getUserRegex() . '" switched from team ' . static::getTeamRegex('from_') . ' to ' . static::getTeamRegex('to_') . '/';
	}

	protected function process($data) {
		if ($data['steam_id'] != "BOT") {
			$this->matchBot->onUserSwitchedTeams($data['steam_id'], $data['from_team'], $data['to_team']);
		}
	}	
}
