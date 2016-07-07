<?php

include 'config.php';

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require __DIR__ . '/vendor/autoload.php';

function debug($stuff, $level = 3) {
	echo '<pre>';
	\Doctrine\Common\Util\Debug::dump($stuff, $level);
	echo '</pre>';
}

$config = Setup::createAnnotationMetadataConfiguration([__DIR__."/source/models"], $development);
$entityManager = EntityManager::create($database, $config);
Notification::$connection = $entityManager->getConnection();
