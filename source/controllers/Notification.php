<?php

namespace Controller;

class Notification extends Base {

	public function get() {
		session_write_close();
		$queryBuilder = $this->entityManager->getRepository('Notification')->createQueryBuilder('n')
			->where('n.user = :user')
			->andWhere('n.datetime >= :datetime')
			->setParameter('datetime', new \DateTime());

		while (true) {
			$user = $this->getCurrentUser();
			if (! $user->currentMatchPlayer) {
				return;
			}

			$results = $queryBuilder->setParameter('user', $user)->getQuery()->getResult();

			$notifications = [];
			foreach ($results as $notification) {
				$notifications[] = $notification->notification;
			}

			if ($notifications) {
				echo json_encode($notifications);
				return;
			}

			sleep(5);
		}
	}
}
