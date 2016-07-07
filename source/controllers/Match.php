<?php

namespace Controller;

class Match extends Base {

	public function new() {
		if (! $this->getCurrentUser()->canCreateMatch()) {
			throw new \Exception('Cannot create a match');
		}

		echo $this->view('match/new.php', ['match' => new \Match()]);
	}

	public function maintain() {
		$entityManager = $this->entityManager;

		try {
			$currentUser = $this->getCurrentUser();
			$match = $this->findMatch($_POST['id']);
			$newMatch = false;
			if (! $match) {
				$newMatch = true;
				$match = new \Match();
				$entityManager->persist($match);

				$team = $this->findTeamRequired($_POST['teamId']);
				$match->setTeamA($team);

				$match->addUserTeamA($this->getCurrentUser());
				$match->owner = $currentUser;
			}

			if (! $currentUser->canMaintainMatch($match)) {
				throw new \Exception('Cannot maintain this match');
			}

			$server = $this->findServerRequired($_POST['serverId']);
			$match->setServer($server);

			$queryBuilder = $entityManager->getRepository('Match')->createQueryBuilder('m')
				->where('m.server = :server')
				->andWhere('m.finished = 0')
				->setParameter('server', $server);

			if (! $newMatch) {
				$queryBuilder->andWhere('m.id <> :id')
					->setParameter('id', $match->id);
			}

			$openMatches = $queryBuilder->getQuery()->getArrayResult();

			if (count($openMatches) > 0) {
				throw new \Exception('Match already in progress for the server');
			}

			$entityManager->flush();
		} catch (\Exception $e) {
			if ($newMatch) {
				$path = '/match/new';
			} else {
				$path = "/match/edit/$_POST[id]";
			}

			throw new \Redirect($path, $e->getMessage());
		}

		throw new \Redirect("/match/{$match->id}");
	}

	public function index($data) {
		$match = $this->findMatchRequired($data['id']);

		echo $this->view('match.php', ['match' => $match]);
	}

	public function leave($data) {
		$entityManager = $this->entityManager;

		$match = $this->findMatchRequired($data['id']);
		if (! $this->getCurrentUser()->canLeaveMatch($match)) {
			throw new \Exception('Cannot leave this match');
		}

		$match->removeUser($this->getCurrentUser());

		if ($match->toBeDeleted()) {
			$entityManager->remove($match);
		}

		$entityManager->flush();

		throw new \Redirect();
	}

	public function joinA($data) {
		$match = $this->findMatchRequired($data['id']);

		try {
			if (! $this->getCurrentUser()->canJoinMatchTeamA($match)) {
				throw new \Exception('Cannot join this match');
			}

			$match->addUserTeamA($this->getCurrentUser());
			
			$this->entityManager->flush();
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		throw new \Redirect("/match/{$match->id}", $error);
	}

	public function joinB($data) {
		$match = $this->findMatchRequired($data['id']);

		try {
			$team = $this->findTeam($_POST['teamId']);

			if (! $this->getCurrentUser()->canJoinMatchTeamB($match)) {
				throw new \Exception('Cannot join this match');
			}

			$team = $team ?: $match->teamB;
			$match->addUserTeamB($this->getCurrentUser(), $team);

			$this->entityManager->flush();
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		throw new \Redirect("/match/{$match->id}", $error);
	}

	public function start($data) {
		try {
			$match = $this->findMatchRequired($data['id']);

			if (! $this->getCurrentUser()->canStartMatch($match)) {
				throw new \Exception('Cannot start this match');
			}

			$match->server->verify();
			$match->start();
			$this->entityManager->flush();
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		echo json_encode(['error' => $error]);
	}
}
