<form method='post' action='/server/maintain'>
<input type='hidden' name='id' value='<?php echo $server->id ?>'>
<div>IP</div>
<div><input type='text' name='ip' value='<?php echo $server->ip ?>'></div>
<div>Port</div>
<div><input type='text' name='port' value='<?php echo $server->port ?>'></div>
<div>Password</div>
<div><input type='text' name='password' value='<?php echo $server->password ?>'></div>
<div>Rcon</div>
<div><input type='text' name='rcon' value='<?php echo $server->rcon ?>'></div>
<div>
<input type='submit' value='Submit'>
<?php if ($server->id) : ?>
<input type='submit' name='delete' value='Delete'>
<?php endif ?>
</div>
</form>
