<?php
function SQLQuery($query)
{
  global $db_server;
  global $db_user;
  global $db_name;
  global $db_password;

  global $_Debug;

  $start = utime();

  $db_database = mysql_connect($db_server, $db_user, $db_password) or die('Could not connect: ' . mysql_error());
  mysql_select_db($db_name,$db_database) or die('Could not select database');
  $result = mysql_query($query) or die('Query failed: ' . mysql_error());

  $end = utime();
  $run = $end - $start;
  $_Debug['SQL'][] = 'Query "'.$query.'" run in '.substr($run, 0, 5).' sec';

  return( $result );
}

function SQLUpdateSerie( $Type , $SerieID , $Value )
{
  $query = "SELECT COUNT(ID) AS AlreadyExist FROM series_series WHERE id='".$SerieID."'";

  $result = SQLQuery($query);
  $result = mysql_fetch_array($result);
  if($result['AlreadyExist'] == 0)
  {
    $query = "INSERT INTO series_series (`id` , `".$Type."` )
                     VALUES ('".$SerieID."' , '".$Value."' )";
    $query;
    $result = SQLQuery($query);
  } else {
    echo $query = "UPDATE series_series
                     SET `".$Type."` = '".$Value."'
                     WHERE `id` = '".$SerieID."'";
    $result = SQLQuery($query);
  }
  return($result);
}

function SQLUpdateEpisode( $EpisodeID , $Type , $SerieID , $Value)
{
  $query = "SELECT COUNT(ID) AS AlreadyExist FROM series_episodes WHERE ID='".$EpisodeID."'";

  $result = SQLQuery($query);
  $result = mysql_fetch_array($result);
  if($result['AlreadyExist'] == 0)
  {
    $query = "INSERT INTO series_episodes (id , ".$Type." , SerieID)
                     VALUES ('".$EpisodeID."' , '".$Value."' , '".$SerieID."')";
    $result = SQLQuery($query);
  } else
  {
    $query = "UPDATE series_episodes
                     SET ".$Type." = '".$Value."' , SerieID = '".$SerieID."'
                     WHERE id='".$EpisodeID."'";
    $result = SQLQuery($query);
  }
  return($result);
}

?>