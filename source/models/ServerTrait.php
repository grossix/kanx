<?php

trait ServerTrait {
	/**
	 * @Column (name="ip", type="string")
	 */
	public $ip;

	/**
	 * @Column (name="server_port", type="string")
	 */
	public $serverPort = '27015';

	/**
	 * @Column (name="password", type="string", nullable=true)
	 */
	public $password;

	/**
	 * @Column (name="rcon", type="string")
	 */
	public $rcon;

	public function setIp($ip) {
		$ip = trim($ip);
		if (! $ip) {
			throw new \Exception('IP is required');
		}

		$this->ip = $ip;
	}

	public function setPort($port) {
		$port = trim($port);
		if (! $port) {
			throw new \Exception('Port is required');
		}

		$this->serverPort = $port;
	}

	public function setPassword($password) {
		$this->password = trim($password);
	}

	public function setRcon($rcon) {
		$rcon = trim($rcon);
		if (! $rcon) {
			throw new \Exception('Rcon is required');
		}

		$this->rcon = $rcon;
	}

}
