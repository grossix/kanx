<?php

namespace Controller;

class Login extends Base {

	public function validate() {
		$login = new \Ehesp\SteamLogin\SteamLogin();
		$steamId = $login->validate();
		$json = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=2554793AAF2DE1267FB4B8EC105F71BC&steamids=' . $steamId);
		$data = json_decode($json, true);
		$steamData = $data['response']['players'][0];
		
		$entityManager = $this->entityManager;
		$user = $entityManager->getRepository('User')->findOneBySteamId($steamId);
		if (! $user) {
			$user = new \User();
			$entityManager->persist($user);
		}

		$user->steamId = $steamId;
		$user->steamName = $steamData['personaname'];

		$entityManager->flush();

		$_SESSION['userId'] = $user->id;

		throw new \Redirect();
	}

	public function logout() {
		session_destroy();
		throw new \Redirect();
	}
}
