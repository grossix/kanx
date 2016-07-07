<?php
$view->extend('base.php');
$view->slots->set('title', $user->steamName);
$view->slots->start('html');
?>

<h1><?php echo $user->steamName ?></h1>

<?php if ($user->currentMatchPlayer) : ?>
<div><a href='/match/<?php echo $user->currentMatchPlayer->match->id ?>'>Current Match</a></div>
<?php endif ?>

<h2>Teams</h2>

<?php foreach ($user->rosters as $roster) : ?>
<div><a href='/team/<?php echo $roster->team->id ?>'><?php echo $roster->team->name ?></a></div>
<?php endforeach ?>

<h2>Servers</h2>

<?php foreach ($user->servers as $server) : ?>
<?php if ($sessionUser->canMaintainServer($server)) : ?>
<div><a href='/server/edit/<?php echo $server->id ?>'><?php echo $server ?></a></div>
<?php else : ?>
<div><?php echo $server ?></div>
<?php endif ?>
<?php endforeach ?>

<h2>Match History</h2>
<?php
$view->slots->stop();
