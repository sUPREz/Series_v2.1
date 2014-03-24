<?php
require('config.php');
switch($_GET['Area'])
{
  case 'Episode':
    switch($_GET['Type'])
    {
      case 'Downloaded':
      case 'Downloaded720':
      case 'Seen':
      case 'Subtitles':
        //function SQLUpdateEpisode( $EpisodeID , $Type , $SerieID , $Value)
        echo SQLUpdateEpisode( $_GET['EpisodeID'] , $_GET['Type'] , $_GET['SerieID'] , $_GET['Value'] );
        break;
      default:
        break;
    }
    break;
  case 'Season':
    $xml = GetLocalSerieFull($_GET['SerieID']);

    foreach($xml->children() as $NodeName => $Node)
    {
      if($NodeName == 'Episode')
      {
        //echo (int)$Node->id.' - '.(int)$Node->SeasonNumber.' - '.$_GET['SeasonNumber'].'<br>';
        if( (int)$Node->SeasonNumber == $_GET['SeasonNumber'] )
        {
          $EpisodesList[(int)$Node->id]['id'] = (int)$Node->id ;
        }
      }
    }
    foreach($EpisodesList as $Episode)
    {
      SQLUpdateEpisode( $Episode['id'] , $_GET['Type'] , $_GET['SerieID'] , $_GET['Value'] );
    }
    break;
  default :
    break;
}
?>