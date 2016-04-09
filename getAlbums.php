<?php

	$cover_images_path = 'covers/';

	$db = new SQLite3( 'AudirvanaPlusLibrary.sqlite', true )
		or die( "Fuck this shit" );

	$q = 'SELECT ZALBUMARTISTSNAMES, ZTITLE FROM ZALBUM';
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

		$row['cover'] = $cover_images_path . $filename;
		$rows[] = $row;
	}

	echo json_encode( $rows );

?>
