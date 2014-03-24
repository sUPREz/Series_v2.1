<?php
function LoadXMLFile($xmlfile)
{
  global $_Debug;
  global $_MaxLoadTry;
  //echo $xmlfile;
  $start = utime();
  if( file_exists($xmlfile) || stristr( $xmlfile , "http" ) )
  {
    //echo $xmlfile;
    $LoadTry = 0;
    do {
      $LoadTry++;
      if( stristr( $xmlfile , "http" ) )
      {
        //$url = curl($xmlfile);
        $url = get_final_url($xmlfile);
        if( $url )
          $xmlfile = $url;
      }
      //echo ' => '.$xmlfile.'<br />';
      $xml = @simplexml_load_file($xmlfile);
      //print_r('<br>a '.$xml.'<br />');

    } while ( $LoadTry <= $_MaxLoadTry && $xml === FALSE );

    if( $xml === FALSE )
      $xml = '_MISSING_FILE' ;
  } else
  {
    $_Debug ['LoadXMLFile'][] = $xmlfile.' missing';
    $xml = '_MISSING_FILE' ;
  }
  $end = utime();
  $run = $end - $start;

  if( $xml == '_MISSING_FILE' )
    $_Debug ['LoadXMLFile'][] = $xmlfile.' NOT loaded in '.substr($run, 0, 5).' sec ( '.$LoadTry.' try )';
  else
    $_Debug ['LoadXMLFile'][] = $xmlfile.' loaded in '.substr($run, 0, 5).' sec ( '.$LoadTry.' try )';
  return($xml);
}

function CopyFile( $xmlfile , $Dest ){
  global $_Debug;

  //echo $xmlfile.'<br />';
  //echo $Dest.'<br />';
  if( stristr( $xmlfile , "http" ) )
  {
    //$url = curl($xmlfile);
    $url = get_final_url($xmlfile);
    if( $url )
      $xmlfile = $url;
  }

  $start = utime();
  $return = copy( $xmlfile , $Dest );
  $end = utime();
  $run = $end - $start;

  $_Debug ['LoadXMLFile'][] = $xmlfile.' copied to '.$Dest.' in '.substr($run, 0, 5).' sec';

  return( $return );
}

function utime (){
    $time = explode( " ", microtime());
    $usec = (double)$time[0];
    $sec = (double)$time[1];
    return $sec + $usec;
}

function get_final_url( $url, $timeout = 5 )
{
    $url = str_replace( "&amp;", "&", urldecode(trim($url)) );

    $cookie = tempnam ("/tmp", "CURLCOOKIE");
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_ENCODING, "" );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
    $content = curl_exec( $ch );
    $response = curl_getinfo( $ch );
    curl_close ( $ch );

    if( $response['http_code'] == 404 )
    {
      return '_INVALID_URL';
    }
    else if ($response['http_code'] == 301 || $response['http_code'] == 302)
    {
        ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        $headers = get_headers($response['url']);

        $location = "";
        foreach( $headers as $value )
        {
            if ( substr( strtolower($value), 0, 9 ) == "location:" )
                return get_final_url( trim( substr( $value, 9, strlen($value) ) ) );
        }
    }

    if (    preg_match("/window\.location\.replace\('(.*)'\)/i", $content, $value) ||
            preg_match("/window\.location\=\"(.*)\"/i", $content, $value)
    )
    {
        return get_final_url ( $value[1] );
    }
    else
    {
        return $response['url'];
    }
}

function print_r_pre( $var ){
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}

function IsIn( $array , $var ){
  if( !is_array($array) )
    return (0);
  else {
    foreach( $array as $entry ){
      if( is_array($entry) ){
        foreach( $entry as $entry2){
          if( $entry2 == $var )
            return(1);
        }
      } else {
        if( $entry == $var )
          return(1);
      }
    }
    return(0);
  }
}
?>