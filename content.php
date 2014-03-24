<?php
$CONTENT_TEXT['NoSerieData'] =                  "XML introuvable";
$CONTENT_TEXT['UserFavoritesOutdated'] =        'Fichier des Séries Favorites introuvable';
$CONTENT_TEXT['UserFavoritesUpdated'] =         'Fichier des Séries Favorites mise à jour !';
$CONTENT_TEXT['SerieOutdated'] =                "XML p&eacute;rim&eacute;";
$CONTENT_TEXT['SerieUpdated'] =                 "Serie ([[SeriesName]]) mise à jour !";
$CONTENT_TEXT['Refresh'] =                      "Rafraichir la page";
$CONTENT_TEXT['NoEpisodes'] =                   "Aucun &eacute;pisode";
$CONTENT_TEXT['SousTitre.euNotFound'] =         "ST.eu no sub";//"Pas de Sous-Titres";
$CONTENT_TEXT['SousTitre.euFileNotFound'] =     'Impossible de trouver le fichier de sous-titres pour la série "[[SERIE]]"';
$CONTENT_TEXT['SousTitre.euFileNotFoundShort'] ="ST.eu _NONE";//'Pas de fichier sous-titres';
$CONTENT_TEXT['SousTitre.euDown'] =             'Le site <a href="http://www.sous-titres.eu/">www.sous-titres.eu</a> semble inactif.';
$CONTENT_TEXT['TVDB.Down'] =                    'Le site <a href="http://thetvdb.com/">http://thetvdb.com/</a> semble inactif.';
$CONTENT_TEXT['ErrorFile'] =                    'Impossible d\'ouvrir le fichier "[[FILE]]".';
$CONTENT_TEXT['BackupMirror'] =                 'Le fichier [[FILE]] a été récupéré sur le mirroir de secours !';

$CONTENT_TEXT['Continuing'] =                   "En cours";
$CONTENT_TEXT['Ended'] =                        "Terminé";
$CONTENT_TEXT['Pending'] =                      "En attente";
//$CONTENT_TEXT['CheckUpdateSerieAll'] =          'Vérifier les mises à jour';
//$CONTENT_TEXT['UpdateNeeded'] =                 'Séries nécessitant une mise à jour';
$CONTENT_TEXT['UpdateErrors'] =                 'Erreurs';
$CONTENT_TEXT['UpdateSerie'] =                  'Démarer la mise à jour des séries';
$CONTENT_TEXT['UpdateSerieStatus'] =            'Séries mises à jour';
//$CONTENT_TEXT['JsUpdateNeeded'] =               'Mise à jour nécessaire';
$CONTENT_TEXT['JsUpdateSerieEnd'] =             'Mises à jour terminées';
$CONTENT_TEXT['JsNoUpdateNeeded'] =             'Série à jour';
$CONTENT_TEXT['JsErrorScript'] =                'Erreur : Script ?';
$CONTENT_TEXT['JsErrorServer'] =                'Erreur : Serveur inaccessible';

$CONTENT_LINK['MenuListFiltered'] =             '<a class="SubMenu" href="?Area=SerieList&FilterType=userID&FilterValue=[[USERID]]">[[USER]]</a>';;
$CONTENT_LINK['MenuCalendarFiltered'] =         '<a class="SubMenu" href="?Area=Calendar&FilterType=userID&FilterValue=[[USERID]]">[[USER]]</a>';;

$CONTENT_LINK['SeriesPage'] =                   '<a href="?Area=Serie&SerieID=[[SerieID]]">Infos Série</a>';
$CONTENT_LINK['SeriesPage2'] =                  '<a href="?Area=Serie&SerieID=[[SerieID]]">[[SeriesName]]</a>';
$CONTENT_LINK['UpdateSerie'] =                  '<a href="?Area='.$_GET['Area'].'&UpdateSerie=[[SerieID]]">Mise à jour</a>';
$CONTENT_LINK['UpdateUserFavorite'] =           '<a href="?Area='.$_GET['Area'].'&UpdateUserFavorite=1">Mise à jour</a>';
$CONTENT_LINK['UpdateUserFavoriteAlt'] =        '<a href="?Area='.$_GET['Area'].'&UpdateUserFavorite=1">Mettre à jour Séries Favorites ?</a>';
$CONTENT_LINK['SousTitre.euFound'] =            '<a href="[[URL]]" target="_blank">Sous-Titres Dispo</a>';
$CONTENT_LINK['SousTitre.euNotFound'] =         '<a href="http://www.sous-titres.eu/series/[[SERIE]].html" target="_blank">Verif</a>';
$CONTENT_LINK['addic7ed'] =                     '<a href="http://www.addic7ed.com/serie/[[SerieName]]/[[SeasonNumber]]/[[EpisodeNumber]]/[[EpisodeName]]" target="_blank">Addic7ed</a>';
$CONTENT_LINK['addic7ed_href'] =                'http://www.addic7ed.com/serie/[[SerieName]]/[[SeasonNumber]]/[[EpisodeNumber]]/8';
$CONTENT_LINK['addic7ed.euNotFound'] =          '<a href="[[URL]]" target="_blank">Verif</a>';
$CONTENT_LINK['btjunkie.org'] =                 '<a href="http://btjunkie.org/search?q=[[TEXT]]" target="_blank">btjunkie.org</a>';
$CONTENT_LINK['thepiratebay.org'] =             '<a href="http://thepiratebay.org/search/[[TEXT]]" target="_blank">piratebay.org</a>';
$CONTENT_LINK['google'] =                       '<a href="http://www.google.fr/search?q=[[TEXT]]+torrent" target="_blank">Google</a>';

$CONTENT_LINK['Wikipedia'] =                    '<a href="http://en.wikipedia.org/wiki/[[SeriesName]]" target="_blank">Wikipedia</a>';
$CONTENT_LINK['TVDB_Series'] =                  '<a href="http://thetvdb.com/index.php?tab=series&id=[[SerieID]]" target="_blank">The TV DB</a>';
$CONTENT_LINK['TVDB_Episodes'] =                '<a href="http://thetvdb.com/?tab=episode&seriesid=[[SerieID]]&seasonid=[[SeasonID]]&id=[[EpisodeID]]&lid=7" target="_blank">[[EpisodeName]]</a>';
$CONTENT_LINK['DistantXML'] =                   '<a href="[[LIEN]]" target="_blank">XML File</a>';

$CONTENT_HEADER['SerieList'] =              'Liste des Séries';
$CONTENT_HEADER['SerieListUpdate'] =        'Vérifier les mises à jour';
$CONTENT_HEADER['Calendar'] =               'Calendrier';

$CONTENT_HEADER['User1'] =                  'Nous';
$CONTENT_HEADER['User2'] =                  'Wen';
$CONTENT_HEADER['User3'] =                  'Steph';
$CONTENT_HEADER['User4'] =                  '???';

$CONTENT_HEADER['Num'] =                    'Num';
$CONTENT_HEADER['id'] =                     'Id';
$CONTENT_HEADER['SeriesName'] =             'S&eacute;rie';
$CONTENT_HEADER['SeriesName2'] =            'S&eacute;rie';
$CONTENT_HEADER['Status'] =                 'Status';
$CONTENT_HEADER['LastUpdated'] =            'MaJ';
$CONTENT_HEADER['EpisodeName'] =            '&Eacute;pisode';
$CONTENT_HEADER['EpisodeName2'] =           '&Eacute;pisode';
$CONTENT_HEADER['EpisodeYxZ'] =             '&Eacute;pisode';
$CONTENT_HEADER['SeasonNumber'] =           'Saison';
$CONTENT_HEADER['EpisodeNumber'] =          '&Eacute;pisode';
$CONTENT_HEADER['FirstAired'] =             'Date de diffusion';
$CONTENT_HEADER['SerieID'] =                'ID Série';
$CONTENT_HEADER['Downloaded'] =             'DL';
$CONTENT_HEADER['Downloaded720'] =          '720p';
$CONTENT_HEADER['Subtitles'] =              'Sous Titres';
$CONTENT_HEADER['Seen'] =                   'Vu';
$CONTENT_HEADER['SeriePage'] =              'Lien';
$CONTENT_HEADER['Infos'] =                  'Infos';
$CONTENT_HEADER['Saison'] =                 'Saison';
$CONTENT_HEADER['ExternalLinks'] =          'Autres Sites';
$CONTENT_HEADER['Ignore'] =                 'Ignore';
$CONTENT_HEADER['TVDB_Series'] =            'Lien TV DB';
$CONTENT_HEADER['DistantXML'] =             'XML TV DB';
$CONTENT_HEADER['User'] =                   'User';

$CONTENT_HTML['User'] =             '<div>[[User]]</div>';

$CONTENT_FORM['Ignore'] =           '<input onclick="UpdateSQLSerie( \'Ignore\' , \'[[SerieID]]\' );" type="checkbox" name="Ignore_[[SerieID]]" id="Ignore_[[SerieID]]" [[Value]]/>';
$CONTENT_FORM['SeenSerie'] =           '<input onclick="UpdateSQLSerie( \'Seen\' , \'[[SerieID]]\' );" type="checkbox" name="Seen_[[SerieID]]" id="Seen_[[SerieID]]" [[Value]]/>';

$CONTENT_FORM['Downloaded'] =       '<input onclick="UpdateSQLEpisode( \'Downloaded\' ,     \'[[EpisodeID]]\' , \'[[SerieID]]\' );" type="checkbox" name="Downloaded_[[EpisodeID]]"     id="Downloaded_[[EpisodeID]]"     [[Value]]/>';
$CONTENT_FORM['Downloaded720'] =    '<input onclick="UpdateSQLEpisode( \'Downloaded720\' ,  \'[[EpisodeID]]\' , \'[[SerieID]]\' );" type="checkbox" name="Downloaded720_[[EpisodeID]]"  id="Downloaded720_[[EpisodeID]]"  [[Value]]/>';
$CONTENT_FORM['Subtitles'] =        '<input onclick="UpdateSQLEpisode( \'Subtitles\' ,      \'[[EpisodeID]]\' , \'[[SerieID]]\' );" type="checkbox" name="Subtitles_[[EpisodeID]]"      id="Subtitles_[[EpisodeID]]"      [[Value]]/>';
$CONTENT_FORM['Seen'] =             '<input onclick="UpdateSQLEpisode( \'Seen\' ,           \'[[EpisodeID]]\' , \'[[SerieID]]\' );" type="checkbox" name="Seen_[[EpisodeID]]"           id="Seen_[[EpisodeID]]"           [[Value]]/>';

$CONTENT_FORM['DownloadedSeason'] =     '<input onclick="UpdateSQLSeason( \'Downloaded\' ,      new Array([[EpisodeArray]]) , \'[[SeasonID]]\' , \'[[SerieID]]\' );" type="checkbox" name="DownloadedSeason_[[SeasonID]]"       id="DownloadedSeason_[[SeasonID]]"    [[Value]]/>';
$CONTENT_FORM['Downloaded720Season'] =  '<input onclick="UpdateSQLSeason( \'Downloaded720\' ,   new Array([[EpisodeArray]]) , \'[[SeasonID]]\' , \'[[SerieID]]\' );" type="checkbox" name="Downloaded720Season_[[SeasonID]]"    id="Downloaded720Season_[[SeasonID]]" [[Value]]/>';
$CONTENT_FORM['SubtitlesSeason'] =      '<input onclick="UpdateSQLSeason( \'Subtitles\' ,       new Array([[EpisodeArray]]) , \'[[SeasonID]]\' , \'[[SerieID]]\' );" type="checkbox" name="SubtitlesSeason_[[SeasonID]]"        id="SubtitlesSeason_[[SeasonID]]"     [[Value]]/>';
$CONTENT_FORM['SeenSeason'] =           '<input onclick="UpdateSQLSeason( \'Seen\' ,            new Array([[EpisodeArray]]) , \'[[SeasonID]]\' , \'[[SerieID]]\' );" type="checkbox" name="SeenSeason_[[SeasonID]]"             id="SeenSeason_[[SeasonID]]"          [[Value]]/>';
?>