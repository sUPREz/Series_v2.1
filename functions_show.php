<?php
function ShowSeriesList()
{
  global $_GET;
  global $CONTENT_TEXT;
  global $CONTENT_LINK;
  global $CONTENT_HEADER;
  global $_UserDefault;
  global $_SortSerieOrder;
  global $_Content;
  global $_Messages;
  global $_Debug;

  switch( $_GET['FilterType'] ){
    case 'userID':
      $_Content .= "<h1>".$CONTENT_HEADER['SerieList']." - ".$CONTENT_HEADER['User'.$_GET['FilterValue']]."</h1>";
      break;
    default:
      $_Content .= "<h1>".$CONTENT_HEADER['SerieList']."</h1>";
      break;
  }

  $UserFavoritesSeries = GetLocalUserFavorites();
  //$UserFavoritesSeries = GetDistantUserFavorites();

  //*
  if( $UserFavoritesSeries != '_ERROR' )
  {

    $query = "SELECT * FROM series_series";
    switch( $_GET['FilterType'] ){
      case 'userID':
        $query .= " WHERE `userID`='".$_GET['FilterValue']."'";
        break;
    }
    //echo $query;
    $result = SQLQuery($query);

    //print_r_pre( $result );

    //while( $Serie = mysql_fetch_array($result) ){
    while ( $Serie = $result->fetch(PDO::FETCH_ASSOC) ){
      $SerieListFromDB[$Serie['id']]['id'] = $Serie['id'];
      $SerieListFromDB[$Serie['id']]['Ignore'] = $Serie['Ignore'];
      $SerieListFromDB[$Serie['id']]['Seen'] = $Serie['Seen'];
      $SerieListFromDB[$Serie['id']]['userID'] = $Serie['userID'];
    }
    //print_r_pre( $UserFavoritesSeries );
    //print_r_pre( $SerieListFromDB );
    foreach( $UserFavoritesSeries as $SerieID )
    {
      //print_r_pre( $SerieListFromDB );
      //print_r_pre( $SerieID );
      if( IsIn($SerieListFromDB , $SerieID)
      || ( !isset($_GET['FilterType']) && !isset($_GET['FilterValue']) )
        ){
        $xml = GetLocalSerieBase($SerieID);

        if( $xml != '_MISSING_FILE')
        {
          $SeriesList[$SerieID]['id'] =           $SerieID;
          $SeriesList[$SerieID]['SeriesName'] =   (string)$xml->Series->SeriesName;

          $SeriesList[$SerieID]['Ignore'] =       $SerieListFromDB[$SerieID]['Ignore'];
          $SeriesList[$SerieID]['Seen'] =       $SerieListFromDB[$SerieID]['Seen'];
          if( isset($SerieListFromDB[$SerieID]['userID']) )
            $SeriesList[$SerieID]['userID'] =       $SerieListFromDB[$SerieID]['userID'];
          else
            $SeriesList[$SerieID]['userID'] = $_UserDefault;

          if( (string)$xml->Series->Status == '' )
            $SeriesList[$SerieID]['Status'] = 'Pending';
          else
            $SeriesList[$SerieID]['Status'] =     (string)$xml->Series->Status;

          $SeriesList[$SerieID]['LastUpdated'] =  (int)$xml->Series->lastupdated;
          $SeriesList[$SerieID]['DistantLastUpdated'] = (int)$xml->Series->lastupdated;

        } else
        {
          $SeriesList[$SerieID]['id'] =           $SerieID;
          $SeriesList[$SerieID]['SeriesName'] =   '_MISSING_FILE';
          $SeriesList[$SerieID]['Status'] =       '_MISSING_FILE';
          $SeriesList[$SerieID]['LastUpdated'] =  '_MISSING_FILE';
        }
      }
    }
    if( is_array($SeriesList) ){
      //$Format = 'Num|SeriesName|id|LastUpdated|DistantXML|Status|TVDB_Series|Ignore|Infos';
      $Format = 'Num|SeriesName|id|TVDB_Series|Status|Ignore|Seen|User|Infos';
      $Sort = 'SeriesName';

      ShowSerieList( $SeriesList , $Format , $Sort );

      $_Messages[] = '
      <div id="ProgressContainer" style="height:10px;width:452px;background-color:#FFF;border:1px solid #666">
        <div id="UpdateProgress" style="position:relative;left:1px;top:1px;height:8px;width:0px;background-color:#0C0;"></div>
      </div>';

      $_Messages[] = GetJSSeriesList($SeriesList);
      //$_Messages[] = '<script>'.$CONTENT_TEXT['CheckUpdateSerieAll'].'</script>';
      //$_Messages[] = ;
      //$_Messages[] = str_replace( '[[MAX]]' , count($SeriesList) , $CONTENT_TEXT['CheckUpdateSerieAll'] );
    }
  } else
    $_Messages[] = $CONTENT_TEXT['TVDB.Down'];

  //*/
}

function GetJSSeriesList( $SeriesList )
{
  global $CONTENT_TEXT;
  //echo "_Tournaments = [];";
  //echo "_Tournaments.push( [ -1 ,\"No Tournament\" , -1 , -1 , \"No Tournament\" ] );";
  $Script = '
  <div style="float:left;">
    <a name="UpdateAllSeries" href="#">'.$CONTENT_TEXT['UpdateSerie'].'</a>
  </div>';
  //<div style="float:left;" id="CheckUpdateAllSeriesStatus">0 / '.count($SeriesList).'</div>';

  $Script .= "<script>";

  $temp  = '_UpdateSerieAll = \'<div style="float:left;" class="GreenText">'.$CONTENT_TEXT['UpdateSerieStatus'].' :&nbsp;</div>';
  $temp .= '<div style="float:left;" id="UpdateAllSeriesStatus">0 / 0</div>\';';
  $Script .= $temp;

  /*
  $temp  = '_UpdateNeeded = \'<div style="float:left;" class="OrangeText">'.$CONTENT_TEXT['UpdateNeeded'].' :&nbsp;</div>';
  $temp .= '<div style="float:left;" id="UpdateNeeded">0 / '.count($SeriesList).'</div>\';';
  $Script .= $temp;
  //*/

  $temp  = '_UpdateErrors = \'<div style="float:left;" class="RedText">'.$CONTENT_TEXT['UpdateErrors'].' :&nbsp;</div>';
  $temp .= '<div style="float:left;" id="UpdateErrors">0</div>\';';
  $Script .= $temp;

  //UpdateNeededErrors
  //$Script .= '_JsUpdateNeeded = "'.$CONTENT_TEXT['JsUpdateNeeded'].'";';
  $Script .= '_JsNoUpdateNeeded  = "'.$CONTENT_TEXT['JsNoUpdateNeeded'].'";';
  $Script .= '_JsUpdateSerieEnd  = "'.$CONTENT_TEXT['JsUpdateSerieEnd'].'";';

  $Script .= '_JsErrorScript = "'.$CONTENT_TEXT['JsErrorScript'].'";';
  $Script .= '_JsErrorServer = "'.$CONTENT_TEXT['JsErrorServer'].'";';


  $Script .= "SerieList = [];";
  foreach( $SeriesList as $Serie )
    $Script .= "SerieList.push( [ \"".$Serie['id']."\" , \"".$Serie['SeriesName']."\" , \"".$Serie['LastUpdated']."\" ] );";

  $Script .= "</script>";
  return($Script);
}

function ShowSerieList( $SeriesList , $Format , $Sort )
{
  global $_Content;

  global $CONTENT_HEADER;
  global $CONTENT_TEXT;
  global $CONTENT_LINK;
  global $CONTENT_FORM;
  global $CONTENT_HTML;

  global $_APIKey;
  global $_SelectedLanguage;
  global $_DataPath;

  global $_Series;
  global $_Users;
  global $_SortSerieOrder;

  $_SortSerieOrder = $Sort;
  uasort( $SeriesList , 'SortSeries' );

  $Format = explode( '|' , $Format );

  $_Content .= '<table><tbody>';
  $_Content .= '<tr>';
  foreach( $Format as $column )
  {
    $_Content .= '<th class="'.$column.'">'.$CONTENT_HEADER[$column].'</th>';
  }
  $_Content .= '</tr>';

  $counter = 1;
  $i = 1;
  foreach( $SeriesList as $Serie )
  {
    //print_r( $Serie );
    if($counter)
    {
      $DefaultClass = "DarkGrey1";
      $counter = 0;
    } else
    {
      $DefaultClass = "DarkGrey2";
      $counter++;
    }

    $_Content .= '<tr>';
    foreach( $Format as $column )
    {
      $name = $column.'|'.$Serie['id'];
      if( $column == 'Status')
      {
        if( $Serie[$column] == 'Ended' )
          $_Content .= '<td data-series-serieID="'.$Serie['id'].'" name="'.$name.'" class="Red">';
        else if( $Serie[$column] == 'Continuing' )
          $_Content .= '<td data-series-serieID="'.$Serie['id'].'" name="'.$name.'" class="Green">';
        else
          $_Content .= '<td data-series-serieID="'.$Serie['id'].'" name="'.$name.'" class="Yellow">';
      }
      else
        $_Content .= '<td data-series-serieID="'.$Serie['id'].'" name="'.$name.'" class="'.$DefaultClass.'">';

      switch($column)
      {
        case 'User':
          //$_Content .= $Serie['userID'];

          $_Content .= str_replace( '[[User]]' , $_Users[ $Serie['userID'] ] , $CONTENT_HTML['User'] );
          break;
        case 'Status':
          $_Content .= $CONTENT_TEXT[$Serie[$column]];
          break;
        case 'SeriesName':
          $LocalContent = str_replace( '[[SerieID]]', $Serie['id'] , $CONTENT_LINK['SeriesPage2'] );
          $LocalContent = str_replace( '[[SeriesName]]', $Serie['SeriesName'] , $LocalContent );
          $_Content .= $LocalContent;
          break;
        case 'SeriePage':
          $_Content .= str_replace( '[[SerieID]]', $Serie['id'] , $CONTENT_LINK['SeriesPage'] );
          break;
        case 'Infos':
          if( $Serie['SeriesName'] == "_MISSING_FILE" )
            $_Content .= $CONTENT_TEXT['NoSerieData'].' - '.str_replace( '[[SerieID]]', $Serie['id'] , $CONTENT_LINK['UpdateSerie'] );
          else if( $Serie['LastUpdated'] != $Serie['DistantLastUpdated'] )
            $_Content .= $CONTENT_TEXT['SerieOutdated'].' - '.str_replace( '[[SerieID]]', $Serie['id'] , $CONTENT_LINK['UpdateSerie'] );
          break;
        case 'Num':
          $_Content .= $i;
          break;
        case 'Seen':
          if( $Serie['Seen'] )
            $value = "checked";
          else
            $value = '';

          $LocalContent = str_replace( '[[SerieID]]' , $Serie['id'] , $CONTENT_FORM['SeenSerie'] );
          $LocalContent = str_replace( '[[Value]]' , $value , $LocalContent );
          $_Content .= $LocalContent;
          break;
        case 'Ignore';
          if( $Serie['Ignore'] )
            $value = "checked";
          else
            $value = '';

          $LocalContent = str_replace( '[[SerieID]]' , $Serie['id'] , $CONTENT_FORM['Ignore'] );
          $LocalContent = str_replace( '[[Value]]' , $value , $LocalContent );
          $_Content .= $LocalContent;
          break;
        case 'TVDB_Series':
          $_Content .= str_replace( '[[SerieID]]' , $Serie['id'] , $CONTENT_LINK['TVDB_Series'] );
          break;
        case 'DistantXML':
          $Mirrors = GetMirrors();
          $xmlfile = $Mirrors[1]['mirrorpath']."/api/".$_APIKey."/series/".$Serie['id']."/".$_SelectedLanguage.".xml";
          $_Content .= str_replace( '[[LIEN]]' , $xmlfile , $CONTENT_LINK['DistantXML'] );
          break;
        default:
          $_Content .= $Serie[$column];
          break;
      }

      $_Content .= '</td>';
    }
    $i ++;
    $_Content .= '</tr>';
  }
  $_Content .= '</tbody></table>';
}

function ShowSerie($SerieID)
{
  global $CONTENT_TEXT;
  global $CONTENT_LINK;
  global $_Series;
  global $_Debug;
  global $_Content;

  $xml = GetLocalSerieFull($SerieID);

  $_Series[$SerieID]['id'] =           $SerieID;
  $_Series[$SerieID]['SeriesName'] =   (string)$xml->Series->SeriesName;

  if( (string)$xml->Series->Status == "" )
    $_Series[$SerieID]['Status'] = "Pending";
  else
    $_Series[$SerieID]['Status'] =     (string)$xml->Series->Status;

  $_Series[$SerieID]['LastUpdated'] =  (int)$xml->Series->lastupdated;


  $_Content .= '<h1>'.$_Series[$SerieID]['SeriesName'].'</h1>';

  $_Content .= '<h2>Status : '.$CONTENT_TEXT[$_Series[$SerieID]['Status']].'</h2>';

  $_Content .= '<h3>';
  $_Content .= str_replace( '[[SerieID]]' , $SerieID , $CONTENT_LINK['TVDB_Series'] );
  $_Content .= ' - ';
  $_Content .= str_replace( '[[SeriesName]]' , $_Series[$SerieID]['SeriesName'] , $CONTENT_LINK['Wikipedia'] );
  $_Content .= '</h3>';


  foreach($xml->children() as $NodeName => $Node)
  {
    if($NodeName == 'Episode')
    {
      $query = "SELECT * FROM series_episodes WHERE ID='".(int)$Node->id."'";
      //$result = mysql_fetch_array( SQLQuery($query) );

      $result = SQLQuery($query);
      $result = $result->fetch(PDO::FETCH_ASSOC);

      //print_r_pre($result);

      if( !$result['Seen'] || 1 )
      {
        $EpisodesList[(int)$Node->id]['Downloaded'] =     $result['Downloaded'];
        $EpisodesList[(int)$Node->id]['Downloaded720'] =  $result['Downloaded720'];
        $EpisodesList[(int)$Node->id]['Subtitles'] =      $result['Subtitles'];
        $EpisodesList[(int)$Node->id]['Seen'] =           $result['Seen'];

        //echo $Node->SeasonNumber.' - '.$Node->EpisodeNumber.' - '.$Node->EpisodeName.'<br>';
        $EpisodesList[(int)$Node->id]['id'] =              (int)$Node->id ;
        $EpisodesList[(int)$Node->id]['EpisodeName'] =     (string)$Node->EpisodeName ;
        $EpisodesList[(int)$Node->id]['SeasonNumber'] =    (int)$Node->SeasonNumber ;
        $EpisodesList[(int)$Node->id]['EpisodeNumber'] =   (int)$Node->EpisodeNumber ;
        $EpisodesList[(int)$Node->id]['FirstAired'] =      (string)$Node->FirstAired ;
        $EpisodesList[(int)$Node->id]['LastUpdated'] =     (int)$Node->lastupdated ;
        $EpisodesList[(int)$Node->id]['SerieID'] =         (int)$Node->seriesid ;
        $EpisodesList[(int)$Node->id]['SeasonID'] =        (int)$Node->seasonid ;
      }
    }
  }

  if( gettype($EpisodesList) == 'array' )
  {
    ShowSeasonFlags($EpisodesList);

    $Format = 'Num|EpisodeYxZ|EpisodeName2|FirstAired|Downloaded|Downloaded720|Subtitles|Seen|ExternalLinks';
    $Sort = 'SeasonNumber|EpisodeNumber|EpisodeName';
    ShowEpisodeList( $EpisodesList , $Format , $Sort );
  } else
  {
    $_Content .= $CONTENT_TEXT['NoEpisodes'];
  }
}

function ShowEpisodeList( $EpisodesList , $Format , $Sort )
{
  $start = utime();

  global $_Content;
  global $_Debug;
  global $_Messages;

  global $CONTENT_TEXT;
  global $CONTENT_FORM;
  global $CONTENT_HEADER;
  global $CONTENT_LINK;

  global $_Series;
  //print_r( $_Series );
  global $_SortEpisodeOrder;

  $_SortEpisodeOrder = $Sort;

  //print_r_pre($EpisodesList);
  if( $EpisodesList )
  {
    uasort( $EpisodesList , 'SortEpisodes' );

    $Format = explode( '|' , $Format );

    $_Content .= '<table><tbody>';
    $_Content .= '<tr>';
    foreach( $Format as $column )
    {
      $_Content .= '<th class="'.$column.'">'.$CONTENT_HEADER[$column].'</th>';
    }
    $_Content .= '</tr>';


    //usort( $LocalEpisodesList , 'SortEpisodeList' );
    $counter = 1;
    $i=1;

    foreach( $EpisodesList as $Episode )
    {
      $_Content .= '<tr>';

      if($counter)
      {
        $DefaultClass = "DarkGrey1";
        $counter = 0;
      } else
      {
        $DefaultClass = "DarkGrey2";
        $counter++;
      }

      foreach( $Format as $column )
      {
        if( $column == 'FirstAired')
        {
          if( date('Y-m-d') <= $Episode['FirstAired'] )
            $_Content .= '<td class="Red">';
          else if( $Episode['Seen'] )
            $_Content .= '<td class="Green">';
          //else if( ($Episode['Downloaded'] || $Episode['Downloaded720'] ) &&
          else if( $Episode['Downloaded'] &&
                    $Episode['Subtitles'] )
            $_Content .= '<td class="Blue">';
          //else if( ($Episode['Downloaded'] || $Episode['Downloaded720'] ) ||
          else if( $Episode['Downloaded'] ||
                    $Episode['Subtitles'] )
            $_Content .= '<td class="Orange">';
          else if( date('Y-m-d') > $Episode['FirstAired'] )
            $_Content .= '<td class="Yellow">';
          else
            $_Content .= '<td>';
        }
        else
          $_Content .= '<td class="'.$DefaultClass.'">';

        switch($column)
        {
          case 'ExternalLinks':
            $Subtitles = false;
            $CheckSubtiltesXML = false;
            $_Flags['CheckSousTitresEUSubtitltes'] = true;
            if( ( $Episode['Downloaded'] ) &&
                  !$Episode['Subtitles'] && !$Episode['Seen']  )
            {
              if( $_Flags['CheckSousTitresEUSubtitltes'] === true ){
                $CheckSubtiltesXML = CheckSubtiltesXML( $Episode['SerieID'] , $Episode['SeasonNumber'] , $Episode['EpisodeNumber'] );
                //$CheckSubtiltesXML = false;
                $_Content .= '&nbsp;'.$CheckSubtiltesXML.'&nbsp;';
              }

              //echo $CONTENT_TEXT['SousTitre.euFileNotFoundShort'];
              $_Flags['CheckAddictedSubtitltes'] = true;

              if( $_Flags['CheckAddictedSubtitltes'] === true &&
                  (
                    $CheckSubtiltesXML === false ||
                    strpos( $CheckSubtiltesXML , $CONTENT_TEXT['SousTitre.euNotFound'] ) !== false ||
                    strpos( $CheckSubtiltesXML , $CONTENT_TEXT['SousTitre.euFileNotFoundShort'] ) !== false
                  )
                )
              {
                $CheckSubtiltesAddicted = CheckSubtiltesAddicted( $Episode );
                //var_dump($CheckSubtiltesAddicted);
                $FoundValidAddictedSub = false;
                foreach( $CheckSubtiltesAddicted as $version ){
                  if( $version[1] == '' ) {
                    $_Content .= ' | <a target="blank" href="'.$version[2].'">'.$version[0].'</a> ';
                    $FoundValidAddictedSub = true;
                    $link = $version[3];
                  } else if( $version[0] == '_NONE' ) {
                    $_Content .= ' | '.$version[0].'&nbsp;';
                    $link = $version[1];
                  } else {
                    $_Content .= ' | '.$version[0]." ".$version[1];
                    $link = $version[3];
                  }
                }
                if( !$FoundValidAddictedSub )
                  $_Content .= '&nbsp;'.str_replace( '[[URL]]' , $link , $CONTENT_LINK['addic7ed.euNotFound'] );
              }
              $Subtitles = true;
              /*
              $_Content .= ' | ';

              //*/

            }
            if( $Episode['FirstAired'] < date('Y-m-d') &&
                //( !$Episode['Downloaded'] || !$Episode['Downloaded720'] ) &&
                ( !$Episode['Downloaded'] ) &&
                  !$Episode['Seen'] )
            {
              if( $Episode['SeasonNumber'] < 10 )
                $SeasonNumber = '0'.$Episode['SeasonNumber'];
              else
                $SeasonNumber = $Episode['SeasonNumber'];

              if( $Episode['EpisodeNumber'] < 10 )
                $EpisodeNumber = '0'.$Episode['EpisodeNumber'];
              else
                $EpisodeNumber = $Episode['EpisodeNumber'];

              $SearchText = str_replace( ' ' , '+' , $_Series[$Episode['SerieID']]['SeriesName']).'+S'.$SeasonNumber.'E'.$EpisodeNumber;
              $SearchText = str_replace( "'" , '' , $SearchText );

              if( $Episode['Downloaded'] )
                $SearchText .= '+720p';
              //echo $SearchText.$CONTENT_LINK['btjunkie.org'];
              if( $Subtitles )
                $_Content .= ' | ';

              //$_Content .= '&nbsp;'.str_replace( '[[TEXT]]' , $SearchText , $CONTENT_LINK['btjunkie.org'] ).'&nbsp;';
              $_Content .= '&nbsp;'.str_replace( '[[TEXT]]' , $SearchText , $CONTENT_LINK['thepiratebay.org']  ).'&nbsp;';
              $_Content .= '&nbsp;|&nbsp;'.str_replace( '[[TEXT]]' , $SearchText , $CONTENT_LINK['google'] ).'&nbsp;';
            }
            else
              $_Content .= '&nbsp;';
            break;
          case 'Downloaded':
          case 'Downloaded720':
          case 'Subtitles':
          case 'Seen':
            $LocalContent = str_replace( '[[EpisodeID]]' , $Episode['id'] , $CONTENT_FORM[$column] );
            $LocalContent = str_replace( '[[SerieID]]' , $Episode['SerieID'] , $LocalContent );
            //Checked ??
            if( $Episode[$column] )
              $LocalContent = str_replace( '[[Value]]' , 'checked' , $LocalContent );
            else
              $LocalContent = str_replace( '[[Value]]' , '' , $LocalContent );
            $_Content .= $LocalContent;
            break;
          case 'SeriesName':
            $_Content .= '&nbsp;'.$_Series[$Episode['SerieID']]['SeriesName'].'&nbsp;';
            break;
          case 'SeriesName2':
            $LocalContent = str_replace( '[[SerieID]]' , $Episode['SerieID'] , $CONTENT_LINK['SeriesPage2'] );
            $LocalContent = str_replace( '[[SeriesName]]' , $_Series[$Episode['SerieID']]['SeriesName'] , $LocalContent );
            $_Content .= '&nbsp;'.$LocalContent.'&nbsp;';
            break;
          case 'FirstAired':
            if( $Episode[$column] != "" ){
              $_Content .= utf8_decode( ucwords( strftime("%A %d %B %Y", strtotime($Episode[$column]) ) ) ) ;
              //$_Content .= ucwords( strftime("%A %d %B %Y", strtotime($Episode[$column]) ) );
			}
            break;
          case 'EpisodeName2':
            $LocalContent = str_replace( '[[SerieID]]' , $Episode['SerieID'] , $CONTENT_LINK['TVDB_Episodes'] );
            $LocalContent = str_replace( '[[SeasonID]]' , $Episode['SeasonID'] , $LocalContent );
            $LocalContent = str_replace( '[[EpisodeID]]' , $Episode['id'] , $LocalContent );
            $LocalContent = str_replace( '[[EpisodeName]]' , $Episode['EpisodeName'] , $LocalContent );
            $_Content .= $LocalContent;
            break;
          case 'EpisodeYxZ':
            $_Content .= 'S'.$Episode['SeasonNumber'].' E'.$Episode['EpisodeNumber'];
            break;
          case 'Num':
            $_Content .= $i;
            break;
          default:
            $_Content .= $Episode[$column];
            break;
        }
        $_Content .= '</td>';
      }
      $i++;
      $_Content .= '</tr>';
    }
    $_Content .= '</tbody></table>';
  } else
  $_Messages[] = $CONTENT_TEXT['NoEpisodes'];

  $end = utime();
  $run = $end - $start;
  $_Debug['RenderTime'][] = 'ShowEpisodeList run in '.substr($run, 0, 5).' sec';
}

function ShowCalendar()
{
  global $_Series;
  global $_Messages;
  global $_Content;
  global $_Debug;
  global $CONTENT_HEADER;
  global $CONTENT_TEXT;
  global $_GET;
  $UserFavoritesSeries = GetLocalUserFavorites();
  //$UserFavoritesSeries = GetDistantUserFavorites();

  if( $UserFavoritesSeries != '_ERROR' )
  {
    $start = utime();

    $query = "SELECT * FROM series_series";
    switch( $_GET['FilterType'] ){
      case 'userID':
        $query .= " WHERE `userID`='".$_GET['FilterValue']."'";
        break;
    }
    $result = SQLQuery($query);

    while ( $Serie = $result->fetch(PDO::FETCH_ASSOC) ){
    //while( $Serie = mysql_fetch_array($result) ){

	  /*
	  echo "caca ";
	  print_r($Serie);
	  echo "<BR>";
	  //*/
      $SerieListFromDB[$Serie['id']]['id'] = $Serie['id'];
      $SerieListFromDB[$Serie['id']]['Ignore'] = $Serie['Ignore'];
      $SerieListFromDB[$Serie['id']]['Seen'] = $Serie['Seen'];
      $SerieListFromDB[$Serie['id']]['userID'] = $Serie['userID'];
    }
	
	//print_r_pre( $SerieListFromDB );
    $query = "SELECT * FROM `series_episodes`";
//    $query = "SELECT `series_episodes`.ID as ID,SerieID,Downloaded,Downloaded720,Subtitles,Seen
/*
    $query = "SELECT *
              FROM `series_episodes` , `series_series`
              WHERE `series_episodes`.`SerieID` = `series_series`.`id` ";
    switch( $_GET['FilterType'] ){
      case 'userID':
        $query .= " AND `userID`='".$_GET['FilterValue']."'";
        break;
    }
    echo $query;
//*/
    $result = SQLQuery($query);

    //while( $Episode = mysql_fetch_array($result) )

    while ( $Episode = $result->fetch(PDO::FETCH_ASSOC) ){
      $EpisodeListFromDB[$Episode['SerieID']][$Episode['ID']] = $Episode;
    }

    //print_r($EpisodeListFromDB);
    switch( $_GET['FilterType'] ){
      case 'userID':
        $_Content .= "<h1>".$CONTENT_HEADER['Calendar']." - ".$CONTENT_HEADER['User'.$_GET['FilterValue']]."</h1>";
        break;
      default:
        $_Content .= "<h1>".$CONTENT_HEADER['Calendar']."</h1>";
        break;
    }

    foreach( $UserFavoritesSeries as $SerieID )
    {
      //if( $SerieListFromDB[$SerieID]['userID'] != $_GET['FilterValue'] )
	  /*
	  // 2014.03.17	  
	  //var_dump($SerieListFromDB);
	  //echo "caca".$SerieID."<br>";
	  echo "ID: ".$SerieID;
	  if( isset($SerieListFromDB[$SerieID]) )
		echo " ISSET";
	  else
		echo " ISNOTSET";
	  echo "<BR>";
	  //*/
	  //print_r_pre( $SerieListFromDB[$SerieID] );
	  //if( isset($SerieListFromDB[$SerieID]) )
	  //{	  
	  //print_r_pre( $SerieListFromDB[$SerieID] );
	  if( !$SerieListFromDB[$SerieID]['Ignore']
		  && ( IsIn($SerieListFromDB , $SerieID)
		  || ( !isset($_GET['FilterType']) && !isset($_GET['FilterValue']) ) )
	  ) {
		//if( !isset($_GET['FilterType']) && $SerieListFromDB[$SerieID]['userID'] != $_GET['FilterValue'] ){
		$xml = GetLocalSerieFull($SerieID);
		//echo $xml.' ('.$SerieID.')<br />';
		if( $xml != '_MISSING_FILE')
		{
		  $_Series[$SerieID]['id'] =           $SerieID;
		  $_Series[$SerieID]['SeriesName'] =   (string)$xml->Series->SeriesName;

		  if( (string)$xml->Series->Status == '' )
			$_Series[$SerieID]['Status'] = 'Pending';
		  else
			$_Series[$SerieID]['Status'] =     (string)$xml->Series->Status;

		  $_Series[$SerieID]['LastUpdated'] =  (int)$xml->Series->LastUpdated;

		  $start1 = utime();



		  //$_Debug['Arrays1'][] = $EpisodeListFromDB;
		  $NextEpisode = false;

		  foreach($xml->children() as $NodeName => $Node)
		  {
			//echo $NodeName;
			if($NodeName == 'Episode')
			{

			  //$query = "SELECT * FROM series_episodes WHERE ID='".(int)$Node->id."'";
			  //$result = mysql_fetch_array( SQLQuery($query) );

			  //$NextWeek = time() + (7 * 24 * 60 * 60);
			  $NextWeek = time() + (30 * 24 * 60 * 60);
			  $NextWeek = date('Y-m-d' , $NextWeek);

			  //echo (string)$Node->FirstAired.' <= '.$NextWeek.'<br>';
			  /*
			  if( ( !$EpisodeListFromDB[$SerieID][(int)$Node->id]['Downloaded'] ||
					!$EpisodeListFromDB[$SerieID][(int)$Node->id]['Downloaded720'] ||
					!$EpisodeListFromDB[$SerieID][(int)$Node->id]['Subtitles'] ||
					!$EpisodeListFromDB[$SerieID][(int)$Node->id]['Seen'] ) &&
				  ( (string)$Node->FirstAired <= $NextWeek || !$NextEpisode ) &&
				  (string)$Node->FirstAired != '' )
			  //*/
			  
			  /*
			  // 2014.03.17  
			  echo "ID: ".(int)$Node->id;
			  if( isset($EpisodeListFromDB[$SerieID][(int)$Node->id]) )
				echo " ISSET";
			  else
				echo " ISNOTSET";
			  echo "<BR>";
			  //*/
			  
			  // 2014.03.17  
			  if( ( (int)$Node->SeasonNumber != 0 &&
					!$EpisodeListFromDB[$SerieID][(int)$Node->id]['Seen'] ) &&
				  //( (string)$Node->FirstAired <= $NextWeek || !$NextEpisode ) &&
					//(string)$Node->FirstAired <= $NextWeek &&
					!$NextEpisode &&
					(string)$Node->FirstAired != '' )
			  {
				//if( (string)$Node->FirstAired )
				if( (string)$Node->FirstAired > date('Y-m-d') )
				  $NextEpisode = true;

				//print_r_pre($EpisodeListFromDB[$SerieID]);
				$EpisodesList[(int)$Node->id]['Downloaded'] =     $EpisodeListFromDB[$SerieID][(int)$Node->id]['Downloaded'];
				$EpisodesList[(int)$Node->id]['Downloaded720'] =  $EpisodeListFromDB[$SerieID][(int)$Node->id]['Downloaded720'];
				$EpisodesList[(int)$Node->id]['Subtitles'] =      $EpisodeListFromDB[$SerieID][(int)$Node->id]['Subtitles'];
				$EpisodesList[(int)$Node->id]['Seen'] =           $EpisodeListFromDB[$SerieID][(int)$Node->id]['Seen'];

				//echo $Node->SeasonNumber.' - '.$Node->EpisodeNumber.' - '.$Node->EpisodeName.'<br>';
				$EpisodesList[(int)$Node->id]['id'] =              (int)$Node->id ;
				$EpisodesList[(int)$Node->id]['EpisodeName'] =     (string)$Node->EpisodeName ;
				$EpisodesList[(int)$Node->id]['EpisodeNumber'] =   (int)$Node->EpisodeNumber ;
				$EpisodesList[(int)$Node->id]['SeasonNumber'] =    (int)$Node->SeasonNumber ;
				$EpisodesList[(int)$Node->id]['FirstAired'] =      (string)$Node->FirstAired ;
				$EpisodesList[(int)$Node->id]['LastUpdated'] =     (int)$Node->LastUpdated ;
				$EpisodesList[(int)$Node->id]['SerieID'] =         (int)$Node->seriesid ;
			  }
			}
		  }
		  $end1 = utime();
		  $run1 = $end1 - $start1;
		  //$_Debug['RenderTime'][] = 'ShowCalendar Loop1 ('.$_Series[$SerieID]['SeriesName'].') run in '.substr($run1, 0, 5).' sec';
		} else
		{
		  $_Series[$SerieID]['id'] =           $SerieID;
		  $_Series[$SerieID]['SeriesName'] =   '_MISSING_FILE';
		  $_Series[$SerieID]['Status'] =       '_MISSING_FILE';
		  $_Series[$SerieID]['LastUpdated'] =  '_MISSING_FILE';
		}
	  }
	//}
	}
    //$Format = 'Num|SeriesName2|EpisodeYxZ|FirstAired|Downloaded|Downloaded720|Subtitles|Seen|ExternalLinks';
    $Format = 'Num|SeriesName2|EpisodeYxZ|FirstAired|Downloaded|Subtitles|Seen|ExternalLinks';
    $Sort = 'FirstAired|SeriesName|SeasonNumber|EpisodeNumber';

    //print_r($EpisodesList);
    ShowEpisodeList( $EpisodesList , $Format , $Sort );

    $end = utime();
    $run = $end - $start;
    $_Debug['RenderTime'][] = 'ShowCalendar run in '.substr($run, 0, 5).' sec';
  } else {
    $_Messages[] = $CONTENT_TEXT['TVDB.Down'];
    $_Messages[] = str_replace( '[[FILE]]' , 'UserFavoritesSeries' , $CONTENT_TEXT['ErrorFile'] );
  }
}

function ShowSeasonFlags($EpisodesList)
{
  global $_Messages;
  global $_Debug;
  global $_SortEpisodeOrder;
  global $CONTENT_FORM;
  global $CONTENT_HEADER;

  $SerieID = $EpisodeList[0]['SerieID'];

  $_SortEpisodeOrder = 'SeasonNumber|EpisodeNumber';
  uasort( $EpisodesList , 'SortEpisodes' );

  $SeasonNumber = -1;

  $Format = array("Downloaded","Downloaded720","Subtitles","Seen");

  foreach( $EpisodesList as $Episode )
  {
    $SeasonList[$Episode['SeasonNumber']][] = $Episode;

    $NewEpisodesList[ $Episode['SeasonNumber'] ] = $Episode;
    //$Message .= $Episode['SeasonNumber'].' - '.$Episode['EpisodeNumber'].' - '.$Episode['EpisodeName'].'<br>';
  }

  $Message .= '<table><tbody>';
  $Message .= '<tr>';
  $Message .= '<th class="Saison">'.$CONTENT_HEADER['Saison'].'</th>';
  foreach( $Format as $column )
    $Message .= '<th class="'.$column.'">'.$CONTENT_HEADER[$column].'</th>';
  $Message .= '</tr>';

  $counter = 1;

  foreach($SeasonList as $SeasonID => $Season)
  {
    if($counter)
    {
      $DefaultClass = "DarkGrey1";
      $counter = 0;
    } else
    {
      $DefaultClass = "DarkGrey2";
      $counter++;
    }

    $Message .= '<tr>';

    $EpisodeArray = '';

    $Message .= '<td class="'.$DefaultClass.'">';
    $Message .= 'Season '.$SeasonID.' : ';
    $Message .= '</td>';

    foreach($Season as $Episode)
    {
      $EpisodeArray .= "'".$Episode['id']."',";
      foreach( $Format as $column )
      {
        $Seasons[$SeasonID][$column] += $Episode[$column];
      }
    }
    foreach( $Format as $column )
    {
      $Message .= '<td>';
      //$CONTENT_FORM['DownloadedSeason']
      $LocalContent = str_replace( '[[EpisodeArray]]' , substr($EpisodeArray,0,-1) , $CONTENT_FORM[$column.'Season'] );
      $LocalContent = str_replace( '[[SerieID]]' , $Episode['SerieID'] , $LocalContent );
      $LocalContent = str_replace( '[[SeasonID]]' , $SeasonID , $LocalContent );

      if( $Seasons[$SeasonID][$column] == count($Season) )
        $Value = "checked";
      else
        $Value = "";

      $LocalContent = str_replace( '[[Value]]' , $Value , $LocalContent );

      $Message .= $LocalContent;
      $Message .= '</td>';
    }
    $Message .= '</tr>';
  }
  $Message .= '</tbody></table>';
  $_Messages[] = $Message;
}

function ShowUpdateAllSeries( $SerieList )
{
  global $_Messages;
  global $_Debug;
  global $CONTENT_LINK;

  $Array;
  $i;

  foreach($SerieList as $Serie)
  {
    if( $Serie['LastUpdated'] != $Serie['DistantLastUpdated'] || $Serie['SeriesName'] == '_MISSING_FILE' )
    {
      $_Debug['Update'][] = $Serie['SeriesName'];
      $Array .= '\''.$Serie['id'].'\',';
      $i++;
    }
  }
  if($i == 0)
    $Message = 'No Update Needed';
  else
  {
    $Message = str_replace( '[[MAX]]' , $i , $CONTENT_LINK['UpdateSerieAll'] );
    $Message = str_replace( '[[ARRAY]]' , substr($Array,0,-1) , $Message );
    //$Message = '<a onclick="UpdateAllSeries(new Array('.substr($Array,0,-1).'))" href="#">Update All Series</a> <div id="UpdateAllSeriesStatus">0 / '.$i.'</div>';
  }
  $_Messages[] = $Message;
}
?>