<?php
$view->extend('base.php');
$view->slots->set('title', $team->name);
$view->slots->start('html');
?>

<div class='widget'>
 <span class='header'><?php echo $team->name ?></span>
 <?php if ($sessionUser->canMaintainTeam($team)) : ?><a href='/team/edit/<?php echo $team->id ?>'>Edit</a><?php endif ?>
 <?php if ($sessionUser->canLeaveTeam($team)) : ?><a href='/team/leave/<?php echo $team->id ?>'>Leave</a><?php endif ?>
</div>

<div>Owner <a href='/user/<?php echo $team->owner->id ?>'><?php echo $team->owner ?></a></div>

  <?php if ($sessionUser->canJoinTeam($team)) : ?>
  <form style='display: inline-block; margin-bottom: 0px;' method='post' action='/team/join'>
   <input type='hidden' name='teamId' value='<?php echo $team->id ?>'>
   <input type='text' name='password'>
   <input type='submit' value='Join'>
  </form>
  <?php endif ?>

<h2>Roster</h2>

<?php foreach ($team->roster as $roster) : ?>
<div><a href='/user/<?php echo $roster->user->id ?>'><?php echo $roster->user ?></a></div>
<?php endforeach ?>

<h2>Match History</h2>
<?php
$view->slots->stop();
