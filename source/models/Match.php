<?php
use xPaw\SourceQuery\SourceQuery;

/**
 * @Entity
 * @Table (name="matches")
 */
class Match {
	use ServerTrait;

	/**
	 * @Id
	 * @Column (type="integer")
	 * @GeneratedValue
	 */
	public $id;

	/**
	 * @ManyToOne (targetEntity="User")
	 * @JoinColumn (name="owner_id")
	 */
	public $owner;

	/**
	 * @Column (name="udp_port", type="string")
	 */
	public $port = '';

	/**
	 * @Column (name="map", type="string")
	 */
	public $map = '';

	/**
	 * @ManyToOne (targetEntity="Server")
	 * @JoinColumn (name="server_id", onDelete="SET NULL")
	 */
	public $server;

	/**
	 * @Column (name="udp_address", type="string")
	 */
	public $udpAddress = '';

	/**
	 * @Column (name="team_a_name", type="string")
	 */
	public $teamAName;

	/**
	 * @ManyToOne (targetEntity="Team")
	 * @JoinColumn (name="team_a_id", onDelete="SET NULL")
	 */
	public $teamA;

	/**
	 * @Column (name="team_a_before_elo", type="integer")
	 */
	public $teamABeforeElo = 0;

	/**
	 * @Column (name="team_a_after_elo", type="integer")
	 */
	public $teamAAfterElo = 0;

	/**
	 * @Column (name="team_b_name", type="string")
	 */
	public $teamBName = '';

	/**
	 * @ManyToOne (targetEntity="Team")
	 * @JoinColumn (name="team_b_id", onDelete="SET NULL")
	 */
	public $teamB;

	/**
	 * @Column (name="team_b_before_elo", type="integer")
	 */
	public $teamBBeforeElo = 0;

	/**
	 * @Column (name="team_b_after_elo", type="integer")
	 */
	public $teamBAfterElo = 0;

	/**
	 * @Column (name="team_a_score", type="integer")
	 */
	public $teamAScore = 0;

	/**
	 * @Column (name="team_b_score", type="integer")
	 */
	public $teamBScore = 0;

	/**
	 * @Column (name="team_a_start_ct", type="boolean")
	 */
	public $teamAStartCt;

	/**
	 * @Column (name="started", type="boolean")
	 */
	public $started = false;

	/**
	 * @Column (name="finished", type="boolean")
	 */
	public $finished = false;

	/**
	 * @OneToMany (targetEntity="Half", mappedBy="match", cascade={"persist", "remove"})
	 */
	public $halfs;

	/**
	 * @OneToMany (targetEntity="MatchPlayer", mappedBy="match", cascade={"persist", "remove"}, orphanRemoval=true)
	 */
	public $matchPlayers;

	public function __construct() {
		$this->halfs = new \Doctrine\Common\Collections\ArrayCollection();
		$this->matchPlayers = new \Doctrine\Common\Collections\ArrayCollection();
		$this->teamAStartCt = rand(0, 1);
		$this->map = MAPS[rand(0, count(MAPS) - 1)];
		$this->port = rand(MIN_PORT, MAX_PORT);
	}

	public function setServer(Server $server) {
		$server->verify();

		$this->server = $server;
		$this->setIp($server->ip);
		$this->setPort($server->serverPort);
		$this->setPassword($server->port);
		$this->setRcon($server->rcon);
	}

	public function setTeamA(Team $team) {
		$this->teamA = $team;
		$this->teamAName = $team->name;
		$this->teamABeforeElo = $team->elo;
	}

	public function setTeamB(Team $team) {
		$this->teamB = $team;
		$this->teamBName = $team->name;
		$this->teamBBeforeElo = $team->elo;
	}

	public function getTeamAPlayers() {
		return $this->matchPlayers->filter(function($matchPlayer) {
			return (bool) $matchPlayer->teamA;
		});
	}

	public function getTeamBPlayers() {
		return $this->matchPlayers->filter(function($matchPlayer) {
			return (bool) $matchPlayer->teamB;
		});
	}

	public function getTeamACount() {
		return $this->getTeamAPlayers()->count();
	}

	public function getTeamBCount() {
		return $this->getTeamBPlayers()->count();
	}

	public function isFull() {
		return ! $this->isTeamAOpen() && ! $this->isTeamBOpen();
	}

	public function isTeamAOpen() {
		return ! $this->finished && $this->getTeamACount() < MAX_TEAM_PLAYERS;
	}

	public function isTeamBOpen() {
		return ! $this->finished && $this->getTeamBCount() < MAX_TEAM_PLAYERS;
	}

	private function getNewMatchPlayer(User $user) {
		$matchPlayer = new MatchPlayer();
		$matchPlayer->match = $this;
		$matchPlayer->user = $user;
		$this->matchPlayers->add($matchPlayer);

		return $matchPlayer;
	}

	public function addUserTeamA(User $user) {
		$this->notifyUsers("$user joined $this->teamA");
		$matchPlayer = $this->getNewMatchPlayer($user);
		$matchPlayer->teamA = $this->teamA;

		$user->setMatchPlayer($matchPlayer);
	}

	public function addUserTeamB(User $user, Team $team = null) {
		if (! $this->teamB && $team) {
			$this->teamB = $team;
		}

		$this->notifyUsers("$user joined $this->teamB");
		$matchPlayer = $this->getNewMatchPlayer($user);
		$matchPlayer->teamB = $this->teamB;
		$user->setMatchPlayer($matchPlayer);
	}

	public function clearMatchPlayers() {
		foreach ($this->matchPlayers as $matchPlayer) {
			$matchPlayer->user->leaveMatch();
		}

		$this->matchPlayers->clear();
	}

	public function removeUser(\User $user) {
		$this->matchPlayers->removeElement($user->currentMatchPlayer);
		$user->leaveMatch();

		if ($this->toBeDeleted()) {
			$this->notifyUsers("Match removed by owner");
			$this->clearMatchPlayers();
		} else {
			$this->notifyUsers("$user left match");
		}

		if (! $this->getTeamBCount()) {
			$this->teamB = null;
			$this->teamBName = null;
			$this->teamBBeforeElo = 0;
		}
	}

	public function toBeDeleted() {
		return (! $this->owner->currentMatchPlayer) && ! $this->started;
	}

	public function getStatus() {
		if (! $this->started) {
			$status = 'Not Started';
		} else if ($this->started && ! $this->finished) {
			$status = 'In Progress';
		}

		return $status;
	}

	private function findUdpAddress() {
		$loop = React\EventLoop\Factory::create();
		$udp = new React\Datagram\Factory($loop);
		$port = $this->port;
		$key = 'Kanx Match Key: ' . rand(100000, 999999);
		$udp->createServer("0.0.0.0:$port")->then(function (React\Datagram\Socket $server) use ($key, $port) {
			$server->on('message', function($message, $address, $server) use ($key, $port) {
				if (strpos($message, $key)) {
					$this->udpAddress = $address;
					$this->removeLogAddress($port);
					throw new Exception();
				}
			});
		});

		$loop->addTimer(5, function() {
			$this->removeLogAddress($port);
			throw new Exception('Could not establish communcation with server');
		});

		try {
			$this->addLogAddress($port);
			$this->rcon(["say $key"]);
			$loop->run();
		} catch (Exception $e) {
			if ($e->getMessage()) {
				throw $e;
			}
			$loop->stop();
		}
	}

	public function start() {
		$this->findUdpAddress();
		$output = "/kanx/logs/Match-$this->id";
		$pid = exec("php /kanx/start-match.php $this->id >> $output 2>> $output & echo $!");
		if (! $pid) {
			throw new Exception('Could not start match');
		}

		exec("php /kanx/check-match.php $pid $this->id >> $output 2>> $output &");

		$this->started = true;
		foreach ($this->matchPlayers as $matchPlayer) {
			Notification::add($matchPlayer->user, "Match started please join {$matchPlayer->user->getStartingSide()}");
		}
	}

	public function addHalf(\Half $half) {
		$this->halfs->add($half);
	}

	public function __toString() {
		$string = "Team A: $this->teamA Team B: $this->team B \n $this->teamAScore - $this->teamBScore\n";
		foreach ($this->halfs as $half) {
			$string .= (string) $half;
		}

		return $string;
	}

	public function getSteamProtocol() {
		return "steam://connect/" . $this->server->ip;
	}

	public function notifyUsers($message) {
		foreach ($this->matchPlayers as $matchPlayer) {
			Notification::add($matchPlayer->user, $message);
		}
	}

	public function rcon($commands = []) {
		$query = new SourceQuery();
		$query->connect($this->ip, $this->serverPort, 1, SourceQuery::SOURCE);
		$query->setRconPassword($this->rcon);
		foreach ($commands as $command) {
			$query->rcon($command);
		}
		$query->disconnect();
	}

	public function addLogAddress($port) {
		$this->rcon(["logaddress_add " . EXTERNAL_IP . ":$port"]);
	}

	public function removeLogAddress($port) {
		$this->rcon(["logaddress_del " . EXTERNAL_IP . ":$port"]);
	}

	public function setTeamAAfterElo($elo) {
		$this->teamAAfterElo = $elo;
		$this->teamA->elo = $elo;
	}

	public function setTeamBAfterElo($elo) {
		$this->teamBAfterElo = $elo;
		$this->teamB->elo = $elo;
	}

	public function isTeamA(Team $team) {
		return $this->teamA->id == $team->id ? true : false;
	}
}
