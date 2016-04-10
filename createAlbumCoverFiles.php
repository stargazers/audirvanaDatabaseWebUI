<?php

	require 'shared_functions.php';

	$cover_images_path = 'covers/';

	if(! file_exists( $cover_images_path ) )
		mkdir( $cover_images_path, 0755 );
	
	$db = new SQLite3( 'AudirvanaPlusLibrary.sqlite' );
	$q = 'SELECT ZARTWORKVIGNETTE, ZALBUMARTISTSNAMES, ZTITLE FROM ZALBUM';
	$ret = $db->query( $q );

	while( $row = $ret->fetchArray())
	{
		$filename = get_album_cover_filename( $row );
		$filename = $cover_images_path . strtolower( $filename );

		if(! file_exists( $filename ) )
		{
			$fh = fopen( $filename, 'w' );
			fwrite( $fh, $row['ZARTWORKVIGNETTE'] );
			fclose( $fh );
		}
	}

?>
