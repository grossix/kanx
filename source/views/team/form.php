<form method='post' action='/team/maintain'>
<input type='hidden' name='id' value='<?php echo $team->id ?>'>
<div>Name</div>
<div><input type='text' name='name' value='<?php echo $team->name ?>'></div>
<div>Password</div>
<div><input type='text' name='password' value='<?php echo $team->password ?>'></div>
<div>
<input type='submit' value='Submit'>
<?php if ($team->id) : ?>
<input type='submit' name='delete' value='Delete'>
<?php endif ?>
</div>
</form>
