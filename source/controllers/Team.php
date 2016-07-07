<?php

namespace Controller;

class Team extends Base {

	public function new() {
		if (! $this->getCurrentUser()->canCreateTeam()) {
			throw new \Exception('Cannot create a team');
		}

		echo $this->view('team/new.php', ['team' => new \Team()]);
	}

	public function edit($data) {
		$team = $this->findTeamRequired($data['id']);

		if (! $this->getCurrentUser()->canMaintainTeam($team)) {
			throw new \Exception('Cannot maintain this team');
		}

		echo $this->view('team/edit.php', ['team' => $team]);
	}

	public function maintain() {
		$entityManager = $this->entityManager;
		try {
			$currentUser = $this->getCurrentUser();

			$team = $this->findTeam($_POST['id']);
			$newTeam = false;
			if (! $team) {
				$newTeam = true;
				$team = new \Team();
				$team->owner = $currentUser;
				$team->addUser($currentUser);
				$entityManager->persist($team);
			}

			if (! $currentUser->canMaintainTeam($team)) {
				throw new \Exception('Cannot maintain this team');
			}

			$team->setName($_POST['name']);
			$team->setPassword($_POST['password']);

			$queryBuilder = $entityManager->getRepository('Team')->createQueryBuilder('t')
				->where('t.name = :name')
				->setParameter('name', $team->name);

			if (! $newTeam) {
				$queryBuilder->andWhere('t.id <> :id')
					->setParameter('id', $team->id);
			}

			$existing = $queryBuilder->getQuery()->getArrayResult();

			if (count($existing) > 0) {
				throw new \Exception("Team already exists with the name '$team->name'");
			}

			if (isset($_POST['delete'])) {
				$entityManager->remove($team);
				$deleted = true;
			}

			$entityManager->flush();

		} catch (\Exception $e) {
			if ($newTeam) {
				$path = '/team/new';
			} else {
				$path = "/team/edit/$_POST[id]";
			}

			throw new \Redirect($path, $e->getMessage());
		}

		if ($deleted) {
			throw new \Redirect('/', 'Team deleted successfully');
		} else {
			throw new \Redirect("/team/{$team->id}");
		}
	}

	public function index($data) {
		$team = $this->findTeamRequired($data['id']);
		echo $this->view('team.php', ['team' => $team]);
	}

	public function join() {
		$team = $this->findTeamRequired($_POST['teamId']);

		try {
			$this->getCurrentUser()->joinTeam($team, $_POST['password']);
			$this->entityManager->flush();
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		throw new \Redirect("/team/{$team->id}", $error);
	}

	public function leave($data) {
		$team = $this->findTeamRequired($data['id']);

		try {
			$this->getCurrentUser()->leaveTeam($team);
			$this->entityManager->flush();
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		throw new \Redirect("/team/{$team->id}", $error);
	}
}
