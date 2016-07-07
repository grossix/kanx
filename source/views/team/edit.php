<?php
$view->extend('base.php');
$view->slots->set('title', "Edit {$team->name}");
$view->slots->start('html');
?>

<h1>Edit <?php echo $team->name ?></h1>
<?php echo $view->includeView('team/form.php', ['team' => $team]) ?>

<?php
$view->slots->stop();
