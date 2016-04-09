<?php

	$cover_images_path = 'covers/';

	if(! file_exists( $cover_images_path ) )
		mkdir( $cover_images_path, 0755 );
	
	$db = new SQLite3( 'AudirvanaPlusLibrary.sqlite' );
	$q = 'SELECT ZARTWORKVIGNETTE, ZALBUMARTISTSNAMES, ZTITLE FROM ZALBUM';
	$ret = $db->query( $q );

	while( $row = $ret->fetchArray())
	{
		$filename = $row['ZALBUMARTISTSNAMES'] . '-' . $row['ZTITLE'];
		$filename = str_replace( ' ', '_', $filename );
		$filename = str_replace( '/', '_', $filename );
		$filename = str_replace( 'ä', 'a', $filename );
		$filename = str_replace( 'ö', 'o', $filename );
		$filename .= '.jpg';
		$filename = strtolower( $filename );

		$fh = fopen( $cover_images_path . $filename, 'w' );
		fwrite( $fh, $row['ZARTWORKVIGNETTE'] );
		fclose( $fh );
	}

?>
