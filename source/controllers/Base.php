<?php

namespace Controller;

class Base {

	public $entityManager;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}

	public function view($path, $data) {
		return (new \View($this->getCurrentUser(), $path, $data))->generate();
	}

	public function getCurrentUser() {
		$user = $this->entityManager->find('User', trim($_SESSION['userId']));
		if (! $user) {
			$user = new \User();
		}

		return $user;
	}

	public function index() {
		$matches = $this->entityManager->getRepository('Match')->findByStarted(0);

		echo $this->view('index.php', ['user' => $this->getCurrentUser(), 'matches' => $matches]);
	}

	public function __call($function, $arguments) {
		$required = 0;
		preg_match('/^find(?<object>.*)Required$/', $function, $matches);
		if (! $matches['object']) {
			preg_match('/^find(?<object>.*)$/', $function, $matches);
		} else {
			$required = 1;
		}

		if ($matches['object']) {
			$object = $this->entityManager->find("\\$matches[object]", trim($arguments[0]));

			if ($required && ! $object) {
				throw new \Exception("$matches[object] not found");
			}

			return $object;
		}
	}
}
