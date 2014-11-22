<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <!-- Favicon !-->
  <link rel="shortcut icon" href="imgs/favicon.png" type="image/vnd.microsoft.icon" />
  <link rel="icon" href="imgs/favicon.png" type="image/vnd.microsoft.icon" />
<link rel="stylesheet" href="js.jQuery/jQuery.CustomSelect.css" type="text/css" />
<link rel="stylesheet" href="Styles.css" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script language="JavaScript" src="js.jQuery/jQuery.CustomSelect.js" type="text/javascript"></script>
<?php
//phpinfo();
require('config.php');

$start = utime();

$_Series;
$_Users = getUsersFromDB();

if( isset($_GET['UpdateSerie']) )
  GetDistantSerieUpdate( $_GET['UpdateSerie'] );
if( isset($_GET['UpdateUserFavorite']) )
  GetDistantUserFavoriteUpdate();

if( isset($_GET['Area']) )
{
  switch($_GET['Area'])
  {
    case 'SerieList':
      ShowSeriesList();
      break;
    case 'Serie':
      ShowSerie($_GET['SerieID']);
      break;
    case 'Calendar':
      ShowCalendar();
      break;
    default:
      echo 'Choose a page in the menu';
      break;
  }
}
?>
  <title>S&eacute;ries !! v2.1</title>
</head>

<body>
<?php
//echo $_SERVER["REMOTE_ADDR"];
?>
<script language="JavaScript" src="functions.js" type="text/javascript"></script>
<script language="JavaScript" src="../TextEditor/TextEditor/TextEditor.js" type="text/javascript"></script>
<script>
$(document).ready(function() {
  $("legend").click(function () {
    //alert('a');
    name = $(this).attr('name');
    $("div[name='"+name+"']").slideToggle( 500 );
  })
});
</script>
<fieldset name="Menu">
  <legend name="Menu">Menu</legend>
  <div name="Menu">
    <div id="Menu">
      <a class="Menu" href="?Area=SerieList"><?php echo $CONTENT_HEADER['SerieList'] ?></a>
        <?php
        foreach( $_Users as $UserID => $User ){
          $Item = str_replace( '[[USERID]]' , $UserID , $CONTENT_LINK['MenuListFiltered'] );
          $Item = str_replace( '[[USER]]' , $User , $Item );
          echo ' - '.$Item;
        }
        ?>
      <br />
      <a class="Menu" href="?Area=Calendar"><?php echo $CONTENT_HEADER['Calendar'] ?></a>
        <?php
        foreach( $_Users as $UserID => $User ){
          $Item = str_replace( '[[USERID]]' , $UserID , $CONTENT_LINK['MenuCalendarFiltered'] );
          $Item = str_replace( '[[USER]]' , $User , $Item );
          echo ' - '.$Item;
        }
        ?>
      <br /><br />
    </div>
    <div id="SubMenu">
      <a class="SubMenu" href="http://suprez.hd.free.fr/phpMyAdmin/" target="_blank">PhpMyAdmin (suprez.hd.free.fr)</a> -
      <a class="SubMenu" href="http://thetvdb.com/" target="_blank">The TV DB</a> -
      <a class="SubMenu" href="http://www.sous-titres.eu/" target="_blank">Sous-titres.eu</a> -
      <a class="SubMenu" href="http://www.addic7ed.com/" target="_blank">addic7ed.com</a>
    </div>
    <div id="DateAndTime">
    <?php
    echo ucwords ( strftime("%A %d %B %Y - %Hh %Mm %Ss", time() ) );
    ?>
    </div>
  </div>
</fieldset>
<fieldset name="Messages">
  <legend name="Messages">Messages</legend>
  <div name="Messages">
<?php
if( isset($_Messages) )
{
  foreach( $_Messages as $Message)
  {
    if( trim($Message) != '' )
      echo $Message.'<br>';
  }
}
?>
  </div>
</fieldset>
<fieldset name="Content">
  <legend name="Content">Content</legend>
  <div name="Content">
<?php
  echo $_Content;
?>
  </div>
</fieldset>
<fieldset name="Debug">
  <legend name="Debug">Debug</legend>
  <div name="Debug" style="display:none">
  <script>
  PostItHere('Debug','Series2');
  </script>
  <fieldset name="Debug_js">
    <legend>Javascript</legend>
    <div id="Debug"></div>
  </fieldset>
<?php
//*
foreach($_Debug as $key => $values)
{
  echo '<fieldset name="Debug_php"><legend>'.$key.'</legend>';

  if( gettype($values) == 'array' )
  {
    foreach($values as $value)
    {
      if( gettype($value) == 'array' )
        print_r($value);
      else
        echo $value;
      echo "<br>";
    }
  } else
    echo $values;

  echo '</fieldset>';
}
//print_r($_Debug);
//*/
?>
  <fieldset name="Debug_render">
    <legend>Render Time</legend>
<?php
$end = utime();
$run = $end - $start;
echo "Page created in: ".substr($run, 0, 5) . " seconds.";
?>
  </fieldset>
  </div>
</fieldset>
<script language="JavaScript" type="text/javascript">
InitParentTagClassName();
$('td[name^="User"] div').CustomSelect({
  callback: function( FieldID , FieldText , ValueID ){
    var Field = $('div.CustomSelect.Field[data-customselect-index="'+FieldID+'"]');
    var SerieID = Field.parent('td').attr('data-series-serieid');
    //console.log( SerieID, Field );
    var url = 'UpdateSerieDB.php?Area=Serie&Type=userID&Value='+ValueID+'&SerieID='+SerieID;
    $.get(url);

    //console.log('callback!!',FieldID,FieldText,ValueID);
  }
});
</script>

</body>

</html>