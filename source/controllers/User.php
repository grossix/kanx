<?php

namespace Controller;

class User extends Base {

	public function index($data) {
		$user = $this->findUserRequired($data['id']);

		echo $this->view('user.php', ['user' => $user]);
	}

}
