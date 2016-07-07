<?php

/**
 * @Entity
 * @Table (name="match_players", uniqueConstraints={@UniqueConstraint(name="user_match", columns={"user_id", "match_id"})})
 */
class MatchPlayer {

	/**
	 * @Id
	 * @Column (type="integer")
	 * @GeneratedValue
	 */
	public $id;

	/**
	 * @ManyToOne (targetEntity="User")
	 * @JoinColumn (name="user_id")
	 */
	public $user;

	/**
	 * @ManyToOne (targetEntity="Match", inversedBy="matchPlayers")
	 * @JoinColumn (name="match_id")
	 */
	public $match;

	/**
	 * @ManyToOne (targetEntity="Team")
	 * @JoinColumn (name="team_a_id")
	 */
	public $teamA;

	/**
	 * @ManyToOne (targetEntity="Team")
	 * @JoinColumn (name="team_b_id")
	 */
	public $teamB;
}
