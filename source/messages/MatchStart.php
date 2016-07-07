<?php

class MatchStart extends Message {

	public function __construct(\MatchBot $matchBot) {
		parent::__construct($matchBot);

		$this->regex = '/World triggered "Match_Start" on "' . $this->matchBot->map . '"/';
	}


	protected function process($data) {
		$this->matchBot->onMatchStart();
	}	
}
