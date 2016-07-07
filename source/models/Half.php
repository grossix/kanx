<?php

/**
 * @Entity
 * @Table (name="halfs")
 */
class Half {

	/**
	 * @Id
	 * @Column (type="integer")
	 * @GeneratedValue
	 */
	public $id;

	/**
	 * @ManyToOne (targetEntity="Match", inversedBy="halfs")
	 * @JoinColumn (name="match_id")
	 */
	public $match;

	/**
	 * @Column (name="description", type="string")
	 */
	public $description;

	/**
	 * @ManyToOne (targetEntity="Team")
	 * @JoinColumn (name="ct_team_id", nullable=true)
	 */
	public $ct;

	/**
	 * @Column (name="ct_score", type="integer")
	 */
	public $ctScore = 0;

	/**
	 * @ManyToOne (targetEntity="Team")
	 * @JoinColumn (name="t_team_id", nullable=true)
	 */
	public $t;

	/**
	 * @Column (name="t_score", type="integer")
	 */
	public $tScore = 0;

	/**
	 * @OneToMany (targetEntity="Stat", mappedBy="half")
	 */
	public $stats;

	private $ctAorB;
	private $tAorB;
	public $previousHalf;

	public function __construct($mixed, $overtimesPlayed = null) {
		$this->stats = new \Doctrine\Common\Collections\ArrayCollection();

		switch (true) {
			case $mixed instanceof \Match:
				$this->setMatch($mixed);
				$oppositeStartingSide = false;
				if ($overtimesPlayed !== null) {
					$oppositeStartingSide = $overtimesPlayed % 2 == 0 ? true : false;
					$overtimesPlayed += 1;
					$description = "Overtime $overtimesPlayed";
				}

				$isCt = $mixed->teamAStartCt;
				if ($oppositeStartingSide) {
					$isCt = ! $isCt;
				}

				if ($isCt) {
					$this->setTeamAToCt($mixed);
				} else {
					$this->setTeamAToT($mixed);
				}

				break;
			case $mixed instanceof \Half:
				$this->setMatch($mixed->match);
				$this->ct = $mixed->t;
				$this->ctAorB = $mixed->tAorB;
				$this->t = $mixed->ct;
				$this->tAorB = $mixed->ctAorB;
				break;
		}

		$this->description = $description ?: 'Regulation';
	}

	private function setMatch(\Match $match) {
		$this->match = $match;
		$match->addHalf($this);
	}

	private function setTeamAToCt(\Match $match) {
		$this->ct = $match->teamA;
		$this->ctAorB = 'A';
		$this->t = $match->teamB;
		$this->tAorB = 'B';
	}

	private function setTeamAToT(\Match $match) {
		$this->t = $match->teamA;
		$this->tAorB = 'A';
		$this->ct = $match->teamB;
		$this->ctAorB = 'B';
	}

	public function addToTeamAScore($score) {
		$property = $this->ctAorB == 'A' ? 'ctScore' : 'tScore';
		$this->$property += $score;
	}

	public function addToTeamBScore($score) {
		$property = $this->ctAorB == 'B' ? 'ctScore' : 'tScore';
		$this->$property += $score;
	}

	public function teamScored($team) {
		switch ($team) {
			case 'TERRORIST':
				$this->tScore++;
				$property = "team$this->tAorB" . "Score";
				break;
			case 'CT':
				$this->ctScore++;
				$property = "team$this->ctAorB" . "Score";
				break;
		}

		$this->match->$property += 1;
	}

	public function getTeamAScore() {
		$property = $this->ctAorB == 'A' ? 'ctScore' : 'tScore';
		$teamAScore = $this->$property;
		if ($this->previousHalf) {
			$teamAScore += $this->previousHalf->getTeamAScore();
		}

		return $teamAScore;
	}

	public function getTeamBScore() {
		$property = $this->ctAorB == 'B' ? 'ctScore' : 'tScore';
		$teamBScore = $this->$property;
		if ($this->previousHalf) {
			$teamBScore += $this->previousHalf->getTeamBScore();
		}

		return $teamBScore;
	}

	public function getTeamASide() {
		return $this->ctAorB == 'A' ? 'CT' : 'TERRORIST';
	}

	public function getTeamBSide() {
		return $this->ctAorB == 'B' ? 'CT' : 'TERRORIST';
	}

	public function getCorrectTeam(\User $user) {
		$validTeamAPlayer = $user->isValidTeamAPlayer($this->match);

		return $validTeamAPlayer ? $this->getTeamASide() : $this->getTeamBSide();
	}

	public function getTeamBySide($side) {
		return $side == 'CT' ? $this->ct : $this->t;
	}

	public function getStat(\User $user, \Team $team) {
		foreach ($this->stats as $stat) {
			if ($stat->team->id == $team->id && $stat->user->id == $user->id) {
				return $stat;
			}
		}
	}

	public function addStat(\Stat $stat) {
		$this->stats->add($stat);
	}

	public function __toString() {
		$string = "CT: $this->ct T: $this->t $this->ctScore - $this->tScore\n";
		foreach ($this->stats as $stat) {
			$string .= (string) $stat;
		}

		return $string;
	}
}
