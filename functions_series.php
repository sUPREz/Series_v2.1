<?php
function SortSeries($a,$b)
{
  global $_SortSerieOrder;
  $SortOrder = explode('|',$_SortSerieOrder);
  return ( CustomSort($a,$b,$SortOrder,0) );
}

function SortEpisodes($a,$b)
{
  global $_SortEpisodeOrder;
  $SortOrder = explode('|',$_SortEpisodeOrder);

  return ( CustomSort($a,$b,$SortOrder,0) );
}

function CustomSort($a,$b,$SortOrder,$Index)
{
  global $_SortSerieOrder;

  if( $a[ $SortOrder[$Index] ] == $b[ $SortOrder[$Index] ] )
  {
    if( $Index < sizeof($SortOrder) )
      return( CustomSort($a,$b,$SortOrder,$Index+1) );
    else
    return (0);
  }
  else if( $a[ $SortOrder[$Index] ] > $b[ $SortOrder[$Index] ] )
    return (1);
  else
    return (-1);
}

/*
function CreateJSArrays($EpisodesList)
{
  $js .= '<script language="JavaScript" type="text/javascript">';
  $js .= 'var Episodes = new Array();';
  foreach($EpisodesList as $Episode)
  {
    $js .= 'Episodes.push( new Array('.$Episode['id'].','.$Episode['SerieID'].','.$Episode['SeasonNumber'].','.$Episode['EpisodeNumber'].') );';
  }
  $js .= '</script>';
  echo $js;
}
//*/
?>