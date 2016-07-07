<?php

namespace Controller;

class Server extends Base {

	public function new() {
		if (! $this->getCurrentUser()->canCreateServer()) {
			throw new \Exception('Cannot create a server');
		}

		echo $this->view('server/new.php', ['server' => new \Server()]);
	}

	public function edit($data) {
		$server = $this->findServerRequired($data['id']);

		if (! $this->getCurrentUser()->canMaintainServer($server)) {
			throw new \Exception('Cannot maintain this server');
		}

		echo $this->view('server/edit.php', ['server' => $server]);
	}

	public function maintain() {
		$entityManager = $this->entityManager;
		try {
			$currentUser = $this->getCurrentUser();

			$server = $this->findServer($_POST['id']);
			$newServer = false;
			if (! $server) {
				$newServer = true;
				$server = new \Server();
				$server->owner = $currentUser;
				$entityManager->persist($server);
			}

			if (! $currentUser->canMaintainServer($server)) {
				throw new \Exception('Cannot maintain this server');
			}

			$server->setIp($_POST['ip']);
			$server->setPort($_POST['port']);
			$server->setPassword($_POST['password']);
			$server->setRcon($_POST['rcon']);

			if (isset($_POST['delete'])) {
				$entityManager->remove($server);
				$deleted = true;
			}

			$entityManager->flush();

		} catch (\Exception $e) {
			if ($newServer) {
				$path = '/server/new';
			} else {
				$path = "/server/edit/$_POST[id]";
			}

			throw new \Redirect($path, $e->getMessage());
		}

		if ($deleted) {
			throw new \Redirect('/', 'Server deleted successfully');
		} else {
			throw new \Redirect("/user/{$this->getCurrentUser()->id}");
		}
	}
}
