<?php

	require 'shared_functions.php';

	$cover_images_path = 'covers/';

	// For str_replace so we can return real URL
	$original_path = 'Users/stargazers/Music/';
	$new_path = 'http://media.korpilaakso.net/';

	$db = new SQLite3( 'AudirvanaPlusLibrary.sqlite', true );
	$q = 'SELECT Z_PK, ZALBUMARTISTSNAMES, ZTITLE FROM ZALBUM';
	$ret = $db->query( $q );

	while( $row = $ret->fetchArray())
	{
		// Get tracks
		$q = 'SELECT ZTRACKNUMBER, ZPLAYCOUNT, ZTITLE, ZLOCATIONRELPATH '
			. 'FROM ZTRACK WHERE ZALBUM="' . $row['Z_PK'] . '"'
			. 'ORDER BY ZTRACKNUMBER';

		$track_ret = $db->query( $q );
		$tracks = '';

		// Add tracks array to this row
		while( $track_row = $track_ret->fetchArray())
		{
			$file_url = str_replace( $original_path, $new_path, $track_row['ZLOCATIONRELPATH'] );

			$track['number'] = $track_row['ZTRACKNUMBER'];
			$track['title'] = $track_row['ZTITLE'];
			$track['playcount'] = $track_row['ZPLAYCOUNT'];
			$track['url'] = $file_url;

			$tracks[] = $track;
		}

		$filename = get_album_cover_filename( $row );
			
		$row['cover'] = $cover_images_path . $filename;
		$row['tracks'] = $tracks; 
		
		$rows[] = $row;
	}

	echo json_encode( $rows );

?>
