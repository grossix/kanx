<?php
$view->extend('base.php');
$view->slots->set('title', 'New Server');
$view->slots->start('html');
?>

<h1>New Server</h1>
<?php echo $view->includeView('server/form.php', ['server' => $server]) ?>

<?php
$view->slots->stop();
