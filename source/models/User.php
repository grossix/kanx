<?php

/**
 * @Entity
 * @Table (name="users")
 */
class User {

	/**
	 * @Id
	 * @Column (type="integer")
	 * @GeneratedValue
	 */
	public $id;

	/**
	 * @Column (name="steam_id", type="string", unique=true)
	 */
	public $steamId;

	/**
	 * @Column (name="steam_name", type="string")
	 */
	public $steamName;

	/**
	 * @ManyToOne (targetEntity="MatchPlayer")
	 * @JoinColumn (name="current_match_player_id", onDelete="SET NULL")
	 */
	public $currentMatchPlayer;

	/**
	 * @OneToMany (targetEntity="Roster", mappedBy="user")
	 */
	public $rosters;

	/**
	 * @OneToMany (targetEntity="Server", mappedBy="owner")
	 */
	public $servers;

	public function __construct() {
		$this->rosters = new \Doctrine\Common\Collections\ArrayCollection();
		$this->servers = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setMatchPlayer(MatchPlayer $matchPlayer) {
		$this->currentMatchPlayer = $matchPlayer;
	}

	public function leaveMatch() {
		$this->currentMatchPlayer = null;
	}

	public function isInMatch(Match $match) {
		return ($this->currentMatchPlayer->match->id == $match->id);
	}

	public function getStartingSide() {
		$match = $this->currentMatchPlayer->match;
		if ($match->teamAStartCt) {
			return $this->isValidTeamAPlayer($match) ? 'CT' : 'Terrorist';
		} else {
			return $this->isValidTeamAPlayer($match) ? 'Terrorist' : 'CT';
		}
	}

	public function isValidTeamAPlayer(Match $match) {
		return $this->isInMatch($match) && $this->currentMatchPlayer->teamA;
	}

	public function isValidTeamBPlayer(Match $match) {
		return $this->isInMatch($match) && $this->currentMatchPlayer->teamB;
	}

	public function isOwnerOf($object) {
		return ($object->owner->id == $this->id);
	}

	public function __toString() {
		return $this->steamName;
	}

	public function isLoggedIn() {
		return $_SESSION['userId'] ? $_SESSION['userId'] == $this->id : false;
	}

	public function canLogin() {
		return ! $this->isLoggedIn();
	}

	public function canLogout() {
		return $this->isLoggedIn();
	}

	public function canMaintainMatch(\Match $match) {
		return $this->isOwnerOf($match) && $this->isLoggedIn() && ! $match->started;
	}

	public function canStartMatch(Match $match) {
		return $this->canMaintainMatch($match) && $match->isFull();
	}

	public function canLeaveMatch(Match $match) {
		return $this->isLoggedIn() && $this->isInMatch($match);
	}

	public function canJoinMatchTeamA(Match $match) {
		return $this->isLoggedIn() && $match->teamA->isValidPlayer($this) && $match->isTeamAOpen() && ! $this->currentMatchPlayer;
	}

	public function canJoinMatchTeamB(Match $match) {
		if ($match->teamB) {
			$bool = $match->teamB->isValidPlayer($this);
		} else {
			$bool = $this->rosters->count() > 0;
		}

		return $this->isLoggedIn() && $bool && $match->isTeamBOpen() && ! $this->currentMatchPlayer;
	}

	public function canConnect(Match $match) {
		return $this->isLoggedIn() && $this->isInMatch($match) && $match->started && ! $match->finished;
	}	

	public function canCreateMatch() {
		return $this->isLoggedIn() && ! $this->currentMatchPlayer && $this->servers->count() && $this->rosters->count();
	}

	public function canCreateTeam() {
		return $this->isLoggedIn();
	}

	public function canMaintainTeam(\Team $team) {
		return $this->isOwnerOf($team) && $this->isLoggedIn();
	}

	public function canLeaveTeam(\Team $team) {
		return $this->isLoggedIn() && ! $this->isOwnerOf($team) && $team->isValidPlayer($this);
	}

	public function leaveTeam(\Team $team) {
		if (! $this->canLeaveTeam($team)) {
			throw new \Exception("Cannot leave $team");
		}

		$team->removeUser($this);
	}

	public function canJoinTeam(\Team $team) {
		return $this->isLoggedIn() && ! $team->isValidPlayer($this);
	}

	public function joinTeam(\Team $team, $password) {
		if (! $this->canJoinTeam($team) || $password != $team->password) {
			throw new \Exception("Cannot join $team");
		}

		$team->addUser($this);
	}

	public function canCreateServer() {
		return $this->isLoggedIn();
	}

	public function canMaintainServer(\Server $server) {
		return $this->isOwnerOf($server) && $this->isLoggedIn();
	}
}
