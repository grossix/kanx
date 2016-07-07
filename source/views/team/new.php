<?php
$view->extend('base.php');
$view->slots->set('title', 'New Team');
$view->slots->start('html');
?>

<h1>New Team</h1>
<?php echo $view->includeView('team/form.php', ['team' => $team]) ?>

<?php
$view->slots->stop();
