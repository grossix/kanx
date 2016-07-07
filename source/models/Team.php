<?php

/**
 * @Entity
 * @Table (name="teams")
 */
class Team {

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
	 * @Column (name="name", type="string", unique=true)
	 */
	public $name;

	/**
	 * @Column (name="password", type="string")
	 */
	public $password;

	/**
	 * @Column (name="elo", type="integer")
	 */
	public $elo = 0;

	/**
	 * @OneToMany (targetEntity="\Roster", mappedBy="team", cascade={"persist"}, orphanRemoval=true)
	 */
	public $roster;

	public function __construct() {
		$this->roster = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setName($name) {
		$name = trim($name);
		if (! $name) {
			throw new \Exception('Name is required');
		}

		$this->name = $name;
	}

	public function setPassword($password) {
		$password = trim($password);
		if (! $password) {
			throw new \Exception('Password is required');
		}

		$this->password = $password;
	}

	public function setOwner(\User $user) {
		$this->owner = $user;
		$this->join($user);
	}

	public function addUser(\User $user) {
		$this->roster->add(new \Roster($user, $this));
	}

	public function removeUser(\User $user) {
		foreach ($this->roster as $roster) {
			if ($roster->user->id == $user->id) {
				$this->roster->removeElement($roster);
			}
		}
	}

	public function __toString() {
		return $this->name;
	}

	public function isValidPlayer(\User $user) {
		foreach ($this->roster as $roster) {
			if ($roster->user->id == $user->id) {
				return true;
			}			
		}

		return false;
	}
}
