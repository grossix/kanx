<?php
$view->extend('base.php');
$view->slots->set('title', 'Kanx');
$view->slots->start('html');
?>

<h1>Matches</h1>

<table>
 <tbody>
<?php foreach ($matches as $match) : ?>

  <tr>
   <td><?php echo $view->showTeam($match->teamA, $match->teamAName) ?></td>
   <td><?php echo $match->teamABeforeElo ?>
   <td><?php echo "{$match->getTeamACount()}/5" ?>
   <td><?php echo $view->showTeam($match->teamB, $match->teamBName) ?></td>
   <td><?php echo $match->teamBBeforeElo ?>
   <td><?php echo "{$match->getTeamBCount()}/5" ?>
   <td><a href='/match/<?php echo $match->id ?>'>View</a></td>
  </tr>

<?php endforeach ?>

<?php if (! $matches) : ?>
  <tr><td colspan='3'>No Available Matches Found</td></tr>
<?php endif ?>

 </tbody>
</table>

<?php
$view->slots->stop();
