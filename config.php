<?php
ini_set( 'default_charset' , "iso-8859-1" );
setlocale (LC_TIME, 'fr_FR.utf8','fra');
//setlocale (LC_TIME, 'fr_FR','fra');
error_reporting(E_ALL ^ E_NOTICE);

/* Free
$db_server = "localhost";
$db_user = "srcproject";
$db_password = "200983";
// */
//$_CONFIG['host'] = '127.0.0.1';
//$_CONFIG['db'] = 'cuisine';
//$_CONFIG['dsn'] = 'mysql:dbname='.$_CONFIG['db'].';host='.$_CONFIG['host'];
//$_CONFIG['user'] = 'root';
//$_CONFIG['password'] = 'l33t43v3r';

$db_server = "localhost";
$db_name = "series_v2.1";
$db_dsn = 'mysql:dbname='.$db_name.';host='.$db_server;
$db_user = "root";
//$db_password = "mSQLpIvS";
$db_password = "l33t43v3r";

require('functions_generic.php');
require('functions_db.php');

require('functions_distant.php');
require('functions_local.php');
require('functions_show.php');
require('functions_series.php');
require('content.php');
require('phpAPI/simple_html_dom_1.5.php');

$_Debug = array();

$_APIKey = "2D9FC059E236450A";
$_AccountIdentifier = "6AFD6D60297DF7F7";

$_DataPath = "Datas/";

$_Mirrors = '_NONE'; // ['mirrorpath']['typemask']
$_BackupMirror = 'http://cache.thetvdb.com';
$_UserFavoritesSeries = '_NONE'; // [id]
$_SelectedLanguage = "en";

$_ST_EU;

$_MaxLoadTry = 3;

$_UserDefault = 0;
$_UserFavoriteFileName = '_UserFavorite.xml';
?>