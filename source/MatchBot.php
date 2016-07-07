<?php
use xPaw\SourceQuery\SourceQuery;

class MatchBot {

	public $invalidAddresses = [];

	public $entityManager;
	public $match;
	public $port;
	public $cfg;
	public $map;

	private $currentHalf;
	private $overtimesPlayed = 0;

	private $warmup = false;
	public $live = false;

	private $globalMessages = [];
	private $liveMessages = [];
	private $pregameMessages = [];

	private $teamAPlayers = 0;
	private $teamBPlayers = 0;

	public function __construct(\Match $match) {
		$this->match = $match;
		$this->map = $match->map;

		$this->globalMessages = [
			new \Connected($this),
			new \Disconnected($this),
			new \SwitchTeam($this)
		];

		$this->liveMessages = [
			new \TeamWon($this),
			new \Frag($this),
			new \Assist($this),
			new \Suicide($this)
		];

		$this->pregameMessages = [
			new \GameCommencing($this),
			new \MatchStart($this)
		];
	}

	public function say($message) {
		$this->rcon(["say $message"]);
		echo $message. "\n";
	}

	private function kick($steamId, $message) {
		$this->rcon(["kickid $steamId $message"]);
		$this->say("Kicking $steamId with message: $message");
	}

	private function rcon($commands = []) {
		try {
			$this->match->rcon($commands);
		} catch (\Exception $e) {
			echo "RCON ERROR: $e\n";
		}
	}

	public function onUserConnected($steamId) {
		$this->say("User Connected: $steamId");

		$user = $this->findUser($steamId);
		if (! $user) {
			$this->kick($steamId, 'You are not a valid user. Please create an account.');	
		} else if (($this->teamAPlayers + $this->teamBPlayers + 1) > MAX_PLAYERS) {
			$this->kick($steamId, 'Match is full');
		} else {
			$validTeamA = $user->isValidTeamAPlayer($this->match);
			$validTeamB = $user->isValidTeamBPlayer($this->match);

			if (! $validTeamA && ! $validTeamB) {
				$this->kick($steamId, 'You are not a valid player for this match');
			}

			if (($this->teamAPlayers + 1) > 5 || ($this->teamBPlayers + 1) > 5) {
				$this->kick($steamId, 'Your team is full');
			}

			if ($validTeamA) {
				$this->teamAPlayers++;
			} else if ($validTeamB) {
				$this->teamBPlayers++;
			}
		}
	}

	public function onUserDisconnected($steamId, $team) {
		$this->say("User Disconnected: $steamId");
		$user = $this->findUser($steamId);
		if ($user) {
			$validTeamA = $user->isValidTeamAPlayer($this->match);
			$validTeamB = $user->isValidTeamBPlayer($this->match);
			if ($validTeamA) {
				$this->teamAPlayers--;
			} else if ($validTeamB) {
				$this->teamBPlayers--;
			}

			$totalPlayers = $this->teamAPlayers + $this->teamBPlayers;
			if ($this->warmup && $totalPlayers == 0) {
				$this->warmup = false;
			}

			if ($this->live && ($this->teamAPlayers == 0 || $this->teamBPlayers == 0)) {
				$teamAScore = $this->match->teamAScore;
				$teamBScore = $this->match->teamBScore;
				$totalRounds = $teamAScore + $teamBScore;
			
				if ($totalRounds <= MAX_ROUNDS) {
					$winningRounds = (MAX_HALF_ROUNDS) + 1;
				} else {
					$winningRounds = (MAX_OVERTIME_HALF_ROUNDS) + 1;
				}

				$half = $this->currentHalf;
				if ($this->teamAPlayers == 0) {
					$difference = $winningRounds - $half->getTeamBScore();
					$half->addToTeamBScore($difference);
				} else if ($this->teamBPlayers == 0) {
					$difference = $winningRounds - $half->getTeamAScore();
					$this->half->addToTeamAScore($difference);
				}

				$this->checkForWinner($winningRounds);
			}
		}
	}

	private function findUser($steamId) {
		$steamId = (new \SteamID($steamId))->ConvertToUInt64();
		return $this->entityManager->getRepository('User')->findOneBySteamId($steamId);
	}

	public function onUserSwitchedTeams($steamId, $fromTeam, $toTeam) {
		$this->say("Switched Team: $steamId from '$fromTeam' to '$toTeam'");
		$user = $this->findUser($steamId);
		$correctTeam = $this->currentHalf->getCorrectTeam($user);
		if ($correctTeam != $toTeam) {
			$this->kick($steamId, "Please reconnect and join '$correctTeam'");	
		}
	}

	private function getStat($steamId, $side) {
		$half = $this->currentHalf;
		$user = $this->findUser($steamId);
		$team = $half->getTeamBySide($side);
		$stat = $half->getStat($user, $team);

		if (! $stat) {
			$stat = new \Stat($half, $team, $user);
			$this->entityManager->persist($stat);
		}

		return $stat;
	}

	public function onTeamkill($steamId, $team) {
		$this->say("Teamkill: $steamId Team: $team");
		$this->getStat($steamId, $team)->recordTeamkill();
	}

	public function onFrag($steamId, $team) {
		$this->say("Frag: $steamId Team: $team");
		$this->getStat($steamId, $team)->recordFrag();
	}

	public function onDeath($steamId, $team) {
		$this->say("Death: $steamId Team: $team");
		$this->getStat($steamId, $team)->recordDeath();
	}

	public function onAssist($steamId, $team) {
		$this->say("Assist: $steamId Team: $team");
		$this->getStat($steamId, $team)->recordAssist();
	}

	public function onMatchStart() {
		$this->say("Got Match Start");
		if (! $this->warmup) {
			$this->say("Starting Warmup");
			$this->warmup = true;
		} else {
			if ($this->teamAPlayers + $this->teamBPlayers < MAX_PLAYERS) {
				throw new Exception('Not enough players for match to go live.');
			}

			$this->say("Going Live...");
			$this->live = true;
			$this->showSidesAndScore();
		}
	}

	public function onGameCommencing() {
		$this->say('Initializing match');
		$this->rcon($this->cfg->commands);
	}

	private function checkForWinner($winningScore) {
		$teamAScore = $this->currentHalf->getTeamAScore();
		$teamBScore = $this->currentHalf->getTeamBScore();
		if ($teamAScore == $winningScore) {
			$this->say('Team A Wins : ' . $this->match->teamAScore . ' - ' . $this->match->teamBScore);
			throw new \FinishedMatch();
		}
		if ($teamBScore == $winningScore) {
			$this->say('Team B Wins : ' . $this->match->teamBScore . ' - ' . $this->match->teamAScore);
			throw new \FinishedMatch();
		}
	}

	private function newOvertimeSwitch() {
		$this->say("Overtime Switch");
		$this->currentHalf = new \Half($this->match, $this->overtimesPlayed);
		$this->entityManager->persist($this->currentHalf);
		$this->showSidesAndScore();
	}

	private function halftimeSwitch() {
		$this->say("Halftime Switch");
		$newHalf = new \Half($this->currentHalf);
		$newHalf->previousHalf = $this->currentHalf;
		$this->currentHalf = $newHalf;
		$this->entityManager->persist($this->currentHalf);
		$this->showSidesAndScore();
	}

	public function onTeamWon($team) {
		$this->say("Team Won: $team");
		$this->currentHalf->teamScored($team);

		$previousHalf = $this->currentHalf->previousHalf;
		$teamAScore = $this->match->teamAScore;
		$teamBScore = $this->match->teamBScore;
		$totalRounds = $teamAScore + $teamBScore;
		if ($totalRounds == MAX_HALF_ROUNDS) {
			$this->halftimeSwitch();
		}
		if ($previousHalf && $totalRounds <= MAX_ROUNDS) {
			$this->checkForWinner((MAX_HALF_ROUNDS) + 1);
		}
		if ($totalRounds == MAX_ROUNDS && $teamAScore == $teamBScore) {
			$this->newOvertimeSwitch();
		}
		if ($totalRounds > MAX_ROUNDS) {
			$totalRounds = ($totalRounds - MAX_ROUNDS) - (MAX_OVERTIME_ROUNDS * $this->overtimesPlayed);
			if ($totalRounds == MAX_OVERTIME_HALF_ROUNDS) {
				$this->halftimeSwitch();
			}
			if ($previousHalf) {
				$this->checkForWinner((MAX_OVERTIME_HALF_ROUNDS) + 1);
			}
			if ($totalRounds == MAX_OVERTIME_ROUNDS && $teamAScore == $teamBScore) {
				$this->overtimesPlayed++;
				$this->newOvertimeSwitch();
			}
		}
	}

	public function getMessage($message, $address) {
		if ($address != $this->match->udpAddress) {
			$this->invalidAddresses[$address][] = $message;
			return;
		}

		$parsed = $this->parseMessages($this->globalMessages, $message);
		if ($parsed) {
			return;
		}

		if ($this->live) {
			$parsed = $this->parseMessages($this->liveMessages, $message);
			if ($parsed) {
				return;
			}
		} else {
			$parsed = $this->parseMessages($this->pregameMessages, $message);
			if ($parsed) {
				return;
			}
		}
	}

	private function parseMessages($messageParsers, $message) {
		foreach ($messageParsers as $messageParser) {
			if ($messageParser->parse($message)) {
				return true;
			}
		}

		return false;
	}

	public function start() {
		$this->currentHalf = new \Half($this->match);
		$this->entityManager->persist($this->currentHalf);
		$this->match->addLogAddress($this->port);
		$this->rcon(["map $this->map"]);
	}

	public function showSidesAndScore() {
		$half = $this->currentHalf;
		$match = $this->match;
		$this->say('Team A on side ' . $half->getTeamASide());
		$this->say('Team B on side ' . $half->getTeamBSide());
		$this->say("Score A: $match->teamAScore - B: $match->teamBScore");
	}
}
