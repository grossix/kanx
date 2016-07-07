<?php

include 'bootstrap.php';

try {
	$loop = React\EventLoop\Factory::create();

	$match = $entityManager->find('Match', trim($argv[1]));
	if (! $match) {
		throw new \Exception('Invalid match id: ' . trim($argv[1]));
	}

	$port = $match->port;

	$matchBot = new MatchBot($match);
	$matchBot->entityManager = $entityManager;
	$matchBot->port = $port;
	$matchBot->cfg = new \Cfg();

	$udp = new React\Datagram\Factory($loop);
	$udp->createServer("0.0.0.0:$port")->then(function (React\Datagram\Socket $server) use ($matchBot) {
		$server->on('message', function($message, $address, $server) use ($matchBot) {
			$matchBot->getMessage($message, $address);
		});
	});

	$matchBot->start();
	$loop->addTimer(MATCH_TIMEOUT, function() use ($matchBot) {
		if (! $matchBot->live) {
			throw new Exception('Match not live before timeout');
		}
	});

	$loop->run();
} catch (\Exception $e) {
	$loop->stop();

	if ($match) {
		$match->removeLogAddress($port);
		$entityManager->beginTransaction();

		if ($e instanceof FinishedMatch) {
			echo "Match Finished\n";
			$match->notifyUsers('Match Finished');
			$match->clearMatchPlayers();
			$match->finished = 1;
			$eloResults = EloCalculator::calculate($match->teamABeforeElo, $match->teamAScore, $match->teamBBeforeElo, $match->teamBScore);
			$match->setTeamAAfterElo($eloResults['elo1']);
			$match->setTeamBAfterElo($eloResults['elo2']);
		} else {
			echo "Match Removed: $e->getMessage() \n";
			$match->notifyUsers('Match Removed: ' . $e->getMessage());
			$match->clearMatchPlayers();
			$entityManager->remove($match);
			$matchBot->say('Match Removed: ' . $e->getMessage());
		}

		try {
			$entityManager->flush();
			$entityManager->commit();
		} catch (Exception $e) {
			$entityManager->rollback();
			$entityManager->getConnection()->executeQuery("delete from match_players where match_id = $match->id");
			$entityManager->getConnection()->executeQuery("delete from matches where id = $match->id");
			$match->notifyUsers('Match Removed: ' . $e->getMessage());
			$matchBot->say('Match Removed: ' . $e->getMessage());
		}
	}

	if ($matchBot) {
		echo "Invalid Addresses\n";
		print_r($matchBot->invalidAddresses ?: 'No Invalid Addresses');
	}

	echo $e->getMessage() . "\n";
}
