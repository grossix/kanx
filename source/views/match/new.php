<?php
$view->extend('base.php');
$view->slots->set('title', 'New Match');
$view->slots->start('html');
?>

<h1>New Match</h1>
<?php echo $view->includeView('match/form.php', ['match' => $match]) ?>

<?php
$view->slots->stop();
