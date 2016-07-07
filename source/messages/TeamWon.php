<?php

class TeamWon extends Message {
	protected $regex = '/Team "(?<team>.+)" triggered "SFUI/';

	protected function process($data) {
		$this->matchBot->onTeamWon($data['team']);
	}	
}
