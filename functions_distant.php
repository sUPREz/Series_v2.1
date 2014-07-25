<?php
function GetMirrors( )
{
  global $_APIKey;
  global $_Mirrors;

  if( $_Mirrors == '_NONE' )
  {
    $xmlfile = "http://www.thetvdb.com/api/".$_APIKey."/mirrors.xml";
    $xml = LoadXMLFile($xmlfile);
    if( $xml != '_MISSING_FILE' )
    {
      foreach ($xml->children() as $Mirror) {
        //echo "-".$Mirror->id." ".$Mirror->mirrorpath." ".$Mirror->typemask."<br>";
        $Mirrors[(int)$Mirror->id]['mirrorpath'] = (string)$Mirror->mirrorpath;
        $Mirrors[(int)$Mirror->id]['typemask'] = (int)$Mirror->typemask;
      }
      $_Mirrors = $Mirrors;
      return($Mirrors);
    }
    else
      return( '_MISSING_FILE' );
  } else
    return($_Mirrors);
}

function GetDistantUserFavorites( $copy = FALSE )
{
  global $_AccountIdentifier;
  global $_UserFavoritesSeries;
  global $_DataPath;
  global $_UserFavoriteFileName;

  $Mirrors = GetMirrors(); // ['mirrorpath']['typemask']
  //print_r($Mirrors);

  //*
  if( $Mirrors != '_MISSING_FILE' )
  {
    if( $_UserFavoritesSeries == '_NONE' )
    {
      $xmlfile = $Mirrors[1]['mirrorpath']."/api/User_Favorites.php?accountid=".$_AccountIdentifier;
      //echo '<a href="'.$xmlfile.'">XML</a>';

      if( $copy ){
        $Dest = $_DataPath.$_UserFavoriteFileName;
        $copy = CopyFile( $xmlfile , $Dest );
        $xmlfile = $Dest;
      }

      $xml = LoadXMLFile($xmlfile);
      //echo $xml.'<br />';

      if( $xml != '_MISSING_FILE' )
      {
        foreach ($xml->children() as $Serie) {
          $UserFavoritesSeries[] = (int)$Serie;
        }
        $_UserFavoritesSeries = $UserFavoritesSeries;
        return($UserFavoritesSeries);
      }
      else
        return('_ERROR');
    } else
      return($_UserFavoritesSeries);
  } else
    return ('_ERROR');
  //*/
}

function GetDistantSerieFull( $SerieID , $alternate = FALSE )
{
  global $_APIKey;
  global $_SelectedLanguage;
  global $_DataPath;
  global $_BackupMirror;

  $Mirrors = GetMirrors();

  //*
  $xmlfile2 = $_BackupMirror."/api/".$_APIKey."/series/".$SerieID."/all/".$_SelectedLanguage.".xml";
  $xmlfile = $Mirrors[1]['mirrorpath']."/api/".$_APIKey."/series/".$SerieID."/all/".$_SelectedLanguage.".xml";

  $Dest = $_DataPath.$SerieID.'_full.xml';

  if( $alternate )
    $copy = CopyFile( $xmlfile2 , $Dest );
  else
    $copy = CopyFile( $xmlfile , $Dest );

  if( @simplexml_load_file($Dest) !== FALSE )
    return( $copy );
  else if(!$alternate)
    GetDistantSerieFull( $SerieID , TRUE );
  else
    return( '_ERROR' );

  //*/
}

function GetDistantSerieBase( $SerieID , $alternate = FALSE )
{
  global $_APIKey;
  global $_SelectedLanguage;
  global $_DataPath;
  global $_BackupMirror;

  $Mirrors = GetMirrors();

  $xmlfile2 = $_BackupMirror."/api/".$_APIKey."/series/".$SerieID."/".$_SelectedLanguage.".xml";
  $xmlfile = $Mirrors[1]['mirrorpath']."/api/".$_APIKey."/series/".$SerieID."/".$_SelectedLanguage.".xml";

  $Dest = $_DataPath.$SerieID.'_base.xml';

  if( $alternate )
    $copy = CopyFile( $xmlfile2 , $Dest );
  else
    $copy = CopyFile( $xmlfile , $Dest );

  if( @simplexml_load_file($Dest) !== FALSE )
    return( $copy );
  else if(!$alternate)
    GetDistantSerieBase( $SerieID , TRUE );
  else
    return( '_ERROR' );
}

function GetDistantSerieLastUpdated($SerieID)
{
  global $_APIKey;
  global $_SelectedLanguage;

  $Mirrors = GetMirrors();

  $xmlfile = $Mirrors[1]['mirrorpath']."/api/".$_APIKey."/series/".$SerieID."/".$_SelectedLanguage.".xml";
  $xml = LoadXMLFile($xmlfile);

  return((int)$xml->Series->lastupdated);
}

function CheckSubtiltesAddicted( $Episode )
{
  global $CONTENT_LINK;
  global $_Series;
  global $_Debug;

  $start = utime();
  //http://www.addic7ed.com/serie/[[SerieName]]/[[SeasonNumber]]/[[EpisodeNumber]]/[[EpisodeName]]
  $SerieName = str_replace( ' ' , '_' , $_Series[ $Episode['SerieID'] ]['SeriesName'] );
  switch( $SerieName ){
    case "Grey's_Anatomy":
      $SerieName = "Grey%27s_Anatomy";
      break;
	case "House_of_Cards_(US)":
	  $SerieName = "House%20of%20Cards%20(2013)";
	  break;
    case "The_Americans_(2013)":
	  $SerieName = "The_Americans";
	  break;
  }
  $Addic7ed = str_replace( '[[SerieName]]' , $SerieName , $CONTENT_LINK['addic7ed_href'] );
  $Addic7ed = str_replace( '[[SeasonNumber]]' , $Episode['SeasonNumber'] , $Addic7ed );
  $Addic7ed = str_replace( '[[EpisodeNumber]]' , $Episode['EpisodeNumber'] , $Addic7ed );
  //$Addic7ed = str_replace( '[[EpisodeName]]' , $Episode['EpisodeName'] , $Addic7ed );
  //echo '<a href="'.$Addic7ed.'" >'.$SerieName.' '.$Episode['SeasonNumber'].' '.$Episode['EpisodeNumber'].'</a> - ';

  $html = file_get_html( $Addic7ed );



  if( strpos( $html->plaintext , "Couldn't find any subs with the specified language. Filter ignored" ) !== false )
  {
    $return[] = array( '_NONE' , $Addic7ed );
    //echo 'NO FRENCH SUBS YET<br />';
  }
  else
  {
    $tables = $html->find('table.tabel95 table.tabel95');
    //echo sizeof( $tables ).' - '.gettype( $tables ).'<br />';
    if( sizeof( $tables ) < 1 )
      $return[] = array( '_NONE' , $Addic7ed );
    else
    {
      foreach( $tables as $table )
      {
        //echo $table.'<br />';
        $version = $table->find('td.NewsTitle');
        $version = substr( $version[0]->plaintext , 8 , strpos( $version[0]->plaintext , ',' ) - 8  );
        $HD = $table->find('img[title=720/1080]');
        $Completed = $table->find('b' , 0);
        $Link = $table->find('a.buttonDownload');

        //echo '$Completed: '.$Completed.'<br />';
        //var_dump( $Completed );

        $link_base = 'http://www.addic7ed.com/';

        $return_completed = '';

        if( $HD[0]->tag == 'img' )
          $version .= ' (HD)';
        if( strpos( $Completed->innertext , '%' ) !== false ){
          $return_completed = substr( $Completed->innertext.' - ' , 0 , strpos( $Completed->innertext , '%' ) +1 );
        }
        $return[] = array( $version , $return_completed , $link_base.$Link[0]->getAttribute('href') , $Addic7ed );
        //var_dump( $return );
      }
    }
  }

  $end = utime();
  $run = $end - $start;

  $_Debug ['Addic7ed'][] = $Addic7ed.' loaded in '.substr($run, 0, 5).' sec';
  return( $return );
}

function CheckSubtiltesXML( $SerieID , $SeasonNumber , $EpisodeNumber )
{
  global $_Series;
  global $_ST_EU;
  global $CONTENT_TEXT;
  global $CONTENT_TEXT;
  global $CONTENT_LINK;
  global $_ST_EU_DownCounter;
  global $_Messages;

  $_ST_EU_DownCounter = 0;

  if($EpisodeNumber < 10)
    $EpisodeNumber = "0".$EpisodeNumber;

  $SerieName = $_Series[$SerieID]['SeriesName'];
  switch($SerieName)
  {
    case 'Shameless (US)':
      $SerieName = 'shameless_US';
      break;
    case 'V (2009)':
    case 'v (2009)':
      $SerieName = 'v_2009';
      break;
    case "Him & Her":
      $SerieName = "him_her";
      break;
    default:
      $SerieName = strtolower( $SerieName );

      $firstpos = strpos( $SerieName , '('  );
      $lastpos = strpos( $SerieName , ')' );
      if( $firstpos && $lastpos )
        $SerieName = substr( $SerieName , 0 , $firstpos );

      $SerieName = trim($SerieName);

      $SerieName = str_replace(' ' , '_' , $SerieName);
      $SerieName = str_replace("'" , '' , $SerieName);
      $SerieName = str_replace("-" , '' , $SerieName);
      $SerieName = str_replace("." , '' , $SerieName);
      break;
  }


  if($_ST_EU_DownCounter < 2)
  {
    if( !$_ST_EU[$SerieID] )
    {
      $xmlfile = 'http://www.sous-titres.eu/series/'.$SerieName.'.xml';
      $xml = LoadXMLFile($xmlfile);
      //print_r($xml);
      if( $xml != '_MISSING_FILE' )
        $_ST_EU[$SerieID] = $xml;
      else
      {
        $_ST_EU[$SerieID] = '_MISSING_FILE';
        $_ST_EU_DownCounter ++;
      }
    }

    if( $_ST_EU[$SerieID] != '_MISSING_FILE')
    {
      foreach ($_ST_EU[$SerieID]->channel->children() as $NodeName1 => $Node1)
      {
        if((string)$NodeName1 == 'item')
        {
          if( strpos($Node1->title , $SeasonNumber.'x'.$EpisodeNumber) &&
              (!strpos($Node1->title , 'EN') || strpos($Node1->title , 'FR') ) )
            $result = (string)$Node1->link;
        }
      }

      if($result)
        return( str_replace( '[[URL]]' , $result , $CONTENT_LINK['SousTitre.euFound']) );
      else
        return( $CONTENT_TEXT['SousTitre.euNotFound'].' '.str_replace( '[[SERIE]]' , $SerieName , $CONTENT_LINK['SousTitre.euNotFound']) );
    }
    else
    {
      $_Messages[] = str_replace( '[[SERIE]]' , $_Series[$SerieID]['SeriesName'] , $CONTENT_TEXT['SousTitre.euFileNotFound']);
      return( $CONTENT_TEXT['SousTitre.euFileNotFoundShort'].' '.str_replace( '[[SERIE]]' , $SerieName , $CONTENT_LINK['SousTitre.euNotFound']) );
    }
  }
  $_Messages[] = $CONTENT_TEXT['SousTitre.euDown'];
}

function GetDistantSerieUpdate( $SerieID )
{
  global $_Messages;
  global $_Debug;
  global $CONTENT_TEXT;

  $_Debug['php'][] = $SerieID;

  $a = GetDistantSerieFull( $SerieID );
  // TODO : Rajouter test sur le XML copié !
  $b = GetDistantSerieBase( $SerieID );
  // TODO : Rajouter test sur le XML copié !
  if($a && $b)
  {
    $xml = GetLocalSerieBase( $SerieID );
    $_Messages[] .= str_replace( '[[SeriesName]]' , (string)$xml->Series->SeriesName , $CONTENT_TEXT['SerieUpdated'] );
    return(1);
  }
  else
    return(0);
}

function GetDistantUserFavoriteUpdate()
{
  global $_Messages;
  global $CONTENT_TEXT;

  $a = GetDistantUserFavorites( TRUE );
  // TODO : Rajouter test sur le XML copié !
  if($a)
  {
    $xml = GetLocalSerieBase( $SerieID );
    $_Messages[] .= $CONTENT_TEXT['UserFavoritesUpdated'];
    return(1);
  }
  else
    return(0);
}
?>