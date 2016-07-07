<html>
 <head>
  <title><?php echo $view->slots->output('title') ?></title>
  <link rel='stylesheet' href='/css/normalize.css'>
  <link rel='stylesheet' href='/css/kanx.css'>

  <?php echo $view->slots->output('css') ?>
 </head>
 <body>
  <div id='notification'></div>
  <div id='main-960'>

   <div class='align-center'><?php echo $_SESSION['error']; $_SESSION['error'] = null; ?></div>

   <div class='half'>
<?php if ($sessionUser->canLogout()) : ?>
    <a href='/'>Kanx</a>
    <a href='/logout'>Logout</a>
<?php endif ?>
<?php if ($sessionUser->isLoggedIn()) : ?>
    <a href='/user/<?php echo $sessionUser->id ?>'><?php echo $sessionUser->steamName ?></a>
<?php endif ?>
<?php if ($sessionUser->canLogin()) : ?>
<?php $login = new \Ehesp\SteamLogin\SteamLogin() ?>
<?php $returnUrl = "$_SERVER[REQUEST_SCHEME]://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/login" ?>
    <a href='<?php echo $login->url($returnUrl) ?>'><img src='/images/sits_01.png'></img></a>
<?php endif ?>
  </div>

  <div class='half align-right'>
<?php if ($sessionUser->canCreateTeam()) : ?>
    <a href='/team/new'>New Team</a>
<?php endif ?>
<?php if ($sessionUser->canCreateServer()) : ?>
    <a href='/server/new'>New Server</a>
<?php endif ?>
<?php if ($sessionUser->currentMatchPlayer) : ?>
    <a href='/match/<?php echo $sessionUser->currentMatchPlayer->match->id ?>'>Current Match</a>
<?php endif ?>
<?php if ($sessionUser->canCreateMatch()) : ?>
    <a href='/match/new'>New Match</a>
<?php endif ?>
   </div>

   <?php echo $view->slots->output('html') ?>

  </div>

  <script type='text/javascript'>
   var notifications = <?php echo json_encode((bool) $sessionUser->currentMatchPlayer) ?>;
  </script>
  <script type='text/javascript' src='/js/kanx.js'></script>
  <?php echo $view->slots->output('js') ?>

 </body>
</html>
