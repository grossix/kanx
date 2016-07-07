<?php

abstract class Message {
	static protected $position = '\[[\-]?[0-9]+ [\-]?[0-9]+ [\-]?[0-9]+]';

	protected $matchBot;
	protected $regex;

	public function __construct(\MatchBot $matchBot) {
		$this->matchBot = $matchBot;
	}

	public function parse($message) {
		if (preg_match($this->regex, $message, $data)) {
			echo $message . "\n";
			$this->process($data);
			return true;
		}

		return false;
	}

	static public function getUserRegex($prefix = null) {
		return '(.+)<\d+><(?<' . $prefix . 'steam_id>.+)>';
	}

	static public function getTeamRegex($prefix = null) {
		return '<(?<' . $prefix . 'team>CT|TERRORIST|Unassigned|Spectator)>';
	}

	abstract protected function process($data);
}
