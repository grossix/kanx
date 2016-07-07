<?php

/**
 * @Entity
 * @Table (name="servers")
 */
class Server {
	use ServerTrait;

	/**
	 * @Id
	 * @Column (type="integer")
	 * @GeneratedValue
	 */
	public $id;

	/**
	 * @ManyToOne (targetEntity="User", inversedBy="servers")
	 * @JoinColumn (name="owner_id")
	 */
	public $owner;

	public function __toString() {
		return "$this->ip:$this->serverPort";
	}

	public function verify() {
		$query = new \xPaw\SourceQuery\SourceQuery();
		$query->connect($this->ip, $this->serverPort, 1, \xPaw\SourceQuery\SourceQuery::SOURCE);
		$query->setRconPassword($this->rcon);

		preg_match('/game_mode. . .(?<game_mode>.)/', $query->rcon('game_mode'), $matches);
		if ($matches['game_mode'] != '1') {
			throw new \Exception('Server must be classic competitive');
		}

		preg_match('/game_type. . .(?<game_type>.)/', $query->rcon('game_type'), $matches);
		if ($matches['game_type'] != '0') {
			throw new \Exception('Server must be classic competitive');
		}

		$response = explode("\n", $query->rcon('log'));
		if (! preg_match('/currently logging to: .*console/', $response[1])) {
			throw new \Exception('Logging not active on server');
		}

		$response = explode("\n", $query->rcon('logaddress_list'));
		foreach ($response as $address) {
			if (preg_match('/no addresses in the list/', $address)) {
				break;
			}

			if (preg_match('/' . EXTERNAL_IP . '/', $address)) {
				$query->rcon("logaddress_del $address");
			}
		}

		$query->disconnect();
	}
}
