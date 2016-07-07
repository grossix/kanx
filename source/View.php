<?php

class View {

	private $includes;
	private $parameters;

	private $level;

	public $slots;

	private static $id;
	private static $baseDirectory = null;

	public $sessionUser;

	public function __construct(\User $sessionUser = null, $path, array $parameters = []) {
		$this->sessionUser = $sessionUser;

		if ($this->getBaseDirectory() === null) {
			$this->setBaseDirectory(__DIR__ . "/views");
		}

		$this->level = 0;
		$this->includes = [];
		$this->parameters = $parameters;

		$this->slots = new \Slots($this);

		$this->extend($path);
	}

	public function setParameters($name, $value) {
		$this->parameters[$name] = $value;
	}

	public function getParameter($name) {
		return $this->parameters[$name];
	}

	public function getParameters() {
		return $this->parameters;
	}

	public static function setBaseDirectory($directory) {
		self::$baseDirectory = $directory;
	}

	public static function getBaseDirectory() {
		return self::$baseDirectory;
	}

	public function getLevel() {
		return $this->level;
	}

	public function extend($path) {
		$this->includes[] = sprintf("%s/%s", $this->getBaseDirectory(), $path);
	}

	public function includeView($path, array $parameters = []) {
		$view = new self($this->sessionUser, $path, $parameters);
		return $view->generate();
	}

	public function generate() {
		ob_start();
		while ($file = array_shift($this->includes)) {
			$this->level++;
			$this->includeFile($file);
		}
		$html = trim(ob_get_clean());

		return $html;
	}

	private function includeFile($file) {
		includeFile($this, $file);
	}


	public function showTeam(\Team $team = null) {
		if ($team) {
			return "<a href='/team/{$team->id}'>$team->name</a>";
		}
	}

	public function showPlayer(User $user = null) {
		if ($user) {
			return "<a href='/user/$user->id'>$user</a>";
		}
	}

}

/** This keeps $this from being accessible from within the views */
function includeFile(\View $view, $file) {
		foreach ($view->getParameters() as $key => $value) {
			if (in_array($key, ["view", "file", 'sessionUser'])) {
				throw new \Exception("Cannot not use '$key' as a variable name.");
			}
		}
		extract($view->getParameters());
		$sessionUser = $view->sessionUser;
		include($file);
}
