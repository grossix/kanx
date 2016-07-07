<?php

/**
 * @Entity
 * @Table (name="stats")
 */
class Stat {

	/**
	 * @Id
	 * @Column (type="integer")
	 * @GeneratedValue
	 */
	public $id;

	/**
	 * @ManyToOne (targetEntity="User")
	 * @JoinColumn (name="user_id", nullable=false)
	 */
	public $user;

	/**
	 * @ManyToOne (targetEntity="Team")
	 * @JoinColumn (name="team_id", onDelete="SET NULL")
	 */
	public $team;

	/**
	 * @Column (name="team_name", type="string")
	 */
	public $teamName;

	/**
	 * @Column (name="is_team_a", type="boolean")
	 */
	public $isTeamA = false;

	/**
	 * @ManyToOne (targetEntity="Half", inversedBy="stats")
	 * @JoinColumn (name="half_id", nullable=false)
	 */
	public $half;

	/**
	 * @Column (name="frags", type="integer")
	 */
	public $frags = 0;

	/**
	 * @Column (name="assists", type="integer")
	 */
	public $assists = 0;

	/**
	 * @Column (name="deaths", type="integer")
	 */
	public $deaths= 0;

	public function __construct(\Half $half, \Team $team, \User $user) {
		$this->half = $half;
		$this->team = $team;
		$this->teamName = $team->name;
		$this->isTeamA = $half->match->isTeamA($team);
		$this->user = $user;
		$half->addStat($this);
	}

	public function recordTeamkill() {
		$this->frags--;
	}

	public function recordFrag() {
		$this->frags++;
	}

	public function recordDeath() {
		$this->deaths++;
	}

	public function recordAssist() {
		$this->assists++;
	}

	public function __toString() {
		return "Team: $this->team User: $this->user Frags: $this->frags Assists: $this->assists Deaths: $this->deaths\n";
	}
}
