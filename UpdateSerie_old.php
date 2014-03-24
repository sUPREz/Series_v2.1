<?php
require('config.php');

if( isset($_GET['SerieID']) )
  echo GetDistantSerieUpdate( $_GET['SerieID'] );
else
  echo '0';

?>