<?php
$view->extend('base.php');
$view->slots->set('title', "Match {$match->id}");
$view->slots->start('html');
?>

<div>

<?php if ($sessionUser->canStartMatch($match)) : ?>
<button onclick="startMatch('/match/start/<?php echo $match->id ?>')">Start</button>
<?php endif ?>
<?php if ($sessionUser->canConnect($match)) : ?>
<a href='<?php echo $match->getSteamProtocol() ?>'><button>Connect</button></a>
<?php endif ?>
<?php if ($sessionUser->canLeaveMatch($match)) : ?>
<a href='/match/leave/<?php echo $match->id ?>'>Leave</a>
<?php endif ?>

</div>

<div>Owner <a href='/user/<?php echo $match->owner->id ?>'><?php echo $match->owner ?></a></div>
<div>Server <?php echo $match->server ?></div>
<div><?php if ($match->started): ?>Map <?php echo $match->map ?><?php endif ?></div>
<div><?php echo $match->getStatus() ?></div>

<div class='forth'>
<h2><?php echo $view->showTeam($match->teamA) ?></h2>

<?php if ($sessionUser->canJoinMatchTeamA($match)) : ?>
<form method='post' action='/match/joinA/<?php echo $match->id ?>'>
 <input type='submit' value='Join'>
</form>
<?php endif ?>

<?php foreach ($match->getTeamAPlayers() as $matchPlayer) : ?>
<div><?php echo $view->showPlayer($matchPlayer->user) ?></div>
<?php endforeach ?>
</div>

<div class='forth align-right'><h1><?php echo $match->teamAScore ?></h1></div>
<div class='forth'><h1><?php echo $match->teamBScore ?></h1></div>

<div class='forth align-right'>
<h2><?php echo $view->showTeam($match->teamB) ?></h2>

<?php if ($sessionUser->canJoinMatchTeamB($match)) : ?>
<form method='post' action='/match/joinB/<?php echo $match->id ?>'>
 <?php if (! $match->teamB) : ?>
 <select name='teamId'>
 <?php foreach ($sessionUser->rosters as $roster) : ?>
 <?php if ($roster->team->id == $match->teamA->id) : ?>
 <?php continue ?>
 <?php endif ?>
  <option value='<?php echo $roster->team->id ?>'><?php echo $roster->team ?></option>
 <?php endforeach ?>
 </select>
 <?php endif ?>
 <input type='submit' value='Join'>
</form>
<?php endif ?>

<?php foreach ($match->getTeamBPlayers() as $matchPlayer) : ?>
<div><?php echo $view->showPlayer($matchPlayer->user) ?></div>
<?php endforeach ?>
</div>

<?php
$view->slots->stop();
$view->slots->start('js');
?>

<script type='text/javascript'>

function startMatch(action) {
	getJSON(action, function(json) {});
}
</script>

<?php
$view->slots->stop();
