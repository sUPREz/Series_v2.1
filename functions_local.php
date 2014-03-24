<?php
function GetLocalSerieBase( $SerieID )
{
  global $_DataPath;

  $xmlfile = $_DataPath.$SerieID.'_base.xml';

  $xml = LoadXMLFile($xmlfile);

  return($xml);
}

function GetLocalSerieFull( $SerieID )
{
  global $_DataPath;

  $xmlfile = $_DataPath.$SerieID.'_full.xml';

  $xml = LoadXMLFile($xmlfile);

  return($xml);
}

function GetLocalUserFavorites()
{
  global $_DataPath;
  global $_UserFavoriteFileName;
  global $_Messages;
  global $CONTENT_TEXT;
  global $CONTENT_LINK;

  $xmlfile = $_DataPath.$_UserFavoriteFileName;

  $xml = LoadXMLFile($xmlfile);
  if( $xml != '_MISSING_FILE' )
  {
    if( !IsIn( $_Messages , $CONTENT_TEXT['UserFavoritesUpdated'] ) )
      $_Messages[] = $CONTENT_LINK['UpdateUserFavoriteAlt'];

    foreach ($xml->children() as $Serie) {
      $UserFavoritesSeries[] = (int)$Serie;
    }
    $_UserFavoritesSeries = $UserFavoritesSeries;
    return($UserFavoritesSeries);
  } else {
    $_Messages[] = $CONTENT_TEXT['UserFavoritesOutdated'].' - '.$CONTENT_LINK['UpdateUserFavorite'];
    return GetDistantUserFavorites();
  }
}

function getUsersFromDB(){
  $query = "SELECT * FROM series_users";
  $result = SQLQuery($query);
  while( $User = mysql_fetch_array($result) )
    $UserList[$User['ID']] = $User['name'];

  return( $UserList );
}
?>