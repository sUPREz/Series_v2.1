<?php
//echo $db_dsn." - ".$db_user." - ".$db_password."<br>";
try {    
    $dbh = new PDO( $db_dsn , $db_user , $db_password );
} catch(PDOException $e) {
    print $e->getMessage();
}
    
function SQLQuery($query)
{
  global $db_server;
  global $db_user;
  global $db_name;
  global $db_password;

  global $dbh;

  global $_Debug;

  $start = utime();

  /* update to PDO method
  //$db_database = mysql_connect($db_server, $db_user, $db_password) or die('Could not connect: ' . mysql_error());
  //mysql_select_db($db_name,$db_database) or die('Could not select database');
  //$result = mysql_query($query) or die('Query failed: ' . mysql_error());
  //*/

  //echo $query.'<br>';

  $result = $dbh->query( $query );
  
  $end = utime();
  $run = $end - $start;
  $_Debug['SQL'][] = 'Query "'.$query.'" run in '.substr($run, 0, 5).' sec';

  return( $result );
}

function SQLUpdateSerie( $Type , $SerieID , $Value )
{
  $query = "SELECT COUNT(ID) AS AlreadyExist FROM series_series WHERE id='".$SerieID."'";

  $result = SQLQuery($query);

  //$result = mysql_fetch_array($result);
  $result = $result->fetch(PDO::FETCH_ASSOC);
  //print_r_pre( $result );
  if($result['AlreadyExist'] == 0)
  {
    $query = "INSERT INTO series_series (`id` , `".$Type."` )
                     VALUES ('".$SerieID."' , '".$Value."' )";
    $query;
    $result = SQLQuery($query);
  } else {
    $query = "UPDATE series_series
                     SET `".$Type."` = '".$Value."'
                     WHERE `id` = '".$SerieID."'";
    $result = SQLQuery($query);
  }
  $result = $result->fetch(PDO::FETCH_ASSOC);
  //print_r_pre($result);
  return($result);
}

function SQLUpdateEpisode( $EpisodeID , $Type , $SerieID , $Value)
{
  $query = "SELECT COUNT(ID) AS AlreadyExist FROM series_episodes WHERE ID='".$EpisodeID."'";

  $result = SQLQuery($query);
  $result = $result->fetch(PDO::FETCH_ASSOC);
  //$result = mysql_fetch_array($result);
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
  $result = $result->fetch(PDO::FETCH_ASSOC);
  return($result);
}

?>