<?php
require_once __DIR__ . '/../bootstrap.php';

session_start();

$router = new AltoRouter();
$router->map('GET', '/', 'Base#index');

$router->map('GET', '/login', 'Login#validate');
$router->map('GET', '/logout', 'Login#logout');

$router->map('GET', '/user/[i:id]', 'User#index');

$router->map('GET', '/team/new', 'Team#new');
$router->map('GET', '/team/[i:id]', 'Team#index');
$router->map('GET', '/team/edit/[i:id]', 'Team#edit');
$router->map('GET', '/team/leave/[i:id]', 'Team#leave');
$router->map('POST', '/team/join', 'Team#join');
$router->map('POST', '/team/maintain', 'Team#maintain');

$router->map('GET', '/server/new', 'Server#new');
$router->map('GET', '/server/edit/[i:id]', 'Server#edit');
$router->map('POST', '/server/maintain', 'Server#maintain');

$router->map('GET', '/match/new', 'Match#new');
$router->map('GET', '/match/[i:id]', 'Match#index');
$router->map('GET', '/match/leave/[i:id]', 'Match#leave');
$router->map('GET', '/match/start/[i:id]', 'Match#start');
$router->map('POST', '/match/joinA/[i:id]', 'Match#joinA');
$router->map('POST', '/match/joinB/[i:id]', 'Match#joinB');
$router->map('POST', '/match/maintain', 'Match#maintain');

$router->map('GET', '/get-notifications', 'Notification#get');

$match = $router->match();
$target = $match['target'];
$parameters = $match['params'];

try {
	switch (true) {
		case preg_match('/(?<controller>.*)#(?<action>.*)/', $target, $matches):
			$controller = "Controller\\$matches[controller]";
			$action = $matches['action'];
			$controller = new $controller($entityManager, $router);
			$controller->$action($parameters);
			break; 
		default:
			throw new Exception('Page not found');
			break;
	}
} catch (Exception $e) {
	$_SESSION['error'] = $e->getMessage();
	$path = '/';
	if ($e instanceof Redirect) {
		$path = $e->path;
	}

	header("Location: $path");
}
