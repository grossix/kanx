<form method='post' action='/match/maintain'>
<input type='hidden' name='id' value='<?php echo $match->id ?>'>
<div>Server</div>
<div>
 <select name='serverId'>

<?php foreach ($sessionUser->servers as $server) : ?>
  <option <?php echo $server->id == $match->server->id ? 'selected="selected"' : null ?> value='<?php echo $server->id ?>'><?php echo $server->ip ?></option>
<?php endforeach ?>

 </select>
</div>

<div>

<?php if (! $match->id) : ?>
 <div>Team</div>
 <select name='teamId'>

<?php foreach ($sessionUser->rosters as $roster) : ?>
<?php $team = $roster->team ?>
  <option value='<?php echo $team->id ?>'><?php echo $team->name ?></option>
<?php endforeach ?>

 </select>
<?php endif ?>

</div>

<div><input type='submit' value='Submit'></div>
</form>
