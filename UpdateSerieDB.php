<?php
require('config.php');
switch($_GET['Area'])
{
  case 'Serie':
    switch($_GET['Type'])
    {
      case 'Ignore':
      case 'Seen':
        echo SQLUpdateSerie( $_GET['Type'] , $_GET['SerieID'] , $_GET['Value'] );
        break;
      case 'userID':
        echo SQLUpdateSerie( $_GET['Type'] , $_GET['SerieID'] , $_GET['Value'] );
        break;
      default:
        break;
    }
    break;
  default :
    break;
}
?>