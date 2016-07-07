<?php

include 'bootstrap.php';

$loop = React\EventLoop\Factory::create();

$pid = $argv[1];
$matchId = $argv[2];

if (! $pid || ! $matchId) {
	exit;
}

$loop->addPeriodicTimer(MATCH_TIMEOUT, function(React\EventLoop\Timer\Timer $timer) use ($loop, $pid, $matchId, $entityManager) {
	if (! file_exists("/proc/$pid")) {
		$match = $entityManager->find('Match', trim($matchId));
		if ($match && ! $match->finished) {
			$match->notifyUsers('Match Dead: Unknown error');
			$match->clearMatchPlayers();
			$entityManager->remove($match);
			$entityManager->flush();
		}

		$loop->cancelTimer($timer);
	}
});

$loop->run();
