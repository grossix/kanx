<?php
$view->extend('base.php');
$view->slots->set('title', "Edit {$server->ip}");
$view->slots->start('html');
?>

<h1>Edit <?php echo $server->ip ?></h1>
<?php echo $view->includeView('server/form.php', ['server' => $server]) ?>

<?php
$view->slots->stop();
