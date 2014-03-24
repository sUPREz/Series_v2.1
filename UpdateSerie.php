<?php
require('config.php');

if( isset($_POST['ID']) && isset($_POST['Index']) )
{
  echo GetDistantSerieUpdate($_POST['ID']).'|'.$_POST['ID'].'|'.$_POST['Index'];

} else
  echo "_MISSING_PARAMETER";
?>