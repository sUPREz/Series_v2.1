<?php

ini_set ( 'safemode', false );

function LoadXMLFile($xmlfile)
{
  global $_Debug;
  global $_MaxLoadTry;

  if( file_exists($xmlfile) || stristr( $xmlfile , "http" ) )
  {
    echo '<br />PATATE !<br />';
    //echo $xmlfile;
    $start = utime();

    $LoadTry = 3;
    do {
      $LoadTry++;
      //*
      if( stristr( $xmlfile , "http" ) )
      {
        echo '<br />$xmlfile: '.$xmlfile;
        $url = curl($xmlfile);
        //$url = get_url($xmlfile);
        echo '<br />$url: '.$url;
        print_r($url);
        if( $url )
        {
          $xmlfile = $url;
          echo '<br />$xmlfile: '.$xmlfile;
        }
      }
      //*/
      //echo ' => '.$xmlfile.'<br />';
      //$xml = simplexml_load_file($xmlfile);
      //print_r('<br>a '.$xml.'<br />');

    } while ( $LoadTry <= $_MaxLoadTry && $xml === FALSE );
    //*
    if( $xml === FALSE )
      $xml = '_MISSING_FILE' ;

    $end = utime();
    $run = $end - $start;

    $_Debug ['LoadXMLFile'][] = $xmlfile.' loaded in '.substr($run, 0, 5).' sec ( '.$LoadTry.' try )';
    //*/

  } else
  {
    $_Debug ['LoadXMLFile'][] = $xmlfile.' missing';
    $xml = '_MISSING_FILE' ;
  }
  //return($xml);
}

function CopyFile( $xmlfile , $Dest ){
  global $_Debug;

  //echo $xmlfile.'<br />';
  //echo $Dest.'<br />';
  if( stristr( $xmlfile , "http" ) )
  {
    $url = curl($xmlfile);
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

function curl($url){
  $go = curl_init($url);
  curl_setopt ($go, CURLOPT_URL, $url);
  curl_setopt ($go, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
  echo '<br />PROUT';

  //follow on location problems

  if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')){
    echo '<br />CACA';
    curl_setopt ($go, CURLOPT_FOLLOWLOCATION, $l);
    $syn = curl_exec($go);
  }else{
    echo '<br />PIPI';
    $syn = curl_redir_exec($go);
    $syn = curl_exec_follow($go);

  }
  curl_close($go);
  return $syn;
}


function curl_redir_exec($ch , $new_url= FALSE)
{
  static $curl_loops = 0;
  static $curl_max_loops = 20;

  if( $new_url )
    return ( $new_url );
  if ($curl_loops++ >= $curl_max_loops)
  {
    $curl_loops = 0;
    return FALSE;
  }

  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $data = curl_exec($ch);
  list($header, $data) = explode("\n\n", $data, 2);
  echo $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($http_code == 301 || $http_code == 302)
  {
    $matches = array();
    echo '<br />courgettes !<br />';
    print_r($header);
    preg_match('/Location:(.*?)\n/', $header, $matches);
    $url = @parse_url(trim(array_pop($matches)));
    if (!$url)
    {
      //couldn't process the url to redirect to
      $curl_loops = 0;
      return $data;
    }

    $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
    if (!$url['scheme'])
      $url['scheme'] = $last_url['scheme'];

    if (!$url['host'])
      $url['host'] = $last_url['host'];

    if (!$url['path'])
      $url['path'] = $last_url['path'];

    $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');
    curl_setopt($ch, CURLOPT_URL, $new_url);
    return curl_redir_exec($ch,$new_url);

  } else {

    $curl_loops=0;
    return $data;
  }
}

function curl_exec_follow(/*resource*/ $ch, /*int*/ &$maxredirect = null) {
    $mr = $maxredirect === null ? 5 : intval($maxredirect);
    if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
    } else {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        if ($mr > 0) {
            $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

            $rch = curl_copy_handle($ch);
            curl_setopt($rch, CURLOPT_HEADER, true);
            curl_setopt($rch, CURLOPT_NOBODY, true);
            curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
            do {
                curl_setopt($rch, CURLOPT_URL, $newurl);
                $header = curl_exec($rch);
                if (curl_errno($rch)) {
                    $code = 0;
                } else {
                    $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                    if ($code == 301 || $code == 302) {
                        preg_match('/Location:(.*?)\n/', $header, $matches);
                        $newurl = trim(array_pop($matches));
                    } else {
                        $code = 0;
                    }
                }
            } while ($code && --$mr);
            curl_close($rch);
            if (!$mr) {
                if ($maxredirect === null) {
                    trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                } else {
                    $maxredirect = 0;
                }
                return false;
            }
            curl_setopt($ch, CURLOPT_URL, $newurl);
        }
    }
    return curl_exec($ch);
}
?>
?>


