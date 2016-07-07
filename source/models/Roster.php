<?php

/**
 * @Entity
 * @Table (name="rosters", uniqueConstraints={@UniqueConstraint(name="user_team", columns={"user_id", "team_id"})})
 */
class Roster {

	/**
	 * @Id
	 * @Column (type="integer")
	 * @GeneratedValue
	 */
	public $id;

	/**
	 * @ManyToOne (targetEntity="User", inversedBy="rosters")
	 * @JoinColumn (name="user_id")
	 */
	public $user;

	/**
	 * @ManyToOne (targetEntity="Team", inversedBy="roster")
	 * @JoinColumn (name="team_id")
	 */
	public $team;

	public function __construct(\User $user, \Team $team) {
		$this->user = $user;
		$this->team = $team;
	}
}
