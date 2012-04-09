<?php

/**
  * @author Miguel Angel Frías Bonfil miguel@inventmx.com
  * @date 2012/04/05
  * @description Get the timeline or make a search of a user or hashtags
  * @usage Este script se ejecuta desde consola, idealmente desde un cron.
  *
  */


$timelines      = '@miguelmafb, #colorelectoral, @actitudfem, #estilofem';	//Users or hashtags to get timeline. Must be separated by comma's. ej. '@miguelmafb, #colorelectoral, @actitudfem, #estilofem'
$dir            = '';	//Folder where will be located the local files. You can not customize the name of files.
$followers_file = '_timeline.txt';
$hashtag_file   = '_hashtag.txt';









split_Timelines( $timelines, $dir, $followers_file, $hashtag_file );

function split_Timelines( $timelines, $dir, $followers_file, $hashtag_file ){
	
	$timelines = stripcslashes( stripslashes( strip_tags( $timelines ) ) );
	$timelines = explode( ',', $timelines );

	foreach( $timelines as $item ){

		$item = str_replace( ' ', '', $item );
		$twitter_symbol = substr( $item , 0, 1 );
		
		if( $twitter_symbol === '@' ){

			$twitter_user  = substr( $item , 1 );
			get_userTimeline( $twitter_user, $dir, $followers_file );
		} else if( $twitter_symbol === '#' ){

			$twitter_search = substr( $item , 1 );
			get_searchTwitter( $twitter_search, $dir, $hashtag_file );
		} else{

			print "<br />Error. Input entry not valid.<br />";
		}
	}
	print "<br />Todo funciono correctamente<br />";

}


function get_userTimeline( $user, $dir, $followers_file ){

	//Lee la API de Twitter y lo guarda en una variable
	$url = file_get_contents( 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name='. $user );

	//Error
	if( empty( $url ) ){
		print "<br />No se pudo obtener la información de todos los followers. <br />";
		return 0;
	} else{
		$file = $user . $followers_file;
		createFile( $dir, $file, $url );
		print "<br />Se ha creado el archivo. <br />";
	}

	return $url;
}


function get_searchTwitter( $twitter_search, $dir, $hashtag_file ){
	//Lee la API de Twitter y lo guarda en una variable
	$url = file_get_contents( 'http://search.twitter.com/search.json?q=%23'. $twitter_search .'&lang=es&rpp=25' );

	//Error
	if( empty( $url ) ){
		print "<br />No se pudo obtener la información de todos los followers. <br />";
		return 0;
	} else{
		$file = $twitter_search . $hashtag_file;
		createFile( $dir, $file, $url );
		print "<br />Se ha creado el archivo. <br />";
	}

	return $url;
}

//Crea un archivo
function createFile( $carpeta, $archivo, $data ){
	//Verifica si existe la carpeta
	if( !is_dir ( $carpeta ) ){
		mkdir ( $carpeta, 0777 );
	}
	//Crear el string final de donde estara el archivo
	$FileSave = $carpeta . $archivo ;
	//Abre el archivo y marca error, en caso de no encontrar el archivo.
	$OpenFile = fopen ( $FileSave, 'w' ) or die( '\nError al momento de crear el Archivo: '. $FileSave );

	//Escribe en el archivo.
	fwrite( $OpenFile, $data );
	//Cierra el archivo.
	fclose( $OpenFile );
}

?>