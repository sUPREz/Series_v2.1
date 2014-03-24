/*

Counter bug
Need Lock feature to avoid multiple clics
*/

var StartTime;

var size;
var fail;
var success;

// Ajax Update
var MaxSimultaneousConnexion = 3;
var SimultaneousConnexion = 0;
var DebugMaxIndex = -1;
var StopDebug = false;

var CheckUpdateAllSeriesStarted = false;

var SerieListInitialCount = 0;
//var SerieCheckedCount = 0;
//var UpdateNeededCount = 0;
//var SerieCheckedErrorCount = 0;
var UpdatedSerieCount = 0;
var UpdatedSerieCountError = 0;

var SerieListIndex = -1;
var SerieList = [];
//var SerieListFailToBeUpdated = [];
var SerieListToBeUpdated = [];
$.ajaxSetup({async:true});


//$('[name=Num|76290]').text();
/*
function CheckUpdateAllSeries()
{

}
//*/
function RunUpdate()
{
  if( SimultaneousConnexion < MaxSimultaneousConnexion && (SerieListIndex < DebugMaxIndex || DebugMaxIndex == -1) && !StopDebug )
  {
    //alert ( SimultaneousConnexion +' < '+ MaxSimultaneousConnexion);
    SerieListIndex++;
    if( SerieListIndex < SerieList.length )
    {
      //debug( 'RunUpdate '+SerieList[SerieListIndex]+" "+SerieListIndex+" "+SimultaneousConnexion );
      Update();
    }
    else
    {
      //if( $('#UpdateAllSeriesStatus').text() == '' && ( SerieCheckedCount + SerieCheckedErrorCount ) == SerieList.length )
      if( UpdatedSerieCount == SerieListInitialCount)
      {
        alert('updates terminées');
        $('div[name=Messages]').append( _JsUpdateSerieEnd );
      }
    }
  }
}

function Update()
{
  //debug( 'CheckUpdate '+SerieList[SerieListIndex]+" "+SerieListIndex+" "+SimultaneousConnexion );

  SimultaneousConnexion++;
  $.post("UpdateSerie.php" , {'ID':SerieList[SerieListIndex][0] , 'Index':SerieListIndex});
  RunUpdate();
}

function UpdateError( settings , Context , errorText )
{
  if( settings.url == 'UpdateSerie.php' )
  {
    SerieList.push( SerieList[ Context['Index'] ] ) ;
    $('[name=Infos|'+Context['ID']+']').html( '&nbsp;'+errorText+'&nbsp;' );
    $('[name=Infos|'+Context['ID']+']').addClass('RedText');
    UpdatedSerieCountError++;
    $('#UpdateErrors').text( UpdatedSerieCountError );
    //$('#CheckProgressError').animate( {width: (ProgressBarMaxWidth / SerieList.length * SerieCheckedErrorCount) } , 100 );
  }
}

$(document).ready( function(){
  $('[name=UpdateAllSeries]').click( function(){
    if( CheckUpdateAllSeriesStarted != true )
    {
      CheckUpdateAllSeriesStarted = true;
      SerieListInitialCount = SerieList.length;

      ProgressContainerWidth = $('#ProgressContainer').css('width').slice(0 , $('#ProgressContainer').css('width').indexOf('px') );
      CheckProgressLeft = $('#UpdateProgress').css('top').slice(0 , $('#UpdateProgress').css('top').indexOf('px') );
      ProgressBarMaxWidth = ProgressContainerWidth - ( 2 * CheckProgressLeft );
      StartTime = new Date();

      //debug = (ProgressBarMaxWidth / SerieListInitialCount * SerieCheckedCount);
      //alert( ProgressBarMaxWidth+' - '+ProgressContainerWidth+' - ');

      $('div[name=Messages]').append( _UpdateSerieAll+'<br>'+_UpdateErrors);

      RunUpdate();
    }
  });

  $(document).ajaxError( function (e, xhr, settings, exception){
    if( settings.url == 'UpdateSerie.php' )
    {
      SimultaneousConnexion--;
      Vars = SettingsDataDecode( settings.data );
      UpdateError(settings,Vars,_JsErrorServer);
      RunUpdate();
    }
  });

  $(document).ajaxSuccess(function(e, xhr, settings){
    if( settings.url == 'UpdateSerie.php' )
    {
      SimultaneousConnexion--;
      if( xhr.responseText  != '_MISSING_PARAMETER' && xhr.responseText != undefined )
      {
        values = xhr.responseText.split('|');
        phpResult = values[0];
        SerieID = values[1];
        Index = values[2];

        if( SerieList[ Index ] != undefined || phpResult == 1)
        {
          UpdatedSerieCount++;
          Vars = SettingsDataDecode( settings.data );

          CurrentTime = new Date;
          DiffTime = CurrentTime - StartTime;
          Time = ( Math.round( DiffTime/10 ) ) /100;

          // progess text
          $('#UpdateAllSeriesStatus').text( UpdatedSerieCount+' / '+SerieListInitialCount+' ('+Time+' sec)');

          //progress bas
          newWidth = ( (ProgressBarMaxWidth / SerieListInitialCount) * UpdatedSerieCount );
          $('div#UpdateProgress').animate( {'width': newWidth } , 100 );

          //serie 'infos' Cell
          $('[name=Infos|'+Vars['ID']+']').html( '&nbsp;'+_JsNoUpdateNeeded+'&nbsp;' );
          $('[name=Infos|'+Vars['ID']+']').removeClass('RedText').addClass('GreenText');
        }
        else
          UpdateError(settings,Vars,_JsErrorScript);
      }
      else
        UpdateError(settings,Vars,_JsErrorScript);

      RunUpdate();
    }
  });
});

function SettingsDataDecode( data )
{
  var vars = [];
  var hashes = data.slice(data.indexOf('?') + 1).split('&');

  for(var i = 0; i < hashes.length; i++)
  {
    hash = hashes[i].split('=');
    vars.push(hash[0]);
    vars[hash[0]] = hash[1];
    //alert(hash[0]+'='+hash[1]);
  }
  //alert( data +'-'+vars)
  return vars;
}

function debug(text)
{
  CurrentTime = new Date;
  DiffTime = CurrentTime - StartTime;
  $('div[name=Messages]').append( '<br>'+text+' ('+DiffTime+'ms)' );
}


function UpdateParentTagClassName( id , value)
{
  //alert(id +' - '+value);
  if( value == 1 )
    document.getElementById(id).parentElement.className = 'Green';
  else
    document.getElementById(id).parentElement.className = 'Red';
}

function InitParentTagClassName()
{
  for( i in document.getElementsByTagName('input') )
  {
    //alert(input);
    if( document.getElementsByTagName('input')[i].type == 'checkbox' &&
        document.getElementsByTagName('input')[i].parentElement.nodeName == 'TD' )
    {
      if( document.getElementsByTagName('input')[i].checked )
        document.getElementsByTagName('input')[i].parentElement.className = 'Green';
      else
        document.getElementsByTagName('input')[i].parentElement.className = 'Red';
    }
  }
}

function UpdateSQLSerie( Type , SerieID )
{
  var value;
  if( document.getElementById(Type+'_'+SerieID).checked )
    value = 1;
  else
    value = 0;

  UpdateParentTagClassName(Type+'_'+SerieID , value);

  var url = 'UpdateSerieDB.php?Area=Serie&Type='+Type+'&Value='+value+'&SerieID='+SerieID;
  result = loadXMLDoc(url);
  //alert(result);
}

function UpdateSQLEpisode( Type , EpisodeID , SerieID )
{
  //alert(document.getElementById(Type+'_'+EpisodeID));
  var value;
  if( document.getElementById(Type+'_'+EpisodeID).checked )
    value = 1;
  else
    value = 0;

  UpdateParentTagClassName(Type+'_'+EpisodeID , value);

  var url = 'UpdateEpisodeDB.php?Area=Episode&Type='+Type+'&EpisodeID='+EpisodeID+'&Value='+value+'&SerieID='+SerieID;
  //Debug(url);
  result = loadXMLDoc(url);
  Debug(url+' : '+result);
}

function UpdateSQLSeason( Type , EpisodesID , SeasonNumber , SerieID)
{
  //alert(Type+'Season_'+SeasonNumber);
  var value;
  if( document.getElementById(Type+'Season_'+SeasonNumber).checked )
    value = 1;
  else
    value = 0;

  UpdateParentTagClassName(Type+'Season_'+SeasonNumber , value);

  var url = 'UpdateEpisodeDB.php?Area=Season&Type='+Type+'&SeasonNumber='+SeasonNumber+'&Value='+value+'&SerieID='+SerieID;
  CheckCheckboxValue( Type , EpisodesID , value )
  //Debug(url);
  result = loadXMLDoc(url);
  //Debug(result);
}

function CheckCheckboxValue( Type , EpisodesID , Value )
{
  var textValue;
  if( Value )
    textValue = true;
  else
    textValue = false;

  for( i=0 ; i < EpisodesID.length ; i++ )
  {
    //alert(Type+'_'+EpisodesID[i]);
    UpdateParentTagClassName( Type+'_'+EpisodesID[i] , Value );

    document.getElementById(Type+'_'+EpisodesID[i]).checked = textValue;
    //Debug(document.getElementsByName(Type+'_'+EpisodesID[i])[0].checked)
  }
}

function Debug(DebugText)
{
  document.getElementById('Debug').innerHTML += DebugText+'<br>';
}

function loadXMLDoc(url,async)
{
//  alert(url+' - '+RemoveAccent(url));
  //url = RemoveAccent(url);
  if( async == 'undefined' )
    async = false;
  //alert(async);

  var xmlhttp;
  xmlhttp=null;
  if (window.XMLHttpRequest)
  {// code for IE7, Firefox, Opera, etc.
    xmlhttp=new XMLHttpRequest();
  }
  else if (window.ActiveXObject)
  {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  if (xmlhttp!=null)
  {
    xmlhttp.url = url;
    if( async )
    {
      xmlhttp.open("GET",url,true);
      xmlhttp.send(null);
    }
    else
    {
      xmlhttp.open("GET",url,false);
      xmlhttp.send(null);
    }
    /*
    alert( xmlhttp.status + ' - ' +
           xmlhttp.statusText + ' - ' +
           xmlhttp.responseText);
    //*/
  }
  else
  {
    alert("Your browser does not support XMLHTTP.");
  }



  if( async )
    return(xmlhttp);
  else
    return(xmlhttp.responseText);
}