<?php

	$cover_images_path = 'covers/';

	$db = new SQLite3( 'AudirvanaPlusLibrary.sqlite', true )
		or die( "Fuck this shit" );

	$q = 'SELECT Z_PK, ZALBUMARTISTSNAMES, ZTITLE FROM ZALBUM';
	$ret = $db->query( $q );

	while( $row = $ret->fetchArray())
	{
		$q = 'SELECT ZTRACKNUMBER, ZTITLE FROM ZTRACK WHERE ZALBUM="' . $row['Z_PK'] . '" ORDER BY ZTRACKNUMBER';
		$track_ret = $db->query( $q );
		$tracks = '';

		while( $track_row = $track_ret->fetchArray())
			$tracks[] = $track_row['ZTRACKNUMBER'] . '. ' . $track_row['ZTITLE'];

		$filename = $row['ZALBUMARTISTSNAMES'] . '-' . $row['ZTITLE'];
		$filename = str_replace( ' ', '_', $filename );
		$filename = str_replace( '/', '_', $filename );
		$filename = str_replace( 'ä', 'a', $filename );
		$filename = str_replace( 'ö', 'o', $filename );
		$filename .= '.jpg';
		$filename = strtolower( $filename );

		$row['cover'] = $cover_images_path . $filename;
		$row['tracks'] = $tracks; 
		
		$rows[] = $row;
	}

	echo json_encode( $rows );

?>
